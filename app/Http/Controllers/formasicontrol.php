<?php

namespace App\Http\Controllers;

use App\Models\Formasi;
use App\Models\seleksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;

class formasicontrol extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'id_seleksi' => 'required|exists:selections,id',
        ]);

        $seleksi = Seleksi::findOrFail($request->id_seleksi);

        Formasi::create([
            'id_seleksi' => $seleksi->id,
            'id_desas'   => $seleksi->id_desas,
            'tahun'      => $seleksi->tahun,
        ]);




        return back()->with('success', 'Formasi berhasil dibuat');
    }

    public function show(string $hashFormasi)
    {
        $decoded = Hashids::decode($hashFormasi);

        if (empty($decoded)) {
            abort(404);
        }

        $id = $decoded[0];

        $formasi = Formasi::with(['seleksi', 'kebutuhan'])->findOrFail($id);

        return view('admin.formasi.addformasi', compact('formasi'));
    }
    public function storeKebutuhan(Request $request, Formasi $formasi)
    {
        $request->validate([
            'nama_kebutuhan' => 'required|string|max:255',
            'jumlah'       => 'required|integer|min:1',
        ]);

        

        $formasi->kebutuhan()->create([
            'nama_kebutuhan' => $request->nama_kebutuhan    ,
            'jumlah'       => $request->jumlah,
            'tahun'        => $formasi->tahun,
        ]);
        

        return back()->with('success', 'Kebutuhan berhasil ditambahkan');
    }



}
