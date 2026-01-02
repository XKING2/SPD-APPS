<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;

class weeklypendaftarChart
{
    protected LarapexChart $chartx;

    public function __construct(LarapexChart $chartx)
    {
        $this->chartx = $chartx;
    }

     public function build(array $labels, array $data)
    {
       return $this->chartx
        ->lineChart()
        ->setTitle('Jumlah Pendaftar per Minggu')
        ->setSubtitle('Berdasarkan tanggal pendaftaran')
        ->addData('Pendaftar', $data)
        ->setXAxis($labels)
        ->setHeight(350)
        ->setGrid();
    }
}