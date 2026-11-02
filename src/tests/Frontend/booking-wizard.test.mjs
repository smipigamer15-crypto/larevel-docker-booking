import test from "node:test";
import assert from "node:assert/strict";
import fs from "node:fs";
import vm from "node:vm";
test("the wizard validates steps, updates progress and exposes invalid hidden fields", () => {
    const callbacks = [], nodes = new Map();
    const element = () => {
        const node = {
            value: "",
            dataset: {},
            style: {},
            handlers: {},
            attributes: {},
            textContent: "",
            options: [],
            selectedIndex: 0,
            valid: true,
            addEventListener(name, callback) {
                this.handlers[name] = callback;
            },
            setAttribute(name, value) {
                this.attributes[name] = value;
            },
            removeAttribute(name) {
                delete this.attributes[name];
            },
            checkValidity() {
                return this.valid;
            },
            reportValidity() {
                this.reported = true;
            }
        };
        const classes = new Set();
        node.classList = { add: (...names) => names.forEach((name) => classes.add(name)), remove: (...names) => names.forEach((name) => classes.delete(name)), toggle(name, enabled) {
            if (enabled) classes.add(name);
            else classes.delete(name);
        } };
        return node;
    };
    const steps = [1, 2, 3].map((number) => ({ ...element(), dataset: { step: String(number) } }));
    const progress = [element(), element(), element()], lines = [element(), element()];
    const document = {
        addEventListener(name, callback) {
            callbacks.push(callback);
        },
        querySelectorAll(selector) {
            return selector === ".booking-step" ? steps : selector === ".step" ? progress : selector === ".step-line" ? lines : [];
        },
        getElementById(id) {
            if (["calendarGrid", "prevMonth", "nextMonth"].includes(id)) return null;
            if (!nodes.has(id)) nodes.set(id, element());
            return nodes.get(id);
        },
        querySelector() {
            return element();
        }
    };
    const warnings = [];
    vm.runInNewContext(fs.readFileSync(new URL("../../public/js/script.js", import.meta.url), "utf8"), {
        document,
        Date,
        Swal: { fire: (data) => warnings.push(data) }
    });
    const initialize = callbacks.find((callback) => callback.toString().includes("bookingForm"));
    initialize();
    const service = nodes.get("service_id"), date = nodes.get("booking_date"), time = nodes.get("start_time");
    const next = nodes.get("nextStep"), back = nodes.get("prevStep"), submit = nodes.get("submitBtn");
    assert.equal(steps[0].style.display, "block");
    next.handlers.click();
    assert.equal(warnings.length, 1);
    assert.equal(steps[0].style.display, "block");
    service.value = "1";
    service.selectedIndex = 0;
    service.options = [{ dataset: { name: "Consultation", price: "50", duration: "30" }, text: "Consultation" }];
    next.handlers.click();
    assert.equal(steps[1].style.display, "block");
    assert.equal(back.style.display, "inline-flex");
    date.value = "2026-11-03";
    time.value = "10:00";
    time.options = [{ textContent: "10:00 — 10:30" }];
    date.valid = false;
    next.handlers.click();
    assert.equal(date.reported, true);
    assert.equal(steps[1].style.display, "block");
    date.valid = true;
    next.handlers.click();
    assert.equal(steps[2].style.display, "block");
    assert.equal(submit.style.display, "inline-flex");
    assert.equal(progress[2].attributes["aria-current"], "step");
    assert.match(nodes.get("booking-step-status").textContent, /Step 3 of 3/);
    assert.equal(nodes.get("summaryService").textContent, "Consultation");
    nodes.get("bookingForm").handlers.invalid({ target: { closest: () => steps[0] } });
    assert.equal(steps[0].style.display, "block");
    assert.equal(submit.style.display, "none");
});
