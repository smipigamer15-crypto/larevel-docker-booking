import { translate as localize } from "./localization.js";

if (typeof document !== 'undefined' && document.querySelector('[data-page-script="profile/edit"]')) {
    function togglePasswordVisibility(fieldId) {
        const input = document.getElementById(fieldId);
        const icon = input.parentElement.querySelector('.password-toggle i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    function copyToClipboard(elementId) {
        var copyText = document.getElementById(elementId);
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);

        var btn = copyText.nextElementSibling;
        var originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i>';
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-success');

        setTimeout(function() {
            btn.innerHTML = originalHtml;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-primary');
        }, 2000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        enhanceProfileNavigation(document.querySelector('[data-profile-workspace]'));
        const avatarInput = document.getElementById('avatarInputForm');

        if (avatarInput) {
            avatarInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                if (!file.type.startsWith('image/')) {
                    alert(localize("Please select an image file"));
                    avatarInput.value = '';
                    return;
                }

                if (file.size > 2 * 1024 * 1024) {
                    alert(localize("File is too large. Max 2MB"));
                    avatarInput.value = '';
                    return;
                }
            });
        }

    });

    document.querySelectorAll('[data-view-action="profile-edit-1"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { this.parentElement.style.display='none' }).call(this);
            if (result === false) event.preventDefault();
        });
    });

    document.querySelectorAll('[data-view-action="profile-edit-2"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { event.preventDefault(); document.getElementById('logout-form').submit(); }).call(this);
            if (result === false) event.preventDefault();
        });
    });

    document.querySelectorAll('[data-view-action="profile-edit-3"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { this.parentElement.style.display='none' }).call(this);
            if (result === false) event.preventDefault();
        });
    });

    document.querySelectorAll('[data-view-action="profile-edit-4"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { this.parentElement.style.display='none' }).call(this);
            if (result === false) event.preventDefault();
        });
    });

    document.querySelectorAll('[data-view-action="profile-edit-5"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { if(confirm(localize("Remove your profile photo?"))) document.getElementById('removeAvatarForm').submit(); }).call(this);
            if (result === false) event.preventDefault();
        });
    });

    document.querySelectorAll('[data-view-action="profile-edit-6"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { copyToClipboard('apiToken') }).call(this);
            if (result === false) event.preventDefault();
        });
    });

    document.querySelectorAll('[data-view-action="profile-edit-7"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { return confirm(localize("Regenerate token? Old key will stop working.")) }).call(this);
            if (result === false) event.preventDefault();
        });
    });

    document.querySelectorAll('[data-view-action="profile-edit-8"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { return confirm(localize("Disable 2FA?")) }).call(this);
            if (result === false) event.preventDefault();
        });
    });

    document.querySelectorAll('[data-view-action="profile-edit-9"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { this.parentElement.style.display='none' }).call(this);
            if (result === false) event.preventDefault();
        });
    });

    document.querySelectorAll('[data-view-action="profile-edit-10"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { togglePasswordVisibility('current_password') }).call(this);
            if (result === false) event.preventDefault();
        });
    });

    document.querySelectorAll('[data-view-action="profile-edit-11"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { togglePasswordVisibility('password') }).call(this);
            if (result === false) event.preventDefault();
        });
    });

    document.querySelectorAll('[data-view-action="profile-edit-12"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { return confirm(localize("Are you sure you want to delete your account?")) }).call(this);
            if (result === false) event.preventDefault();
        });
    });

}

export function resolveProfileSection(sections, serverSection, hash, remembered) {
    const requested = [serverSection, hash.replace(/^#/, ''), remembered, 'account'];
    return requested.find(section => sections.includes(section)) || sections[0];
}

export function enhanceProfileNavigation(workspace) {
    if (!workspace) return;
    const tabs = [...workspace.querySelectorAll('[data-profile-tab]')];
    const panels = [...workspace.querySelectorAll('[data-profile-panel]')];
    const sections = tabs.map(tab => tab.dataset.profileTab);
    if (!tabs.length || panels.length !== tabs.length) return;
    let remembered = '';
    try { remembered = sessionStorage.getItem('profile-section') || ''; } catch {}
    const activate = (section, updateHash = true) => {
        if (!sections.includes(section)) return;
        tabs.forEach(tab => {
            const active = tab.dataset.profileTab === section;
            tab.classList.toggle('active', active);
            tab.setAttribute('aria-selected', String(active));
            tab.tabIndex = active ? 0 : -1;
        });
        panels.forEach(panel => { panel.hidden = panel.dataset.profilePanel !== section; });
        try { sessionStorage.setItem('profile-section', section); } catch {}
        if (updateHash) { try { history.replaceState(null, '', '#' + section); } catch {} }
    };
    workspace.querySelector('[data-profile-tablist]').setAttribute('role', 'tablist');
    workspace.dataset.profileEnhanced = 'true';
    tabs.forEach((tab, index) => {
        tab.setAttribute('role', 'tab');
        tab.setAttribute('aria-controls', tab.dataset.profileTab);
        tab.addEventListener('click', event => { event.preventDefault(); activate(tab.dataset.profileTab); });
        tab.addEventListener('keydown', event => {
            const direction = ['ArrowRight', 'ArrowDown'].includes(event.key) ? 1 : ['ArrowLeft', 'ArrowUp'].includes(event.key) ? -1 : 0;
            let target = direction ? (index + direction + tabs.length) % tabs.length : event.key === 'Home' ? 0 : event.key === 'End' ? tabs.length - 1 : -1;
            if (target >= 0) { event.preventDefault(); activate(tabs[target].dataset.profileTab); tabs[target].focus(); }
        });
    });
    panels.forEach(panel => {
        panel.setAttribute('role', 'tabpanel');
        panel.querySelectorAll('form').forEach(form => form.addEventListener('submit', () => {
            try { sessionStorage.setItem('profile-section', panel.dataset.profilePanel); } catch {}
        }));
    });
    workspace.querySelectorAll('[data-reveal-target]').forEach(button => button.addEventListener('click', () => {
        const field = document.getElementById(button.dataset.revealTarget);
        const revealed = field.type === 'password';
        field.type = revealed ? 'text' : 'password';
        button.setAttribute('aria-pressed', String(revealed));
        button.setAttribute('aria-label', revealed ? localize("Hide API token") : localize("Show API token"));
        button.querySelector('i').className = 'fas ' + (revealed ? 'fa-eye-slash' : 'fa-eye');
    }));
    activate(resolveProfileSection(sections, workspace.dataset.profileInitial || '', location.hash, remembered), false);
    window.addEventListener('hashchange', () => activate(resolveProfileSection(sections, '', location.hash, ''), false));
}
