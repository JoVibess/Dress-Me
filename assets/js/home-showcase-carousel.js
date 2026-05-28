const wrap = (value, max) => {
    if (max === 0) {
        return 0;
    }

    return ((value % max) + max) % max;
};

const AUTO_ADVANCE_DELAY = 4000;

const getShortestDelta = (from, to, total) => {
    let delta = to - from;

    if (delta > total / 2) {
        delta -= total;
    } else if (delta < -total / 2) {
        delta += total;
    }

    return delta;
};

const getZIndex = (items, activeIndex) => (
    items.map((_, index) => {
        const distance = Math.abs(getShortestDelta(activeIndex, index, items.length));

        return Math.max(1, items.length - distance);
    })
);

const createShowcaseCarousel = (root) => {
    if (root.dataset.homeShowcaseReady === 'true') {
        return;
    }

    const media = root.querySelector('[data-home-showcase-carousel]');
    const items = Array.from(root.querySelectorAll('.home-showcase__carousel-item'));

    if (!media || items.length === 0) {
        return;
    }

    let progress = 1;
    let autoAdvanceId = null;
    const displayItem = (item, index, activeIndex) => {
        const zIndex = getZIndex(items, activeIndex)[index];
        const delta = getShortestDelta(activeIndex, index, items.length);

        item.style.setProperty('--zIndex', zIndex);
        item.style.setProperty('--active', delta / items.length);
    };

    const animate = () => {
        const activeIndex = Math.floor(wrap(progress, items.length));

        items.forEach((item, index) => displayItem(item, index, activeIndex));
    };

    const restartAutoAdvance = () => {
        if (autoAdvanceId !== null) {
            window.clearInterval(autoAdvanceId);
        }

        autoAdvanceId = window.setInterval(() => {
            progress += 1;
            animate();
        }, AUTO_ADVANCE_DELAY);
    };

    items.forEach((item, index) => {
        item.addEventListener('click', () => {
            progress = index;
            animate();
            restartAutoAdvance();
        });
    });

    root.dataset.homeShowcaseReady = 'true';
    animate();
    restartAutoAdvance();
};

const initShowcaseCarousels = () => {
    document.querySelectorAll('[data-home-showcase]').forEach(createShowcaseCarousel);
};

document.addEventListener('DOMContentLoaded', initShowcaseCarousels);
document.addEventListener('turbo:load', initShowcaseCarousels);
