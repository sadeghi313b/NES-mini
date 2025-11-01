div
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
                    'batches': batches,
                    'page.props': page.props,
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
                                    ├   Date
                                    └─────────────────────── -->
                                <div class="row q-gutter-md">
                                    <date-field v-model="form.date" :readonly="readonly" label="Date" class="q-mb-md col-5"> </date-field>
                                    <!-- ──────────────────────
                                    ├   Employee
                                    └─────────────────────── -->
                                    <optional-select
                                        :rules="[(val) => !!val || 'Field is required']"
                                        v-model="form.employee_id"
                                        :readonly="readonly"
                                        label="Employee"
                                        :options="employeeOptions"
                                        option-value="id"
                                        option-label="full_name"
                                        class="q-mb-md col"
                                    />
                                </div>
                                <!-- ──────────────────────
                                ├   //# Works
                                └─────────────────────── -->
                                <div class="area-container q-mt-xl">
                                    <p class="area-label">✅ Works & Vacations : Daily TimeSheet</p>
                                    <div class="q-mx-md q-pb-md q-gutter-y-md">
                                        <q-tabs
                                            v-model="tab"
                                            class="text-grey-9"
                                            active-color="primary"
                                            indicator-color="primary"
                                            dense
                                            align="justify"
                                            :breakpoint="0"
                                        >
                                            <q-tab name="Batched" icon="assignment_turned_in" label="Batched" class="" />
                                            <q-tab name="nonBatched" icon="fact_check" label="nonBatched" class="" />
                                            <q-tab name="Extra" icon="local_shipping" label="Extra" class="" />
                                            <q-tab name="noWork" icon="block" label="no Work" class="" />
                                            <q-tab name="Vacation" icon="beach_access" label="Vacation" class="" />
                                        </q-tabs>
                                        <!-- //. tab panels -->
                                        <q-tab-panels v-model="tab" animated>
                                            <!-- //# Batched -->
                                            <q-tab-panel name="Batched">
                                                <q-list separator class="">
                                                    <p dir="rtl">کارهایی که شماره بچ دارند</p>
                                                    <q-item v-for="(item, index) in batchedWorks" :key="index" class="column q-gutter-sm">
                                                        <q-item-section class="">
                                                            <div class="row">
                                                                <SCategoryDetailSelect
                                                                    v-model="selectedPair"
                                                                    :relatedData="activities"
                                                                    category-field="zone"
                                                                    detail-field="name"
                                                                    class="col"
                                                                    class1="col-5"
                                                                    class2="col"
                                                                    label1="Zone"
                                                                    label2="Activity"
                                                                />
                                                            </div>
                                                        </q-item-section>

                                                        <q-item-section class="">
                                                            <div class="row q-gutter-x-sm">
                                                                <SInputInArray
                                                                    v-model:arr="batchedWorks[index].batches"
                                                                    label="Batche No"
                                                                    :readonly="readonly"
                                                                    class=""
                                                                />
                                                            </div>
                                                        </q-item-section>

                                                        <q-item-section>
                                                            <div class="row">
                                                                <q-chip
                                                                    v-for="(el, i) in batchedWorks[index].batches"
                                                                    :key="i"
                                                                    outline
                                                                    color="primary"
                                                                    text-color="white"
                                                                    removable
                                                                    @remove="batchedWorks[index].batches.splice(i, 1)"
                                                                    class="q-ma-xs"
                                                                    dense
                                                                >
                                                                    {{ el }}
                                                                </q-chip>
                                                            </div>
                                                        </q-item-section>

                                                        <div class="q-my-sm row q-gutter-sm justify-end">
                                                            <q-btn
                                                                v-if="!readonly"
                                                                @click="removeBatchedWork(index)"
                                                                size="sm"
                                                                label="remove"
                                                                color="red-9"
                                                                icon="delete"
                                                            >
                                                                <q-tooltip class="bg-accent">اضافه کردن یک فعالیت روزانه جدید</q-tooltip>
                                                            </q-btn>
                                                            <q-btn
                                                                v-if="!readonly"
                                                                @click="addNewBatchedWork"
                                                                size="sm"
                                                                label="Add"
                                                                color="brown-5"
                                                                icon="add"
                                                            />
                                                        </div>
                                                    </q-item>
                                                </q-list>
                                            </q-tab-panel>
                                            <q-tab-panel name="nonBatched"> کارهایی که شماره بچ ندارند </q-tab-panel>
                                            <q-tab-panel name="Extra"> کارهایی مثل باربری و نظافت </q-tab-panel>
                                            <q-tab-panel name="noWork"> عدم وجود کار و بیکاری ناشی از نبود کار </q-tab-panel>
                                            <q-tab-panel name="Vacation"> مرخصی و تعطیلات </q-tab-panel>
                                        </q-tab-panels>
                                    </div>
                                </div>
                                <!-- -------------------------------- btn: add new part -------------------------------- -->

                                <!-- //#
                                // Status 
                                <q-toggle v-model="form.status" :readonly="readonly" label="Status" class="q-mb-md col-3" />
                                // Tags 
                                <q-input
                                    v-model="tagInput"
                                    filled
                                    :readonly="readonly"
                                    label="Tags"
                                    @keydown.tab="addTag"
                                    class="q-mb-md col"
                                    bottom-slots
                                >
                                    <template v-slot:hint>
                                        <div>Write tag and press Tab</div>
                                    </template>
                                </q-input>
                                <div class="q-mt-sm">
                                    {{ form.tags?.join('; ') || '' }}
                                </div>
                                -->
                                <!-- ──────────────────────
                                ├   Submit 
                                └─────────────────────── -->
                                <div class="q-mt-md row justify-end">
                                    <q-card-actions>
                                        <q-btn label="Back" @click="router.visit(route('dashboard.works.index'))" icon="arrow_back" color="brown" />
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
import SCategoryDetailSelect from '@/components/SCategoryDetailSelect.vue';
import SInputInArray from '@/components/SInputInArray.vue';
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
const selectedPair = ref({ category: null, detail: null });

