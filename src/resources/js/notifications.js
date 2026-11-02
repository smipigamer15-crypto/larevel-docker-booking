import { translate as localize } from "./localization.js";

const icons = { success: "check", error: "exclamation", warning: "exclamation-triangle", info: "info" };
let stack, audio, pendingTone, soundEnabled = true;
export function toastTone(value) {
    return Object.hasOwn(icons, value) ? value : "info";
}
export function toastDuration(value) {
    return toastTone(value) === "error" ? 8e3 : 6e3;
}
export function playNotificationChime(context, type = "success") {
    if (!context || context.state !== "running") return false;
    const frequencies = { success: [660, 880], error: [392, 330], warning: [523, 440], info: [523, 659] }[toastTone(type)];
    const gain = context.createGain();
    gain.connect(context.destination);
    gain.gain.setValueAtTime(0, context.currentTime);
    frequencies.forEach((frequency, index) => {
        const oscillator = context.createOscillator();
        const start = context.currentTime + index * 0.12;
        oscillator.type = "sine";
        oscillator.frequency.value = frequency;
        oscillator.connect(gain);
        oscillator.onended = () => {
            oscillator.disconnect?.();
            if (index === frequencies.length - 1) gain.disconnect?.();
        };
        gain.gain.setValueAtTime(0, start);
        gain.gain.linearRampToValueAtTime(0.035, start + 0.015);
        gain.gain.exponentialRampToValueAtTime(1e-3, start + 0.11);
        oscillator.start(start);
        oscillator.stop(start + 0.12);
    });
    return true;
}
function updateSoundButtons() {
    document.querySelectorAll("[data-toast-sound]").forEach((button) => {
        button.setAttribute("aria-pressed", String(soundEnabled));
        button.setAttribute("aria-label", soundEnabled ? localize("Mute notification sound") : localize("Enable notification sound"));
        button.title = soundEnabled ? localize("Mute sound") : localize("Enable sound");
        button.querySelector("i").className = "fas " + (soundEnabled ? "fa-volume-high" : "fa-volume-xmark");
    });
}
async function unlockSound(event) {
    if (!soundEnabled || event?.target?.closest?.("[data-toast-sound]")) return;
    const Audio = window.AudioContext || window.webkitAudioContext;
    if (!Audio) return;
    try {
        audio ??= new Audio();
        if (audio.state !== "running") await audio.resume();
        if (pendingTone && stack.querySelector("[data-toast]:not(.is-leaving)")) playNotificationChime(audio, pendingTone);
        pendingTone = null;
    } catch {
    }
}
function bindToast(toast) {
    if (toast.dataset.toastBound) return;
    toast.dataset.toastBound = "true";
    const tone = toastTone(toast.dataset.tone);
    let remaining = toastDuration(tone), timer, started, paused = false;
    toast.style.setProperty("--toast-lifetime", remaining + "ms");
    const dismiss = () => {
        clearTimeout(timer);
        toast.classList.add("is-leaving");
        setTimeout(() => toast.remove(), 180);
    };
    const start = () => {
        clearTimeout(timer);
        paused = false;
        started = Date.now();
        timer = setTimeout(dismiss, remaining);
    };
    const pause = () => {
        if (paused) return;
        paused = true;
        clearTimeout(timer);
        remaining = Math.max(0, remaining - (Date.now() - started));
    };
    toast.querySelector("[data-toast-dismiss]").addEventListener("click", dismiss);
    toast.querySelector("[data-toast-sound]").addEventListener("click", () => {
        soundEnabled = !soundEnabled;
        try {
            localStorage.setItem("booking-notification-sound", soundEnabled ? "on" : "off");
        } catch {
        }
        updateSoundButtons();
        if (soundEnabled) {
            pendingTone = tone;
            unlockSound();
        }
    });
    toast.addEventListener("mouseenter", pause);
    toast.addEventListener("mouseleave", () => {
        if (!toast.contains(document.activeElement)) start();
    });
    toast.addEventListener("focusin", pause);
    toast.addEventListener("focusout", (event) => {
        if (!toast.contains(event.relatedTarget) && !toast.matches(":hover")) start();
    });
    updateSoundButtons();
    start();
    if (soundEnabled && document.visibilityState !== "hidden") {
        try {
            const Audio = window.AudioContext || window.webkitAudioContext;
            if (Audio) audio ??= new Audio();
            pendingTone = playNotificationChime(audio, tone) ? null : tone;
        } catch {
            pendingTone = tone;
        }
    }
}
export function notify(type, title, message = "") {
    if (!stack) return;
    title = localize(title);
    message = localize(message);
    const tone = toastTone(type), toast = document.createElement("article");
    toast.className = "site-toast";
    toast.dataset.toast = "";
    toast.dataset.tone = tone;
    toast.setAttribute("role", tone === "error" ? "alert" : "status");
    const icon = document.createElement("span");
    icon.className = "site-toast-icon";
    const glyph = document.createElement("i");
    glyph.className = "fas fa-" + icons[tone];
    glyph.setAttribute("aria-hidden", "true");
    icon.append(glyph);
    const copy = document.createElement("div");
    copy.className = "site-toast-copy";
    const heading = document.createElement("strong");
    heading.textContent = title;
    const text = document.createElement("p");
    text.textContent = message;
    copy.append(heading, text);
    const controls = document.createElement("div");
    controls.className = "site-toast-controls";
    for (const [attribute, label, symbol] of [["toastSound", localize("Notification sound"), "volume-high"], ["toastDismiss", localize("Dismiss notification"), "xmark"]]) {
        const button = document.createElement("button");
        button.type = "button";
        button.dataset[attribute] = "";
        button.setAttribute("aria-label", label);
        const i = document.createElement("i");
        i.className = "fas fa-" + symbol;
        i.setAttribute("aria-hidden", "true");
        button.append(i);
        controls.append(button);
    }
    const progress = document.createElement("span");
    progress.className = "site-toast-progress";
    progress.setAttribute("aria-hidden", "true");
    toast.append(icon, copy, controls, progress);
    stack.append(toast);
    while (stack.querySelectorAll("[data-toast]").length > 3) stack.querySelector("[data-toast]").remove();
    bindToast(toast);
    return toast;
}
function initializeNotifications() {
    stack = document.getElementById("site-notifications");
    if (!stack) return;
    try {
        soundEnabled = localStorage.getItem("booking-notification-sound") !== "off";
    } catch {
    }
    document.addEventListener("pointerdown", unlockSound);
    document.addEventListener("keydown", (event) => {
        if (["Enter", " ", "Tab"].includes(event.key)) unlockSound(event);
    });
    stack.querySelectorAll("[data-toast]").forEach(bindToast);
    window.bookingNotify = notify;
    const resend = document.querySelector("[data-verification-resend]");
    if (resend) {
        let seconds = Math.max(0, Math.min(60, Number(resend.dataset.cooldown) || 0));
        const hint = document.querySelector("[data-verification-countdown]");
        const update = () => {
            resend.disabled = seconds > 0;
            if (hint) hint.textContent = seconds ? `You can send another link in ${seconds}s.` : localize("Still missing the message? You can request a new link.");
        };
        update();
        if (seconds > 0) {
            const timer = setInterval(() => {
                seconds--;
                update();
                if (!seconds) clearInterval(timer);
            }, 1e3);
            window.addEventListener("pagehide", () => clearInterval(timer), { once: true });
        }
    }
}
if (typeof document !== "undefined") {
    if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", initializeNotifications, { once: true });
    else initializeNotifications();
}
