<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;

class PendaftaranKecamatanChart
{
    protected LarapexChart $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

     public function build(array $labels, array $data)
    {
        return $this->chart
            ->barChart() // ⬅️ WAJIB barChart
            ->setTitle('Jumlah Pendaftar per Kecamatan')
            ->setSubtitle('Berdasarkan filter yang dipilih')
            ->addData('Jumlah Peserta', $data)
            ->setXAxis($labels) // ⬅️ INI yang bikin nama kecamatan muncul di bawah bar
            ->setHeight(350)
            ->setColors(['#4e73df'])
            ->setGrid();
    }
}