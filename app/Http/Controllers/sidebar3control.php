<?php

namespace App\Http\Controllers;

use App\Models\Desas;
use Illuminate\Http\Request;
use App\Models\Kecamatans;
use App\Models\seleksi;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Charts\PendaftaranKecamatanChart;

use Vinkla\Hashids\Facades\Hashids;

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
            'seleksiHash' => Hashids::encode($seleksi->id),
            'desaHash'    => Hashids::encode($desaId),
        ]);
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
            'seleksiHash' => Hashids::encode($seleksi->id),
            'desaHash'    => Hashids::encode($desaId),
        ]);
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
            'seleksiHash' => Hashids::encode($seleksi->id),
            'desaHash'    => Hashids::encode($desaId),
        ]);
    }


   

    public function resolveSeleksiByDesa2($desaId)
    {
        $seleksi = Seleksi::where('id_desas', $desaId)
            ->latest('tahun')
            ->firstOrFail();

        return redirect()->route('showobservasi', [
            'seleksiHash' => Hashids::encode($seleksi->id),
            'desaHash'    => Hashids::encode($desaId),
        ]);
    }

    public function showMainOrb()
    {
        $kecamatans = Kecamatans::orderBy('nama_kecamatan')->get();
       
        return view('penguji.nilaiorbmain',compact('kecamatans'));
    }


    public function showtambahTPUMain(Seleksi $seleksi, Desas $desa)
    {
        $kecamatans = Kecamatans::orderBy('nama_kecamatan')->get();
            
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
            'seleksiHash' => Hashids::encode($seleksi->id),
            'desaHash'    => Hashids::encode($desaId),
        ]);
    }

    
    public function showtambahWWNMain(Seleksi $seleksi, Desas $desa)
    {
        $kecamatans = Kecamatans::orderBy('nama_kecamatan')->get();
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
            'seleksiHash' => Hashids::encode($seleksi->id),
            'desaHash'    => Hashids::encode($desaId),
            
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
