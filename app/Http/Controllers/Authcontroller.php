<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Verification;
use App\Mail\OtpMail;
use App\Models\Kecamatans;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class Authcontroller extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        // VALIDASI INPUT
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.'
        ]);

        // CREDENTIALS
        $credentials = $request->only('email', 'password');

        // COBA LOGIN
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user(); // ambil data user setelah login

            // 🔥 REDIRECT BERDASARKAN ROLE
            return match ($user->role) {
                'admin'     => redirect()->route('admindashboard')->with('success', 'Login berhasil!'),
                'penguji'   => redirect()->route('pengujidashboard')->with('success', 'Login berhasil!'),
                'users'     => redirect()->route('userdashboard')->with('success', 'Login berhasil!'),
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
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah logout.');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'       => 'required',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:8',
            'id_desas'   => 'required|exists:desas,id',
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'id_desas'  => $request->id_desas,
            'status'    => 'verify'
        ]);

        $code = rand(100000,999999);

        Verification::create([
            'user_id'=>$user->id,
            'unique_id'=>uniqid(),
            'otp'=>$code,
            'type'=>'register',
            'send_via'=>'email',
            'resend'=>0,
            'status'=>'active'
        ]);

        Mail::to($user->email)->queue(new OtpMail($code));


        Auth::login($user); 

        return redirect()->route('otp.form')->with('success','Kode OTP telah dikirim ke email');
    }

    public function otpForm()
    {
        return view('auth.otppost');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required'
        ]);

        $users = Auth::user();

        // Cek apakah ada OTP yang valid
        $verification = Verification::where('user_id', $users->id)
            ->where('otp', $request->otp)
            ->where('status', 'active')
            ->first();

        if (!$verification) {
            return back()->withErrors(['otp' => 'Kode OTP salah atau sudah kadaluarsa.']);
        }

        // Update status verifikasi
        $verification->update([
            'status' => 'valid'
        ]);

        // Update status verifikasi
        $users->update([
            'status' => 'actived'
        ]);

        return redirect()->route('userdashboard')
            ->with('success', 'Akun berhasil diaktifkan!');
    }

    public function showRegisterForm()
    {

    $kecamatans = Kecamatans::orderBy('nama_kecamatan')->get();

    return view('register', compact('kecamatans'));

    }
}
