<?php

namespace App\Http\Controllers;

use App\Models\biodata;
use App\Models\Formasi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Vinkla\Hashids\Facades\Hashids;

class biodatacontrol extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'id_formasi'        => 'required|exists:formasis,id',
            'id_kebutuhan'      => 'required|exists:kebutuhan_formasi,id',
            'profile_img'       => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'kartu_keluarga'    => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'ktp'               => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'ijazah'            => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'cv'                => 'required|mimes:pdf,jpg,jpeg,png|max:4096',
            'surat_pendaftaran' => 'required|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $user = Auth::user();

        $biodata = Biodata::firstOrNew([
            'id_user' => $user->id
        ]);

        // === SIMPAN PILIHAN FORMASI & KEBUTUHAN (INI INTI) ===
        $biodata->id_formasi   = $request->id_formasi;
        $biodata->id_kebutuhan = $request->id_kebutuhan;

        // === HANDLE FILE UPLOAD ===
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

                // hapus file lama
                if ($biodata->$field) {
                    Storage::disk('public')->delete($biodata->$field);
                }

                // simpan file baru
                $biodata->$field = $request->file($field)
                    ->store("uploads/$field", 'public');
            }
        }

        $biodata->id_desas = $user->id_desas;
        $biodata->status = 'draft';
        $biodata->save();

        return back()->with('success', 'Biodata dan pilihan formasi berhasil disimpan!');
    }

    public function edit(string $hashbio)
    {
        $decoded = Hashids::decode($hashbio);

        if (empty($decoded)) {
            abort(404);
        }

        $user = Auth::user();

        $formasis = Formasi::with('kebutuhan')
            ->where('id_desas', $user->id_desas)
            ->where('tahun', now()->year)
            ->get();


        $biodata = Biodata::where('id_user', $user->id)
            ->where('id', $decoded[0])
            ->firstOrFail();

        $profileImg = $biodata->profile_img ?? 'img/undraw_profile.svg';

        return view('users.updatebio', compact('biodata','formasis','profileImg'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $biodata = Biodata::where('id_user', $user->id)->firstOrFail();

        // 🔒 jika sudah valid → stop
        if ($biodata->status === 'valid') {
            return back()->with('error', 'Biodata sudah valid dan tidak dapat diubah');
        }

        $request->validate([
            'id_formasi'        => 'required|exists:formasis,id',
            'id_kebutuhan'      => 'required|exists:kebutuhan_formasi,id',
            'profile_img'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'kartu_keluarga'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'ktp'               => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'ijazah'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'cv'                => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
            'surat_pendaftaran' => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        // update relasi formasi
        $biodata->update([
            'id_formasi'   => $request->id_formasi,
            'id_kebutuhan' => $request->id_kebutuhan,
            'status'       => 'draft', // reset validasi
        ]);

        // upload file
        $files = [
            'profile_img',
            'kartu_keluarga',
            'ktp',
            'ijazah',
            'cv',
            'surat_pendaftaran'
        ];

        foreach ($files as $file) {
            if ($request->hasFile($file)) {
                if ($biodata->$file) {
                    Storage::disk('public')->delete($biodata->$file);
                }

                $path = $request->file($file)->store('biodata', 'public');
                $biodata->update([$file => $path]);
            }
        }

         return redirect()->route('showbiodata')->with('success', 'Validasi berhasil.');
    }


    public function index()
    {
        $desaId = Auth::user()->id_desas;
        $admin = Auth::user();

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

    public function show($hash)
    {
        $decoded = Hashids::decode($hash);

        if (count($decoded) === 0) {
            abort(404);
        }

        $id = $decoded[0];

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


        return redirect()->route('validasi.index')->with('success', 'Validasi berhasil.');
    }


    public function editbio(Request $request, Biodata $biodata)
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


        return redirect()->route('validasi.index')->with('success', 'Validasi berhasil.');
    }



}
