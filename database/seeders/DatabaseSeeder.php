<?php

namespace Database\Seeders;

use App\Models\Desas;
use App\Models\FuzzyRule;
use App\Models\Kecamatans;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        

        FuzzyRule::create([
            'min_value' => '0',
            'max_value' => '19',
            'crisp_value' => '1',
        ]);

        FuzzyRule::create([
            'min_value' => '20',
            'max_value' => '39',
            'crisp_value' => '2',
        ]);

        FuzzyRule::create([
            'min_value' => '40',
            'max_value' => '59',
            'crisp_value' => '3',
        ]);

        FuzzyRule::create([
            'min_value' => '60',
            'max_value' => '79',
            'crisp_value' => '4',
        ]);

        FuzzyRule::create([
            'min_value' => '80',
            'max_value' => '100',
            'crisp_value' => '5',
        ]);

        Kecamatans::create([
            'nama_kecamatan' => 'Gianyar',
        ]);

        Kecamatans::create([
            'nama_kecamatan' => 'Sukawati',
        ]);

        Kecamatans::create([
            'nama_kecamatan' => 'Blahbatuh',
        ]);
        
        Kecamatans::create([
            'nama_kecamatan' => 'Tampaksiring',
        ]);

        Kecamatans::create([
            'nama_kecamatan' => 'Ubud',
        ]);

        Kecamatans::create([
            'nama_kecamatan' => 'Tegallalang',
        ]);

        Kecamatans::create([
            'nama_kecamatan' => 'Payangan',
        ]);



    }

}

