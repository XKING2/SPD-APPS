<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>{{ $exam->judul }}</title>
<meta name="csrf-token" content="{{ csrf_token() }}">

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
<link href="{{ asset('css/ujian.css') }}" rel="stylesheet">

<style>
/* WARNING MODAL */
#warningModal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.95);
    z-index: 9999;
    justify-content: center;
    align-items: center;
}

#warningModal.show {
    display: flex;
}

.warning-content {
    background: white;
    padding: 40px;
    border-radius: 15px;
    text-align: center;
    max-width: 500px;
    animation: shake 0.5s;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-10px); }
    75% { transform: translateX(10px); }
}

.violation-badge {
    position: fixed;
    top: 10px;
    right: 10px;
    background: #dc3545;
    color: white;
    padding: 10px 20px;
    border-radius: 25px;
    font-weight: bold;
    z-index: 1000;
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
}
</style>

</head>

<body class="bg-light">

<!-- WARNING MODAL -->
<div id="warningModal">
    <div class="warning-content">
        <h2 class="text-danger mb-3">⚠️ PERINGATAN!</h2>
        <p id="warningText" class="fs-5 mb-4"></p>
        <p class="text-muted">Pelanggaran: <span id="violationCount" class="text-danger fw-bold">0</span>/3</p>
        <button id="continueBtn" class="btn btn-primary btn-lg">Kembali ke Ujian</button>
    </div>
</div>

<!-- VIOLATION BADGE -->
<div id="violationBadge" class="violation-badge d-none">
    ⚠️ Pelanggaran: <span id="badgeCount">0</span>/3
</div>

<div class="container py-4">

<div class="card mb-3">
    <div class="card-body d-flex justify-content-between align-items-center">
        <strong>{{ $exam->judul }}</strong>
        <div id="timer" class="bg-danger text-white px-3 py-2 rounded">00:00</div>
    </div>
</div>

<div class="row">

<div class="col-md-3">
    <div class="card">
        <div class="card-header bg-primary text-white text-center">Soal</div>
        <div class="card-body d-flex flex-wrap gap-2" id="nomor"></div>
    </div>
</div>

<div class="col-md-9">
    <div class="card">
        <div class="card-body">

            <h6>Soal <span id="no">1</span></h6>
            <div id="pertanyaan" class="mb-2"></div>

            <div id="gambarWrapper" class="soal-image-wrapper d-none">
                <img id="gambarSoal">
            </div>

            <div id="opsi"></div>

            <hr>

            <button id="flagBtn" class="btn btn-warning btn-sm">Flag</button>

            <div class="d-flex justify-content-between mt-3">
                <button id="prevBtn" class="btn btn-secondary">Prev</button>
                <button id="nextBtn" class="btn btn-primary">Next</button>
                <button id="submitBtn" class="btn btn-success d-none">Submit</button>
            </div>

        </div>
    </div>
</div>

</div>
</div>

<script id="exam-data" type="application/json">
{!! json_encode([
    'examId'    => $exam->id,
    'questions' => $questions,
    'duration'  => (int) $exam->duration * 60,
    'submitUrl' => route('exam.tpu.submit', $exam->id),
], JSON_UNESCAPED_UNICODE) !!}
</script>

