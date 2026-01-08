<?php

use App\Http\Controllers\Authcontroller;
use App\Http\Controllers\biodatacontrol;
use App\Http\Controllers\sidebar2control;
use App\Http\Controllers\sidebarcontrol;
use App\Http\Controllers\Enrollcontrol;
use App\Http\Controllers\formasicontrol;
use App\Http\Controllers\OrbControl;
use App\Http\Controllers\PrakControl;
use App\Http\Controllers\Sawcontrol;
use App\Http\Controllers\seleksicontrol;
use App\Http\Controllers\sidebar3control;
use App\Http\Controllers\Startcontrol;
use App\Http\Controllers\tpuControl;
use App\Http\Controllers\ujiancontrol;
use App\Http\Controllers\WWNControl;
use App\Models\Desas;
use App\Models\seleksi;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('login');
});


Route::middleware(['guest', 'otp.not.pending'])->group(function () {

    Route::get('/register', [Authcontroller::class, 'showRegisterForm'])
        ->name('register.form');

    Route::post('/register', [Authcontroller::class, 'register'])
        ->middleware('throttle:5,10')
        ->name('register');

    Route::get('/login', [Authcontroller::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [Authcontroller::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.post');
});

Route::post('/otp/verify', [Authcontroller::class, 'verify'])
        ->middleware('throttle:5,1')
        ->name('otp.verify');

Route::get('/otp', [Authcontroller::class, 'otpForm'])
    ->middleware('otp.session')
    ->name('otp.form');

Route::post('/otp/resend', [Authcontroller::class, 'resendOtp'])
    ->middleware(['otp.session', 'throttle:3,10'])
    ->name('otp.resend');
    
Route::post('/logout', [Authcontroller::class, 'logout'])
    ->middleware('auth')
    ->name('logout');




Route::get('/ajax/desa/{kecamatan}', [Authcontroller::class, 'getDesa'])
    ->middleware('throttle:60,1');

Route::middleware(['auth','check.role:users'])->group(function () {
    Route::get('/User/Dashboard', [sidebarcontrol::class, 'showdashboard'])->name('userdashboard');
    Route::get('/User/Biodata', [sidebarcontrol::class, 'showbiodata'])->name('showbiodata');
    Route::get('/User/Cekdata', [sidebarcontrol::class, 'preview'])->name('showpreview');
    Route::get('/User/Ujian', [sidebarcontrol::class, 'showmainujian'])->name('showmainujian');
    Route::post('/biodata', [biodatacontrol::class, 'store'])->name('biodata.post');
    Route::post('/exam/tpu/{exam}/verify', [Enrollcontrol::class, 'verifyEnrollmentTPU'])->name('exam.tpu.verify');
    Route::get('/ujian/TPU/{exam}', [Startcontrol::class, 'startTPU'])->name('exam.tpu.start');
    Route::post('/exam/TPU/{exam}/submit', [ujiancontrol::class, 'submit'])->name('exam.tpu.submit');
    Route::get('/ujian/WWN/{exam}', [Startcontrol::class, 'startWWN'])->name('exam.wwn.start');
    Route::post('/exam/WWN/{exam}/verify', [Enrollcontrol::class, 'verifyEnrollmentWawancara'])->name('exam.wwn.verify');
    Route::post('/exam/WWN/{exam}/submit', [ujiancontrol::class, 'submitWawancara'])->name('exam.wwn.submit');
});



Route::middleware(['auth','check.role:admin'])->group(function () {
    Route::get('/admin/dashboard', [sidebar2control::class, 'index'])->name('admindashboard');
    Route::get('/ujian', [sidebar2control::class, 'startexams'])->name('adminujian');
    Route::post('/admin/exam/{exam}/generate',[sidebar2control::class, 'generate'])->name('admin.tpu.generate');
    Route::post('/admin/exams/{exam}/generateWWN',[sidebar2control::class, 'generateWWN'])->name('admin.wwn.generate');
    Route::get('/validasi-biodata', [biodatacontrol::class, 'index'])->name('validasi.index');
    Route::post('/validasi-biodata/{biodata}', [BiodataControl::class, 'validasi'])->name('validasi.submit');
    Route::get('/validasi-biodata/{hash}', [BiodataControl::class, 'show'])->name('validasi.show');
    Route::get('/Admin/Generate', [sidebar2control::class, 'generatePageSaw'])->name('generate.admin');
    Route::post('/Admin/saw/generate/{seleksi}',[Sawcontrol::class, 'generateAdminSaw'])->name('saw.admin.generate');
    Route::get('/Admin/Exams', [ujiancontrol  ::class, 'ShowExamsAdmin'])->name('adminexams');
    Route::get('/exams/{hashexam}/edits', [sidebar2control::class, 'editExams'])->name('adminexam.edit');
    Route::put('/exams/{exam}', [sidebar2control::class, 'updateExams'])->name('Adminexam.update');
    Route::get('Admin/Formasi/Main/Page', [sidebar2control::class, 'FormasiIndex'])->name('formasi.index');
    Route::post('Admin/Formasi/store', [formasicontrol::class, 'store'])->name('formasi.store');
    Route::get('Admin/Formasi/{Hashformasi}', [formasicontrol::class, 'show'])->name('formasi.show');
    Route::post('Admin/Formasi/{formasi}/kebutuhan', [formasicontrol::class, 'storeKebutuhan'])->name('formasi.kebutuhan.store');
});

Route::middleware(['auth','check.role:penguji'])->group(function () {
    Route::get('/Penguji/Dashboard', [sidebar3control  ::class, 'ShowDashboard'])->name('pengujidashboard');
    Route::get('/api/chart/kecamatan', [sidebar3control::class, 'chartKecamatan']);
    Route::get('/api/chart/desa/{kecamatan}', [sidebar3control::class, 'chartDesa']);
    Route::get('/api/chart/desa-detail/{desa}', [sidebar3control::class, 'chartDesaDetail']);
    Route::get('/get-desa/{kecamatan}', function ($kecamatanId) {
        return Desas::where('id_kecamatans', $kecamatanId)->get();
    });
    Route::get('/desa/by-kecamatan/{id}', [sidebar3control::class, 'getDesaByKecamatan']);
    Route::get('/cek-seleksi-desa/{desa}', [sidebar3control::class, 'cekSeleksiDesa']);

    Route::get('/Penguji/Seleksi', [sidebar3control  ::class, 'showSeleksi'])->name('addseleksi');
    Route::post('/Seleksi/import', [seleksicontrol::class, 'store'])->name('seleksi.import');
    Route::get('/seleksi/{hashseleksi}/edit', [seleksicontrol ::class, 'edit'])->name('seleksi.edit');
    Route::put('/seleksi/{seleksi}', [seleksicontrol::class, 'update'])->name('seleksi.update');
    Route::delete('/seleksi/{seleksi}', [seleksicontrol::class, 'destroy'])->name('seleksi.destroy');

    Route::get('/Penguji/Exams', [sidebar3control  ::class, 'showExams'])->name('addexams');
    Route::post('/Exams/import', [sidebar3control::class, 'storeExams'])->name('exams.import');
    Route::get('/exam/{exam}/edit', [ujiancontrol::class, 'edit'])->name('exam.edit');
    Route::put('/exam/{exam}', [ujiancontrol::class, 'update'])->name('exam.update');
    Route::delete('/exam/{exam}', [ujiancontrol::class, 'destroy'])->name('exam.destroy');
    Route::post('/exam/{exam}/validasi',[ujiancontrol::class, 'validasiExam'])->name('exam.validasi');

    Route::get('/Penguji/Main/TPU', [sidebar3control::class, 'showMainTPU'])->name('showtpuMain');
    Route::get('/Penguji/Nilai/TPU/desa/{desa}',[sidebar3control::class, 'resolveSeleksiByDesa3'])->name('praktik.resolve');
    Route::get('/Penguji/Nilai/TPU/{seleksiHash}/desa/{desaHash}', [tpuControl::class, 'shownilaiTPU'])->name('showtpu');
    Route::get('/Penguji/AddSeleksi/TPU/desa/{desa}',[sidebar3control::class, 'resolveSeleksiByDesa5'])->name('praktik.resolve');
    Route::get('/Penguji/AddSeleksi',[sidebar3control::class, 'showTambahTPUMain'])->name('tambahtpu');
    Route::post('/penguji/add-SOAL/tpu',[tpuControl::class, 'storeTPU'])->name('exam-questions.import');
    Route::get('/penguji/add-SOAL/tpu',[tpuControl::class, 'showTambahTPU'])->name('addTPU');
    Route::get('/TPU/{id}/edit', [tpuControl::class, 'editTPU'])->name('TPU.edit');
    Route::put('/TPU/{id}', [tpuControl::class, 'updateTPU'])->name('TPU.update');
    Route::delete('/TPU/{id}', [tpuControl::class, 'destroyTPU'])->name('TPU.destroy');

    Route::get('/Penguji/Main/WWN', [sidebar3control::class, 'showMainWWN'])->name('showWwnMain');
    Route::get('/Penguji/Nilai/Wawancara/desa/{desa}',[sidebar3control::class, 'resolveSeleksiByDesa4'])->name('praktik.resolve');
    Route::get('/Penguji/Nilai/Wawancara/{seleksiHash}/desa/{desaHash}', [WWNControl::class, 'shownilaiWWN'])->name('ShowWWN');
    Route::get('/Penguji/Tambah/Wawancara', [sidebar3control::class, 'showtambahWWNMain'])->name('tambahwawan');
    Route::get('/Penguji/AddSeleksi/WWN/desa/{desa}',[sidebar3control::class, 'resolveSeleksiByDesa6'])->name('praktik.resolve');
    Route::post('/Penguji/Post/Add-Soal//WWN', [WWNControl::class, 'storeWawancara'])->name('exam-wawancara.import');
    Route::get('/Penguji/Add-Soal/WWN', [WWNControl::class, 'showtambahwawancara'])->name('addWWN');
    Route::get('/Wawancara/{id}/edit', [ujiancontrol::class, 'editWawancara'])->name('wawan.edit');
    Route::put('/Wawancara/{id}', [ujiancontrol::class, 'updateWawancara'])->name('wawan.update');
    Route::delete('/wawancara/{id}', [ujiancontrol::class, 'destroy'])->name('wawancara.destroy');

    Route::get('/Penguji/Main/PRAK', [sidebar3control::class, 'showMainPrak'])->name('showPrakMain');
    Route::get('/Penguji/Nilai/Praktik/desa/{desa}',[sidebar3control::class, 'resolveSeleksiByDesa'])->name('praktik.resolve');
    Route::get('/Penguji/Nilai/Praktik/{seleksiHash}/desa/{desaHash}',[PrakControl::class, 'shownilaipraktik'])->name('showpraktik');
    Route::get('/nilai/Prak/{seleksiHash}/{userHash}', [PrakControl::class, 'addnilaiprak'])->name('add.praktik');
    Route::post('/nilai/praktik/{seleksiHash}/{userHash}', [PrakControl::class, 'storePrak'])->name('nilaiprakstore');

    Route::get('/Penguji/Main/ORB', [sidebar3control::class, 'showMainOrb'])->name('showOrbMain');
    Route::get('/Penguji/Nilai/Observasi/desa/{desa}',[sidebar3control::class, 'resolveSeleksiByDesa2'])->name('praktik.resolve');
    Route::get('/Penguji/Nilai/Observasi/{seleksiHash}/desa/{desaHash}', [OrbControl::class, 'shownilaiobservasi'])->name('showobservasi');
    Route::get('/nilai/ORB/{seleksiHash}/{userHash}', [OrbControl::class, 'addnilaiorb'])->name('add.observasi');
    Route::post('/nilai/Observasi/{seleksiHash}/{userHash}',[OrbControl::class, 'storeOrb'])->name('nilaiorbstore');

    Route::get('/Penguji/Generate', [sidebar3control::class, 'generatePage'])->name('generate.page');
    Route::post('/Penguji/saw/generate/{seleksi}',[Sawcontrol::class, 'generate'])->name('saw.generate');

});
