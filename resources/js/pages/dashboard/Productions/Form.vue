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
                    <div class="">
                        <!-- ──────────────────────
                        ├   title
                        └─────────────────────── -->
                        <TitleInPanel />
                    </div>
                </q-card-section>
            </q-card>

            <clg
                where=""
                :vars="{
                    'form.orders': form.orders,
                    'page.props.orders': page.props.orders,
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
                                <div class="row q-gutter-md">
                                    <!-- ──────────────────────
                                    ├   Date
                                    └─────────────────────── -->
                                    <date-field v-model="form.date" :readonly="readonly" label="Date" class="q-mb-md col-3"> </date-field>
                                    <!-- ──────────────────────
                                    ├   Product
                                    └─────────────────────── -->
                                    <optional-select
                                        v-model="form.product_id"
                                        :readonly="readonly"
                                        label="Product"
                                        :options="productOptions"
                                        :rules="[(val) => !!val || 'Field is required']"
                                        class="q-mb-md col"
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
                                <div class="row q-gutter-sm">
                                    <!-- ──────────────────────
                                    ├   Tags 
                                    └─────────────────────── -->
                                    <q-input
                                        v-model="tagInput"
                                        filled
                                        :readonly="readonly"
                                        label="Tags"
                                        @keydown.tab="addTag"
                                        class="q-mb-md col"
                                        bottom-slots
                                        hide-bottom-space
                                    >
                                    </q-input>

                                    <!-- ──────────────────────
                                    ├   Status 
                                    └─────────────────────── -->
                                    <q-toggle v-model="form.status" :readonly="readonly" label="Status" class="col-3" />
                                </div>

                                <!-- -----------------------------------------------------------------------
                                ├                                 show Tags
                                └ ---------------------------------------------------------------------- -->
                                <div class="" v-if="form.tags?.length > 0">
                                    {{ `tags: ${form.tags?.join('; ')}` || '' }}
                                </div>

                                <!-- ──────────────────────
                                ├   orders 
                                └─────────────────────── -->
                                <div class="area-container q-mt-xl q-py-md">
                                    <p class="area-label" v-if="routeMethod != 'create'">
                                        ✅ Orders for producing {{ page.props.record?.id }} for {{ page.props.record?.orders[0]?.product_id }}
                                    </p>
                                    <p class="area-label" v-if="routeMethod == 'create'">
                                        ✅ Orders for producing $this {{ form.product_id ? `for ${form.product_id}` : '' }} 
                                    </p>
                                    <div v-for="(order, i) in form.orders" :key="i" class="q-ma-md">
                                        <div class="row q-gutter-sm">
                                            <!-- { selected1: order.month_name, selected2: order.product_id, selected3: order_id } -->
                                            <SIndexedRelatedListPicker
                                                v-model:selected1="order.month_name"
                                                v-model:selected2="order.product_id"
                                                v-model:selected3="order.order_id"
                                                :data="relatedDataSet"
                                                :fixed-value2="fixedValue2"
                                                :readonly="readonly"
                                                label1="Month"
                                                label2="dont show"
                                                label3="Order"
                                                class1="min-w-[150px]"
                                                class3="max-w-[150px]"
                                                class=""
                                            />

                                            <q-input
                                                :readonly="readonly"
                                                v-model="form.orders[i].quantity"
                                                label="Quantity"
                                                filled
                                                dense
                                                class="col-2"
                                            />

                                            <div v-if="!readonly" side top>
                                                <q-btn
                                                    flat
                                                    round
                                                    size="md"
                                                    color="brown-5"
                                                    text-color="negative"
                                                    icon="delete"
                                                    @click="removeSet(i)"
                                                />
                                            </div>
                                        </div>
                                        <q-separator spaced="md" v-if="i != form.orders.length" />
                                    </div>
                                    <!-- -------------------------------- add button -------------------------------- -->
                                    <div class="row q-mr-xl justify-end">
                                        <q-btn
                                            v-if="!readonly"
                                            @click="addSet"
                                            size="sm"
                                            label="Add New"
                                            color="brown-5"
                                            icon="add"
                                            class="q-mt-xs"
                                        />
                                    </div>
                                </div>

                                <!-- ──────────────────────
                                ├   Submit 
                                └─────────────────────── -->
                                <div class="q-mt-md row justify-end">
                                    <q-card-actions>
                                        <q-btn
                                            label="Back"
                                            @click="router.visit(route('dashboard.productions.index'))"
                                            icon="arrow_back"
                                            color="brown"
                                        />
                                    </q-card-actions>
                                    <q-card-actions v-if="!readonly">
                                        <q-btn label="Submit" type="submit" icon="send" color="primary" />
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
                                        v-if="routeMethod != 'create' && page.props.record?.updated_at"
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
import OptionalSelect from '@/Components/OptionalSelect.vue';
import TitleInPanel from '@/Components/TitleInPanel.vue';
import SIndexedRelatedListPicker from '@/components/SIndexedRelatedListPicker.vue';

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
/*                                  variables                                 */
/* -------------------------------------------------------------------------- */
const readonly = computed(() => {
    return !['edit', 'create'].includes(routeMethod);
});

