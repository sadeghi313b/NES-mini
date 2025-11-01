import '../css/app.css';
import '../css/app.scss';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { initializeTheme } from './composables/useAppearance';

//ziggy in app.js
import { ZiggyVue } from 'ziggy-js';
import { Ziggy } from './ziggy';

//Quasar
import '@quasar/extras/material-icons/material-icons.css';
import { Dialog, Notify, Quasar } from 'quasar';
import 'quasar/src/css/index.sass';

const appName = import.meta.env.VITE_APP_NAME || 'NES';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(Quasar, {
                plugins: { Dialog, Notify },
                config: {},
            })
            .use(ZiggyVue, Ziggy)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
