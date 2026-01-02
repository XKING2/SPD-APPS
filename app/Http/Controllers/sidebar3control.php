<?php

namespace App\Http\Controllers;

use App\Models\Desas;
use Illuminate\Http\Request;
use App\Models\ExamQuestion;
use App\Models\exams;
use App\Models\Kecamatans;
use App\Models\seleksi;
use App\Models\User;
use App\Models\wawancaraquest;
use Illuminate\Support\Facades\DB;
use App\Charts\PendaftaranKecamatanChart;

class sidebar3control extends Controller
{
    public function ShowDashboard(Request $request, PendaftaranKecamatanChart $areaChart)
    {
        $desaId      = $request->desa;
        $kecamatanId = $request->kecamatan;

        $query = User::query();

        if ($desaId) {
            $query->where('id_desas', $desaId);
        }

        if ($kecamatanId) {
            $query->whereHas('desas', function ($q) use ($kecamatanId) {
                $q->where('id_kecamatans', $kecamatanId);
            });
        }

        // ===== STATISTIK =====
        $totalPeserta = (clone $query)->count();
        $lulus = (clone $query)->where('role', 'penguji')->count();
        $belum = (clone $query)->where('role', 'users')->count();
        $totalDesa = (clone $query)->distinct('id_desas')->count('id_desas');

        $chartData = (clone $query)
            ->join('desas', 'users.id_desas', '=', 'desas.id')
            ->join('kecamatans', 'desas.id_kecamatans', '=', 'kecamatans.id')
            ->select(
                'kecamatans.nama_kecamatan as nama',
                DB::raw('COUNT(users.id) as total')
            )
            ->groupBy('kecamatans.nama_kecamatan')
            ->orderBy('kecamatans.nama_kecamatan', 'ASC')
            ->get();

        $labels = $chartData->pluck('nama')->toArray();
        $data   = $chartData->pluck('total')->toArray();

        return view('penguji.dashboard', [
            'totalPeserta' => $totalPeserta,
            'lulus' => $lulus,
            'belum' => $belum,
            'totalDesa' => $totalDesa,
            'kecamatans' => Kecamatans::orderBy('nama_kecamatan')->get(),
            'desas' => Desas::orderBy('nama_desa')->get(),
            'desaId' => $desaId,
            'kecamatanId' => $kecamatanId,
            'areaChart' => $areaChart->build($labels, $data),
        ]);
    }


    public function showSeleksi()
    {
        $seleksi = seleksi::orderBy('id')->get();
        $kecamatans = Kecamatans::orderBy('nama_kecamatan')->get();

        return view('penguji.seleksi', compact('kecamatans','seleksi'));
    }

    public function getDesaByKecamatan($id)
    {
        return Desas::where('id_kecamatans', $id)
            ->orderBy('nama_desa')
            ->get(['id', 'nama_desa']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'         => 'required|string|max:255',
            'deskripsi'     => 'nullable|string',
            'tahun'         => 'required|digits:4',
            'id_kecamatans' => 'required|exists:kecamatans,id',
            'id_desas'      => 'required|exists:desas,id',
        ]);

        // VALIDASI RELASI DESA ↔ KECAMATAN
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

    public function cekSeleksiDesa($desaId)
    {
        $seleksi = Seleksi::where('id_desas', $desaId)
            ->latest('tahun')
            ->first();

        if (!$seleksi) {
            return response()->json([
                'status' => false,
                'message' => 'Seleksi untuk desa ini belum tersedia.'
            ]);
        }

        return response()->json([
            'status' => true,
            'seleksi_id' => $seleksi->id
        ]);
    }




    public function showMainTPU()
    {
        $kecamatans = Kecamatans::orderBy('nama_kecamatan')->get();
        return view('penguji.nilaiTPUmain',compact('kecamatans'));
    }

    public function resolveSeleksiByDesa3($desaId)
    {
        $seleksi = Seleksi::where('id_desas', $desaId)
            ->latest('tahun')
            ->first();

        if (!$seleksi) {
            return redirect()
                ->back()
                ->with('error', 'Seleksi untuk desa ini belum tersedia.');
        }

        return redirect()->route('showtpu', [
            'seleksi' => $seleksi->id,
            'desa'    => $desaId
        ]);
    }

