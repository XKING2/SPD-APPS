<?php

namespace App\Http\Controllers;

use App\Models\FuzzyScore;
use App\Models\rankings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Sawcontrol extends Controller
{
    public function generate($seleksiId)
    {
        
        $scores = FuzzyScore::where('id_seleksi', $seleksiId)
            ->get()
            ->groupBy('user_id');
        $userIds = $scores->keys();


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

        $max = [
            'TPU'  => max(1, collect($matrix)->max('TPU')),
            'PRAK' => max(1, collect($matrix)->max('PRAK')),
            'WWN'  => max(1, collect($matrix)->max('WWN')),
            'ORB'  => max(1, collect($matrix)->max('ORB')),
        ];

        
        $weights = [
            'TPU'  => 35,
            'PRAK' => 35,
            'WWN'  => 20,
            'ORB'  => 10,
        ];

        DB::transaction(function () use ($matrix, $max, $weights, $seleksiId) {

            
            rankings::where('id_seleksi', $seleksiId)->delete();

            $results = [];

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

            usort($results, function ($a, $b) {
                return
                    $b['nilai_saw'] <=> $a['nilai_saw']
                    ?: $b['TPU']     <=> $a['TPU']
                    ?: $b['PRAK']    <=> $a['PRAK']
                    ?: $b['WWN']     <=> $a['WWN']
                    ?: $b['ORB']     <=> $a['ORB'];
            });


            $rank = 1;
            foreach ($results as $row) {
                rankings::create([
                    'id_seleksi' => $seleksiId,
                    'user_id'    => $row['user_id'],
                    'nilai_saw'  => $row['nilai_saw'],
                    'peringkat'  => $rank++,
                ]);
            }
        });

        return back()->with('success', 'Ranking SAW (ABSOLUT + Tie Breaker) berhasil digenerate.');
    }


    public function generateAdminSaw($seleksiId)
    {
        
        $scores = FuzzyScore::where('id_seleksi', $seleksiId)
            ->get()
            ->groupBy('user_id');
        $userIds = $scores->keys();


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

        $max = [
            'TPU'  => max(1, collect($matrix)->max('TPU')),
            'PRAK' => max(1, collect($matrix)->max('PRAK')),
            'WWN'  => max(1, collect($matrix)->max('WWN')),
            'ORB'  => max(1, collect($matrix)->max('ORB')),
        ];

        
        $weights = [
            'TPU'  => 35,
            'PRAK' => 35,
            'WWN'  => 20,
            'ORB'  => 10,
        ];

        DB::transaction(function () use ($matrix, $max, $weights, $seleksiId) {

            
            rankings::where('id_seleksi', $seleksiId)->delete();

            $results = [];

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

            usort($results, function ($a, $b) {
                return
                    $b['nilai_saw'] <=> $a['nilai_saw']
                    ?: $b['TPU']     <=> $a['TPU']
                    ?: $b['PRAK']    <=> $a['PRAK']
                    ?: $b['WWN']     <=> $a['WWN']
                    ?: $b['ORB']     <=> $a['ORB'];
            });


            $rank = 1;
            foreach ($results as $row) {
                rankings::create([
                    'id_seleksi' => $seleksiId,
                    'user_id'    => $row['user_id'],
                    'nilai_saw'  => $row['nilai_saw'],
                    'peringkat'  => $rank++,
                ]);
            }
        });

        return back()->with('success', 'Ranking SAW (ABSOLUT + Tie Breaker) berhasil digenerate.');
    }

}
