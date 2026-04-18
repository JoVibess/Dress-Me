const ACTIVE_CLASS = 'is-active';

const setActiveButton = (switcher, selectedPeriod) => {
    switcher.querySelectorAll('[data-billing-filter]').forEach((button) => {
        const isActive = button.dataset.billingFilter === selectedPeriod;

        button.classList.toggle(ACTIVE_CLASS, isActive);
        button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });
};

const showPeriodOffers = (selectedPeriod) => {
    document.querySelectorAll('[data-billing-period]').forEach((offer) => {
        offer.hidden = offer.dataset.billingPeriod !== selectedPeriod;
    });
};

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-billing-filter]');

    if (!button) {
        return;
    }

    const switcher = button.closest('[data-subscription-switcher]');

    if (!switcher) {
        return;
    }

    const selectedPeriod = button.dataset.billingFilter;

    setActiveButton(switcher, selectedPeriod);
    showPeriodOffers(selectedPeriod);
});
