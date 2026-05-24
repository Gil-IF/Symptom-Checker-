// ── Toggle show/hide password ─────────────────────────────
document.querySelectorAll('.toggle-pw').forEach(btn => {
    btn.addEventListener('click', function () {
        const inp  = document.getElementById(this.dataset.target);
        const show = inp.type === 'password';
        inp.type       = show ? 'text' : 'password';
        this.textContent = show ? '🙈' : '👁️';
    });
});

// ── Popup ─────────────────────────────────────────────────
function showPopup(msg) {
    document.getElementById('popupMsg').textContent = msg;
    document.getElementById('popupOverlay').classList.add('show');
}
function closePopup() {
    document.getElementById('popupOverlay').classList.remove('show');
}
document.getElementById('popupOverlay').addEventListener('click', function (e) {
    if (e.target === this) closePopup();
});

// ── Validasi client-side sebelum submit ───────────────────
document.getElementById('regForm').addEventListener('submit', function (e) {
    const npm = this.npm.value.trim();
    const pw  = document.getElementById('pw1').value;
    const cpw = document.getElementById('pw2').value;

    if (!npm || !pw || !cpw) {
        e.preventDefault();
        showPopup('Please complete your data!');
        return;
    }
    if (pw.length < 6) {
        e.preventDefault();
        showPopup('Password minimal 6 karakter.');
        return;
    }
    if (pw !== cpw) {
        e.preventDefault();
        showPopup('Password dan konfirmasi tidak cocok!');
        return;
    }

    // Validasi lolos — tampilkan loading spinner
    const btn    = document.getElementById('regBtn');
    const spinner = document.getElementById('spinner');
    btn.disabled             = true;
    spinner.style.display    = 'inline-block';
    btn.lastChild.textContent = ' Mendaftarkan...';
});