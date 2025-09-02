import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import { createI18n } from 'vue-i18n';
import maska from 'maska';

const appName = document.title || 'IceburgCRM';
const defaultLocale = document.documentElement.lang || 'en';

async function fetchLocaleMessages(locale) {
    //const cached = localStorage.getItem(`locale-${locale}`);
    //if (cached) return JSON.parse(cached);

    const res = await fetch(`/lang/${locale}`);
    if (!res.ok) throw new Error(`Failed to load locale: ${locale}`);
    const messages = await res.json();
    localStorage.setItem(`locale-${locale}`, JSON.stringify(messages));
    return messages;
}

(async () => {
    try {
        // 1️⃣ Load translations first
        const messages = await fetchLocaleMessages(defaultLocale);
        console.log('Loaded messages:', messages); // confirm

        // 2️⃣ Create i18n instance with legacy mode
        const i18n = createI18n({
            legacy: true, // <-- changed to true for global $t
            locale: defaultLocale,
            messages: { [defaultLocale]: messages },
        });

        // 3️⃣ Create Inertia app
        createInertiaApp({
            title: (title) => `${title} - ${appName}`,
            resolve: (name) => require(`./Pages/${name}.vue`),
            setup({ el, app, props, plugin }) {
                const vueApp = createApp({ render: () => h(app, props) });
                vueApp
                    .use(plugin)
                    .use(i18n)
                    .use(maska)
                    .mixin({ methods: { route } })
                    .mount(el);

                return vueApp;
            }
        });

        InertiaProgress.init({ color: '#4B5563' });
    } catch (err) {
        console.error('Failed to load translations', err);
    }
})();
