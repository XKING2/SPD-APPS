<?php

namespace App\Http\Controllers;

use App\Models\FuzzyScore;
use App\Models\rankings;
use App\Models\seleksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Sawcontrol extends Controller
{
    public function generatePage(Request $request)
    {
        $seleksis = Seleksi::orderBy('created_at', 'desc')->get();

        $selectedSeleksi = null;
        $resultExams = collect();
        $fuzzyScores = collect();
        $maxValues = [];
        $rankings = collect();

        if ($request->filled('seleksi_id')) {

            $selectedSeleksi = Seleksi::find($request->seleksi_id);

            if ($selectedSeleksi) {

                /* =======================
                |  TABEL 4 – RESULT EXAMS
                ======================= */
                $resultExams = DB::table('exam_user_results as eur')
                    ->join('users as u', 'eur.user_id', '=', 'u.id')
                    ->join('exams as e', 'eur.exam_id', '=', 'e.id')
                    ->where('e.id_seleksi', $selectedSeleksi->id)
                    ->where('eur.is_submitted', true)
                    ->select(
                        'u.id as user_id',
                        'u.name',
                        'eur.type',
                        'eur.score'
                    )
                    ->get()
                    ->groupBy('user_id');

                /* =======================
                |  TABEL 5 – FUZZY SCORE
                ======================= */
                $fuzzyScores = DB::table('fuzzy_scores as fs')
                    ->join('users as u', 'fs.user_id', '=', 'u.id')
                    ->where('fs.id_seleksi', $selectedSeleksi->id)
                    ->select(
                        'u.id as user_id',
                        'u.name',
                        'fs.type',
                        'fs.score_crisp'
                    )
                    ->get()
                    ->groupBy('user_id');

                /* =======================
                |  NILAI MAX PER KRITERIA
                ======================= */
                if ($fuzzyScores->isNotEmpty()) {
                    $all = $fuzzyScores->flatten();

                    $maxValues = [
                        'TPU'  => max(1, $all->where('type', 'TPU')->max('score_crisp')),
                        'PRAK' => max(1, $all->where('type', 'PRAK')->max('score_crisp')),
                        'WWN'  => max(1, $all->where('type', 'WWN')->max('score_crisp')),
                        'ORB'  => max(1, $all->where('type', 'ORB')->max('score_crisp')),
                    ];
                }

                /* =======================
                |  TABEL 7 – RANKING SAW
                ======================= */
                $rankings = DB::table('rankings as r')
                    ->join('users as u', 'r.user_id', '=', 'u.id')
                    ->where('r.id_seleksi', $selectedSeleksi->id)
                    ->orderBy('r.peringkat')
                    ->select(
                        'u.name',
                        'r.nilai_saw',
                        'r.peringkat'
                    )
                    ->get();
            }
        }

        return view('penguji.generateSaw', compact(
            'seleksis',
            'selectedSeleksi',
            'resultExams',
            'fuzzyScores',
            'maxValues',
            'rankings'
        ));
    }

    /**
     * Generate ranking SAW
     */
    public function generate($seleksiId)
    {
        // Ambil semua fuzzy scores untuk seleksi ini
        $scores = FuzzyScore::where('id_seleksi', $seleksiId)
            ->get()
            ->groupBy('user_id');
        
        $userIds = $scores->keys();

        // Buat matrix nilai crisp
        $matrix = [];
        foreach ($userIds as $userId) {
            $userScores = $scores->get($userId, collect());

            $matrix[$userId] = [
                'TPU'  => (int) optional($userScores->firstWhere('type', 'TPU'))->score_crisp ?? 0,
                'PRAK' => (int) optional($userScores->firstWhere('type', 'PRAK'))->score_crisp ?? 0,
                'WWN'  => (int) optional($userScores->firstWhere('type', 'WWN'))->score_crisp ?? 0,
                'ORB'  => (int) optional($userScores->firstWhere('type', 'ORB'))->score_crisp ?? 0,
            ];
        }

        // Hitung nilai maksimum untuk setiap kriteria
        $max = [
            'TPU'  => max(1, collect($matrix)->max('TPU')),
            'PRAK' => max(1, collect($matrix)->max('PRAK')),
            'WWN'  => max(1, collect($matrix)->max('WWN')),
            'ORB'  => max(1, collect($matrix)->max('ORB')),
        ];

        // Bobot kriteria
        $weights = [
            'TPU'  => 35,
            'PRAK' => 35,
            'WWN'  => 20,
            'ORB'  => 10,
        ];

        // Proses perhitungan SAW dalam transaction
        DB::transaction(function () use ($matrix, $max, $weights, $seleksiId) {
            // Hapus ranking lama
            Rankings::where('id_seleksi', $seleksiId)->delete();

            $results = [];

            // Hitung nilai SAW untuk setiap user
            foreach ($matrix as $userId => $nilai) {
                $nilaiSaw =
                    ($nilai['TPU']  / $max['TPU'])  * $weights['TPU'] +
                    ($nilai['PRAK'] / $max['PRAK']) * $weights['PRAK'] +
                    ($nilai['WWN']  / $max['WWN'])  * $weights['WWN'] +
                    ($nilai['ORB']  / $max['ORB'])  * $weights['ORB'];

                $results[] = [
                    'user_id'   => $userId,
                    'nilai_saw' => round($nilaiSaw, 2),
                    'TPU'       => $nilai['TPU'],
                    'PRAK'      => $nilai['PRAK'],
                    'WWN'       => $nilai['WWN'],
                    'ORB'       => $nilai['ORB'],
                ];
            }

            // Sorting hasil berdasarkan nilai SAW (descending)
            // Dengan tie breaker: TPU > PRAK > WWN > ORB
            usort($results, function ($a, $b) {
                return
                    $b['nilai_saw'] <=> $a['nilai_saw']
                    ?: $b['TPU']     <=> $a['TPU']
                    ?: $b['PRAK']    <=> $a['PRAK']
                    ?: $b['WWN']     <=> $a['WWN']
                    ?: $b['ORB']     <=> $a['ORB'];
            });

            // Simpan ranking ke database
            $rank = 1;
            foreach ($results as $row) {
                Rankings::create([
                    'id_seleksi' => $seleksiId,
                    'user_id'    => $row['user_id'],
                    'nilai_saw'  => $row['nilai_saw'],
                    'peringkat'  => $rank++,
                ]);
            }
        });

         // 🔐 LOG AKTIVITAS (BENAR & RINGKAS)
        activity_log(
            'generate',
            'Generate ranking SAW seleksi',
            null,
            null,
            [
                'id_seleksi' => $seleksiId,
                'method' => 'SAW + Fuzzy'
            ]
        );

        return back()->with('success', 'Ranking SAW berhasil digenerate! Silakan lihat hasil perhitungan di bawah.');
    }


    public function generateAdminPage(Request $request)
    {
        $user = Auth::user();

        // 🔒 ADMIN DESA HANYA LIHAT DESANYA SENDIRI
        $seleksis = Seleksi::where('id_desas', $user->id_desas)
            ->orderBy('created_at', 'desc')
            ->get();

        $selectedSeleksi = null;
        $resultExams = collect();
        $fuzzyScores = collect();
        $maxValues = [];
        $rankings = collect();

        if ($request->filled('seleksi_id')) {

            $selectedSeleksi = Seleksi::where('id', $request->seleksi_id)
                ->where('id_desas', $user->id_desas)
                ->firstOrFail();

            if (!$selectedSeleksi) {
                    abort(403, 'Anda tidak memiliki akses ke seleksi ini');
                }

                $resultExams = DB::table('exam_user_results as eur')
                    ->join('users as u', 'eur.user_id', '=', 'u.id')
                    ->join('exams as e', 'eur.exam_id', '=', 'e.id')
                    ->where('e.id_seleksi', $selectedSeleksi->id)
                    ->where('eur.is_submitted', true)
                    ->when($user->role !== 'admin', function ($q) use ($user) {
                        $q->where('u.id_desas', $user->id_desas);
                    })
                    ->select(
                        'u.id as user_id',
                        'u.name',
                        'eur.type',
                        'eur.score'
                    )
                    ->get()
                    ->groupBy('user_id');

                $fuzzyScores = DB::table('fuzzy_scores as fs')
                    ->join('users as u', 'fs.user_id', '=', 'u.id')
                    ->where('fs.id_seleksi', $selectedSeleksi->id)
                    ->when($user->role !== 'admin', function ($q) use ($user) {
                        $q->where('u.id_desas', $user->id_desas);
                    })
                    ->select(
                        'u.id as user_id',
                        'u.name',
                        'fs.type',
                        'fs.score_crisp'
                    )
                    ->get()
                    ->groupBy('user_id');

                if ($fuzzyScores->isNotEmpty()) {

                    $all = $fuzzyScores->flatten();

                    $maxValues = [
                        'TPU'  => max(1, $all->where('type', 'TPU')->max('score_crisp')),
                        'PRAK' => max(1, $all->where('type', 'PRAK')->max('score_crisp')),
                        'WWN'  => max(1, $all->where('type', 'WWN')->max('score_crisp')),
                        'ORB'  => max(1, $all->where('type', 'ORB')->max('score_crisp')),
                    ];
                }

                $rankings = DB::table('rankings as r')
                    ->join('users as u', 'r.user_id', '=', 'u.id')
                    ->where('r.id_seleksi', $selectedSeleksi->id)
                    ->when($user->role !== 'admin', function ($q) use ($user) {
                        $q->where('u.id_desas', $user->id_desas);
                    })
                    ->orderBy('r.peringkat')
                    ->select(
                        'u.name',
                        'r.nilai_saw',
                        'r.peringkat'
                    )
                    ->get();
        }

        return view('admin.generatesaws', compact(
            'seleksis',
            'selectedSeleksi',
            'resultExams',
            'fuzzyScores',
            'maxValues',
            'rankings'
        ));
    }






    public function generateAdmin($seleksiId)
    {
        // Ambil semua fuzzy scores untuk seleksi ini
        $scores = FuzzyScore::where('id_seleksi', $seleksiId)
            ->get()
            ->groupBy('user_id');
        
        $userIds = $scores->keys();

        // Buat matrix nilai crisp
        $matrix = [];
        foreach ($userIds as $userId) {
            $userScores = $scores->get($userId, collect());

            $matrix[$userId] = [
                'TPU'  => (int) optional($userScores->firstWhere('type', 'TPU'))->score_crisp ?? 0,
                'PRAK' => (int) optional($userScores->firstWhere('type', 'PRAK'))->score_crisp ?? 0,
                'WWN'  => (int) optional($userScores->firstWhere('type', 'WWN'))->score_crisp ?? 0,
                'ORB'  => (int) optional($userScores->firstWhere('type', 'ORB'))->score_crisp ?? 0,
            ];
        }

        // Hitung nilai maksimum untuk setiap kriteria
        $max = [
            'TPU'  => max(1, collect($matrix)->max('TPU')),
            'PRAK' => max(1, collect($matrix)->max('PRAK')),
            'WWN'  => max(1, collect($matrix)->max('WWN')),
            'ORB'  => max(1, collect($matrix)->max('ORB')),
        ];

        // Bobot kriteria
        $weights = [
            'TPU'  => 35,
            'PRAK' => 35,
            'WWN'  => 20,
            'ORB'  => 10,
        ];

        // Proses perhitungan SAW dalam transaction
        DB::transaction(function () use ($matrix, $max, $weights, $seleksiId) {
            // Hapus ranking lama
            Rankings::where('id_seleksi', $seleksiId)->delete();

            $results = [];

            // Hitung nilai SAW untuk setiap user
            foreach ($matrix as $userId => $nilai) {
                $nilaiSaw =
                    ($nilai['TPU']  / $max['TPU'])  * $weights['TPU'] +
                    ($nilai['PRAK'] / $max['PRAK']) * $weights['PRAK'] +
                    ($nilai['WWN']  / $max['WWN'])  * $weights['WWN'] +
                    ($nilai['ORB']  / $max['ORB'])  * $weights['ORB'];

                $results[] = [
                    'user_id'   => $userId,
                    'nilai_saw' => round($nilaiSaw, 2),
                    'TPU'       => $nilai['TPU'],
                    'PRAK'      => $nilai['PRAK'],
                    'WWN'       => $nilai['WWN'],
                    'ORB'       => $nilai['ORB'],
                ];
            }

            // Sorting hasil berdasarkan nilai SAW (descending)
            // Dengan tie breaker: TPU > PRAK > WWN > ORB
            usort($results, function ($a, $b) {
                return
                    $b['nilai_saw'] <=> $a['nilai_saw']
                    ?: $b['TPU']     <=> $a['TPU']
                    ?: $b['PRAK']    <=> $a['PRAK']
                    ?: $b['WWN']     <=> $a['WWN']
                    ?: $b['ORB']     <=> $a['ORB'];
            });

            // Simpan ranking ke database
            $rank = 1;
            foreach ($results as $row) {
                Rankings::create([
                    'id_seleksi' => $seleksiId,
                    'user_id'    => $row['user_id'],
                    'nilai_saw'  => $row['nilai_saw'],
                    'peringkat'  => $rank++,
                ]);
            }
        });

        return back()->with('success', 'Ranking SAW berhasil digenerate! Silakan lihat hasil perhitungan di bawah.');
    }

}
