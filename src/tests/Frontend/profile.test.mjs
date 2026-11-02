import test from "node:test";
import assert from "node:assert/strict";
import { enhanceProfileNavigation, resolveProfileSection } from "../../resources/js/profile.js";
const sections = ["account", "billing", "workspace", "security"];
test("profile opens the correct form after server errors and preserves direct links", () => {
    assert.equal(resolveProfileSection(sections, "security", "#account", "billing"), "security");
    assert.equal(resolveProfileSection(sections, "", "#workspace", "billing"), "workspace");
    assert.equal(resolveProfileSection(sections, "", "", "billing"), "billing");
    assert.equal(resolveProfileSection(sections, "", "#unknown", "unknown"), "account");
});
test("profile tabs support clicking, keyboard navigation and form return state", () => {
    const makeElement = (dataset) => ({
        dataset,
        attributes: {},
        classes: new Set(),
        handlers: {},
        hidden: false,
        classList: { toggle(name, enabled) {
            if (enabled) this.owner.classes.add(name);
            else this.owner.classes.delete(name);
        } },
        setAttribute(name, value) {
            this.attributes[name] = value;
        },
        addEventListener(name, callback) {
            this.handlers[name] = callback;
        },
        focus() {
            this.focused = true;
        }
    });
    const tabs = sections.map((section) => makeElement({ profileTab: section }));
    const panels = sections.map((section) => makeElement({ profilePanel: section }));
    tabs.forEach((tab) => {
        tab.classList.owner = tab;
    });
    panels.forEach((panel) => {
        panel.form = makeElement({});
        panel.querySelectorAll = () => [panel.form];
    });
    const nav = makeElement({});
    const workspace = {
        dataset: { profileInitial: "" },
        querySelectorAll(selector) {
            return selector === "[data-profile-tab]" ? tabs : selector === "[data-profile-panel]" ? panels : [];
        },
        querySelector() {
            return nav;
        }
    };
    const stored = new Map(), events = {};
    const globals = ["sessionStorage", "history", "location", "window"].map((key) => [key, Object.getOwnPropertyDescriptor(globalThis, key)]);
    Object.defineProperty(globalThis, "sessionStorage", { configurable: true, value: { getItem: (key) => stored.get(key), setItem: (key, value) => stored.set(key, value) } });
    Object.defineProperty(globalThis, "history", { configurable: true, value: { replaceState(a, b, hash) {
        globalThis.location.hash = hash;
    } } });
    Object.defineProperty(globalThis, "location", { configurable: true, value: { hash: "" } });
    Object.defineProperty(globalThis, "window", { configurable: true, value: { addEventListener(name, callback) {
        events[name] = callback;
    } } });
    try {
        enhanceProfileNavigation(workspace);
        assert.equal(nav.attributes.role, "tablist");
        assert.deepEqual(panels.map((panel) => panel.hidden), [false, true, true, true]);
        tabs[1].handlers.click({ preventDefault() {
        } });
        assert.deepEqual(panels.map((panel) => panel.hidden), [true, false, true, true]);
        assert.equal(tabs[1].attributes["aria-selected"], "true");
        tabs[1].handlers.keydown({ key: "ArrowRight", preventDefault() {
        } });
        assert.equal(panels[2].hidden, false);
        assert.equal(tabs[2].focused, true);
        tabs[2].handlers.keydown({ key: "End", preventDefault() {
        } });
        assert.equal(panels[3].hidden, false);
        panels[3].form.handlers.submit();
        assert.equal(stored.get("profile-section"), "security");
        globalThis.location.hash = "#billing";
        events.hashchange();
        assert.equal(panels[1].hidden, false);
    } finally {
        for (const [key, descriptor] of globals) {
            if (descriptor) Object.defineProperty(globalThis, key, descriptor);
            else delete globalThis[key];
        }
    }
});
