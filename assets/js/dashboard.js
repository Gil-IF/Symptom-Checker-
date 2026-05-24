    function openNavDrawer() {
        document.getElementById('navDrawer').classList.add('open');
        document.getElementById('navOverlay').classList.add('show');
        document.body.classList.add('nav-open');
    }

    function closeNavDrawer() {
        document.getElementById('navDrawer').classList.remove('open');
        document.getElementById('navOverlay').classList.remove('show');
        document.body.classList.remove('nav-open');
    }
    function toggleAvatarOptions() {
        var form = document.getElementById('avatarForm');
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    }
    function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                btn.textContent = '🙈';
            } else {
                input.type = 'password';
                btn.textContent = '👁️';
            }
        }