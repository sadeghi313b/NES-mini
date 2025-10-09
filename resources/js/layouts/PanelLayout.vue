<!-- ------------------- resources\js\layouts\MainLayout.vue ------------------ -->
<template>
    <!-- "lHh LpR fFf" -->
    <q-layout view="lHh LpR fFf">
        <!-- ──────────────────────
        ├   Header 
        └─────────────────────── -->
        <q-header elevated class="bg-primary text-white" height-hint="98">
            <q-toolbar>
                <q-btn dense flat round icon="menu" @click.prevent="leftDrawerOpen = !leftDrawerOpen" />

                <q-toolbar-title>
                    <q-avatar>
                        <img src="../../../public/logo/logo.jpg" />
                    </q-avatar>
                    <!-- NES -->
                </q-toolbar-title>
                <slot name="header" />

                <!-- Dark Mode Toggle -->
                <q-btn dense flat round :icon="darkMode ? 'brightness_5' : 'brightness_4'" @click.prevent="toggleDarkMode" type="button" />

                <q-btn dense flat round icon="menu" @click.prevent="rightDrawerOpen = !rightDrawerOpen" />
            </q-toolbar>
        </q-header>

        <!-- ──────────────────────
        ├   Left 
        └─────────────────────── -->
        <q-drawer show-if-above v-model="leftDrawerOpen" side="left" bordered>
            <div v-if="$page.props.auth?.user?.full_name" class="q-mx-md q-mt-md">
                {{ $page.props.auth.user.full_name }}
            </div>
            <q-breadcrumbs class="text-caption text-secondary q-ml-md" active-color="secondary" gutter="none">
                <template v-slot:separator>
                    <q-icon size="1.5em" name="chevron_right" color="secondary" />
                </template>
                <q-breadcrumbs-el
                    v-for="(part, index) in page.props.theRoute.parts"
                    :key="index"
                    :label="part"
                    :href="routeExists(index)"
                    :disable="!routeExists(index)"
                >
                </q-breadcrumbs-el>
                <q-breadcrumbs-el label="">
                    <span v-if="['edit', 'show'].includes(routeMethod)" class="text-caption"> row{{ page.props.order?.id }} </span>
                    <span v-else-if="routeMethod == 'create'">: new</span>
                </q-breadcrumbs-el>
            </q-breadcrumbs>
            <slot name="left-drawer" />
        </q-drawer>

        <!-- ──────────────────────
        ├   Right 
        └─────────────────────── -->
        <q-drawer
            side="right"
            show-if-above
            v-model="rightDrawerOpen"
            bordered
            :mini="miniState"
            @mouseenter="miniState = false"
            @mouseleave="miniState = true"
            :width="200"
            :breakpoint="500"
            class=""
        >
            <slot name="right-drawer" />
            <q-scroll-area class="fit" :horizontal-thumb-style="{ opacity: 0 }">
                <q-list padding>
                    <q-item clickable v-ripple>
                        <q-item-section avatar>
                            <q-icon name="inbox" />
                        </q-item-section>

                        <q-item-section> Inbox </q-item-section>
                    </q-item>

                    <q-item active clickable v-ripple>
                        <q-item-section avatar>
                            <q-icon name="star" />
                        </q-item-section>

                        <q-item-section> Star </q-item-section>
                    </q-item>

                    <q-item clickable v-ripple>
                        <q-item-section avatar>
                            <q-icon name="send" />
                        </q-item-section>

                        <q-item-section> Send </q-item-section>
                    </q-item>

                    <q-separator />

                    <q-item clickable v-ripple>
                        <q-item-section avatar>
                            <q-icon name="drafts" />
                        </q-item-section>

                        <q-item-section> Drafts </q-item-section>
                    </q-item>
                </q-list>
            </q-scroll-area>
        </q-drawer>

        <!-- ──────────────────────
        ├   Page 
        └─────────────────────── -->
        <q-page-container class="">
            <slot />
        </q-page-container>

        <!-- ──────────────────────
        ├   Footer 
        └─────────────────────── -->
        <footer class="bg-grey-8 text-white">
            <q-toolbar>
                <q-toolbar-title>
                    <q-avatar>
                        <img src="../../../public/logo/logo.jpg" />
                    </q-avatar>
                    <slot name="footer" />
                    <div>NES</div>
                </q-toolbar-title>
            </q-toolbar>
        </footer>
    </q-layout>
</template>

<script setup>
import { useRouteInfo } from '@/composables/useRouteInfo';
import { usePage } from '@inertiajs/vue3';
import { Dark, useQuasar } from 'quasar';
import { ref } from 'vue';
import { route } from 'ziggy-js';
const page = usePage();
const { routeMethod } = useRouteInfo();
Dark.set(true);

// Drawer states
const leftDrawerOpen = ref(false);
const rightDrawerOpen = ref(false);
const drawer = ref(false);
const miniState = ref(true);

// Dark mode reactive computed
const $q = useQuasar();
const darkMode = ref($q.dark.isActive);
const toggleDarkMode = () => {
    darkMode.value = !darkMode.value;
    $q.dark.set(darkMode.value);
};

/* -------------------------------------------------------------------------- */
/*                                 breadcrumbs                                */
/* -------------------------------------------------------------------------- */
function routeExists(index) {
    const routeName = page.props.theRoute.parts.slice(0, index + 1).join('.');
    // const alternativeRoute = routeName.replace(/\./g, '/');

    try {
        return route(routeName + '.index');
    } catch {
        try {
            return route(routeName);
        } catch {
            return null;
        }
    }
}
</script>

<style scoped>
.drawer-open-right {
    margin-right: 200px !important; /* rtl:ignore */
    transition: margin-right 0.3s;
}

.drawer-open-left {
    margin-left: 200px !important; /* rtl:ignore */
    transition: margin-left 0.3s;
}

.fixed-right {
    left: auto !important;
    right: 0 !important;
}
</style>
