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
                    'page.props.cut': page.props.cut,
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
                                    ├   Order
                                    └─────────────────────── -->
                                    <q-input
                                        v-model="form.order_id"
                                        filled
                                        clearable
                                        :readonly="readonly"
                                        label="Order"
                                        :rules="[(val) => (val && val > 0) || 'Field is required']"
                                        class="q-mb-md col-5"
                                    />
                                    <!-- ──────────────────────
                                    ├   Quantity
                                    └─────────────────────── -->
                                    <q-input
                                        v-model.number="form.quantity"
                                        filled
                                        :readonly="readonly"
                                        type="number"
                                        label="Quantity"
                                        :rules="[(val) => (val && val > 0) || 'Must be positive']"
                                        class="q-mb-md col"
                                    />
                                    <!-- ──────────────────────
                                    ├   Maximum Batch Size
                                    └─────────────────────── -->
                                    <q-input
                                        v-model.number="form.maximum_batch_size"
                                        filled
                                        :readonly="readonly"
                                        type="number"
                                        label="Maximum Batch Size"
                                        :rules="[(val) => (val && val > 0) || 'Must be positive']"
                                        class="q-mb-md col-3"
                                    />
                                </div>
                                <!-- -------------------------------- / -------------------------------- -->
                                <div class="row q-gutter-sm">
                                    <!-- ──────────────────────
                                    ├   Printing Date
                                    └─────────────────────── -->
                                    <q-input
                                        v-model="form.printing_date"
                                        filled
                                        :readonly="readonly"
                                        label="Printing Date"
                                        mask="####/##/##"
                                        :rules="[
                                            (val) => {
                                                if (!val) return true;
                                                const date = new Date(val);
                                                return (date instanceof Date && !isNaN(date)) || 'Invalid date';
                                            },
                                        ]"
                                        class="q-mb-md col-5"
                                    >
                                        <template v-slot:append>
                                            <q-icon name="event" class="cursor-pointer">
                                                <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                                    <q-date
                                                        v-model="form.printing_date"
                                                        mask="YYYY/MM/DD"
                                                        title="تاریخ چاپ"
                                                        subtitle="printing date"
                                                        calendar="persian"
                                                    >
                                                        <div class="row items-center justify-end">
                                                            <q-btn v-close-popup label="Close" color="primary" flat />
                                                        </div>
                                                    </q-date>
                                                </q-popup-proxy>
                                            </q-icon>
                                        </template>
                                    </q-input>
                                    <!-- ──────────────────────
                                    ├   Cutting Date
                                    └─────────────────────── -->
                                    <q-input
                                        v-model="form.cutting_date"
                                        filled
                                        :readonly="readonly"
                                        label="Cutting Date"
                                        mask="####/##/##"
                                        :rules="[(val) => !!val || 'Field is required', 'date']"
                                        class="q-mb-md col"
                                    >
                                        <template v-slot:append>
                                            <q-icon name="event" class="cursor-pointer">
                                                <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                                    <q-date
                                                        v-model="form.cutting_date"
                                                        mask="YYYY/MM/DD"
                                                        title="تاریخ برش"
                                                        subtitle="cutting date"
                                                        calendar="persian"
                                                    >
                                                        <div class="row items-center justify-end">
                                                            <q-btn v-close-popup label="Close" color="primary" flat />
                                                        </div>
                                                    </q-date>
                                                </q-popup-proxy>
                                            </q-icon>
                                        </template>
                                    </q-input>
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
                                        :rules="[(val) => (val !== null && val !== undefined) || 'Field is required']"
                                        class="q-mb-md col-3"
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
                                        <q-btn label="Back" @click="router.visit(route('dashboard.cuts.index'))" icon="arrow_back" color="brown" />
                                    </q-card-actions>
                                </div>

                                <!-- ──────────────────────
                                ├   created by 
                                └─────────────────────── -->
                                <div class="q-mt-md">
                                    <q-badge
                                        v-if="routeMethod != 'create'"
                                        outline
                                        color="secondary"
                                        :label="`created by : ${page.props.cut.created_by.full_name}  ${page.props.cut.created_at}`"
                                    />
                                    <q-badge
                                        v-if="routeMethod != 'create' && page.props.cut.updated_at"
                                        outline
                                        color="secondary"
                                        :label="`updated at : ${page.props.cut.updated_at}`"
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
    { label: 'true', value: true },
    { label: 'false', value: false },
];

/* -------------------------------------------------------------------------- */
/*                                   useform                                  */
/* -------------------------------------------------------------------------- */
const form = useForm({
    order_id: page.props.cut?.order_id ?? null,
    quantity: page.props.cut?.quantity ?? null,
    maximum_batch_size: page.props.cut?.maximum_batch_size ?? null,
    printing_date: page.props.cut?.printing_date ?? null,
    cutting_date: page.props.cut?.cutting_date ?? null,
    status: page.props.cut?.status ?? true,
    description: page.props.cut?.description ?? '',
});

/* -------------------------------------------------------------------------- */
/*                                   submit                                   */
/* -------------------------------------------------------------------------- */
const submitForm = () => {
    switch (routeMethod) {
        case 'create':
            form.post('/dashboard/cuts', {
                onSuccess: () => {
                    console.log('Cut created successfully');
                },
                onError: (errors) => {
                    console.log('Errors:', errors);
                },
            });
            break;
        case 'edit':
            if (!page.props.cut?.id) {
                console.error('Cut ID is missing');
                return;
            }
            form.put(`/dashboard/cuts/${page.props.cut.id}`, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    console.log('Cut updated successfully');
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
