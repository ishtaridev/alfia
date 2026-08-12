import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

export function useLocale() {
    const { t, locale } = useI18n();
    const page = usePage();

    const direction = computed(() => page.props.direction as 'ltr' | 'rtl');

    async function setLocale(newLocale: string) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

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
            locale.value = newLocale as 'en' | 'fr' | 'ar';

            const dir = newLocale === 'ar' ? 'rtl' : 'ltr';
            document.documentElement.dir = dir;
            document.documentElement.lang = newLocale;
        }
    }

    return {
        t,
        locale,
        direction,
        setLocale,
    };
}
