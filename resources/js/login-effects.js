class LoginEffects {
    constructor() {
        this.initShakeOnError();
    }

    initShakeOnError() {
        const loginForm = document.getElementById('login-form');
        const loginPanel = document.querySelector('.login-panel');

        if (!loginForm || !loginPanel) return;

        if (document.querySelector('.text-danger')) {
            loginPanel.classList.add('shake');
            setTimeout(() => {
                loginPanel.classList.remove('shake');
            }, 500);
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('.login-panel')) {
        new LoginEffects();
    }
});