const readonly = computed(() => {
    return !['edit', 'create'].includes(routeMethod);
});

const workTypeOptions = [
    { label: 'Batched', value: 'Batched' },
    { label: 'Non-Batched', value: 'nonBatched' },
    { label: 'Extra', value: 'Extra' },
    { label: 'No Work', value: 'noWork' },
    { label: 'Vacation', value: 'Vacation' },
];

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

const employeeOptions = computed(() =>
    page.props.employees.map((e) => ({
        value: e.id, // یا e.user.id اگر می‌خوای v-model روی user_id باشد
        label: `${e.user.full_name} - ${e.employee_number}`,
    })),
);

/* -------------------------------------------------------------------------- */
/*                                   options                                  */
/* -------------------------------------------------------------------------- */
const activities = page.props.activities;

const batches = page.props.batches;


/* -------------------------------------------------------------------------- */
/*                                   useform                                  */
/* -------------------------------------------------------------------------- */
const form = useForm({
    date: page.props.record?.date ?? null,
    employee_id: page.props.record?.employee_id ?? null,
    work_type: page.props.record?.work_type ?? null,
    description: page.props.record?.description ?? '',
    status: page.props.record?.status ?? true,
    tags: page.props.record?.tags ?? [],
});

/* -------------------------------------------------------------------------- */
/*                                    works                                   */
/* -------------------------------------------------------------------------- */
const batchedWorks = ref([
    {
        activity: null,
        batches: [],
    },
]);

const addNewBatchedWork = () => {
    batchedWorks.value.push({
        activity: null,
        batches: [],
    });
};

const removeBatchedWork = (index) => {
    batchedWorks.value.splice(index, 1);
};

const tab = ref('Batched');

/* -------------------------------------------------------------------------- */
/*                                     batches                                */
/* -------------------------------------------------------------------------- */
const batchInput = ref([]);

/* -------------------------------------------------------------------------- */
/*                                   submit                                   */
/* -------------------------------------------------------------------------- */
const submitForm = () => {
    switch (routeMethod) {
        case 'create':
            form.post('/dashboard/works', {
                onSuccess: () => {
                    console.log('Work created successfully');
                },
                onError: (errors) => {
                    console.log('Errors:', errors);
                },
            });
            break;
        case 'edit':
            if (!page.props.record?.id) {
                console.error('Work ID is missing');
                return;
            }
            form.put(`/dashboard/works/${page.props.record.id}`, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    // انجام کاری در صورت موفقیت
                    console.log('Work updated successfully');
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
