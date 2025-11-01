// Form.vue
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
                                <!-- ──────────────────────
                                    ├   Notification Date
                                    └─────────────────────── -->
                                <div class="row q-gutter-md">
                                    <date-field v-model="form.notification_date" :readonly="readonly" label="Notification Date" class="q-mb-md col-5">
                                    </date-field>
                                </div>
                                <!-- ──────────────────────
                                    ├   Month showAddButton
                                    └─────────────────────── -->
                                <div class="row q-gutter-md">
                                    <q-select
                                        v-model="form.month_id"
                                        outlined
                                        clearable
                                        use-input
                                        :readonly="readonly"
                                        label="Month Assignment"
                                        :options="page.props.months"
                                        option-value="id"
                                        option-label="name"
                                        emit-value
                                        map-options
                                        @filter="filterFn"
                                        :rules="[(val) => !!val || 'Field is required']"
                                        class="q-mb-md col-6"
                                    >
                                        <template v-slot:after>
                                            <div style="min-width: 60px; display: flex; align-items: center">
                                                <q-btn
                                                    v-show="showAddButton"
                                                    icon="add"
                                                    color="primary"
                                                    flat
                                                    @click="addNewMonth"
                                                    class="border-orange"
                                                />
                                            </div>
                                        </template>
                                        <!-- <template v-if="showAddButton" v-slot:befor>
                                            <q-btn icon="add" color="primary" flat @click="addNewMonth" />
                                        </template> -->
                                    </q-select>
                                </div>
                                <!-- -------------------------------- / -------------------------------- -->
                                <div class="row q-gutter-sm">
                                    <!-- ──────────────────────
                                    ├   Product
                                    └─────────────────────── -->
                                    <q-select
                                        :rules="[(val) => !!val || 'Field is required']"
                                        class="q-mb-md col-5"
                                        v-model="form.product_id"
                                        filled
                                        clearable
                                        use-input
                                        input-debounce="0"
                                        @filter="qselectFilter"
                                        :readonly="readonly"
                                        label="Product"
                                        :options="productOptions"
                                        option-value="value"
                                        option-label="label"
                                        emit-value
                                        map-options
                                        @blur="fillFirstOption"
                                    />
                                    <!----------------------
                                    ├   Quantity 
                                    └─────────────────────── -->
                                    <QuantityField v-model:quantity="form.quantity" :readonly="readonly" :step="500" />

                                    <!-- ──────────────────────
                                    ├   Status 
                                    └─────────────────────── -->
                                    <q-select
                                        v-model="form.status"
                                        filled
                                        :readonly="readonly"
                                        label="Status"
                                        :rules="[(val) => !!val || 'Field is required']"
                                        class="q-mb-md col-3"
                                        :options="page.props.statusOptions"
                                        option-value="value"
                                        option-label="label"
                                        emit-value
                                        map-options
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
                                ├   Deadlines 
                                └─────────────────────── -->
                                <div class="area-container q-mt-xl">
                                    <p class="area-label">Deadlines</p>
                                    <q-list separator class="q-mx-md q-pb-md q-gutter-y-md">
                                        <div v-if="form.deadlines[0]?.promised_date || routeMethod == 'create'">
                                            <q-item v-for="(deadline, index) in form.deadlines" :key="index">
                                                <q-item-section>
                                                    <div class="row q-gutter-md">
                                                        <!-- -------------------------------- quantity -------------------------------- -->
                                                        <q-input
                                                            v-model.number="deadline.promised_quantity"
                                                            filled
                                                            :readonly="readonly"
                                                            type="number"
                                                            label="Promised Part Quantity"
                                                            class="col"
                                                            :rules="[(val) => !val || val > 0 || 'Must be positive']"
                                                        />
                                                        <!-- -------------------------------- due date -------------------------------- -->
                                                        <date-field
                                                            v-model="deadline.promised_date"
                                                            :readonly="readonly"
                                                            label="Promised Date"
                                                            class="col-6"
                                                        >
                                                        </date-field>
                                                    </div>
                                                    <!-- -------------------------------- description -------------------------------- -->
                                                    <q-input
                                                        v-model="deadline.description"
                                                        filled
                                                        :readonly="readonly"
                                                        label="Description"
                                                        class=""
                                                    />
                                                </q-item-section>
                                                <!-- -------------------------------- btn: Trash -------------------------------- -->
                                                <q-item-section v-if="!readonly" side top>
                                                    <q-btn
                                                        flat
                                                        round
                                                        size="lg"
                                                        color="brown-5"
                                                        text-color="negative"
                                                        icon="delete"
                                                        @click="removeDeadline(index)"
                                                    />
                                                </q-item-section>
                                            </q-item>
                                        </div>
                                        <div v-else>
                                            <p>No dead-time has assigned</p>
                                        </div>
                                    </q-list>
                                </div>
                                <!-- -------------------------------- btn: add new part -------------------------------- -->
                                <q-btn
                                    v-if="!readonly"
                                    @click="addDeadline"
                                    size="sm"
                                    label="Add deadline"
                                    color="brown-5"
                                    icon="add"
                                    class="q-mt-xs"
                                >
                                    <q-tooltip class="bg-accent">اضافه کردن یک ناحیه جدید برای پارت بندی سفارش</q-tooltip>
                                </q-btn>
                                <!-- ──────────────────────
                                ├   Submit 
                                └─────────────────────── -->
                                <div class="q-mt-md row justify-end">
                                    <q-card-actions>
                                        <q-btn label="Back" @click="router.visit(route('dashboard.orders.index'))" icon="arrow_back" color="brown" />
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
import TitleInPanel from '@/Components/TitleInPanel.vue';
import DateField from '@/Components/DateField.vue';
import QuantityField from '@/Components/QuantityField.vue';
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


