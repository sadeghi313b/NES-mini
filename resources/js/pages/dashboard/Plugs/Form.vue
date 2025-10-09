<template>
    <panel-layout>
        <!-- ---------------------------------------------------------------- -->
        <!--                        Header                                    -->
        <!-- ---------------------------------------------------------------- -->
        <template #header> </template>
        <!-- ---------------------------------------------------------------- -->
        <!--                        Left Sidebar                              -->
        <!-- ---------------------------------------------------------------- -->
        <template #left-drawer>
            <q-card>
                <q-card-section>
                    <!-- ──────────────────────
                    ├   title
                    └─────────────────────── -->
                    <title-in-panel />
                </q-card-section>
            </q-card>
            <clg
                where=""
                :vars="{
                    'page.props.plug': page.props.plug,
                }"
            />
        </template>
        <!-- ---------------------------------------------------------------- -->
        <!--                        Right Sidebar                             -->
        <!-- ---------------------------------------------------------------- -->
        <template #right-drawer> </template>

        <q-page padding>
            <div class="row q-mb-md">
                <div class="col-12">
                    <q-card>
                        <q-card-section>
                            <q-form @submit.prevent="submitForm">
                                <!-- -------------------------------- / -------------------------------- -->
                                <div class="row q-gutter-md">
                                    <!-- ──────────────────────
                                    ├   Type
                                    └─────────────────────── -->
                                    <q-input
                                        v-model="form.type"
                                        filled
                                        :readonly="readonly"
                                        label="Type"
                                        :rules="[(val) => !!val || 'Field is required']"
                                        class="q-mb-md col-5"
                                    />
                                    <!-- ──────────────────────
                                    ├   Tag
                                    └─────────────────────── -->
                                    <q-input
                                        v-model="form.tag"
                                        filled
                                        :readonly="readonly"
                                        label="Tag"
                                        class="q-mb-md col"
                                    />
                                </div>
                                <!-- ──────────────────────
                                ├   Description 
                                └─────────────────────── -->
                                <q-input
                                    v-model="form.description"
                                    filled
                                    :readonly="readonly"
                                    label="Description"
                                    type="textarea"
                                    class="q-mb-md"
                                    :rows="2"
                                />
                                <!-- ──────────────────────
                                ├   Submit 
                                └─────────────────────── -->
                                <div class="q-mt-md">
                                    <q-card-actions v-if="!readonly" align="right">
                                        <q-btn label="Submit" type="submit" icon="send" color="primary" />
                                    </q-card-actions>
                                    <q-card-actions v-if="['show'].includes(routeMethod)" align="right">
                                        <q-btn label="Back" @click="router.visit(route('dashboard.plugs.index'))" icon="arrow_back" color="brown" />
                                    </q-card-actions>
                                </div>

                                <!-- ──────────────────────
                                ├   created by 
                                └─────────────────────── -->
                                <div class="q-mt-md">
                                    <q-badge
                                        v-if="routeMethod != 'create' && page.props.columns?.created_by"
                                        outline
                                        color="secondary"
                                        :label="`created by : ${page.props.plug.created_by.full_name}  ${page.props.plug.created_at}`"
                                    />
                                    <q-badge
                                        v-if="routeMethod != 'create' && page.props.plug.updated_at"
                                        outline
                                        color="secondary"
                                        :label="`updated at : ${page.props.plug.updated_at}`"
                                    />
                                </div>
                            </q-form>
                        </q-card-section>
                    </q-card>
                </div>
            </div>
        </q-page>
    </panel-layout>
</template>

<script setup>
/* -------------------------------------------------------------------------- */
/*                                   Imports                                  */
/* -------------------------------------------------------------------------- */
import { router, useForm, usePage } from '@inertiajs/vue3';
import { useQuasar } from 'quasar';
import { computed, ref } from 'vue';
import { route } from 'ziggy-js';
//. Layouts
import PanelLayout from '@/Layouts/PanelLayout.vue';
//. Components
import TitleInPanel from '@/components/TitleInPanel.vue';
//. Composables
import { useSafeRoute } from '@/Composables/useSafeRoute';
import { useRouteInfo } from '@/composables/useRouteInfo';
const { routeMethod, baseRouteWithDot } = useRouteInfo();
//. Consts
const page = usePage();
const { flash } = usePage().props;
const $q = useQuasar(); // Quasar instance
const { safeRoute } = useSafeRoute();

//. Temp
import Clg from '@/components/Clg.vue';
const vars = ref([]); //temp
// // [/]

/* -------------------------------------------------------------------------- */
/*                                 definitions                                */
/* -------------------------------------------------------------------------- */
const readonly = computed(() => {
    return !['edit', 'create'].includes(routeMethod);
});

/* -------------------------------------------------------------------------- */
/*                                   useform                                  */
/* -------------------------------------------------------------------------- */
const form = useForm({
    type: page.props.plug?.type ?? null,
    tag: page.props.plug?.tag ?? null,
    description: page.props.plug?.description ?? '',
});

/* -------------------------------------------------------------------------- */
/*                                   submit                                   */
/* -------------------------------------------------------------------------- */
const submitForm = () => {
    switch (routeMethod) {
        case 'create':
            form.post('/dashboard/plugs', {
                onSuccess: () => {
                    console.log('Plug created successfully');
                },
                onError: (errors) => {
                    console.log('Errors:', errors);
                },
            });
            break;
        case 'edit':
            if (!page.props.plug?.id) {
                console.error('Plug ID is missing');
                return;
            }
            form.put(`/dashboard/plugs/${page.props.plug.id}`, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    console.log('Plug updated successfully');
                },
                onError: (errors) => {
                    console.log('Errors:', errors);
                },
            });
            break;
        default:
            alert('routeMethod is not create neither edit');
    }
};
</script>

<style scoped lang="scss">
.border-orange {
    border: 1px solid Brown;
    border-radius: 25%;
    padding: 10px;
}
</style>