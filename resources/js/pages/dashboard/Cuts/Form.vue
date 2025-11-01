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
                    'createBatchRows': createBatchRows ,
                    'batches.fullsCount': creatingBatches,
                    'Math.floor': Math.floor(form.quantity / form.maximum_batch_size),
                    'form.batches': form.batches,
                    'page.props.record': page.props.record,
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
                                        class="q-mb-md col-2"
                                    />
                                    <!-- ──────────────────────
                                    ├   Quantity
                                    └─────────────────────── -->
                                    <QuantityField
                                        v-model:quantity="form.quantity"
                                        :readonly="readonly"
                                        :step="form.maximum_batch_size"
                                        class="q-mb-md col-4"
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
                                        class="q-mb-md col"
                                    />
                                </div>
                                <!-- -------------------------------- / -------------------------------- -->
                                <div class="row q-gutter-sm">
                                    <!-- ──────────────────────
                                    ├   Printing Date
                                    └─────────────────────── -->
                                    <date-field v-model="form.printing_date" :readonly="readonly" label="Printing Date" class="q-mb-md col-4" />

                                    <!-- ──────────────────────
                                    ├   Cutting Date
                                    └─────────────────────── -->
                                    <date-field v-model="form.cutting_date" :readonly="readonly" label="Cutting Date" class="q-mb-md col-4">
                                    </date-field>
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
                                ├   batches 
                                └─────────────────────── -->
                                <div class="area-container q-mt-xl">
                                    <p class="area-label" v-if="routeMethod != 'create'">Batches : {{ form.batches.length }}</p>
                                    <p class="area-label" v-if="routeMethod == 'create'">Batches : {{ creatingBatches.totalsCount }}</p>
                                    <div v-if="form.batches[0]?.id || routeMethod == 'create'" class="within-area-container">
                                        <!-- ⏺ show & edit -->
                                        <q-table
                                            v-if="routeMethod != 'create'"
                                            :columns="batchColumns"
                                            :rows="page.props.record?.batches"
                                            row-key="id"
                                            dense
                                            class="q-mx-lg"
                                        />
                                        <!-- ⏺ create -->
                                        <q-table
                                            v-if="routeMethod == 'create'"
                                            :columns="createBatchColumns"
                                            :rows="createBatchRows"
                                            row-key="index"
                                            dense
                                            class="q-mx-lg"
                                        />
                                    </div>
                                    <div v-else>
                                        <p>No batches is Exist for this cut order</p>
                                    </div>
                                </div>
                                <!-- -------------------------------- btn: add new part -------------------------------- -->
                                <q-btn v-if="!readonly" @click="" size="sm" label="Add New Batch" color="brown-5" icon="add" class="q-mt-xs">
                                    <q-tooltip class="bg-accent">اضافه کردن یک ناحیه جدید برای پارت بندی سفارش</q-tooltip>
                                </q-btn>
                                <!-- ──────────────────────
                                ├   Submit 
                                └─────────────────────── -->
                                <div class="q-mt-md row justify-end">
                                    <q-card-actions v-if="!readonly">
                                        <q-btn label="Submit" type="submit" icon="send" color="primary" />
                                    </q-card-actions>
                                    <q-card-actions>
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
                                        :label="`created by : ${page.props.record?.created_by?.full_name}  ${page.props.record?.created_at}`"
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
import DateField from '@/Components/DateField.vue';
import QuantityField from '@/Components/QuantityField.vue';
import TitleInPanel from '@/Components/TitleInPanel.vue';
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
import columns from '../Users/columns';
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
    order_id: page.props.record?.order_id ?? null,
    quantity: page.props.record?.quantity ?? null,
    maximum_batch_size: page.props.record?.maximum_batch_size ?? 300,
    printing_date: page.props.record?.printing_date ?? null,
    cutting_date: page.props.record?.cutting_date ?? null,
    status: page.props.record?.status ?? true,
    description: page.props.record?.description ?? '',
    batches: page.props.record?.batches ?? [{ id: null, cut_id: null, size: null, description: null, status: true, tags: null }],
    creatingBatches: [],
});

/* -------------------------------------------------------------------------- */
/*                                   batches                                  */
/* -------------------------------------------------------------------------- */

const creatingBatches = computed(() => {
    const fullBatchesCount = Math.floor(form.quantity / form.maximum_batch_size);
    const finalBatchSize = form.quantity - fullBatchesCount * form.maximum_batch_size;
    const totalBatchesCount = fullBatchesCount + (finalBatchSize > 0 ? 1 : 0);

    return {
        fullsCount: fullBatchesCount,
        finalsSize: finalBatchSize,
        totalsCount: totalBatchesCount,
        indexes: Array.from({ length: totalBatchesCount }, (_, i) => i + 1),
        sizes: [...Array.from({ length: fullBatchesCount }, (_, i) => form.maximum_batch_size), ...(finalBatchSize !== 0 ? [finalBatchSize] : [])],
    };
});

const batchColumns = [
    { name: 'id', label: 'id', field: 'id', align: 'center' },
    { name: 'size', label: 'size', field: 'size', align: 'center' },
    { name: 'description', label: 'description', field: 'description', align: 'center' },
];
const createBatchColumns = [
    { name: 'index', label: 'index', field: 'index', align: 'center' },
    { name: 'size', label: 'size', field: 'size', align: 'center' },
];
const createBatchRows = computed(() =>
    creatingBatches.value.indexes.map((index, i) => ({
        index,
        size: creatingBatches.value.sizes[i],
    })),
);

/* -------------------------------------------------------------------------- */
/*                                   submit                                   */
/* -------------------------------------------------------------------------- */
const submitForm = () => {
    switch (routeMethod) {
        case 'create':
            form.creatingBatches = createBatchRows.value;
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
            if (!page.props.record?.id) {
                console.error('Cut ID is missing');
                return;
            }
            form.put(`/dashboard/cuts/${page.props.record.id}`, {
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

.area-container {
    position: relative;
    border: 1px solid darkgray;
    border-start-start-radius: 40px;
    border-end-end-radius: 40px;
    padding-top: 20px; /* فضایی برای قرار گرفتن متن روی border */
}
.within-area-container {
    border-radius: 40px;
    padding-top: 20px;
    padding-bottom: 40px;
}

.area-label {
    position: absolute;
    top: -10px;
    left: 36px;
    padding: 0 8px;
    font-weight: bold;
    background: var(--background);
    // color: mix(black, white, lightness(var(--background)));
    //   color: inver(var(--background));
}
</style>
