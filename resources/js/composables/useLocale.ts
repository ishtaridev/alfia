import { usePage } from '@inertiajs/vue3';
import { computed, nextTick } from 'vue';
import { useI18n } from 'vue-i18n';

export function useLocale() {
    const { t, locale } = useI18n();
    const page = usePage();

    const direction = computed(() => (locale.value === 'ar' ? 'rtl' : 'ltr'));

    async function setLocale(newLocale: string) {
        const csrfToken =
            document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute('content') ?? '';

        const response = await fetch('/settings/locale', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
            },
            body: JSON.stringify({ locale: newLocale }),
        });

        if (response.ok) {
            // Suppress CSS transitions during locale/direction switch
            const html = document.documentElement;
            html.style.setProperty('transition', 'none', 'important');

            locale.value = newLocale as 'en' | 'fr' | 'ar';

            const dir = newLocale === 'ar' ? 'rtl' : 'ltr';
            html.dir = dir;
            html.lang = newLocale;

            await nextTick();

            html.style.removeProperty('transition');
        }
    }

    return {
        t,
        locale,
        direction,
        setLocale,
    };
}
