import { formatPrice, getCartCount, getCartItemMeta, getCartTotal, readCart, writeCart } from './cart-store.js';

const buildCheckoutItem = (item) => {
    const template = document.querySelector('[data-checkout-item-template]');

    if (!template) {
        return document.createDocumentFragment();
    }

    const fragment = template.content.cloneNode(true);
    const root = fragment.querySelector('[data-checkout-item]');

    root.querySelector('[data-checkout-item-name]').textContent = item.name;
    root.querySelector('[data-checkout-item-meta]').textContent = getCartItemMeta(item);
    root.querySelector('[data-checkout-item-price]').textContent = formatPrice(item.price);
    root.querySelector('[data-checkout-item-quantity]').textContent = `x${item.quantity}`;
    root.querySelector('[data-checkout-item-subtotal]').textContent = formatPrice(Number(item.price) * item.quantity);
    root.querySelector('[data-checkout-remove]').dataset.checkoutRemove = item.id;

    return fragment;
};

const renderCheckout = () => {
    const items = readCart();
    const list = document.querySelector('[data-checkout-list]');
    const emptyState = document.querySelector('[data-checkout-empty]');
    const summary = document.querySelector('[data-checkout-summary]');
    const subtotal = document.querySelector('[data-checkout-subtotal]');
    const total = document.querySelector('[data-checkout-total]');
    const count = document.querySelector('[data-checkout-count]');
    const orderButton = document.querySelector('[data-checkout-order]');

    if (!list || !emptyState || !summary || !subtotal || !total || !count || !orderButton) {
        return;
    }

    const fragment = document.createDocumentFragment();

    items.forEach((item) => fragment.appendChild(buildCheckoutItem(item)));
    list.replaceChildren(fragment);

    const hasItems = items.length > 0;

    emptyState.hidden = hasItems;
    summary.hidden = !hasItems;
    subtotal.textContent = formatPrice(getCartTotal(items));
    total.textContent = formatPrice(getCartTotal(items));
    count.textContent = String(getCartCount(items));
    orderButton.disabled = !hasItems;
};

document.addEventListener('click', (event) => {
    const removeButton = event.target.closest('[data-checkout-remove]');
    const orderButton = event.target.closest('[data-checkout-order]');

    if (removeButton) {
        const items = readCart().filter((item) => item.id !== removeButton.dataset.checkoutRemove);

        writeCart(items);
        renderCheckout();
    }

    if (orderButton) {
        event.preventDefault();
    }
});

document.addEventListener('DOMContentLoaded', renderCheckout);
document.addEventListener('turbo:load', renderCheckout);
