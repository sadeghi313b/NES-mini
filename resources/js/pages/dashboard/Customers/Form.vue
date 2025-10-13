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
                                    ├   Name
                                    └─────────────────────── -->
                                    <q-input
                                        v-model="form.name"
                                        filled
                                        :readonly="readonly"
                                        label="Name"
                                        :rules="[(val) => !!val || 'Field is required']"
                                        class="q-mb-md col"
                                    />
                                    <!-- ──────────────────────
                                    ├   Status
                                    └─────────────────────── -->
                                    <q-select
                                        v-model="form.status"
                                        filled
                                        :readonly="readonly"
                                        label="Status"
                                        :options="statusOptions"
                                        option-value="value"
                                        option-label="label"
                                        emit-value
                                        map-options
                                        class="q-mb-md col-3"
                                    />
                                </div>
                                <!-- ──────────────────────
                                ├   Tags
                                └─────────────────────── -->
                                <div>Write each tag and then press Enter:</div>
                                <q-input v-model="tagInput" filled :readonly="readonly" label="Tags" @keydown.enter="addTag"> </q-input>
                                <div class="q-mb-xl text-gray-400">tags: {{ form.tags?.join('; ') || '' }}</div>
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
                                        <q-btn
                                            label="Back"
                                            @click="router.visit(route('dashboard.customers.index'))"
                                            icon="arrow_back"
                                            color="brown"
                                        />
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
                                        :label="`created by : ${page.props.record.created_by.full_name}  ${page.props.record.created_at}`"
                                    />
                                    <q-badge
                                        v-if="routeMethod != 'create' && page.props.record.updated_at"
                                        outline
                                        color="secondary"
                                        :label="`updated at : ${page.props.record.updated_at}`"
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

const statusOptions = [
    { label: 'Active', value: true },
    { label: 'Inactive', value: false },
];

/* -------------------------------------------------------------------------- */
/*                                   useform                                  */
/* -------------------------------------------------------------------------- */
const form = useForm({
    name: page.props.record?.name ?? null,
    description: page.props.record?.description ?? '',
    tags: page.props.record?.tags ?? [],
    status: page.props.record?.status ?? true,
});

/* -------------------------------------------------------------------------- */
/*                                    tags                                    */
/* -------------------------------------------------------------------------- */
const tagInput = ref('');

const addTag = (event) => {
    if (event.key === 'Enter') {
        event.preventDefault(); // جلوگیری از تغییر فوکوس
        if (tagInput.value.trim()) {
            form.tags = form.tags || [];
            form.tags.push(tagInput.value.trim());
            tagInput.value = '';
        }
    }
};

/* -------------------------------------------------------------------------- */
/*                                   submit                                   */
/* -------------------------------------------------------------------------- */
const submitForm = () => {
    switch (routeMethod) {
        case 'create':
            form.post('/dashboard/customers', {
                onSuccess: () => {
                    console.log('Customer created successfully');
                },
                onError: (errors) => {
                    console.log('Errors:', errors);
                },
            });
            break;
        case 'edit':
            if (!page.props.record?.id) {
                console.error('Customer ID is missing');
                return;
            }
            form.put(`/dashboard/customers/${page.props.record.id}`, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    console.log('Customer updated successfully');
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
