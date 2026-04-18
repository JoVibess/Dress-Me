export const CART_UPDATED_EVENT = 'dressme:cart-updated';

const STORAGE_KEY = 'dressme_cart_offers';

export const readCart = () => {
    try {
        return JSON.parse(localStorage.getItem(STORAGE_KEY) ?? '[]');
    } catch {
        return [];
    }
};

export const writeCart = (items) => {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
    document.dispatchEvent(new CustomEvent(CART_UPDATED_EVENT));
};

export const formatPrice = (price) => {
    const amount = Number(price);

    if (amount === 0) {
        return 'Free';
    }

    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
    }).format(amount);
};

export const getCartTotal = (items) => items.reduce((total, item) => total + Number(item.price) * item.quantity, 0);

export const getCartCount = (items) => items.reduce((quantity, item) => quantity + item.quantity, 0);

export const getCartItemMeta = (item) => item.duration
    ? `${item.credits} credits / ${item.duration} month${Number(item.duration) > 1 ? 's' : ''}`
    : `${item.credits} credits`;
