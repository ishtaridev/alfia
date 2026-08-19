import { createI18n } from 'vue-i18n';
import ar from '@/i18n/locales/ar.json';
import en from '@/i18n/locales/en.json';
import fr from '@/i18n/locales/fr.json';

export const i18n = createI18n({
    legacy: false,
    locale: 'ar',
    fallbackLocale: 'en',
    messages: { en, fr, ar },
});
