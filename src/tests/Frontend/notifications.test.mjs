import test from "node:test";
import assert from "node:assert/strict";
import fs from "node:fs";
import vm from "node:vm";
import { parseAst } from "rollup/parseAst";
import { playNotificationChime, toastDuration, toastTone } from "../../resources/js/notifications.js";
test("notifications normalize unknown tones and allow more reading time for errors", () => {
    assert.equal(toastTone("<script>"), "info");
    assert.equal(toastDuration("success"), 6e3);
    assert.equal(toastDuration("error"), 8e3);
});
test("notification audio respects browser permission and uses a short, quiet chime", () => {
    assert.equal(playNotificationChime(null), false);
    assert.equal(playNotificationChime({ state: "suspended" }), false);
    const notes = [], volumes = [];
    const context = {
        state: "running",
        currentTime: 10,
        destination: {},
        createGain: () => ({ connect() {
        }, gain: {
            setValueAtTime(value) {
                volumes.push(value);
            },
            linearRampToValueAtTime(value) {
                volumes.push(value);
            },
            exponentialRampToValueAtTime(value) {
                volumes.push(value);
            }
        } }),
        createOscillator() {
            const note = { frequency: {}, connect() {
            }, start(time) {
                this.started = time;
            }, stop(time) {
                this.stopped = time;
            } };
            notes.push(note);
            return note;
        }
    };
    assert.equal(playNotificationChime(context, "success"), true);
    assert.deepEqual(notes.map((note) => note.frequency.value), [660, 880]);
    assert.ok(volumes.every((value) => value <= 0.035));
    assert.ok(notes.every((note) => note.stopped - note.started < 0.2));
});
test("feature category filters change only the feature cards", () => {
    const source = fs.readFileSync(new URL("../../resources/js/pages.js", import.meta.url), "utf8");
    const pageBlock = parseAst(source).body.find(
        (node) => node.type === "IfStatement" && node.test.arguments?.[0]?.value === '[data-page-script="features/index"]'
    );
    assert.ok(pageBlock);
    const code = source.slice(pageBlock.start, pageBlock.end);
    const categories = ["all", "features", "integrations", "settings", "extra"];
    const buttons = categories.map((filter) => ({ dataset: { filter }, classList: { toggle() {
    } }, setAttribute() {
    }, addEventListener(name, callback) {
        this.click = callback;
    } }));
    const items = categories.slice(1).map((category) => ({ dataset: { category }, style: {} }));
    let initialize;
    const document = {
        querySelector: () => ({}),
        querySelectorAll(selector) {
            if (selector === ".filter-btn") return buttons;
            if (selector === ".feature-item") return items;
            assert.fail("Filter must not touch sections or footer: " + selector);
        },
        addEventListener(name, callback) {
            initialize = callback;
        }
    };
    vm.runInNewContext(code, { document, URLSearchParams, location: { search: "?category=integrations" } });
    initialize();
    assert.deepEqual(items.map((item) => item.style.display), ["none", "flex", "none", "none"]);
    buttons[3].click();
    assert.deepEqual(items.map((item) => item.style.display), ["none", "none", "flex", "none"]);
    buttons[0].click();
    assert.ok(items.every((item) => item.style.display === "flex"));
});
