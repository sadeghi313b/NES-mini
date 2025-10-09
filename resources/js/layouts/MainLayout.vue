<!-- ------------------- resources\js\layouts\MainLayout.vue ------------------ -->
<template>
    <q-layout view="lHh lpR fFf">
        <q-header elevated class="bg-primary text-white" height-hint="98">
            <q-toolbar>
                <q-btn dense flat round icon="menu" @click.prevent="leftDrawerOpen = !leftDrawerOpen" />

                <q-toolbar-title>
                    <q-avatar>
                        <img src="https://cdn.quasar.dev/logo-v2/svg/logo-mono-white.svg" />
                    </q-avatar>
                    NES
                </q-toolbar-title>
                <slot name="header" />

                <!-- Dark Mode Toggle -->
                <q-btn dense flat round :icon="darkMode ? 'brightness_5' : 'brightness_4'" @click.prevent="toggleDarkMode" type="button" />

                <q-btn dense flat round icon="menu" @click.prevent="rightDrawerOpen = !rightDrawerOpen" />
            </q-toolbar>
        </q-header>

        <q-drawer show-if-above v-model="leftDrawerOpen" side="left" bordered>
            <slot name="left-drawer" />
        </q-drawer>

        <q-drawer side="right"
            show-if-above 
            v-model="rightDrawerOpen" 
            bordered
            :mini="miniState"
            @mouseenter="miniState = false"
            @mouseleave="miniState = true"
            :width="200"
            :breakpoint="500"
        >
                    <slot name="right-drawer" />
                    <q-scroll-area class="fit" :horizontal-thumb-style="{ opacity: 0 }">
                        <q-list padding>
                            <q-item clickable v-ripple>
                            <q-item-section avatar>
                                <q-icon name="inbox" />
                            </q-item-section>

                            <q-item-section>
                                Inbox
                            </q-item-section>
                            </q-item>

                            <q-item active clickable v-ripple>
                            <q-item-section avatar>
                                <q-icon name="star" />
                            </q-item-section>

                            <q-item-section>
                                Star
                            </q-item-section>
                            </q-item>

                            <q-item clickable v-ripple>
                            <q-item-section avatar>
                                <q-icon name="send" />
                            </q-item-section>

                            <q-item-section>
                                Send
                            </q-item-section>
                            </q-item>

                            <q-separator />

                            <q-item clickable v-ripple>
                            <q-item-section avatar>
                                <q-icon name="drafts" />
                            </q-item-section>

                            <q-item-section>
                                Drafts
                            </q-item-section>
                            </q-item>
                        </q-list>
                    </q-scroll-area>
        </q-drawer>

        <q-page-container class="">
            <slot />
        </q-page-container>

        <footer class="bg-grey-8 text-white">
            <q-toolbar>
                <q-toolbar-title>
                    <q-avatar>
                        <img src="https://cdn.quasar.dev/logo-v2/svg/logo-mono-white.svg" />
                    </q-avatar>
                    <slot name="footer" />
                    <div>NES</div>
                </q-toolbar-title>
            </q-toolbar>
        </footer>
    </q-layout>
</template>

<script setup>
import { useQuasar } from 'quasar';
import { computed, ref } from 'vue';
import { Dark } from 'quasar'
Dark.set(true)

// Drawer states
const leftDrawerOpen = ref(false);
const rightDrawerOpen = ref(false);
const drawer= ref(false);
const miniState= ref(true);

// Dark mode reactive computed
const $q = useQuasar()
const darkMode = ref($q.dark.isActive)
const toggleDarkMode = () => {
  darkMode.value = !darkMode.value
  $q.dark.set(darkMode.value)
}
</script>
