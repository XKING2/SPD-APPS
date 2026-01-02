<?php

use App\Http\Controllers\Authcontroller;
use App\Http\Controllers\biodatacontrol;
use App\Http\Controllers\sidebar2control;
use App\Http\Controllers\sidebarcontrol;
use App\Http\Controllers\Admin\UjianController;
use App\Http\Controllers\Sawcontrol;
use App\Http\Controllers\sidebar3control;
use App\Http\Controllers\ujiancontrol;
use App\Models\Desas;
use Illuminate\Support\Facades\Route;



Route::get('/Penguji/Nilaiujian', function () {
    return view('penguji.nilaiujian');
});


Route::get('/ujianpage', function () {
    return view('ujian.ujianpage');
});

Route::get('/register', [Authcontroller::class, 'showRegisterForm'])->name('register.form');
Route::post('/register',[Authcontroller::class, 'register'])->name('register');
Route::get('/get-desa/{kecamatan}', function ($kecamatan) {
    return Desas::where('id_kecamatans', $kecamatan)
        ->orderBy('nama_desa')
        ->get();
});

Route::get('/otp',[Authcontroller::class, 'otpForm'])->name('otp.form');
Route::post('/otp/verify',[Authcontroller::class, 'verify'])->name('otp.verify');

Route::get('/User/Dashboard', function(){
    return "Dashboard User";
})->middleware('auth');



Route::get('/User/Dashboard', [sidebarcontrol::class, 'showdashboard'])->name('userdashboard');
Route::get('/User/Biodata', [sidebarcontrol::class, 'showbiodata'])->name('showbiodata');
Route::get('/User/Verivikasi', [sidebarcontrol::class, 'showverikasibio'])->name('showverivikasi');
Route::get('/User/Cekdata', [sidebarcontrol::class, 'preview'])->name('showpreview');
Route::middleware(['auth', 'biodata.valid'])->group(function () {
    Route::get('/User/Ujian', [sidebarcontrol::class, 'showmainujian'])
        ->name('showmainujian');
});

Route::post('/biodata', [biodatacontrol::class, 'store'])->name('biodata.post');

Route::get('/login', [authcontroller::class, 'showLoginForm'])->name('login');
Route::post('/login', [authcontroller::class, 'login'])->name('login.post');

// Logout
Route::get('/logout', [authcontroller::class, 'logout'])->name('logout');
Route::get('/admin/dashboard', [sidebar2control::class, 'index'])->name('admindashboard');
Route::get('/ujian', [sidebar2control::class, 'startexams'])->name('adminujian');
Route::post('/admin/exam/{exam}/generate',[sidebar2control::class, 'generate'])->name('admin.tpu.generate');
Route::post('/admin/exams/{exam}/generateWWN',[sidebar2control::class, 'generateWWN'])->name('admin.wwn.generate');
Route::get('/validasi-biodata', [biodatacontrol::class, 'index'])->name('validasi.index');
Route::post('/validasi-biodata/{biodata}', [BiodataControl::class, 'validasi'])->name('validasi.submit');

Route::post('/exam/tpu/{exam}/verify', [ujiancontrol::class, 'verifyEnrollment'])->name('exam.tpu.verify');
Route::get('/ujian/TPU/{exam}', [ujiancontrol::class, 'startTPU'])->name('exam.tpu.start');
Route::post('/exam/TPU/{exam}/submit', [ujiancontrol::class, 'submit'])->name('exam.tpu.submit');

Route::post('/exam/WWN/{exam}/verify', [ujiancontrol::class, 'verifyEnrollments'])->name('exam.wwn.verify');
Route::get('/ujian/WWN/{exams}', [ujiancontrol::class, 'startWWN'])->name('exam.wwn.start');
Route::post('/exam/WWN/{exams}/submit', [ujiancontrol::class, 'submitWawancara'])->name('exam.wwn.submit');






Route::get('/validasi-biodata/{id}', [biodatacontrol::class, 'show'])->name('validasi.show');
Route::post('/validasi-biodata/{id}/validasi', [biodatacontrol::class, 'validasi'])->name('validasi.submit');
Route::get('/admin/validasi', [biodatacontrol::class, 'index'])->name('validasi.index');
Route::post('/admin/validasi/{id}', [biodatacontrol::class, 'validasi'])->name('validasi.submit');

Route::get('/Penguji/Dashboard', [sidebar3control  ::class, 'ShowDashboard'])->name('pengujidashboard');
Route::get('/api/chart/kecamatan', [sidebar3control::class, 'chartKecamatan']);
Route::get('/api/chart/desa/{kecamatan}', [sidebar3control::class, 'chartDesa']);
Route::get('/api/chart/desa-detail/{desa}', [sidebar3control::class, 'chartDesaDetail']);
Route::get('/get-desa/{kecamatan}', function ($kecamatanId) {
    return Desas::where('id_kecamatans', $kecamatanId)->get();
});

Route::get('/Penguji/Seleksi', [sidebar3control  ::class, 'showSeleksi'])->name('addseleksi');
Route::post('/Seleksi/import', [sidebar3control::class, 'store'])->name('seleksi.import');
Route::get('/desa/by-kecamatan/{id}', [sidebar3control::class, 'getDesaByKecamatan']);

