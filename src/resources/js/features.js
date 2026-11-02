import { translate as localize } from "./localization.js";

export function buildCampaignUrl(destination, labels) {
    const url = new URL(destination);
    if (!["http:", "https:"].includes(url.protocol)) throw new Error(localize("Unsupported booking URL."));
    for (const key of ["source", "medium", "campaign"]) url.searchParams.set("utm_" + key, labels[key].trim());
    return url.toString();
}
export function parseBookingRequest(text, services, now = new Date()) {
    const normalized = text.normalize("NFKC").toLocaleLowerCase();
    const service = [...services].sort((a, b) => b.title.length - a.title.length).find((item) => normalized.includes(item.title.toLocaleLowerCase()));
    let date = "";
    const iso = text.match(/\b(\d{4}-\d{2}-\d{2})\b/);
    if (iso && !Number.isNaN(Date.parse(iso[1])) && new Date(iso[1]).toISOString().slice(0, 10) === iso[1]) date = iso[1];
    else if (/tomorrow|завтра|jutro|morgen/iu.test(text)) {
        const tomorrow = new Date(now);
        tomorrow.setDate(tomorrow.getDate() + 1);
        date = localDate(tomorrow);
    } else if (/today|сьогодні|сегодня|dzisiaj|dziś|heute/iu.test(text)) date = localDate(now);
    const clock = text.match(/\b(\d{1,2}):(\d{2})\s*(am|pm)?\b/i) || text.match(/\b(\d{1,2})\s*(am|pm)\b/i);
    let time = "";
    if (clock) {
        let hour = Number(clock[1]);
        const minute = clock[2]?.match(/^\d+$/) ? Number(clock[2]) : 0;
        const suffix = (clock[3] || (/am|pm/i.test(clock[2] || "") ? clock[2] : "")).toLowerCase();
        const valid = suffix ? hour >= 1 && hour <= 12 : hour <= 23;
        if (suffix && valid) hour = hour % 12 + (suffix === "pm" ? 12 : 0);
        if (valid && minute <= 59) time = `${String(hour).padStart(2, "0")}:${String(minute).padStart(2, "0")}`;
    }
    return { serviceId: service ? String(service.id) : "", date, time };
}
function localDate(date) {
    return [date.getFullYear(), String(date.getMonth() + 1).padStart(2, "0"), String(date.getDate()).padStart(2, "0")].join("-");
}
let installPrompt;
if (typeof window !== "undefined") {
    window.addEventListener("beforeinstallprompt", (event) => {
        event.preventDefault();
        installPrompt = event;
    });
    window.addEventListener("load", () => {
        if ("serviceWorker" in navigator && document.querySelector('link[rel="manifest"]')) navigator.serviceWorker.register("/sw.js").catch(() => {
        });
    });
    const initialize = () => {
        const root = document.querySelector("[data-feature-tool]");
        document.querySelectorAll("[data-brand-accent]").forEach((element) => setAccent(element, element.dataset.brandAccent));
        const search = document.getElementById("feature-search");
        search?.addEventListener("input", () => document.querySelectorAll("[data-search-item]").forEach((item) => {
            item.hidden = !item.textContent.toLowerCase().includes(search.value.toLowerCase());
        }));
        document.querySelectorAll("[data-copy-target]").forEach((button) => button.addEventListener("click", async () => {
            const field = document.getElementById(button.dataset.copyTarget);
            try {
                if (navigator.clipboard) await navigator.clipboard.writeText(field.value);
                else {
                    field.select();
                    if (!document.execCommand("copy")) throw new Error("copy");
                }
                const label = button.textContent;
                button.textContent = localize("Copied!");
                setTimeout(() => {
                    button.textContent = label;
                }, 2e3);
            } catch {
                field.focus();
                field.select();
                button.textContent = localize("Select and copy the link");
            }
        }));
        const linkForm = document.getElementById("feature-link-form");
        linkForm?.addEventListener("submit", (event) => {
            event.preventDefault();
            const labels = Object.fromEntries(["source", "medium", "campaign"].map((key) => [key, document.getElementById("share-" + key).value]));
            const url = buildCampaignUrl(document.getElementById("share-destination").value, labels);
            document.getElementById("share-result").value = url;
            document.getElementById("facebook-share").href = "https://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(url);
        });
        const preview = document.querySelector(".feature-brand-preview");
        document.getElementById("business-name")?.addEventListener("input", (event) => {
            document.getElementById("brand-name-preview").textContent = event.target.value;
        });
        document.getElementById("brand-welcome")?.addEventListener("input", (event) => {
            document.getElementById("brand-welcome-preview").textContent = event.target.value;
        });
        document.getElementById("brand-accent")?.addEventListener("input", (event) => setAccent(preview, event.target.value));
        document.querySelectorAll('input[name="template"]').forEach((input) => input.addEventListener("change", () => {
            preview.dataset.template = input.value;
        }));
        const emailBody = document.getElementById("email-body");
        const updateEmail = () => {
            document.getElementById("email-live-preview").textContent = emailBody.value.replace(/\{(name|service|date|time|booking_id)\}/g, (_, key) => ({ name: "Alex", service: localize("Example appointment"), date: localDate( new Date()), time: "10:00", booking_id: "123" })[key]);
        };
        if (emailBody) {
            emailBody.addEventListener("input", updateEmail);
            updateEmail();
        }
        document.getElementById("install-booking-app")?.addEventListener("click", async () => {
            const status = document.getElementById("install-status");
            if (!installPrompt) {
                status.textContent = window.matchMedia("(display-mode: standalone)").matches ? localize("Booking is already running as an installed app.") : localize("Use your browser menu to install, or follow the device instructions below.");
                return;
            }
            try {
                await installPrompt.prompt();
                const choice = await installPrompt.userChoice;
                status.textContent = choice.outcome === "accepted" ? localize("Installation requested.") : localize("You can install later from your browser menu.");
                installPrompt = null;
            } catch {
                status.textContent = localize("Use the installation instructions below.");
            }
        });
        if (root?.dataset.featureTool === "voice") initializeVoice(root);
        const demo = document.querySelector("[data-admin-demo]");
        if (demo) {
            const update = (days) => {
                const data = { 7: { bookings: 42, users: 18, completed: 31 }, 30: { bookings: 184, users: 76, completed: 151 }, 90: { bookings: 563, users: 219, completed: 487 } }[days];
                demo.querySelectorAll("[data-demo-metric]").forEach((element) => {
                    element.textContent = data[element.dataset.demoMetric];
                });
                demo.querySelectorAll("[data-demo-days]").forEach((button) => {
                    const active = button.dataset.demoDays === String(days);
                    button.classList.toggle("active", active);
                    button.setAttribute("aria-pressed", String(active));
                });
                demo.querySelectorAll("[data-demo-bar]").forEach((bar, i) => {
                    bar.style.height = Math.max(20, (Number(bar.dataset.demoBar) + Number(days) + i * 3) % 100) + "%";
                });
            };
            demo.querySelectorAll("[data-demo-days]").forEach((button) => button.addEventListener("click", () => update(button.dataset.demoDays)));
            update(30);
        }
        document.querySelectorAll("[data-support-thread]").forEach((thread) => {
            const refresh = async () => {
                const details = thread.closest("details"), modal = thread.closest(".modal");
                if (document.hidden || details && !details.open || modal && !modal.classList.contains("show")) return;
                try {
                    const response = await fetch(thread.dataset.supportThread, { headers: { Accept: "application/json" } });
                    if (!response.ok) return;
                    const data = await response.json();
                    const messages = data.messages.map((message) => {
                        const wrapper = document.createElement("div");
                        wrapper.className = "feature-message" + (message.admin ? " from-support" : "");
                        const label = document.createElement("small");
                        label.textContent = message.admin ? localize("Support") : localize("You");
                        const body = document.createElement("p");
                        body.textContent = message.body;
                        wrapper.append(label, body);
                        return wrapper;
                    });
                    thread.replaceChildren(...messages);
                } catch {
                }
            };
            const timer = setInterval(refresh, 15e3);
            window.addEventListener("pagehide", () => clearInterval(timer), { once: true });
        });
    };
    if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", initialize, { once: true });
    else initialize();
}
function setAccent(element, color) {
    if (!element || !/^#[\da-f]{6}$/i.test(color)) return;
    const [r, g, b] = [1, 3, 5].map((index) => parseInt(color.slice(index, index + 2), 16));
    element.style.setProperty("--feature-accent", color);
    element.style.setProperty("--feature-on-accent", (r * 299 + g * 587 + b * 114) / 1e3 > 160 ? "#0F172A" : "#FFFFFF");
}
function initializeVoice(root) {
    const text = document.getElementById("voice-text"), status = document.getElementById("voice-status");
    const service = document.getElementById("voice-service"), date = document.getElementById("voice-date"), time = document.getElementById("voice-time");
    let request;
    const slots = async () => {
        const output = document.getElementById("voice-slots");
        if (!service.value || !date.value) return;
        if (root.dataset.signedIn !== "1") {
            output.textContent = localize("Sign in to check availability. You can review your draft first.");
            return;
        }
        request?.abort();
        request = new AbortController();
        const url = new URL(document.getElementById("voice-booking-form").dataset.slotsUrl);
        url.searchParams.set("service_id", service.value);
        url.searchParams.set("date", date.value);
        try {
            const response = await fetch(url, { headers: { Accept: "application/json" }, signal: request.signal });
            if (!response.ok) throw new Error("slots");
            const data = await response.json();
            output.textContent = data.slots?.length ? (localize("Available times:") + " ") + data.slots.map((slot) => slot.start).join(", ") : localize("No available times for this date. Choose another date.");
        } catch (error) {
            if (error.name !== "AbortError") output.textContent = localize("Availability could not be loaded. The booking form will check it again.");
        }
    };
    [service, date].forEach((field) => field.addEventListener("change", slots));
    document.getElementById("voice-parse").addEventListener("click", () => {
        const result = parseBookingRequest(text.value, [...service.options].filter((option) => option.value).map((option) => ({ id: option.value, title: option.textContent })));
        if (result.serviceId) service.value = result.serviceId;
        if (result.date) date.value = result.date;
        if (result.time) time.value = result.time;
        status.textContent = localize("Check the selected service, date and time before continuing. No appointment has been created.");
        slots();
    });
    const Speech = window.SpeechRecognition || window.webkitSpeechRecognition;
    const button = document.getElementById("voice-start");
    if (!Speech) {
        button.disabled = true;
        status.textContent = localize("Voice input is unavailable in this browser. Type a request above instead.");
        return;
    }
    const recognition = new Speech();
    recognition.lang = document.documentElement.lang || "en-US";
    recognition.interimResults = false;
    button.addEventListener("click", () => {
        try {
            recognition.start();
            button.disabled = true;
            status.textContent = localize("Listening…");
        } catch {
            status.textContent = localize("Microphone could not start. Try typing your request.");
        }
    });
    recognition.onresult = (event) => {
        text.value = event.results[0][0].transcript;
        document.getElementById("voice-parse").click();
    };
    recognition.onerror = () => {
        status.textContent = localize("Microphone permission or recognition failed. You can type your request instead.");
    };
    recognition.onend = () => {
        button.disabled = false;
    };
    window.addEventListener("pagehide", () => recognition.abort(), { once: true });
}
