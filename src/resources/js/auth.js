import { translate as localize } from "./localization.js";

if (document.querySelector('[data-page-script="auth/login"]')) {
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const emailField = document.getElementById('email');
        if (emailField) {
            setTimeout(() => emailField.focus(), 100);
        }
    });

    document.querySelectorAll('[data-view-action="auth-login-1"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { togglePassword() }).call(this);
            if (result === false) event.preventDefault();
        });
    });

}

if (document.querySelector('[data-page-script="auth/register"]')) {
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const nameField = document.getElementById('name');
        if (nameField) {
            setTimeout(() => nameField.focus(), 100);
        }
    });

    document.querySelectorAll('[data-view-action="auth-register-1"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { togglePassword() }).call(this);
            if (result === false) event.preventDefault();
        });
    });

}

if (document.querySelector('[data-page-script="auth/two-factor"]')) {
    document.addEventListener('DOMContentLoaded', function() {

        let secondsLeft = 60;
        const totalSeconds = 60;

        const timerBox = document.getElementById('timerBox');
        const timerValue = document.getElementById('timerValue');
        const timerProgress = document.getElementById('timerProgress');
        const resendBtn = document.getElementById('resendBtn');
        const resendText = document.getElementById('resendText');
        const codeInput = document.getElementById('code');
        const verifyBtn = document.getElementById('verifyBtn');

        function updateTimer() {
            timerValue.textContent = secondsLeft;

            const progress = (secondsLeft / totalSeconds) * 100;
            timerProgress.style.width = progress + '%';

            timerBox.classList.remove('warning', 'danger', 'expired');

            if (secondsLeft <= 0) {
                timerBox.classList.add('expired');
                timerValue.textContent = '0';
                codeInput.disabled = true;
                verifyBtn.disabled = true;

                resendBtn.disabled = false;
                resendText.textContent = localize("Resend Code");
            } else if (secondsLeft <= 10) {
                timerBox.classList.add('danger');
            } else if (secondsLeft <= 30) {
                timerBox.classList.add('warning');
            }

            if (secondsLeft > 0) {
                secondsLeft--;
                setTimeout(updateTimer, 1000);
            }
        }

        updateTimer();

        if (codeInput) {
            codeInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');

                if (this.value.length === 6) {
                    document.getElementById('verifyForm').submit();
                }
            });

            codeInput.focus();

            codeInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && this.value.length === 6) {
                    e.preventDefault();
                    document.getElementById('verifyForm').submit();
                }
            });
        }

        const resendForm = document.getElementById('resendForm');
        if (resendForm) {
            resendForm.addEventListener('submit', function() {
                secondsLeft = 60;
                codeInput.disabled = false;
                verifyBtn.disabled = false;
                codeInput.value = '';
                codeInput.focus();
                resendBtn.disabled = true;
                resendText.textContent = localize("Resend available in 60s");

                updateTimer();
            });
        }
    });

}
