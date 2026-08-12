import { i18n } from '@/i18n';

export function initializeDirection() {
    const locale = i18n.global.locale.value;
    const dir = locale === 'ar' ? 'rtl' : 'ltr';
    document.documentElement.dir = dir;
    document.documentElement.lang = locale;
}