Route::get('/Penguji/Main/TPU', [sidebar3control::class, 'showMainTPU'])->name('showtpuMain');
Route::get('/Penguji/Main/WWN', [sidebar3control::class, 'showMainWWN'])->name('showWwnMain');
Route::get('/Penguji/Main/PRAK', [sidebar3control::class, 'showMainPrak'])->name('showPrakMain');
Route::get('/Penguji/Main/ORB', [sidebar3control::class, 'showMainOrb'])->name('showOrbMain');


Route::get('/desa/by-kecamatan/{kecamatan}', [sidebar3control::class, 'getDesaByKecamatan']);
Route::get('/cek-seleksi-desa/{desa}', [sidebar3control::class, 'cekSeleksiDesa']);

Route::get('/Penguji/Nilai/TPU/desa/{desa}',[sidebar3control::class, 'resolveSeleksiByDesa3'])->name('praktik.resolve');
Route::get('/Penguji/Nilai/TPU/{seleksi}/desa/{desa}', [sidebar3control::class, 'shownilaiTPU'])->name('showtpu');

Route::get('/Penguji/Nilai/Wawancara/desa/{desa}',[sidebar3control::class, 'resolveSeleksiByDesa4'])->name('praktik.resolve');
Route::get('/Penguji/Nilai/Wawancara/{seleksi}/desa/{desa}', [sidebar3control::class, 'shownilaiWWN'])->name('ShowWWN');


Route::get('/Penguji/Nilai/Praktik/desa/{desa}',[sidebar3control::class, 'resolveSeleksiByDesa'])->name('praktik.resolve');
Route::get('/Penguji/Nilai/Praktik/{seleksi}/desa/{desa}',[sidebar3control::class, 'shownilaipraktik'])->name('showpraktik');



Route::get('/Penguji/Nilai/Observasi/desa/{desa}',[sidebar3control::class, 'resolveSeleksiByDesa2'])->name('praktik.resolve');
Route::get('/Penguji/Nilai/Observasi/{seleksi}/desa/{desa}', [sidebar3control::class, 'shownilaiobservasi'])->name('showobservasi');


Route::get('/nilai/Prak/{seleksi}/{user_id}', [ujiancontrol::class, 'addnilaiprak'])->name('add.praktik');
Route::post('/nilai/praktik/{seleksi}/{user}', [ujiancontrol::class, 'storePrak'])->name('nilaiprakstore');
Route::get('/nilai/ORB/{seleksi}/{user_id}', [ujiancontrol::class, 'addnilaiorb'])->name('add.observasi');
Route::post('/nilai/Observasi/{seleksi}/{user}',[ujiancontrol::class, 'storeOrb'])->name('nilaiorbstore');


Route::get('/Penguji/AddSeleksi/TPU/desa/{desa}',[sidebar3control::class, 'resolveSeleksiByDesa5'])->name('praktik.resolve');
Route::get('/Penguji/AddSeleksi',[sidebar3control::class, 'showTambahTPUMain'])->name('tambahtpu');
Route::post('/Penguji/AddSeleksi/{seleksi}/Desa/{desa}/TPU', [ujiancontrol::class, 'storeTPU'])->name('exam-questions.import');
Route::get('/Penguji/AddSeleksi/{seleksi}/Desa/{desa}/TPU', [sidebar3control::class, 'showtambahTPU'])->name('addTPU');

Route::get('/Penguji/Tambah/Wawancara', [sidebar3control::class, 'showtambahWWNMain'])->name('tambahwawan');
Route::get('/Penguji/AddSeleksi/WWN/desa/{desa}',[sidebar3control::class, 'resolveSeleksiByDesa6'])->name('praktik.resolve');
Route::post('/Penguji/AddSeleksi/{seleksi}/Desa/{desa}/WWN', [ujiancontrol::class, 'storeWawancara'])->name('exam-wawancara.import');
Route::get('/Penguji/AddSeleksi/{seleksi}/Desa/{desa}/WWN', [sidebar3control::class, 'showtambahwawancara'])->name('addWWN');
Route::post('/exam/{exam}/validasi/{type}',[ujiancontrol::class, 'validasiExam'])->name('exam.validasi');

Route::post('/exam-wawancara/import', [ujiancontrol::class, 'storeWawancara'])
    ->name('exam-wawancara.import');

Route::get('/TPU/{id}/edit', [ujiancontrol::class, 'editTPU'])->name('TPU.edit');
Route::put('/TPU/{id}', [ujiancontrol::class, 'updateTPU'])->name('TPU.update');
Route::delete('/TPU/{id}', [ujiancontrol::class, 'destroyTPU'])->name('TPU.destroy');

Route::get('/Wawancara/{id}/edit', [ujiancontrol::class, 'editWawancara'])->name('wawan.edit');
Route::put('/Wawancara/{id}', [ujiancontrol::class, 'updateWawancara'])->name('wawan.update');
Route::delete('/wawancara/{id}', [ujiancontrol::class, 'destroy'])->name('wawancara.destroy');

Route::get('/Observasi/Tambah/Nilai', [ujiancontrol::class, 'addnilaiorb'])->name('add.observasi');

Route::get('/Penguji/Generate', [sidebar3control::class, 'generatePage'])->name('generate.page');
Route::post('/Penguji/saw/generate/{seleksi}',[Sawcontrol::class, 'generate'])->name('saw.generate');

Route::get('/Admin/Generate', [sidebar2control::class, 'generatePageSaw'])->name('generate.admin');
Route::post('/Admin/saw/generate/{seleksi}',[Sawcontrol::class, 'generateAdminSaw'])->name('saw.admin.generate');






