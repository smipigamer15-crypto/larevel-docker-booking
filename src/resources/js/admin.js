if (document.querySelector('[data-page-script="admin/api-tokens/index"]')) {

    document.querySelectorAll('[data-view-action="admin-api-tokens-index-1"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { return confirm('Revoke this token? Client will lose API access.') }).call(this);
            if (result === false) event.preventDefault();
        });
    });

}

if (document.querySelector('[data-page-script="admin/backups/index"]')) {

    document.querySelectorAll('[data-view-action="admin-backups-index-1"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { return confirm('Clean old backups?') }).call(this);
            if (result === false) event.preventDefault();
        });
    });

    document.querySelectorAll('[data-view-action="admin-backups-index-2"]').forEach(element => {
        element.addEventListener('submit', function (event) {
            const result = (function () { return confirm('Delete this backup?') }).call(this);
            if (result === false) event.preventDefault();
        });
    });

}

if (document.querySelector('[data-page-script="admin/bookings/index"]')) {

    document.querySelectorAll('[data-view-action="admin-bookings-index-1"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { window.location.reload() }).call(this);
            if (result === false) event.preventDefault();
        });
    });

}

if (document.querySelector('[data-page-script="admin/bookings/show"]')) {

    document.querySelectorAll('[data-view-action="admin-bookings-show-1"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { return confirm('Are you sure you want to delete this booking?') }).call(this);
            if (result === false) event.preventDefault();
        });
    });

}

if (document.querySelector('[data-page-script="admin/categories/index"]')) {

    document.querySelectorAll('[data-view-action="admin-categories-index-1"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { return confirm('Delete this category?') }).call(this);
            if (result === false) event.preventDefault();
        });
    });

}

if (document.querySelector('[data-page-script="admin/custom-rules/edit"]')) {

    document.querySelectorAll('[data-view-action="admin-custom-rules-edit-1"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { return confirm('Reset all custom rules to defaults?') }).call(this);
            if (result === false) event.preventDefault();
        });
    });

}

if (document.querySelector('[data-page-script="admin/custom-rules/index"]')) {

    document.querySelectorAll('[data-view-action="admin-custom-rules-index-1"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { window.location.reload() }).call(this);
            if (result === false) event.preventDefault();
        });
    });

}

if (document.querySelector('[data-page-script="admin/dashboard"]')) {
    document.addEventListener('DOMContentLoaded', () => {
        const canvas = document.getElementById('bookingChart');
        if (!canvas || !window.Chart) return;
        const data = JSON.parse(document.getElementById('view-data-admin-dashboard-0').value);
        new window.Chart(canvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [
                    { label: 'Bookings', data: data.values, borderColor: '#60a5fa', backgroundColor: 'rgba(96, 165, 250, 0.10)', fill: true, tension: 0.3, pointRadius: data.labels.length > 30 ? 0 : 3, borderWidth: 2 },
                    { label: 'Registrations', data: data.registrations, borderColor: '#2dd4bf', backgroundColor: 'rgba(45, 212, 191, 0.06)', fill: true, tension: 0.3, pointRadius: data.labels.length > 30 ? 0 : 3, borderWidth: 2 },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { labels: { color: '#F8FAFC', usePointStyle: true, padding: 20 } } },
                scales: {
                    y: { beginAtZero: true, ticks: { color: '#94A3B8', precision: 0 }, grid: { color: 'rgba(148, 163, 184, 0.10)' } },
                    x: { ticks: { color: '#94A3B8', maxTicksLimit: 12 }, grid: { display: false } },
                },
            },
        });
    });
}

if (document.querySelector('[data-page-script="admin/firewall/index"]')) {

    document.querySelectorAll('[data-view-action="admin-firewall-index-1"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { return confirm('Delete this rule?') }).call(this);
            if (result === false) event.preventDefault();
        });
    });

}

if (document.querySelector('[data-page-script="admin/firewall/logs"]')) {

    document.querySelectorAll('[data-view-action="admin-firewall-logs-1"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { return confirm('Clear logs older than 30 days?') }).call(this);
            if (result === false) event.preventDefault();
        });
    });

    document.querySelectorAll('[data-view-action="admin-firewall-logs-2"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { return confirm('Permanently block ' + this.getAttribute('data-argument-0') + '?') }).call(this);
            if (result === false) event.preventDefault();
        });
    });

}