    public function shownilaiTPU(Request $request, $seleksiId, $desaId)
    {

        // Ambil desa
        $desa = Desas::findOrFail($desaId);

        // Ambil seleksi DAN pastikan seleksi itu memang milik desa ini
        $seleksi = Seleksi::where('id', $seleksiId)
            ->where('id_desas', $desaId)
            ->firstOrFail();

        $users = User::where('users.id_desas', $desaId)
            ->leftJoin('fuzzy_scores', function ($join) use ($seleksiId) {
                $join->on('users.id', '=', 'fuzzy_scores.user_id')
                    ->where('fuzzy_scores.id_seleksi', $seleksiId)
                    ->where('fuzzy_scores.type', 'TPU');
            })
            ->select(
                'users.id',
                'users.name',
                'fuzzy_scores.score_raw'
            )
            ->orderBy('users.name')
            ->get();
        return view('penguji.nilai.nilaiTPU', compact('users', 'desa', 'seleksi'));
    }

    public function showMainWWN()
    {
        $kecamatans = Kecamatans::orderBy('nama_kecamatan')->get();
        return view('penguji.nilaiWWNmain',compact('kecamatans'));
    }

    public function resolveSeleksiByDesa4($desaId)
    {
        $seleksi = Seleksi::where('id_desas', $desaId)
            ->latest('tahun')
            ->firstOrFail();

        return redirect()->route('ShowWWN', [
            'seleksi' => $seleksi->id,
            'desa'    => $desaId
        ]);
    }

    public function shownilaiWWN(Request $request, $seleksiId, $desaId)
    {
        // Ambil desa
        $desa = Desas::findOrFail($desaId);

        // Ambil seleksi DAN pastikan seleksi itu memang milik desa ini
        $seleksi = Seleksi::where('id', $seleksiId)
            ->where('id_desas', $desaId)
            ->firstOrFail();

        $users = User::where('users.id_desas', $desaId)
            ->leftJoin('fuzzy_scores', function ($join) use ($seleksiId) {
                $join->on('users.id', '=', 'fuzzy_scores.user_id')
                    ->where('fuzzy_scores.id_seleksi', $seleksiId)
                    ->where('fuzzy_scores.type', 'WWN');
            })
            ->select(
                'users.id',
                'users.name',
                'fuzzy_scores.score_raw'
            )
            ->orderBy('users.name')
            ->get();
        return view('penguji.nilai.nilaiWWN',compact('users', 'desa', 'seleksi'));
    }

    public function showMainPrak()
    {
        
        $kecamatans = Kecamatans::orderBy('nama_kecamatan')->get();
        return view('penguji.nilaiprakmain',compact('kecamatans'));
    }

    public function resolveSeleksiByDesa($desaId)
    {
        $seleksi = Seleksi::where('id_desas', $desaId)
            ->latest('tahun')
            ->firstOrFail();

        return redirect()->route('showpraktik', [
            'seleksi' => $seleksi->id,
            'desa'    => $desaId
        ]);
    }

    public function resolveSeleksiByDesa2($desaId)
    {
        $seleksi = Seleksi::where('id_desas', $desaId)
            ->latest('tahun')
            ->firstOrFail();

        return redirect()->route('showobservasi', [
            'seleksi' => $seleksi->id,
            'desa'    => $desaId
        ]);
    }

    

    

    public function shownilaipraktik(Request $request, $seleksiId, $desaId)
    {
        // Ambil desa
        $desa = Desas::findOrFail($desaId);

        // Ambil seleksi DAN pastikan seleksi itu memang milik desa ini
        $seleksi = Seleksi::where('id', $seleksiId)
            ->where('id_desas', $desaId)
            ->firstOrFail();

        // Ambil user berdasarkan desa
        $users = User::where('id_desas', $desaId)
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->orderBy('name')
            ->get();

        return view(
            'penguji.nilai.nilaipraktik',
            compact('users', 'desa', 'seleksi')
        );
    }

    public function showMainOrb()
    {
        $kecamatans = Kecamatans::orderBy('nama_kecamatan')->get();
       
        return view('penguji.nilaiorbmain',compact('kecamatans'));
    }

    public function shownilaiobservasi(Request $request, $seleksiId, $desaId)
    {

        $desa = Desas::findOrFail($desaId);

        // Ambil seleksi DAN pastikan seleksi itu memang milik desa ini
        $seleksi = Seleksi::where('id', $seleksiId)
            ->where('id_desas', $desaId)
            ->firstOrFail();

        // Ambil user berdasarkan desa
        $users = User::where('id_desas', $desaId)
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->orderBy('name')
            ->get();

        return view(
            'penguji.nilai.nilaiobservasi',
            compact('users', 'desa', 'seleksi')
        );
    }


