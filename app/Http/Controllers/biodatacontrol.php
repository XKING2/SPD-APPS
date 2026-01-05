<?php

namespace App\Http\Controllers;

use App\Models\biodata;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class biodatacontrol extends Controller
{

   public function store(Request $request)
    {
        $request->validate([
            'profile_img'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'kartu_keluarga'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'ktp'               => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'ijazah'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'cv'                => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
            'surat_pendaftaran' => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);


        $biodata = Biodata::firstOrNew(['id_user' => Auth::id()]);

        // === HANDLE FILES ===
        $fields = [
            'profile_img',
            'kartu_keluarga',
            'ktp',
            'ijazah',
            'cv',
            'surat_pendaftaran'
        ];

        foreach ($fields as $field) {
            if ($request->hasFile($field)) {

                // hapus file lama kalau ada
                if ($biodata->$field) {
                    Storage::disk('public')->delete($biodata->$field);
                }

                // simpan file baru
                $biodata->$field = $request->file($field)->store("uploads/$field", 'public');
            }
        }

        $biodata->id_user = Auth::id();
        $biodata->save();

        return back()->with('success', 'Biodata berhasil disimpan!');
    }

    public function index()
    {
        $admin = Auth::user();

        // 🔒 Pastikan admin terikat desa
        if (!$admin->id_desas) {
            abort(403, 'Admin tidak terikat dengan desa');
        }

        $biodatas = Biodata::whereIn('status', ['draft', 'valid'])
            ->whereHas('user', function ($q) use ($admin) {
                $q->where('id_desas', $admin->id_desas);
            })
            ->with('user')
            ->orderBy('id', 'asc')
            ->get();

        return view('admin.validasi.index', compact('biodatas'));
    }

    public function show($id)
    {

        $admin = Auth::user();

        $biodata = Biodata::with('user')->findOrFail($id);

        // 🔒 Proteksi desa
        if ($biodata->user->id_desas !== $admin->id_desas) {
            abort(403, 'Anda tidak berhak mengakses data ini');
        }

        return view('admin.validasi.validasibio', compact('biodata'));
    }

   public function Validasi(Request $request, Biodata $biodata)
    {
        // 🔒 Guard: hanya boleh dari pending
        if ($biodata->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Biodata tidak dalam status pending'
            ], 400);
        }

        // 🔁 Update status
        $biodata->update([
            'status'       => 'valid',
            'validated_at' => Carbon::now(),
        ]);


        return redirect()->route('validasi.index');
    }




}
