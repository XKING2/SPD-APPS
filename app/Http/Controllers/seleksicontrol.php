<?php

namespace App\Http\Controllers;

use App\Models\Desas;
use App\Models\Kecamatans;
use App\Models\seleksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Vinkla\Hashids\Facades\Hashids;

class seleksicontrol extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'judul'         => 'required|string|max:255',
            'deskripsi'     => 'nullable|string',
            'tahun'         => 'required|digits:4',
            'id_kecamatans' => 'required|exists:kecamatans,id',
            'id_desas'      => 'required|exists:desas,id',
        ]);

        $desaValid = Desas::where('id', $request->id_desas)
            ->where('id_kecamatans', $request->id_kecamatans)
            ->exists();

        if (!$desaValid) {
            return back()->withErrors([
                'id_desas' => 'Desa tidak sesuai dengan kecamatan yang dipilih'
            ]);
        }

        DB::transaction(function () use ($request) {
            seleksi::create([
                'judul'     => $request->judul,
                'deskripsi' => $request->deskripsi,
                'tahun'     => $request->tahun,
                'id_desas'  => $request->id_desas,
                'id_kecamatans'  => $request->id_kecamatans,
            ]);
        });

        return redirect()
            ->back()
            ->with('success', 'Data seleksi berhasil dibuat');
    }

    public function edit(string $hashseleksi)
    {
        $decoded = Hashids::decode($hashseleksi);

        if (empty($decoded)) {
            abort(404);
        }

        $id = $decoded[0];

        $seleksi = seleksi::all()->findOrFail($id);

        return view('penguji.editseleksi', [
            'seleksi'    => $seleksi,
            'desas'      => Desas::orderBy('nama_desa')->get(),
            'kecamatans' => Kecamatans::orderBy('nama_kecamatan')->get(),
        ]);
    }

    public function update(Request $request, seleksi $seleksi)
    {
        $request->validate([
            'judul'         => 'required|string|max:255',
            'deskripsi'     => 'nullable|string',
            'tahun'         => 'required|digits:4',
            'id_desas'      => 'required|exists:desas,id',
            'id_kecamatans' => 'required|exists:kecamatans,id',
        ]);

        $seleksi->update($request->all());

        return redirect()
            ->route('addseleksi')
            ->with('success', 'Data seleksi berhasil diperbarui');
    }

    public function destroy(seleksi $seleksi)
    {
        // ⚠️ PROTEKSI DATA
        if ($seleksi->exams()->exists()) {
            return back()->withErrors([
                'delete' => 'Seleksi tidak bisa dihapus karena masih memiliki exam'
            ]);
        }

        $seleksi->delete();

        return back()->with('success', 'Data seleksi berhasil dihapus');
    }
}
