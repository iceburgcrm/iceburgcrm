import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import { createI18n } from 'vue-i18n';
import maska from 'maska';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'IceburgCRM';
const defaultLocale = document.documentElement.getAttribute('lang') || 'en';

const i18n = createI18n({
    legacy: false,
    locale: defaultLocale,
    messages: {}, // Start with empty messages
});

console.log('i18n initialized:', i18n);

// Function to load translations (with caching)
const loadLocaleMessages = async (locale) => {
    const resolvedLocale = locale || 'en';

    // Try loading from cache
    const cachedMessages = localStorage.getItem(`locale-${resolvedLocale}`);
    if (cachedMessages) {
        i18n.global.setLocaleMessage(resolvedLocale, JSON.parse(cachedMessages));
        return;
    }

    // Fetch translations from server if not cached
    try {
        const response = await fetch(`/lang/${resolvedLocale}`);
        if (!response.ok) {
            throw new Error(`Failed to load locale messages for "${resolvedLocale}". Status: ${response.status}`);
        }

        const messages = await response.json();
        i18n.global.setLocaleMessage(resolvedLocale, messages);
        i18n.global.locale.value = resolvedLocale;

        // Cache the messages for future use
        localStorage.setItem(`locale-${resolvedLocale}`, JSON.stringify(messages));
    } catch (error) {
        console.error('Error loading locale messages:', error);
    }
};

// Preload default locale during app initialization
loadLocaleMessages(defaultLocale);

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => require(`./Pages/${name}.vue`),
    setup({ el, app, props, plugin }) {
        const vueApp = createApp({ render: () => h(app, props) });

        vueApp
            .use(plugin)
            .use(i18n)
            .directive('maska', maska)
            .mixin({ methods: { route } })
            .mount(el);

        return vueApp;
    },
});

InertiaProgress.init({ color: '#4B5563' });
