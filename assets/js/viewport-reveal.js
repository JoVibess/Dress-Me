const REVEAL_SELECTOR = [
    '.home-showcase__content',
    '.home-video__frame',
    '.home-how-it-works__header',
    '.how-step-card',
    '.home-promises__header',
    '.promise-card',
    '.offers-hero',
    '.offer-card',
    '.feature-showcase',
    '.feature-card',
    '.contact-hero',
    '.contact-panel',
    '.checkout-hero',
    '.checkout-panel',
    '.checkout-summary',
    '.cart-page',
    '.cart-page__panel-card',
    '.auth-card',
].join(', ');

const initViewportReveal = () => {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    const elements = Array.from(document.querySelectorAll(REVEAL_SELECTOR))
        .filter((element) => !element.dataset.revealReady);

    if (elements.length === 0) {
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    }, {
        root: null,
        rootMargin: '0px 0px -10% 0px',
        threshold: 0.12,
    });

    elements.forEach((element, index) => {
        element.dataset.revealReady = 'true';
        element.style.setProperty('--reveal-delay', `${Math.min(index % 4, 3) * 80}ms`);
        element.classList.add('reveal-on-scroll');
        observer.observe(element);
    });
};

document.addEventListener('DOMContentLoaded', initViewportReveal);
document.addEventListener('turbo:load', initViewportReveal);