if (document.querySelector('[data-page-script="admin/monitoring/index"]')) {

    document.querySelectorAll('[data-view-action="admin-monitoring-index-1"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { return confirm('Delete logs older than 30 days?') }).call(this);
            if (result === false) event.preventDefault();
        });
    });

    document.querySelectorAll('[data-view-action="admin-monitoring-index-2"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { return confirm('Are you sure you want to DELETE ALL logs? This action cannot be undone!') }).call(this);
            if (result === false) event.preventDefault();
        });
    });

}

if (document.querySelector('[data-page-script="admin/monitoring/show"]')) {

    function copyToClipboard(text, event) {
        if (!text) {
            alert('Nothing to copy');
            return;
        }

        const btn = event.target.closest('button');
        if (!btn) {
            return;
        }

        navigator.clipboard.writeText(text).then(function () {
            const originalHtml = btn.innerHTML;
            const originalClass = btn.className;

            btn.innerHTML = '<i class="fas fa-check me-2"></i>Copied!';
            btn.className = 'btn btn-success rounded-pill';

            setTimeout(function () {
                btn.innerHTML = originalHtml;
                btn.className = originalClass;
            }, 2000);
        }).catch(function (err) {
            alert('Failed to copy: ' + err);
        });
    }

    document.querySelectorAll('[data-view-action="admin-monitoring-show-1"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { copyToClipboard(this.getAttribute('data-argument-0'), event) }).call(this);
            if (result === false) event.preventDefault();
        });
    });

}

if (document.querySelector('[data-page-script="admin/security/index"]')) {

    document.querySelectorAll('[data-view-action="admin-security-index-1"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { return confirm('Delete this inquiry?') }).call(this);
            if (result === false) event.preventDefault();
        });
    });

}

if (document.querySelector('[data-page-script="admin/security/show"]')) {

    function copyLink(event) {
        const input = document.getElementById('clientLink');
        if (!input) return;

        input.select();
        input.setSelectionRange(0, 99999);

        try {
            document.execCommand('copy');

            const btn = event.target.closest('button');
            const originalHtml = btn.innerHTML;
            const originalClass = btn.className;

            btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
            btn.className = 'btn btn-success';

            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.className = originalClass;
            }, 2000);
        } catch (err) {
            alert('Failed to copy: ' + err);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {

        document.querySelectorAll('.quick-date').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();

                const days = parseInt(this.dataset.days);
                const date = new Date();
                date.setDate(date.getDate() + days);

                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');

                document.getElementById('expiresAtInput').value = `${year}-${month}-${day}T${hours}:${minutes}`;
            });
        });

        const manageBtn = document.getElementById('manageBtn');
        if (manageBtn) {
            manageBtn.addEventListener('click', function(e) {
                e.preventDefault();

                const status = document.getElementById('statusSelect').value;
                const adminNotes = document.getElementById('adminNotes').value;
                const expiresAt = document.getElementById('expiresAtInput').value;
                const sendEmail = document.getElementById('sendEmailCheck').checked;

                if (!expiresAt) {
                    alert('Please select expiration date');
                    return;
                }

                let message = 'Are you sure you want to update this client?';
                if (sendEmail) {
                    message += ' An email with the access link will be sent.';
                }

                if (!confirm(message)) {
                    return;
                }

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = document.getElementById('view-data-admin-security-show-0').value;

                const fields = {
                    '_token': document.getElementById('view-data-admin-security-show-1').value,
                    '_method': 'PUT',
                    'status': status,
                    'admin_notes': adminNotes || '',
                    'expires_at': expiresAt,
                    'send_email': sendEmail ? '1' : '0'
                };

                for (const [name, value] of Object.entries(fields)) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = name;
                    input.value = value;
                    form.appendChild(input);
                }

                document.body.appendChild(form);
                form.submit();
            });
        }
    });

    document.querySelectorAll('[data-view-action="admin-security-show-1"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { return confirm('Regenerate token? The old link will stop working.') }).call(this);
            if (result === false) event.preventDefault();
        });
    });

    document.querySelectorAll('[data-view-action="admin-security-show-2"]').forEach(element => {
        element.addEventListener('submit', function (event) {
            const result = (function () { return confirm('Are you sure you want to deactivate this client?') }).call(this);
            if (result === false) event.preventDefault();
        });
    });

    document.querySelectorAll('[data-view-action="admin-security-show-3"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { copyLink(event) }).call(this);
            if (result === false) event.preventDefault();
        });
    });

}

