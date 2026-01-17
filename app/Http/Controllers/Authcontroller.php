<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Verification;
use App\Mail\OtpMail;
use App\Models\biodata;
use App\Models\Desas;
use App\Models\Kecamatans;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;



class Authcontroller extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.'
        ]);

        $credentials = $request->only('email', 'password');


        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // ================= ADMIN =================
            if ($user->role === 'admin') {

                $count = Biodata::where('status', 'draft')
                    ->where('id_desas', $user->id_desas)
                    ->where(function ($q) {
                        $q->where('notified_admin', 0)
                        ->orWhereNull('notified_admin');
                    })
                    ->count();

                if ($count > 0) {
                    Biodata::where('status', 'draft')
                        ->where('id_desas', $user->id_desas)
                        ->update(['notified_admin' => 1]);
                }

                session()->flash('admin_notifications', [
                    'login_success' => true,
                    'draft_count'   => $count
                ]);
            }


            // ================= USER =================
            if ($user->role === 'users') {

                $biodata = Biodata::where('id_user', $user->id)
                    ->whereIn('status', ['valid', 'rejected'])
                    ->where(function ($q) {
                        $q->where('notified', 0)
                        ->orWhereNull('notified');
                    })
                    ->first();

                if ($biodata) {
                    $biodata->notified = 1;
                    $biodata->save();
                }

                session()->flash('user_notifications', [
                    'login_success' => true,
                    'status'        => $biodata?->status
                ]);
            }


        return match ($user->role) {
            'admin'     => redirect()->route('admindashboard'),
            'penguji'   => redirect()->route('pengujidashboard')->with('success', 'Login berhasil!'),
            'users'     => redirect()->route('userdashboard'),
            default     => redirect()->route('login')->with('error', 'Role tidak dikenali.')
            };
        }

        // LOGIN GAGAL
        return back()->withErrors([
            'email' => 'Email atau password salah.'
        ])->withInput();
    }


    public function logout(Request $request)
    {
        $userId = Auth::id();
        $deleted = DB::table('sessions')->where('user_id', $userId)->delete();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Logout berhasil.');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'id_desas' => 'required|exists:desas,id',
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'id_desas' => $request->id_desas,
                'status'   => 'verify',
                'role'     => 'users',
            ]);

            $otp = random_int(100000, 999999);

            Verification::create([
                'user_id'    => $user->id,
                'unique_id'  => Str::uuid(),
                'otp'        => Hash::make($otp),
                'type'       => 'register',
                'send_via'   => 'email',
                'resend'     => 0,
                'attempts'   => 0,
                'expires_at' => now()->addMinutes(5),
                'status'     => 'active',
            ]);

            Mail::to($user->email)->queue(new OtpMail($otp));

            DB::commit();

            // simpan user sementara (BUKAN LOGIN)
            session(['otp_user_id' => $user->id]);

            return redirect()->route('otp.form')
                ->with('success', 'Kode OTP dikirim ke email.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);

            return back()->withErrors([
                'register' => 'Terjadi kesalahan sistem.'
            ]);
        }
    }

    public function otpForm()
    {
        if (!session()->has('otp_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.otppost');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6'
        ]);

        $userId = session('otp_user_id');
        $user = User::findOrFail($userId);

        $verification = Verification::where('user_id', $user->id)
            ->where('type', 'register')
            ->where('status', 'active')
            ->latest()
            ->first();

        if (!$verification) {
            return back()->withErrors(['otp' => 'OTP tidak valid.']);
        }

        if (now()->greaterThan($verification->expires_at)) {
            $verification->update(['status' => 'expired']);
            return back()->withErrors(['otp' => 'OTP kadaluarsa.']);
        }

        if ($verification->attempts >= 5) {
            $verification->update(['status' => 'blocked']);

            // 🔥 HAPUS USER & SESSION
            User::where('id', $user->id)->delete();
            session()->forget('otp_user_id');

            return redirect()->route('login')
                ->withErrors([
                    'email' => 'Verifikasi gagal. Silakan daftar ulang.'
                ]);
        }

        if (!Hash::check($request->otp, $verification->otp)) {
            $verification->increment('attempts');
            return back()->withErrors(['otp' => 'OTP salah.']);
        }

        // ✅ OTP VALID
        $verification->update(['status' => 'valid']);
        $user->update(['status' => 'actived']);

        // 🔥 HANCURKAN STATE OTP
        session()->forget('otp_user_id');

        return redirect()->route('login')->with([
            'status' => 'Verifikasi berhasil. Silakan login.'
        ]);

    }

    public function cancelOtp(Request $request)
    {
        $userId = session('otp_user_id');

        if (!$userId) {
            return redirect()->route('register.form');
        }

        DB::transaction(function () use ($userId) {

            Verification::where('user_id', $userId)->delete();

            User::where('id', $userId)->delete();
        });

        // 🔥 Bersihkan session OTP
        session()->forget('otp_user_id');

        return redirect()->route('register.form')
            ->with('success', 'Pendaftaran dibatalkan. Silakan daftar ulang.');
    }


    public function showRegisterForm()
    {

    $kecamatans = Kecamatans::orderBy('nama_kecamatan')->get();

    return view('register', compact('kecamatans'));

    }

    public function getDesa($kecamatan)
    {
        return response()->json(
            Desas::where('id_kecamatans', $kecamatan)
                ->orderBy('nama_desa')
                ->get()
        );
    }

    public function resendOtp(Request $request)
    {
        $userId = session('otp_user_id');

        if (!$userId) {
            return response()->json([
                'message' => 'Session OTP tidak valid'
            ], 403);
        }

        $user = User::find($userId);

        // 🔒 Ambil OTP aktif terakhir
        $verification = Verification::where('user_id', $user->id)
            ->where('type', 'register')
            ->where('status', 'active')
            ->latest()
            ->first();

        // 🔥 Kalau tidak ada OTP aktif → buat baru
        if (!$verification) {
            $code = rand(100000, 999999);

            Verification::create([
                'user_id'   => $user->id,
                'unique_id' => Str::uuid(),
                'otp'       => Hash::make($code),         // nanti bisa di-hash
                'type'      => 'register',
                'send_via'  => 'email',
                'resend'    => 1,
                'status'    => 'active',
            ]);

            Mail::to($user->email)->queue(new OtpMail($code));

            // TAMBAHAN: Response untuk AJAX
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kode OTP baru telah dikirim.'
                ]);
            }

            return back()->with('success', 'Kode OTP baru telah dikirim.');
        }

        // 🔒 Batasi resend (anti spam)
        if ($verification->resend >= 3) {
            // TAMBAHAN: Response untuk AJAX
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Batas kirim ulang OTP tercapai. Silakan tunggu beberapa saat.'
                ], 400);
            }

            return back()->withErrors([
                'otp' => 'Batas kirim ulang OTP tercapai. Silakan tunggu beberapa saat.'
            ]);
        }

        // 🔁 Generate OTP baru
        $code = rand(100000, 999999);

        $verification->update([
            'otp'    => Hash::make($code),
            'resend' => $verification->resend + 1,
            'attempts' => 0,
            'expires_at' => now()->addMinutes(5),
        ]);

        Mail::to($user->email)->queue(new OtpMail($code));

        // TAMBAHAN: Response untuk AJAX
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kode OTP berhasil dikirim ulang.'
            ]);
        }

        return back()->with('success', 'Kode OTP berhasil dikirim ulang.');
    }

    public function forgotpass(Request $request)
    {
        $request->validate(['email' => 'required|email']);
    
        $status = Password::sendResetLink(
            $request->only('email')
        );
    
        return $status === Password::ResetLinkSent
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);

    }

    public function updatepass(Request $request)
    {
        $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);
 
    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ]);
 
            $user->save();
 
            event(new PasswordReset($user));
        }
    );
 
    return $status === Password::PasswordReset
        ? redirect()->route('login')->with('status', __($status))
        : back()->withErrors(['email' => [__($status)]]);

    }

    
}