<script>
(() => {

/* ================= DATA ================= */
const DATA = JSON.parse(document.getElementById('exam-data').textContent);
const QUESTIONS  = DATA.questions;
const DURATION   = DATA.duration;
const SUBMIT_URL = DATA.submitUrl;

const EXAM_ID = DATA.examId;
const ANSWER_KEY = `exam_${EXAM_ID}_answers`;
const MAX_VIOLATIONS = 3;

/* ================= STATE ================= */
const state = {
    index: 0,
    answers: JSON.parse(localStorage.getItem(ANSWER_KEY) || '{}'),
    flagged: {},
    submitted: false,
    violations: 0,
    examStarted: false
};

/* ================= ELEMENT ================= */
const el = {
    timer: document.getElementById('timer'),
    nomor: document.getElementById('nomor'),
    no: document.getElementById('no'),
    pertanyaan: document.getElementById('pertanyaan'),
    gambar: document.getElementById('gambarSoal'),
    gambarWrapper: document.getElementById('gambarWrapper'),
    opsi: document.getElementById('opsi'),
    prev: document.getElementById('prevBtn'),
    next: document.getElementById('nextBtn'),
    submit: document.getElementById('submitBtn'),
    flag: document.getElementById('flagBtn'),
    csrf: document.querySelector('meta[name=csrf-token]').content,
    warningModal: document.getElementById('warningModal'),
    warningText: document.getElementById('warningText'),
    violationCount: document.getElementById('violationCount'),
    continueBtn: document.getElementById('continueBtn'),
    violationBadge: document.getElementById('violationBadge'),
    badgeCount: document.getElementById('badgeCount')
};

/* ================= FULLSCREEN FUNCTIONS ================= */
function enterFullscreen() {
    const elem = document.documentElement;
    if (elem.requestFullscreen) {
        elem.requestFullscreen().catch(err => {
            console.error('Fullscreen error:', err);
        });
    } else if (elem.webkitRequestFullscreen) {
        elem.webkitRequestFullscreen();
    } else if (elem.msRequestFullscreen) {
        elem.msRequestFullscreen();
    } else if (elem.mozRequestFullScreen) {
        elem.mozRequestFullScreen();
    }
}

function isFullscreen() {
    return !!(document.fullscreenElement || 
              document.webkitFullscreenElement || 
              document.mozFullScreenElement ||
              document.msFullscreenElement);
}

function recordViolation(message) {
    state.violations++;
    
    el.warningText.textContent = message;
    el.violationCount.textContent = state.violations;
    el.badgeCount.textContent = state.violations;
    el.violationBadge.classList.remove('d-none');
    el.warningModal.classList.add('show');
    
    if (state.violations >= MAX_VIOLATIONS) {
        el.warningText.textContent = `${message}\n\nBatas pelanggaran tercapai (${MAX_VIOLATIONS}x)!\nUjian akan disubmit otomatis.`;
        el.continueBtn.textContent = 'Submit Ujian';
        el.continueBtn.onclick = () => {
            submitExam('auto_submit_violations');
        };
    }
}

/* ================= FULLSCREEN DETECTION ================= */
let fullscreenMonitoringActive = false;

function onFullscreenChange() {
    // Hanya monitor setelah fullscreen benar-benar aktif
    if (!fullscreenMonitoringActive) return;
    
    if (!isFullscreen() && state.examStarted && !state.submitted) {
        recordViolation('Anda keluar dari mode fullscreen!');
        // Paksa kembali ke fullscreen setelah delay kecil
        setTimeout(() => {
            if (!state.submitted) {
                enterFullscreen();
            }
        }, 100);
    }
}

// Event listeners untuk berbagai browser
document.addEventListener('fullscreenchange', onFullscreenChange);
document.addEventListener('webkitfullscreenchange', onFullscreenChange);
document.addEventListener('mozfullscreenchange', onFullscreenChange);
document.addEventListener('MSFullscreenChange', onFullscreenChange);

// Deteksi saat user berpindah tab/window
document.addEventListener('visibilitychange', () => {
    if (document.hidden && state.examStarted && !state.submitted) {
        recordViolation('Anda berpindah ke tab/aplikasi lain!');
    }
});

window.addEventListener('blur', () => {
    if (state.examStarted && !state.submitted) {
        recordViolation('Anda kehilangan fokus dari halaman ujian!');
    }
});

// Continue button handler
el.continueBtn.onclick = () => {
    if (state.violations >= MAX_VIOLATIONS) {
        submitExam('auto_submit_violations');
    } else {
        el.warningModal.classList.remove('show');
        enterFullscreen();
    }
};

/* ================= TIMER (RESET SETIAP LOAD) ================= */
let remaining = DURATION;

const timerInterval = setInterval(() => {
    if (state.submitted) return;

    remaining--;

    if (remaining <= 0) {
        clearInterval(timerInterval);
        submitExam('time_up');
        return;
    }

    const m = Math.floor(remaining / 60);
    const s = String(remaining % 60).padStart(2,'0');
    el.timer.textContent = `${m}:${s}`;
}, 1000);

/* ================= NAV BUTTON ================= */
QUESTIONS.forEach((_, i) => {
    const btn = document.createElement('button');
    btn.className = 'btn btn-outline-primary soal-btn';
    btn.dataset.index = i;
    btn.innerHTML = `<span>${i + 1}</span>`;
    btn.onclick = () => goTo(i);
    el.nomor.appendChild(btn);
});

function goTo(i) {
    if (i < 0 || i >= QUESTIONS.length) return;
    state.index = i;
    render();
}

/* ================= RENDER ================= */
function render() {
    const q = QUESTIONS[state.index];
    el.no.textContent = state.index + 1;
    el.pertanyaan.innerHTML = q.pertanyaan;

    if (q.image_name) {
        el.gambar.src = `/storage/${q.image_name}`;
        el.gambarWrapper.classList.remove('d-none');
    } else {
        el.gambarWrapper.classList.add('d-none');
    }

    el.opsi.innerHTML = '';

    const LABELS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    q.options.forEach((opt, index) => {
        const div = document.createElement('div');
        div.className = 'option';

        if (state.answers[q.id] === opt.id) {
            div.classList.add('active');
        }

        div.innerHTML = `
            <strong>${LABELS[index]}.</strong> ${opt.opsi_tulisan}
        `;

        div.onclick = () => {
            state.answers[q.id] = opt.id;
            localStorage.setItem(ANSWER_KEY, JSON.stringify(state.answers));
            render();
        };

        el.opsi.appendChild(div);
    });

    document.querySelectorAll('.soal-btn').forEach((btn, i) => {
        btn.classList.remove('answered');

        // WARNA HIJAU JIKA TERJAWAB
        if (state.answers[QUESTIONS[i].id]) {
            btn.classList.add('answered');
        }

        // HAPUS FLAG SEBELUM RENDER ULANG
        const oldFlag = btn.querySelector('.flag-badge');
        if (oldFlag) oldFlag.remove();

        // TAMBAHKAN FLAG JIKA DI-FLAG
        if (state.flagged[i]) {
            const flag = document.createElement('div');
            flag.className = 'flag-badge';
            flag.innerHTML = '🚩';
            btn.appendChild(flag);
        }
    });

    el.prev.disabled = state.index === 0;
    el.next.classList.toggle('d-none', state.index === QUESTIONS.length-1);
    el.submit.classList.toggle('d-none', state.index !== QUESTIONS.length-1);
}

/* ================= SUBMIT ================= */
function submitExam(reason = 'manual') {
    if (state.submitted) return;
    state.submitted = true;

    localStorage.removeItem(ANSWER_KEY);

    fetch(SUBMIT_URL, {
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'X-CSRF-TOKEN':el.csrf
        },
        body:JSON.stringify({ 
            answers: state.answers,
            violations: state.violations,
            submit_reason: reason
        })
    })
    .then(r=>r.json())
    .then(r=>window.location.href=r.redirect)
    .catch(()=>alert('Submit gagal'));
}

/* ================= EVENTS ================= */
el.prev.onclick = () => goTo(state.index-1);
el.next.onclick = () => goTo(state.index+1);
el.submit.onclick = () => {
    if (confirm('Yakin ingin submit ujian?')) {
        submitExam('manual');
    }
};
el.flag.onclick = () => {
    state.flagged[state.index] = !state.flagged[state.index];
    render();
};

/* ================= INIT ================= */
render();

/* BLOCK BACK */
history.pushState(null,null,location.href);
window.onpopstate = ()=>history.go(1);

/* ================= START FULLSCREEN SAAT PAGE LOAD ================= */
window.addEventListener('load', () => {
    // Delay kecil agar page fully loaded
    setTimeout(() => {
        if (confirm('Ujian akan dimulai dalam mode FULLSCREEN.\n\nPERINGATAN:\n• Jangan keluar dari fullscreen\n• Jangan pindah tab/aplikasi lain\n• Maksimal 3x pelanggaran = Auto Submit\n\nKlik OK untuk memulai ujian')) {
            state.examStarted = true;
            enterFullscreen();
            
            // Aktifkan monitoring setelah 2 detik (setelah fullscreen stabil)
            setTimeout(() => {
                fullscreenMonitoringActive = true;
            }, 2000);
            
            // Cek setiap 2 detik apakah masih fullscreen
            setInterval(() => {
                if (state.examStarted && !state.submitted && !isFullscreen()) {
                    enterFullscreen();
                }
            }, 2000);
        } else {
            alert('Anda harus menyetujui untuk memulai ujian!');
            window.location.href = '/';
        }
    }, 500);
});

})();
</script>

</body>
</html>