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
                                <div class="row q-gutter-md">
                                    <!-- ──────────────────────
                                    ├   Product
                                    └─────────────────────── -->
                                    <optional-select 
                                        v-model="form.product_id" 
                                        :options="page.props.productOptions" 
                                        :readonly="readonly" 
                                        class="col-4"
                                    />
                                    <!-- ──────────────────────
                                    ├   Zone
                                    └─────────────────────── -->
                                    <q-select
                                        v-model="zone"
                                        filled
                                        clearable
                                        use-input
                                        :readonly="readonly"
                                        label="Zone"
                                        :options="page.props.uniquedZones"
                                        option-value="value"
                                        option-label="label"
                                        emit-value
                                        map-options
                                        :rules="[(val) => !!val || 'Field is required']"
                                        class="q-mb-md col"
                                    />
                                    <!-- ──────────────────────
                                    ├   Activity
                                    └─────────────────────── -->
                                    <q-select
                                        v-model="form.activity_id"
                                        filled
                                        clearable
                                        use-input
                                        :readonly="readonly"
                                        label="Activity"
                                        :options="filteredActivities"
                                        option-value="value"
                                        option-label="label"
                                        emit-value
                                        map-options
                                        :rules="[(val) => !!val || 'Field is required']"
                                        class="q-mb-md col"
                                    />
                                </div>
                                <!-- ──────────────────────
                                    ├   Standard Time
                                    └─────────────────────── -->
                                <div class="row q-gutter-sm">
                                    <q-input
                                        v-model.number="form.standard_time"
                                        filled
                                        :readonly="readonly"
                                        type="number"
                                        label="Standard Time"
                                        step="0.01"
                                        :rules="[(val) => (val && val >= 0) || 'Must be non-negative']"
                                        class="q-mb-md col-3"
                                    />
                                    <!-- ──────────────────────
                                    ├   Status 
                                    └─────────────────────── -->
                                    <q-toggle v-model="form.status" :readonly="readonly" label="Status" class="q-mb-md col-3" />
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
                                >
                                    <template v-slot:hint>
                                        <div>Write tag and press Tab</div>
                                    </template>
                                </q-input>
                                <div class="q-mt-sm">
                                    {{ form.tags?.join('; ') || '' }}
                                </div>
                                <!-- ──────────────────────
                                ├   Submit 
                                └─────────────────────── -->
                                <div class="q-mt-md row justify-end">
                                    <q-card-actions>
                                        <q-btn
                                            label="Back"
                                            @click="router.visit(route('dashboard.product_standard_times.index'))"
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
// [Imports] todo:delete some imports
import { router, useForm, usePage } from '@inertiajs/vue3';
import { useQuasar } from 'quasar';
import { ref, computed, watch } from 'vue';
import { route } from 'ziggy-js';
//. Layouts
import PanelLayout from '@/Layouts/PanelLayout.vue';
//. Components
import OptionalSelect from '@/Components/OptionalSelect.vue';
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
const vars = ref([]); //temp
// // [/]

// [variables]
const readonly = computed(() => {
    return !['edit', 'create'].includes(routeMethod);
});

/* -------------------------------------------------------------------------- */
/*                                     tag                                    */
/* -------------------------------------------------------------------------- */
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

// // [/]

/* --------------------------------- useform -------------------------------- */
const form = useForm({
    product_id: page.props.record?.product_id ?? null,
    activity_id: page.props.record?.activity_id ?? null,
    standard_time: page.props.record?.standard_time ?? null,
    description: page.props.record?.description ?? '',
    status: page.props.record?.status ?? true,
    tags: page.props.record?.tags ?? [],
});

/* ----------------------------- related fields ----------------------------- */

// 🔘 selected zone
const zone = ref('');

// 🔘 all activities from backend
const allActivities = ref(page.props.activities); // each activity: {id, name, zone}

// 🔘 filtered activities depending on selected zone
const filteredActivities = computed(() => {
    if (!zone.value) {
        return allActivities.value.map((a) => ({ value: a.id, label: a.name }));
    }
    return allActivities.value.filter((a) => a.zone == zone.value).map((a) => ({ value: a.id, label: a.name }));
});

// 🔘 reset activity when zone changes
// watch(zone, () => {
//     form.activity_id = null;
// });

/* --------------------------------- submit --------------------------------- */
const submitForm = () => {
    switch (routeMethod) {
        case 'create':
            form.post('/dashboard/product_standard_times', {
                onSuccess: () => {
                    console.log('Product Standard Time created successfully');
                },
                onError: (errors) => {
                    console.log('Errors:', errors);
                },
            });
            break;
        case 'edit':
            if (!page.props.record?.id) {
                console.error('Product Standard Time ID is missing');
                return;
            }
            form.put(`/dashboard/product_standard_times/${page.props.record.id}`, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    // انجام کاری در صورت موفقیت
                    console.log('Product Standard Time updated successfully');
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
