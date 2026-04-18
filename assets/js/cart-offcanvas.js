import { Offcanvas } from 'bootstrap';
import { CART_UPDATED_EVENT, formatPrice, getCartCount, getCartItemMeta, getCartTotal, readCart, writeCart } from './cart-store.js';

const buildCartItem = (item) => {
    const template = document.querySelector('[data-cart-item-template]');

    if (!template) {
        return document.createDocumentFragment();
    }

    const fragment = template.content.cloneNode(true);
    const root = fragment.querySelector('[data-cart-item]');

    root.querySelector('[data-cart-item-name]').textContent = item.name;
    root.querySelector('[data-cart-item-meta]').textContent = getCartItemMeta(item);
    root.querySelector('[data-cart-item-price]').textContent = formatPrice(item.price);
    root.querySelector('[data-cart-item-quantity]').textContent = `x${item.quantity}`;
    root.querySelector('[data-cart-remove]').dataset.cartRemove = item.id;

    return fragment;
};

const renderCart = () => {
    const items = readCart();
    const list = document.querySelector('[data-cart-list]');
    const emptyState = document.querySelector('[data-cart-empty]');
    const count = document.querySelector('[data-cart-count]');
    const total = document.querySelector('[data-cart-total]');
    const checkout = document.querySelector('[data-cart-checkout]');

    if (count) {
        count.textContent = String(getCartCount(items));
    }

    if (!list || !emptyState || !total || !checkout) {
        return;
    }

    const fragment = document.createDocumentFragment();

    items.forEach((item) => fragment.appendChild(buildCartItem(item)));
    list.replaceChildren(fragment);

    emptyState.hidden = items.length > 0;
    total.textContent = formatPrice(getCartTotal(items));
    checkout.classList.toggle('disabled', items.length === 0);
    checkout.setAttribute('aria-disabled', items.length === 0 ? 'true' : 'false');
};

const addToCart = (button) => {
    const offer = {
        id: button.dataset.offerId,
        name: button.dataset.offerName,
        price: button.dataset.offerPrice,
        credits: button.dataset.offerCredits,
        duration: button.dataset.offerDuration,
        quantity: 1,
    };

    const items = readCart();
    const existingItem = items.find((item) => item.id === offer.id);

    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        items.push(offer);
    }

    writeCart(items);
    renderCart();

    const offcanvasElement = document.querySelector('#cartOffcanvas');

    if (offcanvasElement) {
        Offcanvas.getOrCreateInstance(offcanvasElement).show();
    }
};

document.addEventListener('click', (event) => {
    const addButton = event.target.closest('[data-cart-add]');
    const removeButton = event.target.closest('[data-cart-remove]');

    if (addButton) {
        event.preventDefault();
        addToCart(addButton);
    }

    if (removeButton) {
        const items = readCart().filter((item) => item.id !== removeButton.dataset.cartRemove);

        writeCart(items);
        renderCart();
    }

    if (event.target.closest('[data-cart-checkout][aria-disabled="true"]')) {
        event.preventDefault();
    }
});

document.addEventListener('DOMContentLoaded', renderCart);
document.addEventListener('turbo:load', renderCart);
document.addEventListener(CART_UPDATED_EVENT, renderCart);
