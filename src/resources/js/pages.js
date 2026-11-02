import { translate as localize } from "./localization.js";

if (document.querySelector('[data-page-script="home"]')) {
    document.addEventListener('DOMContentLoaded', () => {
        const wrapper = document.querySelector('.hero-slider-wrapper');
        if (!wrapper) return;
        const slides = [...wrapper.querySelectorAll('.slide-card')];
        const dots = [...wrapper.querySelectorAll('.slider-dots .dot')];
        if (!slides.length) return;
        let current = 0;
        let timer;
        const show = index => {
            current = (index + slides.length) % slides.length;
            slides.forEach((slide, i) => {
                slide.classList.toggle('active', i === current);
                slide.setAttribute('aria-hidden', String(i !== current));
            });
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === current);
                dot.classList.toggle('bg-primary', i === current);
                dot.classList.toggle('text-white', i === current);
                dot.classList.toggle('bg-light', i !== current);
                dot.classList.toggle('text-secondary', i !== current);
                dot.setAttribute('aria-pressed', String(i === current));
            });
        };
        const stop = () => { clearInterval(timer); timer = undefined; };
        const start = () => {
            stop();
            if (!document.hidden && !wrapper.matches(':hover') && !wrapper.contains(document.activeElement)) {
                timer = setInterval(() => show(current + 1), 4500);
            }
        };
        dots.forEach((dot, index) => dot.addEventListener('click', () => { show(index); start(); }));
        wrapper.addEventListener('mouseenter', stop);
        wrapper.addEventListener('mouseleave', start);
        wrapper.addEventListener('focusin', stop);
        wrapper.addEventListener('focusout', event => { if (!wrapper.contains(event.relatedTarget)) queueMicrotask(start); });
        document.addEventListener('visibilitychange', () => document.hidden ? stop() : start());
        show(0);
        start();
    });
}

if (document.querySelector('[data-page-script="bookings/create"]')) {
    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }

}

if (document.querySelector('[data-page-script="bookings/index"]')) {

    document.querySelectorAll('[data-view-action="bookings-index-1"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { return confirm(localize("Are you sure you want to cancel this booking?")) }).call(this);
            if (result === false) event.preventDefault();
        });
    });

}

if (document.querySelector('[data-page-script="bookings/show"]')) {
    document.querySelectorAll('[data-view-action="bookings-show-1"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { return confirm(localize("Are you sure you want to cancel this booking?")) }).call(this);
            if (result === false) event.preventDefault();
        });
    });

}

if (document.querySelector('[data-page-script="dashboard"]')) {
    document.addEventListener('DOMContentLoaded', function() {
        const bookingsCtx = document.getElementById('bookingsChart').getContext('2d');
        new Chart(bookingsCtx, {
            type: 'bar',
            data: {
                labels: JSON.parse(document.getElementById('view-data-dashboard-0').value),
                datasets: [{
                        label: localize("Bookings"),
                        data: JSON.parse(document.getElementById('view-data-dashboard-1').value),
                        backgroundColor: 'rgba(37, 99, 235, 0.6)',
                        borderColor: '#2563EB',
                        borderWidth: 1,
                        borderRadius: 6
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } } }
        });

        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: JSON.parse(document.getElementById('view-data-dashboard-2').value),
                datasets: [{
                        label: localize("Revenue ($)"),
                        data: JSON.parse(document.getElementById('view-data-dashboard-3').value),
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderColor: '#10B981',
                        borderWidth: 2,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#10B981',
                        fill: true,
                        tension: 0.4
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } } }
        });

        const topServicesCtx = document.getElementById('topServicesChart').getContext('2d');
        new Chart(topServicesCtx, {
            type: 'doughnut',
            data: {
                labels: JSON.parse(document.getElementById('view-data-dashboard-4').value),
                datasets: [{
                        data: JSON.parse(document.getElementById('view-data-dashboard-5').value),
                        backgroundColor: ['#2563EB', '#7C3AED', '#F59E0B'],
                        borderWidth: 0
                }]
            },
            options: { cutout: '70%', plugins: { legend: { position: 'bottom' } } }
        });
    });

}

