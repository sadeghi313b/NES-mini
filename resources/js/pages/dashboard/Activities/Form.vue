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
                    'form': form ,
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
                                    ├   Name
                                    └─────────────────────── -->
                                <div class="row q-gutter-md">
                                    <q-input
                                        v-model="form.name"
                                        filled
                                        :readonly="readonly"
                                        label="Name"
                                        :rules="[(val) => !!val || 'Field is required']"
                                        class="q-mb-md col-5"
                                    />
                                    <!-- ──────────────────────
                                    ├   Alias
                                    └─────────────────────── -->
                                    <q-input
                                        v-model="form.alias"
                                        filled
                                        :readonly="readonly"
                                        label="Alias"
                                        class="q-mb-md col-2"
                                    />
                                    <!-- ──────────────────────
                                    ├   Zone
                                    └─────────────────────── -->
                                    <q-input
                                        v-model="form.zone"
                                        filled
                                        :readonly="readonly"
                                        label="Zone"
                                        class="q-mb-md col"
                                    />
                                </div>
                                
                                <div class="row q-gutter-md">
                                </div>
                                <!-- ──────────────────────
                                    ├   Standard Times
                                    └─────────────────────── -->
                                <div class="row q-gutter-sm">
                                    <q-input
                                        v-model.number="form.common_standard_tv_time"
                                        filled
                                        :readonly="readonly"
                                        type="number"
                                        label="Common Standard TV Time"
                                        class="q-mb-md col"
                                    />
                                    <q-input
                                        v-model.number="form.common_standard_ref_time"
                                        filled
                                        :readonly="readonly"
                                        type="number"
                                        label="Common Standard Ref Time"
                                        class="q-mb-md col"
                                    />
                                    <!-- ──────────────────────
                                    ├   Status 
                                    └─────────────────────── -->
                                    <q-toggle
                                        v-model="form.status"
                                        :readonly="readonly"
                                        label="Status"
                                        class="q-mb-md col-2"
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
                                        <q-btn label="Back" @click="router.visit(route('dashboard.activities.index'))" icon="arrow_back" color="brown" />
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
import { computed, ref } from 'vue';
import { route } from 'ziggy-js';
//. Layouts
import PanelLayout from '@/Layouts/PanelLayout.vue';
//. Components
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
    name: page.props.record?.name ?? null,
    alias: page.props.record?.alias ?? null,
    zone: page.props.record?.zone ?? null,
    common_standard_tv_time: page.props.record?.common_standard_tv_time ?? null,
    common_standard_ref_time: page.props.record?.common_standard_ref_time ?? null,
    interchangeable_bundle: page.props.record?.interchangeable_bundle ?? null,
    description: page.props.record?.description ?? '',
    status: page.props.record?.status ?? true,
    tags: page.props.record?.tags ?? [],
});

/* --------------------------------- submit --------------------------------- */
const submitForm = () => {
    switch (routeMethod) {
        case 'create':
            form.post('/dashboard/activities', {
                onSuccess: () => {
                    console.log('Activity created successfully');
                },
                onError: (errors) => {
                    console.log('Errors:', errors);
                },
            });
            break;
        case 'edit':
            if (!page.props.record?.id) {
                console.error('Activity ID is missing');
                return;
            }
            form.put(`/dashboard/activities/${page.props.record.id}`, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    // انجام کاری در صورت موفقیت
                    console.log('Activity updated successfully');
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