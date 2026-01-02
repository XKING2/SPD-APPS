<?php


namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;

class pendaftarperdesaChart
{
protected LarapexChart $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(array $labels, array $data)
    {
        return $this->chart
            ->barChart()
            ->setTitle('Jumlah Pendaftar per Desa')
            ->setSubtitle('Data sesuai wilayah admin')
            ->addData('Jumlah Peserta', $data)
            ->setXAxis($labels)
            ->setHeight(350)
            ->setColors(['#4e73df'])
            ->setGrid();
    }
}
 
 