/* -------------------------------------------------------------------------- */
/*                                  variables                                 */
/* -------------------------------------------------------------------------- */
const readonly = computed(() => {
    return !['edit', 'create'].includes(routeMethod);
});


/* -------------------------------------------------------------------------- */
/*                                   useform                                  */
/* -------------------------------------------------------------------------- */
const form = useForm({
    product_id: page.props.record?.product_id ?? null,
    month_id: page.props.record?.month_id ?? null,
    quantity: page.props.record?.quantity ?? null,
    notification_date: page.props.record?.notification_date ?? null,
    seen: page.props.record?.seen ?? 'seen',
    status: page.props.record?.status ?? 'active',
    description: page.props.record?.description ?? '',
    deadlines: page.props.record?.deadlines ?? [
        {
            promised_quantity: null,
            promised_date: null,
            description: '',
        },
    ],
});

/* -------------------------------------------------------------------------- */
/*                        Filter function for q-select                        */
/* -------------------------------------------------------------------------- */
const productOptions = ref([...page.props.products]);

function qselectFilter(val, update) {
    if (val === '') {
        update(() => {
            productOptions.value = [...page.props.products]; // reset to all options
        });
        return;
    }

    update(() => {
        const needle = val.toLowerCase();
        productOptions.value = page.props.products.filter((v) => v.label.toLowerCase().includes(needle));
    });
}

function fillFirstOption() {
    if (!form.product_id && productOptions.value.length > 0) {
        // assign first option to v-model
        form.product_id = productOptions.value[0].value;
    }
}

/* -------------------------------------------------------------------------- */
/*                                 add months                                 */
/* -------------------------------------------------------------------------- */
const inputText = ref('');
const showAddButton = ref(false);
const months = ref(page.props.months);

const filteredMonths = ref([...months.value]);

const filterFn = (val, update) => {
    inputText.value = val;

    if (val === '') {
        filteredMonths.value = [...months.value];
        showAddButton.value = false;
        update();
        return;
    }

    const needle = val.toLowerCase();
    filteredMonths.value = months.value.filter((v) => v.name.toLowerCase().includes(needle));

    // چک کردن اینکه چیزی که تایپ شده در گزینه‌ها نیست
    const exists = months.value.some((m) => m.name.toLowerCase() === needle);
    showAddButton.value = !exists;

    update(() => {
        filteredMonths.value = months.value.filter((v) => v.name.toLowerCase().includes(needle));
    });
};

const addNewMonth = () => {
    if (confirm(`Do you want to add "${inputText.value}" to months?`)) {
        // ارسال درخواست به سرور برای افزودن ماه جدید
        // مثلاً با axios یا Inertia
        // Inertia.post('/months', { name: inputText.value }).then(() => {
        //     months.value.push({ id: newId, name: inputText.value });
        //     form.month_id = newId;
        // });
    }
};

/* -------------------------------------------------------------------------- */
/*                                  deadlines                                 */
/* -------------------------------------------------------------------------- */
const addDeadline = () => {
    form.deadlines.push({
        part_quantity: null,
        due_date: null,
        description: '',
    });
};

const removeDeadline = (index) => {
    form.deadlines.splice(index, 1);
};

/* -------------------------------------------------------------------------- */
/*                                   submit                                   */
/* -------------------------------------------------------------------------- */
const submitForm = () => {
    switch (routeMethod) {
        case 'create':
            form.post('/dashboard/orders', {
                onSuccess: () => {
                    console.log('Order created successfully');
                },
                onError: (errors) => {
                    console.log('Errors:', errors);
                },
            });
            break;
        case 'edit':
            if (!page.props.record?.id) {
                console.error('Order ID is missing');
                return;
            }
            // form.put(route('dashboard.orders.update', page.props.record.id), {
            form.put(`/dashboard/orders/${page.props.record.id}`, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    // انجام کاری در صورت موفقیت
                    console.log('Order updated successfully');
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
