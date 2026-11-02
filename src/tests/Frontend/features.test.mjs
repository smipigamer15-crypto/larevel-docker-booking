import test from "node:test";
import assert from "node:assert/strict";
import fs from "node:fs";
import vm from "node:vm";
import { buildCampaignUrl, parseBookingRequest } from "../../resources/js/features.js";
const services = [{ id: 7, title: "Consultation" }, { id: 8, title: "Deep Consultation" }];
const now = new Date(2026, 10, 2, 23, 0);
for (const [text, expected] of [
    ["Consultation tomorrow at 10:30", { serviceId: "7", date: "2026-11-03", time: "10:30" }],
    ["Deep Consultation сьогодні о 14:45", { serviceId: "8", date: "2026-11-02", time: "14:45" }],
    ["Consultation 2026-12-31 at 9 am", { serviceId: "7", date: "2026-12-31", time: "09:00" }],
    ["Consultation at 12:00am", { serviceId: "7", date: "", time: "00:00" }],
    ["Consultation at 12 pm", { serviceId: "7", date: "", time: "12:00" }],
    ["Consultation 2026-02-30 at 99:99", { serviceId: "7", date: "", time: "" }],
    ["Nothing recognizable", { serviceId: "", date: "", time: "" }]
]) {
    test("voice draft: " + text, () => assert.deepEqual(parseBookingRequest(text, services, now), expected));
}
test("campaign URLs preserve booking parameters and encode tracking labels", () => {
    const result = new URL(buildCampaignUrl("https://example.test/services/7?date=2026-11-03", {
        source: " Instagram ",
        medium: "paid_social",
        campaign: "Spring & summer / ї"
    }));
    assert.equal(result.searchParams.get("date"), "2026-11-03");
    assert.equal(result.searchParams.get("utm_source"), "Instagram");
    assert.equal(result.searchParams.get("utm_campaign"), "Spring & summer / ї");
    assert.throws(() => buildCampaignUrl("javascript:alert(1)", { source: "a", medium: "b", campaign: "c" }));
});
test("the service worker caches only the generic offline screen", async () => {
    const handlers = {}, stored = [], deleted = [];
    let response;
    const self = {
        addEventListener(name, handler) {
            handlers[name] = handler;
        },
        skipWaiting() {
        },
        clients: { claim: async () => {
        } }
    };
    const caches = {
        open: async () => ({ add: async (url) => stored.push(url) }),
        keys: async () => ["booking-offline-v0", "another-application"],
        delete: async (key) => deleted.push(key),
        match: async (url) => ({ offline: url === "/offline.html" })
    };
    vm.runInNewContext(fs.readFileSync(new URL("../../public/sw.js", import.meta.url), "utf8"), {
        self,
        caches,
        fetch: async () => {
            throw new Error("offline");
        }
    });
    let pending;
    handlers.install({ waitUntil(promise) {
        pending = promise;
    } });
    await pending;
    handlers.activate({ waitUntil(promise) {
        pending = promise;
    } });
    await pending;
    handlers.fetch({ request: { method: "GET", mode: "navigate", url: "https://example.test/profile" }, respondWith(promise) {
        response = promise;
    } });
    assert.equal((await response).offline, true);
    handlers.fetch({ request: { method: "POST", mode: "cors" }, respondWith() {
        assert.fail("POST must not be intercepted");
    } });
    assert.deepEqual(stored, ["/offline.html"]);
    assert.deepEqual(deleted, ["booking-offline-v0"]);
});
