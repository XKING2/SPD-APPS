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
            'kerapian'    => 'required|integer|min:0|max:100',
            'kecepatan'   => 'required|integer|min:0|max:100',
            'ketepatan'   => 'required|integer|min:0|max:100',
            'efektifitas' => 'required|integer|min:0|max:100',
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

        // 🔁 Redirect BALIK ke list user desa + seleksi
        return redirect()->route(
            'showpraktik',
            [
                'seleksi' => $seleksi->id,
                'desa'    => $user->id_desas,
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

        // Ambil user berdasarkan desa
        $users = User::where('id_desas', $desaId)
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->orderBy('name')
            ->get();

        return view('penguji.nilai.nilaipraktik', compact('users', 'desa', 'seleksi'
        ))->with([
            'seleksiHash' => $seleksiHashForForm,
            'desaHash'    => $desaHashForForm,
        ]);
    }
}
