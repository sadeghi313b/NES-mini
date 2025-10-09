// [imports]
import MainLayout from '@/Layouts/MainLayout.vue';

import { reactive, ref, shallowRef, computed, watch } from 'vue';
import { router, useForm, usePage, Link } from '@inertiajs/vue3';
import { Quasar, useQuasar, Dialog } from 'quasar';
import { route } from 'ziggy-js';

import { useSafeRoute } from '@/Composables/useSafeRoute';

const page = usePage();
const { flash } = usePage().props;
const { safeRoute } = useSafeRoute();
const $q = useQuasar(); 
// [/]