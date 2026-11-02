function localize(message, replacements = {}) {
    if (typeof window !== "undefined" && window.bookingTranslate) {
        return window.bookingTranslate(message, replacements);
    }
    return Object.entries(replacements).reduce((text, [key, value]) => text.replaceAll(":" + key, String(value)), message);
}

document.addEventListener("DOMContentLoaded", function() {
    let serviceSelect = document.getElementById("service_id");
    let dateInput = document.getElementById("booking_date");
    let timeSelect = document.getElementById("start_time");
    let nextBtn = document.getElementById("nextStep");
    let prevBtn = document.getElementById("prevStep");
    let submitBtn = document.getElementById("submitBtn");
    let steps = document.querySelectorAll(".booking-step");
    let progressSteps = document.querySelectorAll(".step");
    let progressLines = document.querySelectorAll(".step-line");
    let form = document.getElementById("bookingForm");
    if (!form) return;
    let currentStep = 1;
    let totalSteps = 3;
    function warnAboutStep(title, message) {
        if (typeof window !== "undefined" && window.bookingNotify) {
            window.bookingNotify("warning", title, message);
        } else {
            Swal.fire({ icon: "warning", title, text: message, timer: 3e3, showConfirmButton: false, toast: true, position: "top-end" });
        }
    }
    function goToStep(step) {
        if (step === 2 && !serviceSelect.value) {
            warnAboutStep(localize("Please select a service"), localize("Choose a service before proceeding."));
            return;
        }
        if (step === 3 && (!dateInput.value || !timeSelect.value)) {
            warnAboutStep(localize("Please select date and time"), localize("Choose a date and time before confirming."));
            return;
        }
        if (step === 3 && !dateInput.checkValidity()) {
            dateInput.reportValidity();
            return;
        }
        currentStep = step;
        steps.forEach(function(el, index) {
            el.style.display = index + 1 === step ? "block" : "none";
        });
        progressSteps.forEach(function(el, index) {
            el.classList.remove("active", "completed");
            if (index + 1 === step) el.setAttribute("aria-current", "step");
            else el.removeAttribute("aria-current");
            if (index + 1 === step) el.classList.add("active");
            else if (index + 1 < step) el.classList.add("completed");
        });
        progressLines.forEach(function(el, index) {
            el.classList.toggle("active", index < step - 1);
        });
        prevBtn.style.display = step > 1 ? "inline-flex" : "none";
        nextBtn.style.display = step < totalSteps ? "inline-flex" : "none";
        submitBtn.style.display = step === totalSteps ? "inline-flex" : "none";
        const status = document.getElementById("booking-step-status");
        if (status) status.textContent = localize("Step :step of 3 · :label", {
            step,
            label: localize([localize("Choose your service"), localize("Find your date and time"), localize("Review your appointment")][step - 1]),
        });
        if (step === 3) updateSummary();
    }
    nextBtn.addEventListener("click", function() {
        goToStep(currentStep + 1);
    });
    prevBtn.addEventListener("click", function() {
        goToStep(currentStep - 1);
    });
    form.addEventListener("invalid", function(event) {
        const invalidStep = event.target.closest(".booking-step");
        if (invalidStep) goToStep(Number(invalidStep.dataset.step));
    }, true);
    serviceSelect.addEventListener("change", function() {
        let selected = this.options[this.selectedIndex];
        let preview = document.getElementById("servicePreview");
        if (this.value) {
            preview.style.display = "block";
            document.getElementById("previewTitle").textContent = selected.dataset.name || selected.text.split("—")[0].trim();
            document.getElementById("previewPrice").textContent = selected.dataset.price || "0";
            document.getElementById("previewDuration").textContent = selected.dataset.duration || "0";
        } else {
            preview.style.display = "none";
        }
    });
    if (serviceSelect.value) serviceSelect.dispatchEvent(new Event("change"));
    function showTimePlaceholder(message) {
        const option = document.createElement("option");
        option.value = "";
        option.textContent = localize(message);
        timeSelect.replaceChildren(option);
    }
    function loadSlots() {
        let serviceId = serviceSelect.value;
        let date = dateInput.value;
        if (!serviceId || !date) {
            showTimePlaceholder(localize("Select service and date first"));
            return;
        }
        showTimePlaceholder(localize("Loading..."));
        fetch("/booking/slots?service_id=" + serviceId + "&date=" + date).then(function(response) {
            return response.json();
        }).then(function(data) {
            showTimePlaceholder(localize("Select time..."));
            if (data.error) {
                showTimePlaceholder(data.error);
                return;
            }
            if (data.slots && data.slots.length > 0) {
                data.slots.forEach(function(slot) {
                    var option = document.createElement("option");
                    option.value = slot.start;
                    option.textContent = slot.start + " — " + slot.end;
                    option.dataset.end = slot.end;
                    timeSelect.appendChild(option);
                });
                if (timeSelect.dataset.prefillTime) {
                    const preferred = timeSelect.dataset.prefillTime;
                    if ([...timeSelect.options].some((option) => option.value === preferred)) {
                        timeSelect.value = preferred;
                        timeSelect.dispatchEvent(new Event("change"));
                    }
                    delete timeSelect.dataset.prefillTime;
                }
            } else {
                showTimePlaceholder(localize("No available slots"));
            }
        }).catch(function(error) {
            console.error("Error:", error);
            showTimePlaceholder(localize("Error loading slots"));
        });
    }
    serviceSelect.addEventListener("change", loadSlots);
    dateInput.addEventListener("change", loadSlots);
    timeSelect.addEventListener("change", function() {
        let selected = this.options[this.selectedIndex];
        if (selected && selected.dataset.end) {
            let endInput = document.querySelector('input[name="end_time"]');
            if (!endInput) {
                endInput = document.createElement("input");
                endInput.type = "hidden";
                endInput.name = "end_time";
                form.appendChild(endInput);
            }
            endInput.value = selected.dataset.end;
        }
    });
    if (dateInput.value && serviceSelect.value) loadSlots();
    function updateSummary() {
        let selectedService = serviceSelect.options[serviceSelect.selectedIndex];
        let serviceName = selectedService ? selectedService.dataset.name || selectedService.text.split("—")[0].trim() : "—";
        let price = selectedService ? selectedService.dataset.price : "0";
        let duration = selectedService ? selectedService.dataset.duration : "0";
        document.getElementById("summaryService").textContent = serviceName;
        document.getElementById("summaryPrice").textContent = price + " $";
        document.getElementById("summaryDuration").textContent = duration + " " + localize("min");
        document.getElementById("summaryTotal").textContent = price + " $";
        if (dateInput.value) {
            let date = new Date(dateInput.value + "T00:00:00");
            document.getElementById("summaryDate").textContent = date.toLocaleDateString(document.documentElement?.lang || "en", { weekday: "long", year: "numeric", month: "long", day: "numeric" });
        }
        let selectedTime = timeSelect.options[timeSelect.selectedIndex];
        document.getElementById("summaryTime").textContent = selectedTime ? selectedTime.textContent : "—";
    }
    let currentMonth = ( new Date()).getMonth();
    let currentYear = ( new Date()).getFullYear();
    function renderCalendar(month, year) {
        let grid = document.getElementById("calendarGrid");
        if (!grid) return;
        const locale = document.documentElement.lang || "en";
        document.getElementById("calendarMonth").textContent = new Intl.DateTimeFormat(locale, { month: "long", year: "numeric" }).format(new Date(year, month, 1));
        let firstDay = new Date(year, month, 1).getDay();
        let daysInMonth = new Date(year, month + 1, 0).getDate();
        let today = new Date();
        let todayStr = today.getFullYear() + "-" + String(today.getMonth() + 1).padStart(2, "0") + "-" + String(today.getDate()).padStart(2, "0");
        let html = "";
        const weekdays = new Intl.DateTimeFormat(locale, { weekday: "short" });
        Array.from({ length: 7 }, (_, day) => weekdays.format(new Date(2024, 0, 7 + day))).forEach(function(day) {
            html += '<div class="calendar-day-header">' + day + "</div>";
        });
        for (let i = 0; i < firstDay; i++) {
            html += '<div class="calendar-day empty"></div>';
        }
        for (let day = 1; day <= daysInMonth; day++) {
            let dateStr = year + "-" + String(month + 1).padStart(2, "0") + "-" + String(day).padStart(2, "0");
            let isPast = dateStr < todayStr;
            let isToday = dateStr === todayStr;
            let classes = "calendar-day";
            if (isPast) classes += " disabled";
            if (isToday) classes += " today";
            html += '<div class="' + classes + '" data-date="' + dateStr + '">' + day + "</div>";
        }
        grid.innerHTML = html;
        grid.querySelectorAll(".calendar-day:not(.disabled):not(.empty)").forEach(function(el) {
            el.addEventListener("click", function() {
                grid.querySelectorAll(".calendar-day.selected").forEach(function(d) {
                    d.classList.remove("selected");
                });
                this.classList.add("selected");
                dateInput.value = this.dataset.date;
                dateInput.dispatchEvent(new Event("change"));
            });
        });
    }
    renderCalendar(currentMonth, currentYear);
    let prevMonthBtn = document.getElementById("prevMonth");
    let nextMonthBtn = document.getElementById("nextMonth");
    if (prevMonthBtn) {
        prevMonthBtn.addEventListener("click", function() {
            if (currentMonth === 0) {
                currentMonth = 11;
                currentYear--;
            } else currentMonth--;
            renderCalendar(currentMonth, currentYear);
        });
    }
    if (nextMonthBtn) {
        nextMonthBtn.addEventListener("click", function() {
            if (currentMonth === 11) {
                currentMonth = 0;
                currentYear++;
            } else currentMonth++;
            renderCalendar(currentMonth, currentYear);
        });
    }
    goToStep(1);
});