if (document.querySelector('[data-page-script="admin/services/index"]')) {

    document.querySelectorAll('[data-view-action="admin-services-index-1"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { return confirm('Delete this service?') }).call(this);
            if (result === false) event.preventDefault();
        });
    });

}

if (document.querySelector('[data-page-script="admin/subscriptions/index"]')) {

    document.querySelectorAll('[data-view-action="admin-subscriptions-index-1"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { return confirm('Delete this subscription?') }).call(this);
            if (result === false) event.preventDefault();
        });
    });

}

if (document.querySelector('[data-page-script="admin/subscriptions/show"]')) {
    function copyLink(event) {
        const input = document.getElementById('clientLink');
        if (!input) return;

        input.select();
        input.setSelectionRange(0, 99999);

        try {
            document.execCommand('copy');

            const btn = event.target.closest('button');
            const originalHtml = btn.innerHTML;
            const originalClass = btn.className;

            btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
            btn.className = 'btn btn-success';

            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.className = originalClass;
            }, 2000);
        } catch (err) {
            alert('Failed to copy: ' + err);
        }
    }

    document.querySelectorAll('[data-view-action="admin-subscriptions-show-1"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { return confirm('Regenerate token? Old link will stop working.') }).call(this);
            if (result === false) event.preventDefault();
        });
    });

    document.querySelectorAll('[data-view-action="admin-subscriptions-show-2"]').forEach(element => {
        element.addEventListener('submit', function (event) {
            const result = (function () { return confirm('Are you sure you want to deactivate this subscription?') }).call(this);
            if (result === false) event.preventDefault();
        });
    });

    document.querySelectorAll('[data-view-action="admin-subscriptions-show-3"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { copyLink(event) }).call(this);
            if (result === false) event.preventDefault();
        });
    });

}

if (document.querySelector('[data-page-script="admin/support/index"]')) {

    document.querySelectorAll('[data-view-action="admin-support-index-1"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { this.parentElement.style.display='none' }).call(this);
            if (result === false) event.preventDefault();
        });
    });

}

if (document.querySelector('[data-page-script="admin/users/edit"]')) {

    function togglePassword(fieldId, btn) {
        const input = document.getElementById(fieldId);
        const icon = btn.querySelector('i');

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

    document.querySelectorAll('[data-view-action="admin-users-edit-1"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { togglePassword('password', this) }).call(this);
            if (result === false) event.preventDefault();
        });
    });

    document.querySelectorAll('[data-view-action="admin-users-edit-2"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { togglePassword('password_confirmation', this) }).call(this);
            if (result === false) event.preventDefault();
        });
    });

}

if (document.querySelector('[data-page-script="admin/users/index"]')) {

    document.querySelectorAll('[data-view-action="admin-users-index-1"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { window.location.reload() }).call(this);
            if (result === false) event.preventDefault();
        });
    });

    document.querySelectorAll('[data-view-action="admin-users-index-2"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { return confirm('Delete this user?') }).call(this);
            if (result === false) event.preventDefault();
        });
    });

}

if (document.querySelector('[data-page-script="admin/vulnerability/index"]')) {

    document.querySelectorAll('[data-view-action="admin-vulnerability-index-1"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { this.disabled=true; this.form.submit(); }).call(this);
            if (result === false) event.preventDefault();
        });
    });

    document.querySelectorAll('[data-view-action="admin-vulnerability-index-2"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { return confirm('Delete this scan?') }).call(this);
            if (result === false) event.preventDefault();
        });
    });

}