const tagInput = ref('');

const addTag = (event) => {
    if (event.key === 'Tab') {
        event.preventDefault(); // جلوگیری از تغییر فوکوس
        if (tagInput.value.trim()) {
            form.tags = form.tags || [];
            form.tags.push(tagInput.value.trim());
            tagInput.value = '';
        }
    }
};

/* -------------------------------------------------------------------------- */
/*                                   useform                                  */
/* -------------------------------------------------------------------------- */
const form = useForm({
    date: page.props.record?.date ?? null,
    product_id: page.props.record?.product_id ?? null,
    quantity: page.props.record?.quantity ?? null,
    description: page.props.record?.description ?? '',
    status: page.props.record?.status ?? true,
    tags: page.props.record?.tags ?? [],
    orders: page.props.record?.orders ?? [
        {
            month_name: '',
            product_id: null,
            order_id: null,
            quantity: null,
            pivot_id: null,
        },
    ],
});

/* -------------------------------------------------------------------------- */
/*                                   options                                  */
/* -------------------------------------------------------------------------- */
const orders_count = ref(page.props.record?.orders_count);
const productOptions = page.props.products.map((p) => ({ label: p.id, value: p.id }));
const fixedValue2 = computed(() => form.product_id);
const relatedDataSet = page.props.orders.map((o) => ({
    set1: o.month.name,
    set2: o.product_id,
    set3: o.id,
}));

const selectedCollection = ref([
    { selected1: null, selected2: null, selected3: null },
    { selected1: null, selected2: null, selected3: null },
]);

const selectedSet = ref(
    form.orders.map((order) => ({
        selected1: order.month_name ?? null,
        selected2: order.product_id ?? null,
        selected3: order.order_id ?? null,
    })),
);

/* -------------------------------------------------------------------------- */
/*                                  formArea                                  */
/* -------------------------------------------------------------------------- */
const addSet = () => {
    form.orders.push({
        month_name: '',
        product_id: null,
        order_id: null,
        quantity: null,
        pivot_id: null,
    });
};

const removeSet = (index) => {
    form.orders.splice(index, 1);
};

/* -------------------------------------------------------------------------- */
/*                                   submit                                   */
/* -------------------------------------------------------------------------- */
const submitForm = () => {
    switch (routeMethod) {
        case 'create':
            form.post('/dashboard/productions', {
                onSuccess: () => {
                    console.log('Production created successfully');
                },
                onError: (errors) => {
                    console.log('Errors:', errors);
                },
            });
            break;
        case 'edit':
            if (!page.props.record?.id) {
                console.error('Production ID is missing');
                return;
            }
            form.put(`/dashboard/productions/${page.props.record.id}`, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    // انجام کاری در صورت موفقیت
                    console.log('Production updated successfully');
                },
                onError: (errors) => {
                    // انجام کاری در صورت وجود خطا
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
.area-container {
    position: relative;
    border: 1px solid darkgray;
    border-start-start-radius: 40px;
    border-end-end-radius: 40px;
    padding-top: 20px; /* فضایی برای قرار گرفتن متن روی border */
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

// body.body--dark .area-label {
//     background: #262626;
//     color: #bfbfbf;
// }

.border-orange {
    border: 1px solid Brown;
    border-radius: 25%;
    padding: 10px;
}
</style>
