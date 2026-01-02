<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>{{ $exam->judul }}</title>
<meta name="csrf-token" content="{{ csrf_token() }}">

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">

<style>
.option {
    cursor: pointer;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 6px;
    margin-bottom: 8px;
}
.option.active {
    background: #0d6efd;
    color: #fff;
}

/* ================== SOAL BUTTON ================== */
.soal-btn {
    min-width: 44px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    padding: 0;
    border: 1px solid #ced4da;
    background: #fff;
    color: #000;
}

/* HANYA SATU STATUS WARNA */
.soal-btn.answered {
    background: #198754;
    color: #fff;
    border-color: #198754;
}

/* ================================================= */
.soal-image-wrapper {
    width: 240px;
    height: 240px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
}
.soal-image-wrapper img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.soal-btn {
    position: relative;
}

/* BADGE FLAG */
.soal-btn .flag-badge {
    position: absolute;
    top: -6px;
    right: -6px;
    background: #fff;
    color: #fff;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    font-size: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
}
</style>
</head>

<body class="bg-light">

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

/* ================= STATE ================= */
const state = {
    index: 0,
    answers: JSON.parse(localStorage.getItem(ANSWER_KEY) || '{}'),
    flagged: {},
    submitted: false
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
    csrf: document.querySelector('meta[name=csrf-token]').content
};

/* ================= TIMER (RESET SETIAP LOAD) ================= */
let remaining = DURATION;

const timerInterval = setInterval(() => {
    if (state.submitted) return;

    remaining--;

    if (remaining <= 0) {
        clearInterval(timerInterval);
        submitExam();
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
    q.options.forEach(opt => {
        const div = document.createElement('div');
        div.className = 'option';
        if (state.answers[q.id] === opt.id) div.classList.add('active');

        div.innerHTML = `<strong>${opt.label}.</strong> ${opt.opsi_tulisan}`;
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
function submitExam() {
    if (state.submitted) return;
    state.submitted = true;

    localStorage.removeItem(ANSWER_KEY);

    fetch(SUBMIT_URL, {
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'X-CSRF-TOKEN':el.csrf
        },
        body:JSON.stringify({ answers: state.answers })
    })
    .then(r=>r.json())
    .then(r=>window.location.href=r.redirect)
    .catch(()=>alert('Submit gagal'));
}

/* ================= EVENTS ================= */
el.prev.onclick = () => goTo(state.index-1);
el.next.onclick = () => goTo(state.index+1);
el.submit.onclick = submitExam;
el.flag.onclick = () => {
    state.flagged[state.index] = !state.flagged[state.index];
    render();
};

/* ================= INIT ================= */
render();

/* BLOCK BACK */
history.pushState(null,null,location.href);
window.onpopstate = ()=>history.go(1);

})();
</script>

</body>
</html>
