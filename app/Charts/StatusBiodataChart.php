<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;

class StatusBiodataChart
{
    protected LarapexChart $charts;

    public function __construct(LarapexChart $charts)
    {
        $this->charts = $charts;
    }

    public function build(array $labels, array $data)
    {
        return $this->charts
            ->pieChart()
            ->setTitle('Status Biodata Peserta')
            ->setSubtitle('Perbandingan Valid dan Draft')
            ->addData($data)
            ->setLabels($labels)
            ->setHeight(320)
            ->setColors([
                '#1cc88a', // valid - hijau
                '#f6c23e', // draft - kuning
            ]);
    }
}
