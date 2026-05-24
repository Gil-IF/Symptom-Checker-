
        // ── Toggle Show/Hide Password ──────────────────────────────────────────
        const pwInput  = document.getElementById('passwordInput');
        const togglePw = document.getElementById('togglePw');

        togglePw.addEventListener('click', () => {
            const isHidden = pwInput.type === 'password';
            pwInput.type       = isHidden ? 'text' : 'password';
            togglePw.textContent = isHidden ? '🙈' : '👁️';
        });

        // ── Loading state saat submit ──────────────────────────────────────────
        const loginForm = document.getElementById('loginForm');
        const loginBtn  = document.getElementById('loginBtn');
        const spinner   = document.getElementById('spinner');

        loginForm.addEventListener('submit', () => {
            loginBtn.disabled        = true;
            spinner.style.display    = 'block';
            loginBtn.lastChild.textContent = ' Memproses...';
        });