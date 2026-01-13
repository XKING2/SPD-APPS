<?php

namespace App\Http\Controllers;

use App\Models\Desas;
use App\Models\Kecamatans;
use App\Models\seleksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;

class datausercontrol extends Controller
{
    public function showDataUser(Request $request)
    {
        $users = User::with('desas')
            ->whereIn('role', ['admin', 'penguji'])
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->orderBy('id')
            ->get();

        return view('penguji.datauser.datauser', compact('users'));
    }

    public function create()
    {
        $kecamatans = Kecamatans::orderBy('nama_kecamatan')->get();
        $desas = Desas::orderBy('nama_desa')->get();

        return view('penguji.datauser.adduser', compact('desas','kecamatans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:6',
            'role'      => 'required|in:admin,penguji',
            'id_desas'  => 'nullable|required_if:role,admin|exists:desas,id',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
            'status'   => 'verify',
            'id_desas' => $validated['role'] === 'admin'
                            ? $validated['id_desas']
                            : null,
        ]);

        // 🔐 LOG AKTIVITAS (AMAN)
        activity_log(
            'create',
            'Menambahkan user baru',
            $user,
            null,
            collect($user)->except(['password', 'remember_token'])->toArray()
        );

        return redirect()
            ->route('datauser')
            ->with('success', 'User berhasil ditambahkan');
    }

    public function edit(string $hashuser)
    {
        $decoded = Hashids::decode($hashuser);

        if (empty($decoded)) {
            abort(404);
        }

        $user = User::with('desas.kecamatan')
            ->findOrFail($decoded[0]);

        return view('penguji.datauser.edituser', [
            'user'        => $user,
            'desas'       => Desas::orderBy('nama_desa')->get(),
            'kecamatans'  => Kecamatans::orderBy('nama_kecamatan')->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'password'  => 'nullable|min:6',
            'role'      => 'required|in:admin,penguji',
            'id_desas'  => 'nullable|required_if:role,admin|exists:desas,id',
        ]);

        // 🔁 SIMPAN DATA LAMA (SEBELUM UPDATE)
        $oldValues = collect($user->toArray())
            ->except(['password', 'remember_token'])
            ->toArray();

        // 🔄 UPDATE DATA
        $user->update([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => !empty($validated['password'])
                            ? Hash::make($validated['password'])
                            : $user->password,
            'role'     => $validated['role'],
            'status'   => 'verify',
            'id_desas' => $validated['role'] === 'admin'
                            ? $validated['id_desas']
                            : null,
        ]);

        // 🧾 SIMPAN DATA BARU (SETELAH UPDATE)
        $newValues = collect($user->fresh()->toArray())
            ->except(['password', 'remember_token'])
            ->toArray();

        // 🔐 LOG AKTIVITAS
        activity_log(
            'update',
            'Mengupdate data user',
            $user,
            $oldValues,
            $newValues
        );

        return redirect()
            ->route('datauser')
            ->with('success', 'Data user berhasil diperbarui');
    }

    public function validasiUser(User $user)
    {
        // 🔍 Logging ringan (opsional tapi sangat membantu)
        Log::info('VALIDASI user', [
            'exam_id' => $user->id,
            'status_lama' => $user->status,
        ]);

        // 🔒 Hanya boleh dari draft → active
        if ($user->status !== 'verify') {
            return back()->withErrors([
                'status' => 'user tidak bisa divalidasi karena status bukan verify'
            ]);
        }

        // ✅ Update status saja
        $user->update([
            'status' => 'actived'
        ]);

        // 🔐 LOG AKTIVITAS (AMAN)
        activity_log(
            'Validasi data',
            'Validasi Data user',
            $user,
            null,
            collect($user)->toArray()
        );

        return back()->with('success', ' Data User berhasil divalidasi');
    }

    public function destroy(User $user)
    {

        $user->delete();

        // 🔐 LOG AKTIVITAS (AMAN)
        activity_log(
            'Menghapus data',
            'Menghapus Data user',
            $user,
            null,
            collect($user)->toArray()
        );

        return back()->with('success', 'Data User berhasil dihapus');
    }

}
