export const CART_UPDATED_EVENT = 'dressme:cart-updated';

const STORAGE_KEY = 'dressme_cart_offers';

const getLocale = () => (document.documentElement.lang || 'en').toLowerCase();

const translate = (messages) => messages[getLocale().startsWith('fr') ? 'fr' : 'en'];

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
        return translate({
            fr: 'Gratuit',
            en: 'Free',
        });
    }

    return new Intl.NumberFormat(getLocale().startsWith('fr') ? 'fr-FR' : 'en-GB', {
        style: 'currency',
        currency: 'EUR',
    }).format(amount);
};

export const getCartTotal = (items) => items.reduce((total, item) => total + Number(item.price) * item.quantity, 0);

export const getCartCount = (items) => items.reduce((quantity, item) => quantity + item.quantity, 0);

export const getCartItemMeta = (item) => item.duration
    ? `${item.credits} credits / ${item.duration} ${translate({
        fr: 'mois',
        en: Number(item.duration) > 1 ? 'months' : 'month',
    })}`
    : `${item.credits} credits`;
