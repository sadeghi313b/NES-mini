import { reactive, ref, shallowRef, computed, watch } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import { router, useForm, usePage, Link } from '@inertiajs/vue3';
import { useQuasar } from 'quasar';
import { route } from 'ziggy-js';
import { useSafeRoute } from '@/Composables/useSafeRoute';
import { debounce } from 'lodash';

export default {
    install(app) {
        // 1️⃣ Global Components
        app.component('MainLayout', MainLayout);
        app.component('Link', Link);

        // 2️⃣ Global Properties (توابع و instanceها)
        app.config.globalProperties.$router = router;
        app.config.globalProperties.$form = useForm;
        app.config.globalProperties.$page = usePage();
        app.config.globalProperties.$flash = usePage().props.flash;
        app.config.globalProperties.$q = useQuasar();
        app.config.globalProperties.$route = route;

        const { safeRoute } = useSafeRoute();
        app.config.globalProperties.$safeRoute = safeRoute;

        // 3️⃣ Utility functions
        app.config.globalProperties.$reactive = reactive;
        app.config.globalProperties.$ref = ref;
        app.config.globalProperties.$shallowRef = shallowRef;
        app.config.globalProperties.$computed = computed;
        app.config.globalProperties.$watch = watch;
        app.config.globalProperties.$debounce = debounce;
    }
};
