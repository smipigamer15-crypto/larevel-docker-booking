import test from 'node:test';
import assert from 'node:assert/strict';
import { initializeAnimations } from '../../resources/js/animations.js';

function fixture({ admin = false, reducedMotion = false, supported = true, compact = false } = {}) {
    const observed = [], unobserved = [], animations = [];
    let callback, motionChanged, disconnected = false;
    const element = {
        style: {},
        matches: () => false,
        querySelector: () => null,
        animate(frames, options) {
            const animation = { frames, options, cancelled: false, addEventListener() {}, cancel() { this.cancelled = true; } };
            animations.push(animation);
            return animation;
        },
    };
    const elements = [element];
    const motion = { matches: reducedMotion, addEventListener(name, fn) { motionChanged = fn; } };
    const view = {
        matchMedia: query => query.includes('max-width') ? { matches: compact } : motion,
        IntersectionObserver: supported ? class {
            constructor(fn) { callback = fn; }
            observe(node) { observed.push(node); }
            unobserve(node) { unobserved.push(node); }
            disconnect() { disconnected = true; }
        } : undefined,
    };
    const doc = {
        documentElement: { hasAttribute: name => admin && name === 'data-admin' },
        querySelectorAll: () => elements,
    };
    return {
        element, elements, observed, unobserved, animations, motion,
        start: () => initializeAnimations(doc, view),
        intersect: (isIntersecting = true) => callback([{ target: element, isIntersecting, boundingClientRect: { top: 100, left: 0 } }]),
        intersectMany: entries => callback(entries),
        changeMotion() { motion.matches = true; motionChanged(); },
        get disconnected() { return disconnected; },
    };
}

test('sections animate on entry without hiding resting content or retaining transforms', () => {
    const page = fixture();
    page.start();
    assert.deepEqual(page.observed, [page.element]);
    assert.deepEqual(page.element.style, {});
    page.intersect(false);
    assert.equal(page.animations.length, 0);
    page.intersect();
    assert.deepEqual(page.unobserved, [page.element]);
    assert.equal(page.animations.length, 1);
    assert.equal(page.animations[0].frames[0].opacity, 0);
    assert.equal(page.animations[0].frames.at(-1).opacity, 1);
    assert.equal(page.animations[0].options.fill, 'backwards');
    assert.deepEqual(page.element.style, {});
});

test('admin pages never register section animations', () => {
    const page = fixture({ admin: true });
    page.start();
    assert.deepEqual(page.observed, []);
});

test('reduced motion and unsupported observers leave all content visible', () => {
    for (const options of [{ reducedMotion: true }, { supported: false }]) {
        const page = fixture(options);
        page.start();
        assert.deepEqual(page.observed, []);
        assert.deepEqual(page.element.style, {});
    }
});

test('nested section wrappers are skipped so cards do not animate twice', () => {
    const page = fixture();
    page.elements.unshift({ matches: () => false, querySelector: () => page.element });
    page.elements.push({ matches: () => true });
    page.start();
    assert.deepEqual(page.observed, [page.element]);
});

test('changing to reduced motion cancels active animations and disconnects observation', () => {
    const page = fixture();
    page.start();
    page.intersect();
    page.changeMotion();
    assert.equal(page.disconnected, true);
    assert.equal(page.animations[0].cancelled, true);
    page.intersect();
    assert.equal(page.animations.length, 1);
});

test('browsers without the Web Animations API still display sections', () => {
    const page = fixture();
    delete page.element.animate;
    page.start();
    page.intersect();
    assert.deepEqual(page.animations, []);
    assert.deepEqual(page.element.style, {});
});

test('cards stagger left to right within each row with a bounded delay', () => {
    const page = fixture();
    page.start();
    const entries = [500, 0, 300, 100, 200, 400].map(left => ({
        target: page.element, isIntersecting: true, boundingClientRect: { top: 100, left },
    }));
    entries.push({ target: page.element, isIntersecting: true, boundingClientRect: { top: 400, left: 0 } });
    page.intersectMany(entries);
    assert.deepEqual(page.animations.map(animation => animation.options.delay), [0, 90, 180, 270, 270, 270, 0]);
});

test('phone reveals travel less and finish sooner than desktop reveals', () => {
    const desktop = fixture(), phone = fixture({ compact: true });
    desktop.start();
    phone.start();
    desktop.intersect();
    phone.intersect();
    assert.equal(desktop.animations[0].frames[0].transform, 'translateY(40px)');
    assert.equal(phone.animations[0].frames[0].transform, 'translateY(24px)');
    assert.ok(phone.animations[0].options.duration < desktop.animations[0].options.duration);
});
