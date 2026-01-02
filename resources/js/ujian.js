const questions = window.__QUESTIONS__;
const submitUrl = window.__SUBMIT_URL__;

let index = 0;
let answers = {};
let flagged = {};

// ================= DOM =================
const nomorDiv = document.getElementById('nomorSoal');
const finishBtn = document.getElementById('finishBtn');
const nextBtn   = document.getElementById('nextBtn');
const prevBtn   = document.getElementById('prevBtn');
const btnFlag   = document.getElementById('btnFlag');

// ================= NOMOR =================
questions.forEach((q, i) => {
    nomorDiv.innerHTML += `
        <button class="btn btn-outline-primary soal-btn"
            data-index="${i}" style="width:40px">${i + 1}</button>`;
});

// ================= LOAD SOAL =================
function loadSoal() {
    const q = questions[index];
    document.getElementById('soalNo').innerText = index + 1;
    document.getElementById('soalText').innerText = q.pertanyaan;

    let opsiHTML = '';
    q.options.forEach(opt => {
        opsiHTML += `
        <div class="form-check">
            <input class="form-check-input"
                type="radio"
                name="jawaban"
                value="${opt.label}"
                data-question="${q.id}"
                ${answers[q.id] === opt.label ? 'checked' : ''}>
            <label class="form-check-label">
                ${opt.label}. ${opt.opsi_tulisan}
            </label>
        </div>`;
    });

    document.getElementById('opsiJawaban').innerHTML = opsiHTML;
    updateStatus();
}

// ================= STATUS =================
function updateStatus() {
    document.querySelectorAll('.soal-btn').forEach(btn => {
        btn.classList.remove('active','answered','flagged');
        const i = btn.dataset.index;
        if (i == index) btn.classList.add('active');
        if (flagged[i]) btn.classList.add('flagged');
        if (answers[questions[i].id]) btn.classList.add('answered');
    });

    prevBtn.disabled = index === 0;

    if (index === questions.length - 1) {
        nextBtn.classList.add('d-none');
        finishBtn.classList.remove('d-none');
    } else {
        nextBtn.classList.remove('d-none');
        finishBtn.classList.add('d-none');
    }
}

// ================= EVENTS =================
document.addEventListener('change', e => {
    if (e.target.name === 'jawaban') {
        answers[e.target.dataset.question] = e.target.value;
        updateStatus();
    }
});

nextBtn.onclick = () => { index++; loadSoal(); };
prevBtn.onclick = () => { index--; loadSoal(); };

btnFlag.onclick = () => {
    flagged[index] = !flagged[index];
    updateStatus();
};

document.querySelectorAll('.soal-btn').forEach(btn => {
    btn.onclick = () => {
        index = parseInt(btn.dataset.index);
        loadSoal();
    };
});

// ================= SUBMIT =================
finishBtn.onclick = () => {
    if (Object.keys(answers).length === 0) {
        alert('Kamu belum menjawab soal apa pun');
        return;
    }

    fetch(submitUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document
                .querySelector('meta[name=csrf-token]')
                .getAttribute('content')
        },
        body: JSON.stringify({ answers })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            alert('Gagal menyimpan jawaban');
        }
    })
    .catch(() => alert('Server error'));
};

// INIT
loadSoal();
