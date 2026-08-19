import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import { initializeTheme } from '@/composables/useAppearance';
import { initializeDirection } from '@/composables/useDirection';
import { i18n } from '@/i18n';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import OfferLayout from '@/layouts/OfferLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';

const appName = import.meta.env.VITE_APP_NAME || 'Alfia';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.use(plugin);
        app.use(i18n);
        i18n.global.locale.value = props.initialPage.props.locale as 'en' | 'fr' | 'ar';
        initializeDirection();

        if (el) {
            app.mount(el);
        }

        return app;
    },
    layout: (name) => {
        switch (true) {
            case name === 'Welcome':
                return null;
            case name === 'offers/Reserve' || name === 'offers/ReservationSuccess':
                return OfferLayout;
            case name.startsWith('auth/'):
                return AuthLayout;
            case name.startsWith('settings/'):
                return [AppLayout, SettingsLayout];
            default:
                return AppLayout;
        }
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();

// This will listen for flash toast data from the server...
initializeFlashToast();
