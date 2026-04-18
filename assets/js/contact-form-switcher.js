const ACTIVE_CLASS = 'is-active';

const setActiveButton = (switcher, selectedForm) => {
    switcher.querySelectorAll('[data-contact-form-target]').forEach((button) => {
        const isActive = button.dataset.contactFormTarget === selectedForm;

        button.classList.toggle(ACTIVE_CLASS, isActive);
        button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });
};

const showContactForm = (selectedForm) => {
    document.querySelectorAll('[data-contact-form]').forEach((form) => {
        form.hidden = form.dataset.contactForm !== selectedForm;
    });
};

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-contact-form-target]');

    if (!button) {
        return;
    }

    const switcher = button.closest('[data-contact-switcher]');

    if (!switcher) {
        return;
    }

    const selectedForm = button.dataset.contactFormTarget;

    setActiveButton(switcher, selectedForm);
    showContactForm(selectedForm);
});
