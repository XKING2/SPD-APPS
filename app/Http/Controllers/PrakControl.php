<?php

namespace App\Http\Controllers;

use App\Models\Desas;
use App\Models\FuzzyRule;
use App\Models\FuzzyScore;
use App\Models\PrakResult;
use App\Models\ResultExam;
use App\Models\seleksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Vinkla\Hashids\Facades\Hashids;

class PrakControl extends Controller
{
    public function addnilaiprak(string $seleksiHash, string $userHash)
    {
        // Decode hash → ID
        $seleksiId = Hashids::decode($seleksiHash)[0] ?? null;
        $userId    = Hashids::decode($userHash)[0] ?? null;

        if (!$seleksiId || !$userId) {
            abort(404);
        }

        $user = User::with('desas')->findOrFail($userId);
        $seleksi = seleksi::findOrFail($seleksiId);
        $desaHash = Hashids::encode($user->id_desas);

        return view('penguji.tambahform.tambahnilaiprak', [
            'user'         => $user,
            'seleksi'      => $seleksi,
            'seleksiHash'  => $seleksiHash,
            'userHash'     => $userHash,
            'desaHash'    => $desaHash,
        ]);
    }

    public function storePrak(Request $request, string $seleksiHash, string $userHash)
    {
        $seleksiId = Hashids::decode($seleksiHash)[0] ?? null;
        $userId    = Hashids::decode($userHash)[0] ?? null;

        if (!$seleksiId || !$userId) {
            abort(404);
        }

        $seleksi = Seleksi::findOrFail($seleksiId);
        $user    = User::findOrFail($userId);


        // ✅ Validasi input
        $validated = $request->validate([
            'kop_surat'                   => 'required|integer|min:0|max:10',
            'format_dokumen'              => 'required|integer|min:0|max:10',
            'layout_ttd'                  => 'required|integer|min:0|max:10',
            'manajemen_file_waktu'        => 'required|integer|min:0|max:10',
            'format_visualisasi_tabel'    => 'required|integer|min:0|max:10',
            'fungsi_logika'               => 'required|integer|min:0|max:10',
            'fungsi_lanjutan'             => 'required|integer|min:0|max:15',
            'format_data'                 => 'required|integer|min:0|max:10',
            'output_ttd'                  => 'required|integer|min:0|max:5',
            'manajemen_file_excel'        => 'required|integer|min:0|max:10',
        ]);

        // ➕ Total skor
        $totalScore = array_sum($validated);

        DB::transaction(function () use (
            $validated,
            $totalScore,
            $user,
            $seleksi
        ) {

            // 📌 Simpan nilai praktik (per seleksi)
            PrakResult::updateOrCreate(
                [
                    'user_id'    => $user->id,
                ],
                $validated
            );

            // 📌 Simpan hasil ujian
            ResultExam::updateOrCreate(
                [
                    'user_id'    => $user->id,
                    'id_seleksi'    => $seleksi->id,
                    'type'       => 'PRAK',
                ],
                [
                    'score'        => $totalScore,
                    'is_submitted' => true,
                    'submitted_at' => now(),
                ]
            );

            // 🧠 Cari fuzzy rule
            $fuzzyRule = FuzzyRule::where('min_value', '<=', $totalScore)
                ->where('max_value', '>=', $totalScore)
                ->firstOrFail();

            // 🧠 Simpan fuzzy score
            FuzzyScore::updateOrCreate(
                [
                    'user_id'    => $user->id,
                    'id_seleksi' => $seleksi->id,
                    'type'       => 'PRAK',
                ],
                [
                    'score_raw'     => $totalScore,
                    'score_crisp'   => $fuzzyRule->crisp_value,
                    'fuzzy_rule_id' => $fuzzyRule->id,
                ]
            );
        });

        return redirect()->route(
            'showpraktik',
            [
                'seleksiHash' => Hashids::encode($seleksi->id),
                'desaHash'    => Hashids::encode($user->id_desas),
            ]
        )->with('success', 'Nilai praktik berhasil disimpan.');
    }

     public function shownilaipraktik(Request $request, string $seleksiHash, string $desaHash)
    {
        $seleksiId = Hashids::decode($seleksiHash)[0] ?? null;
        $desaId    = Hashids::decode($desaHash)[0] ?? null;

        if (!$seleksiId || !$desaId) {
            abort(404);
        }

        $seleksi = Seleksi::findOrFail($seleksiId);
        $desa    = Desas::findOrFail($desaId);

        $seleksiHashForForm = Hashids::encode($seleksi->id);
        $desaHashForForm    = Hashids::encode($desa->id);
        // Ambil desa
        $desa = Desas::findOrFail($desaId);

        // Ambil seleksi DAN pastikan seleksi itu memang milik desa ini
        $seleksi = Seleksi::where('id', $seleksiId)
            ->where('id_desas', $desaId)
            ->firstOrFail();

        $users = User::where('role', 'users')
            ->where('id_desas', $desaId)
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->with([
                'examResults' => function ($q) use ($seleksi) {
                    $q->where('id_seleksi', $seleksi->id)
                    ->where('type', 'PRAK');
                }
            ])
            ->get()
            ->map(function ($user) {
                $user->score = optional($user->examResults->first())->score;
                return $user;
            });

        return view('penguji.nilai.nilaipraktik', compact('users', 'desa', 'seleksi'
        ))->with([
            'seleksiHash' => $seleksiHashForForm,
            'desaHash'    => $desaHashForForm,
        ]);
    }
}