    public function showtambahTPUMain(Seleksi $seleksi, Desas $desa)
    {
        $kecamatans = Kecamatans::orderBy('nama_kecamatan')->get();
            // 🔒 VALIDASI: desa HARUS milik seleksi
        if ($seleksi->id_desas !== $desa->id) {
            abort(403, 'Desa tidak sesuai dengan seleksi');
        }

        return view('penguji.tambahsoalTPUmain', [
            'seleksi' => $seleksi,
            'desa'    => $desa,
            'kecamatans'=> $kecamatans

        ]);
    }

    public function resolveSeleksiByDesa5($desaId)
    {
        $seleksi = Seleksi::where('id_desas', $desaId)
            ->latest('tahun')
            ->firstOrFail();

        return redirect()->route('addTPU', [
            'seleksi' => $seleksi->id,
            'desa'    => $desaId
        ]);
    }

    public function showtambahTPU(Seleksi $seleksi, Desas $desa)
    {
        // 🔒 Validasi desa milik seleksi
        if ($seleksi->id_desas !== $desa->id) {
            abort(403, 'Desa tidak sesuai dengan seleksi');
        }

        // 1️⃣ Cari exam TPU untuk desa + seleksi ini
        $exam = Exams::where('id_seleksi', $seleksi->id)
            ->where('id_desas', $desa->id)
            ->where('type', 'TPU')
            ->first();

        // 2️⃣ Jika exam ada → ambil soalnya
        $questions = [];
        if ($exam) {
            $questions = ExamQuestion::where('id_exam', $exam->id)
                ->orderBy('id', 'desc')
                ->get();
        }

        return view('penguji.tambahsoal.tambahTPU', [
            'seleksi'   => $seleksi,
            'desa'      => $desa,
            'exam'      => $exam,        // penting untuk view
            'questions' => $questions,
            'types'     => Exams::TYPES,
            'kecamatans'=> Kecamatans::orderBy('nama_kecamatan')->get(),
        ]);
    }

    public function showtambahWWNMain(Seleksi $seleksi, Desas $desa)
    {
        $kecamatans = Kecamatans::orderBy('nama_kecamatan')->get();
            // 🔒 VALIDASI: desa HARUS milik seleksi
        if ($seleksi->id_desas !== $desa->id) {
            abort(403, 'Desa tidak sesuai dengan seleksi');
        }

        return view('penguji.tambahsoalWWNmain', [
            'seleksi' => $seleksi,
            'desa'    => $desa,
            'kecamatans'=> $kecamatans

        ]);
    }

    public function resolveSeleksiByDesa6($desaId)
    {
        $seleksi = Seleksi::where('id_desas', $desaId)
            ->latest('tahun')
            ->firstOrFail();

        return redirect()->route('addWWN', [
            'seleksi' => $seleksi->id,
            'desa'    => $desaId
        ]);
    }



    public function showtambahwawancara(Seleksi $seleksi, Desas $desa)
    {

         // 🔒 Validasi desa milik seleksi
        if ($seleksi->id_desas !== $desa->id) {
            abort(403, 'Desa tidak sesuai dengan seleksi');
        }

        // 1️⃣ Cari exam TPU untuk desa + seleksi ini
        $exam = Exams::where('id_seleksi', $seleksi->id)
            ->where('id_desas', $desa->id)
            ->where('type', 'WWN')
            ->first();

        // 2️⃣ Jika exam ada → ambil soalnya
        $questions = [];
        if ($exam) {
            $questions = wawancaraquest::where('id_exams', $exam->id)
                ->orderBy('id', 'desc')
                ->get();
        }

        return view('penguji.tambahsoal.tambahwawancara', [
            'seleksi'   => $seleksi,
            'desa'      => $desa,
            'exam'      => $exam,        // penting untuk view
            'questions' => $questions,
            'types'     => Exams::TYPES,
            'kecamatans'=> Kecamatans::orderBy('nama_kecamatan')->get(),
        ]);

    }

    public function generatePage(Request $request)
    {
        $seleksis = Seleksi::orderBy('created_at', 'desc')->get();

        $selectedSeleksi = null;

        if ($request->has('seleksi_id')) {
            $selectedSeleksi = Seleksi::find($request->seleksi_id);
        }

        return view('penguji.generateSaw', compact('seleksis', 'selectedSeleksi'));
    }
}
