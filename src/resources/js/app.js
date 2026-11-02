import './notifications';
import './localization';
import './profile';
import './features';
import './pages';
import './admin';
import './auth';
import './bootstrap';

import Alpine from 'alpinejs';
import { initializeAnimations } from './animations';

window.Alpine = Alpine;

function registerViewComponents(Alpine) {
    Alpine.data('bookingDropdown', () => ({
        open: false,
        toggle() { this.open = !this.open; },
        close() { this.open = false; },
        dropdownEvents: {
            ['@click.outside']() { this.close(); },
            ['@close.stop']() { this.close(); },
        },
    }));
    Alpine.data('bookingModal', () => ({
        show: false,
        init() {
            this.show = this.$el.dataset.modalShow === 'true';
            this.$watch('show', value => {
                document.body.classList.toggle('overflow-y-hidden', value);
                if (value && this.$el.dataset.modalFocusable === 'true') {
                    setTimeout(() => this.firstFocusable()?.focus(), 100);
                }
            });
        },
        close() { this.show = false; },
        focusables() {
            return [...this.$el.querySelectorAll('a, button, input:not([type="hidden"]), textarea, select, details, [tabindex]:not([tabindex="-1"])')]
            .filter(element => !element.hasAttribute('disabled'));
        },
        firstFocusable() { return this.focusables()[0]; },
        lastFocusable() { return this.focusables().slice(-1)[0]; },
        nextFocusable() {
            const list = this.focusables();
            return list[(list.indexOf(document.activeElement) + 1) % (list.length + 1)] || this.firstFocusable();
        },
        prevFocusable() {
            const list = this.focusables();
            return list[Math.max(0, list.indexOf(document.activeElement)) - 1] || this.lastFocusable();
        },
        dialogEvents: {
            ['@open-modal.window'](event) { if (event.detail === this.$el.dataset.modalName) this.show = true; },
            ['@close-modal.window'](event) { if (event.detail === this.$el.dataset.modalName) this.close(); },
            ['@close.stop']() { this.close(); },
            ['@keydown.escape.window']() { this.close(); },
            ['@keydown.tab.prevent'](event) { if (!event.shiftKey) this.nextFocusable()?.focus(); },
            ['@keydown.shift.tab.prevent']() { this.prevFocusable()?.focus(); },
        },
    }));
}

document.querySelectorAll('[data-view-width]').forEach(element => {
    element.style.width = Number(element.dataset.viewWidth) + '%';
});
document.querySelectorAll('[data-view-background]').forEach(element => {
    element.style.backgroundImage = 'url("' + element.dataset.viewBackground + '")';
});
document.querySelectorAll('[data-initial-display]').forEach(element => {
    element.style.display = element.dataset.initialDisplay;
});

registerViewComponents(Alpine);
Alpine.start();

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => initializeAnimations(), { once: true });
} else {
    initializeAnimations();
}
