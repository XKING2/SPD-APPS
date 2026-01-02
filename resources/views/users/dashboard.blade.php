@extends('layouts.main')

@section('pageheads')
<div class="container">
    <h4 class="mb-1 text-left">Dashboard</h4>
</div>
@endsection

@section('content')


<div class="container-fluid d-flex justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="dashboard-grid">

        <div class="dashboard-card">
            <div class="icon bg-primary"><i class="fas fa-landmark fa-2x"></i></div>
            <div class="info"><div class="label">Data SPJ</div><div class="value"></div></div>
        </div>

        <div class="dashboard-card">
            <div class="icon bg-primary"><i class="fas fa-money-bill-wave fa-2x"></i></div>
            <div class="info"><div class="label">Data SPJ Tervalidasi Kasubag</div><div class="value"></div></div>
        </div>

        <div class="dashboard-card">
            <div class="icon bg-primary"><i class="fas fa-chart-bar fa-2x"></i></div>
            <div class="info"><div class="label">Laporan</div><div class="value"></div></div>
        </div>

        <div class="dashboard-card">
            <div class="icon bg-primary"><i class="fas fa-clock fa-2x"></i></div>
            <div class="info"><div class="label">Data SPJ Divalidasi Bendahara</div><div class="value"></div></div>
        </div>

    </div>
</div>



@endsection



