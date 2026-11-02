import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { parseAst } from 'rollup/parseAst';
import { translate } from '../../resources/js/localization.js';
import { parseBookingRequest } from '../../resources/js/features.js';

const locales = ['en', 'uk', 'ru', 'pl', 'de'];
const catalogs = Object.fromEntries(locales.map(locale => [locale, JSON.parse(fs.readFileSync(new URL(`../../lang/${locale}.json`, import.meta.url), 'utf8'))]));

test('all five language catalogs have complete keys and preserve interpolation tokens', () => {
    const keys = Object.keys(catalogs.en).sort();
    const tokens = text => (text.match(/:[a-z_]+|\{[a-z_]+\}/gi) || []).sort();
    for (const locale of locales) {
        assert.deepEqual(Object.keys(catalogs[locale]).sort(), keys, locale);
        for (const key of keys) {
            assert.ok(catalogs[locale][key].trim(), `${locale}: ${key}`);
            assert.deepEqual(tokens(catalogs[locale][key]), tokens(key), `${locale}: ${key}`);
        }
    }
});

test('booking labels and interpolated step messages use the selected language', () => {
    assert.equal(translate('My Bookings', {}, catalogs.uk), 'Мої бронювання');
    assert.equal(translate('My Bookings', {}, catalogs.ru), 'Мои бронирования');
    assert.equal(translate('My Bookings', {}, catalogs.pl), 'Moje rezerwacje');
    assert.equal(translate('My Bookings', {}, catalogs.de), 'Meine Buchungen');
    assert.equal(translate('Step :step of 3 · :label', {step: 2, label: 'Datum und Uhrzeit wählen'}, catalogs.de), 'Schritt 2 von 3 · Datum und Uhrzeit wählen');
});

test('public templates and JavaScript only reference translation keys present in the catalogs', () => {
    const viewRoot = fileURLToPath(new URL('../../resources/views/', import.meta.url));
    const walk = directory => fs.readdirSync(directory, {withFileTypes: true}).flatMap(entry =>
        entry.isDirectory() ? ['admin', 'emails', 'vendor'].includes(entry.name) ? [] : walk(path.join(directory, entry.name)) : [path.join(directory, entry.name)]
    );
    const check = (key, file) => assert.ok(Object.hasOwn(catalogs.en, key), `${file}: missing ${key}`);
    for (const file of walk(viewRoot)) {
        if (!file.endsWith('.blade.php') || /report|pdf/i.test(file) || file === path.join(viewRoot, 'layouts/admin.blade.php')) continue;
        const source = fs.readFileSync(file, 'utf8');
        for (const match of source.matchAll(/__\(\s*'((?:\\.|[^'\\])*)'/g)) check(match[1].replaceAll("\\'", "'").replaceAll('\\\\', '\\'), file);
        for (const match of source.matchAll(/\b(?:eyebrow|accent|description)="([^"{}]+)"/g)) check(match[1], file);
    }
    const files = ['auth', 'profile', 'pages', 'features', 'notifications'].map(name => new URL(`../../resources/js/${name}.js`, import.meta.url));
    files.push(new URL('../../public/js/script.js', import.meta.url));
    for (const file of files) {
        const visit = node => {
            if (!node || typeof node !== 'object') return;
            if (node.type === 'CallExpression' && node.callee?.name === 'localize' && node.arguments[0]?.type === 'Literal') check(node.arguments[0].value, file);
            for (const child of Object.values(node)) {
                if (Array.isArray(child)) child.forEach(visit);
                else if (child && typeof child === 'object') visit(child);
            }
        };
        visit(parseAst(fs.readFileSync(file, 'utf8')));
    }
});

test('unknown text falls back without translating user content or prototype properties', () => {
    assert.equal(translate('Customer supplied service name', {}, catalogs.uk), 'Customer supplied service name');
    assert.equal(translate('toString', {}, catalogs.uk), 'toString');
    assert.equal(translate(null, {}, catalogs.uk), '');
});

test('voice drafts recognize today and tomorrow in the supported languages', () => {
    const now = new Date(2026, 10, 2, 12, 0);
    const services = [{id: 1, title: 'Consultation'}];
    for (const word of ['today', 'сьогодні', 'сегодня', 'dzisiaj', 'dziś', 'heute']) {
        assert.equal(parseBookingRequest(`Consultation ${word} 14:30`, services, now).date, '2026-11-02');
    }
    for (const word of ['tomorrow', 'завтра', 'jutro', 'morgen']) {
        assert.equal(parseBookingRequest(`Consultation ${word} 14:30`, services, now).date, '2026-11-03');
    }
});
