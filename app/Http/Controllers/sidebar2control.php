<?php

namespace App\Http\Controllers;

use App\Charts\pendaftarperdesaChart;
use App\Charts\StatusBiodataChart;
use App\Charts\weeklypendaftarChart;
use App\Models\biodata;
use App\Models\exams;
use App\Models\Formasi;
use App\Models\seleksi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;

class sidebar2control extends Controller
{
    public function index(Request $request,pendaftarperdesaChart $pendaftarChart,StatusBiodataChart $statusChart,weeklypendaftarChart $chartx) 
    {
        $admin  = Auth::user();
        $desaId = $admin->id_desas;

        // =============================
        // KARTU STATISTIK
        // =============================
        $totalPeserta = Biodata::whereHas('user', function ($q) use ($desaId) {
            $q->where('id_desas', $desaId);
        })->count();

        $pesertaValid = Biodata::where('status', 'valid')
            ->whereHas('user', fn ($q) => $q->where('id_desas', $desaId))
            ->count();

        $pesertaDraft = Biodata::where('status', 'draft')
            ->whereHas('user', fn ($q) => $q->where('id_desas', $desaId))
            ->count();

        // =============================
        // BAR CHART PENDAFTAR DESA
        // =============================
        $desaStat = Biodata::join('users', 'users.id', '=', 'biodata.id_user')
            ->join('desas', 'desas.id', '=', 'users.id_desas')
            ->where('users.id_desas', $desaId)
            ->selectRaw('desas.nama_desa as desa, COUNT(biodata.id) as total')
            ->groupBy('desas.nama_desa')
            ->first();

        $pendaftarDesaChart = $pendaftarChart->build(
            [$desaStat->desa ?? 'Desa'],
            [$desaStat->total ?? 0]
        );

        // =============================
        // PIE CHART STATUS BIODATA
        // =============================
        $stat = Biodata::whereHas('user', fn ($q) => $q->where('id_desas', $desaId))
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $statusBiodataChart = $statusChart->build(
            ['Valid', 'Draft'],
            [
                $stat['valid'] ?? 0,
                $stat['draft'] ?? 0,
            ]
        );
        
        $weekly = DB::table('biodata')
        ->join('users', 'users.id', '=', 'biodata.id_user')
        ->where('users.id_desas', $desaId)
        ->selectRaw("
            YEARWEEK(biodata.created_at, 1) as year_week,
            MIN(DATE(biodata.created_at)) as week_start,
            COUNT(*) as total")
        ->groupBy('year_week')
        ->orderBy('year_week')
        ->get();


    $labels = [];
    $values = [];

    foreach ($weekly as $row) {
        $start = Carbon::parse($row->week_start);
        $end   = (clone $start)->addDays(6);

        $labels[] = $start->format('d M') . ' - ' . $end->format('d M');
        $values[] = $row->total;
    }

    $weeklyPendaftarChart = $chartx->build($labels, $values);

        return view('admin.dashboard', compact(
            'totalPeserta',
            'pesertaValid',
            'pesertaDraft',
            'pendaftarDesaChart',
            'statusBiodataChart',
            'weeklyPendaftarChart'
        ));


    }

    public function startexams()
    {
        $user = Auth::user();

        // Ambil exam TPU & WWN berdasarkan desa
        $exams = Exams::where('id_desas', $user->id_desas)
            ->whereIn('type', ['tpu', 'wwn','orb'])
            ->get()
            ->keyBy('type');

        $examTPU = $exams->get('tpu');
        $examWWN = $exams->get('wwn');
        $examORB = $exams->get('orb');

        return view('admin.ujian.index', [
            'examTPU' => $examTPU,
            'examWWN' => $examWWN,
            'examORB' => $examORB,
            'now'     => now(),
        ]);
    }



    public function generate(Exams $exam)
    {
        $admin = Auth::user();

        // 🔒 Role check
        abort_if($admin->role !== 'admin', 403);

        // 🔒 Desa check
        abort_if($admin->id_desas !== $exam->id_desas, 403);

        // 🔒 Status exam
        abort_if($exam->status !== 'active', 403, 'Ujian belum aktif');

        if ($exam->key_expired_at && $exam->key_expired_at->isFuture()) {
            return back()->withErrors([
                'enrollment' => 'Enrollment key masih aktif dan belum kedaluwarsa'
            ]);
        }

        // ✅ Generate key baru
        $key = strtoupper(Str::random(6));

        $exam->update([
            'enrollment_key'  => $key,
            'key_generated_at' => now(),
            'key_expired_at'  => now()->addMinutes(10),
        ]);

        return back()->with('enrollment_key', $key);
    }

    public function generateWWN(Exams $exam)
    {
        $admin = Auth::user();

        // 🔒 Role check
        abort_if($admin->role !== 'admin', 403);

        // 🔒 Desa check
        abort_if($admin->id_desas !== $exam->id_desas, 403);

        // 🔒 Status exam
        abort_if($exam->status !== 'active', 403, 'Ujian belum aktif');

        
        if ($exam->key_expired_at && $exam->key_expired_at->isFuture()) {
            return back()->withErrors([
                'enrollment' => 'Enrollment key masih aktif dan belum kedaluwarsa'
            ]);
        }


        $key = strtoupper(Str::random(6));

        $exam->update([
            'enrollment_key'   => $key,
            'key_generated_at' => now(),
            'key_expired_at'  => now()->addMinutes(10),
        ]);

        return back()->with([
            'success' => 'Enrollment key berhasil dibuat.',
            'enrollment_key' => $key,
        ]);
    }

    public function generateOrb(Exams $exam)
    {
        $admin = Auth::user();

        // 🔒 Role check
        abort_if($admin->role !== 'admin', 403);

        // 🔒 Desa check
        abort_if($admin->id_desas !== $exam->id_desas, 403);

        // 🔒 Status exam
        abort_if($exam->status !== 'active', 403, 'Ujian belum aktif');

        if ($exam->key_expired_at && $exam->key_expired_at->isFuture()) {
            return back()->withErrors([
                'enrollment' => 'Enrollment key masih aktif dan belum kedaluwarsa'
            ]);
        }

        // ✅ Generate key baru
        $key = strtoupper(Str::random(6));

        $exam->update([
            'enrollment_key'  => $key,
            'key_generated_at' => now(),
            'key_expired_at'  => now()->addMinutes(10),
        ]);

        return back()->with([
            'success' => 'Enrollment key berhasil dibuat.',
            'enrollment_key' => $key,
        ]);
    }


    

    public function generatePageSaw(Request $request)
    {
        $seleksis = seleksi::orderBy('created_at', 'desc')->get();

        $selectedSeleksi = null;

        if ($request->has('seleksi_id')) {
            $selectedSeleksi = Seleksi::find($request->seleksi_id);
        }

        return view('admin.generatesaws', compact('seleksis', 'selectedSeleksi'));
    }


    public function editExams(string $hashexam)
    {
        $decoded = Hashids::decode($hashexam);

        if (empty($decoded)) {
            abort(404);
        }

        $id = $decoded[0];

        $exam = Exams::all()->findOrFail($id);

        return view('admin.admineditexams', [
            'exam'    => $exam,
            'seleksi' => Seleksi::orderBy('judul')->get(),
            'types'   => Exams::TYPES,
        ]);
    }

    public function updateExams(Request $request, exams $exam)
    {
        $request->validate([
            'judul'      => 'required|string',
            'type'       => 'required|in:tpu,wwn,orb',
            'duration'   => 'required|integer|min:1',
            'id_seleksi' => 'required|exists:selections,id',
            'start_at'   => 'required|date',
            'end_at'     => 'required|date|after:start_at',
        ]);

        $exam->update([
            'judul'      => $request->judul,
            'type'       => $request->type,
            'duration'   => $request->duration,
            'id_seleksi' => $request->id_seleksi,
            'start_at'   => $request->start_at,
            'end_at'     => $request->end_at,
            'status'     => 'draft'
        ]);

        return redirect()
            ->route('adminexams')
            ->with('success', 'Exam berhasil diperbarui');
    }

    public function FormasiIndex()
    {
        $desaId = Auth::user()->id_desas;

        $formasis = Formasi::with('seleksi')
            ->whereHas('seleksi', function ($q) use ($desaId) {
                $q->where('id_desas', $desaId);
            })
            ->latest()
            ->get();

        $seleksis = Seleksi::where('id_desas', $desaId)
            ->orderBy('tahun', 'desc')
            ->get();

        return view('admin.formasi.addformasimain', compact('formasis', 'seleksis'));
    }



}