if (document.querySelector('[data-page-script="errors/429"]')) {
    document.addEventListener('DOMContentLoaded', function () {
        const countdown = document.getElementById('countdown');
        if (!countdown) return;

        let seconds = 60;
        const timer = setInterval(function () {
            seconds--;
            countdown.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(timer);
                countdown.textContent = '0';
            }
        }, 1000);
    });

}

if (document.querySelector('[data-page-script="features/index"]')) {
    document.addEventListener('DOMContentLoaded', () => {
        const buttons = [...document.querySelectorAll('.filter-btn')];
        const items = document.querySelectorAll('.feature-item');
        const apply = category => {
            buttons.forEach(button => {
                const active = button.dataset.filter === category;
                button.classList.toggle('active', active);
                button.classList.toggle('btn-primary', active);
                button.classList.toggle('btn-outline-primary', !active);
                button.setAttribute('aria-pressed', String(active));
            });
            items.forEach(item => { item.style.display = category === 'all' || item.dataset.category === category ? 'flex' : 'none'; });
        };
        buttons.forEach(button => button.addEventListener('click', () => apply(button.dataset.filter)));
        const selected = new URLSearchParams(location.search).get('category');
        apply(buttons.some(button => button.dataset.filter === selected) ? selected : 'all');
    });
}

if (document.querySelector('[data-page-script="reviews/create"]')) {
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.rating-star');
        let selectedRating = 0;

        stars.forEach(star => {
            star.addEventListener('click', function() {
                const value = parseInt(this.querySelector('input').value);
                selectedRating = value;

                stars.forEach((s, index) => {
                    if (index < value) {
                        s.classList.add('active');
                        s.querySelector('i').style.color = '#F59E0B';
                    } else {
                        s.classList.remove('active');
                        s.querySelector('i').style.color = '#D1D5DB';
                    }
                });
            });

            star.addEventListener('mouseenter', function() {
                const value = parseInt(this.querySelector('input').value);
                stars.forEach((s, index) => {
                    if (index < value) {
                        s.querySelector('i').style.color = '#FCD34D';
                    }
                });
            });

            star.addEventListener('mouseleave', function() {
                stars.forEach((s, index) => {
                    if (index < selectedRating) {
                        s.querySelector('i').style.color = '#F59E0B';
                    } else {
                        s.querySelector('i').style.color = '#D1D5DB';
                    }
                });
            });
        });
    });

}

if (document.querySelector('[data-page-script="security/client-status"]')) {
    function confirmCancel() {
        Swal.fire({
            icon: 'warning',
            title: localize("Cancel Subscription?"),
            text: localize("You will lose access at the end of the billing period."),
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: localize("Yes, cancel"),
            cancelButtonText: localize("Keep subscription")
        }).then(function(result) {
            if (result.isConfirmed) {
                document.getElementById('cancelForm').submit();
            }
        });
    }

    document.querySelectorAll('[data-view-action="security-client-status-1"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { confirmCancel() }).call(this);
            if (result === false) event.preventDefault();
        });
    });

}

if (document.querySelector('[data-page-script="subscription/booking-manage"]')) {
    function confirmCancel() {
        Swal.fire({
            icon: 'warning',
            title: localize("Cancel Subscription?"),
            text: localize("You will lose access at the end of the billing period. This can be undone."),
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: localize("Yes, cancel"),
            cancelButtonText: localize("Keep subscription")
        }).then(function(result) {
            if (result.isConfirmed) {
                document.getElementById('cancelForm').submit();
            }
        });
    }

    document.querySelectorAll('[data-view-action="subscription-booking-manage-1"]').forEach(element => {
        element.addEventListener('click', function (event) {
            const result = (function () { confirmCancel() }).call(this);
            if (result === false) event.preventDefault();
        });
    });

}

if (document.querySelector('[data-page-script="subscription/manage"]')) {

    document.querySelectorAll('[data-view-action="subscription-manage-1"]').forEach(element => {
        element.addEventListener('submit', function (event) {
            const result = (function () { return confirm(localize("Are you sure you want to cancel your subscription?")) }).call(this);
            if (result === false) event.preventDefault();
        });
    });

}
