export function initializeAnimations(doc = document, view = window) {
    if (doc.documentElement.hasAttribute("data-admin") || !view.IntersectionObserver) return;
    const motion = view.matchMedia("(prefers-reduced-motion: reduce)");
    if (motion.matches) return;
    const compact = view.matchMedia("(max-width: 767.98px)").matches;
    const selector = [
        "#app main > *",
        "#app main .container > *",
        "#app main section",
        "#app main [data-aos]",
        "#app main :is(.card-modern, .stat-card, .dashboard-card, .detail-card, .service-info-card, .notes-wrapper, .booking-intro, .booking-step, .booking-progress, .limit-alert, .profile-sidebar, .profile-overview > div, .profile-tool-link)",
        "#app footer",
        "[data-animate-section]"
    ].join(", ");
    const animations = new Set();
    const observer = new view.IntersectionObserver((entries) => {
        let rowTop = null;
        let rowIndex = 0;
        const visible = entries.filter((entry) => entry.isIntersecting).sort(
            (a, b) => a.boundingClientRect.top - b.boundingClientRect.top || a.boundingClientRect.left - b.boundingClientRect.left
        );
        visible.forEach((entry) => {
            observer.unobserve(entry.target);
            if (motion.matches || !entry.target.animate) return;
            if (rowTop === null || Math.abs(entry.boundingClientRect.top - rowTop) > 48) {
                rowTop = entry.boundingClientRect.top;
                rowIndex = 0;
            }
            const animation = entry.target.animate([
                { opacity: 0, transform: `translateY(${compact ? 24 : 40}px)` },
                { opacity: 1, transform: "translateY(0)" }
            ], {
                duration: compact ? 720 : 960,
                delay: Math.min(rowIndex++ * 90, 270),
                easing: "cubic-bezier(0.22, 1, 0.36, 1)",
                
                fill: "backwards"
            });
            animations.add(animation);
            animation.addEventListener("finish", () => animations.delete(animation), { once: true });
        });
    }, { threshold: 0, rootMargin: `0px 0px -${compact ? 24 : 48}px 0px` });
    doc.querySelectorAll(selector).forEach((element) => {
        if (element.matches("script, style, link, input, template") || element.querySelector(selector)) return;
        observer.observe(element);
    });
    motion.addEventListener("change", () => {
        if (!motion.matches) return;
        observer.disconnect();
        animations.forEach((animation) => animation.cancel());
        animations.clear();
    });
}
