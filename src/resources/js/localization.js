let dictionary;

export function translate(message, replacements = {}, translations) {
    if (typeof message !== 'string') return String(message ?? '');
    if (!translations) {
        if (!dictionary) {
            const payload = typeof document === 'undefined' ? null : document.getElementById('site-translations');
            try {
                dictionary = payload ? JSON.parse(payload.textContent) : {};
            } catch {
                dictionary = {};
            }
        }
        translations = dictionary;
    }

    let text = Object.hasOwn(translations, message) && typeof translations[message] === 'string' ? translations[message] : message;

    for (const [key, value] of Object.entries(replacements)) {
        text = text.replaceAll(`:${key}`, String(value));
    }

    return text;
}

if (typeof window !== 'undefined') {
    window.bookingTranslate = translate;
    document.querySelectorAll('[data-language-select]').forEach(select => {
        select.addEventListener('change', () => select.form.requestSubmit());
    });
}
