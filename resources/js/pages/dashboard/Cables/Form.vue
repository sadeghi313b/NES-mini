<template>
    <panel-layout>
        <!-- ---------------------------------------------------------------- -->
        <!--                        Header                                    -->
        <!-- ---------------------------------------------------------------- -->
        <template #header> </template>

        <!-- ---------------------------------------------------------------- -->
        <!--                        Left Sidebar                              -->
        <!-- ---------------------------------------------------------------- -->
        <template #left-drawer>
            <q-card>
                <q-card-section>
                    <title-in-panel />
                </q-card-section>
            </q-card>

            <clg where="" :vars="{}" />
        </template>

        <!-- ---------------------------------------------------------------- -->
        <!--                        Right Sidebar                             -->
        <!-- ---------------------------------------------------------------- -->
        <template #right-drawer> </template>

        <!-- ---------------------------------------------------------------- -->
        <!--                           Page Content                           -->
        <!-- ---------------------------------------------------------------- -->
        <q-page padding>
            <div class="row q-mb-md">
                <div class="col-12">
                    <q-card>
                        <q-card-section>
                            <q-form @submit.prevent="submitForm">
                                <!-- ---------------------------- Fields ---------------------------- -->
                                <div class="row q-gutter-md">
                                    <!-- ────────── Name ────────── -->
                                    <q-input
                                        v-model="form.name"
                                        filled
                                        :readonly="readonly"
                                        label="Cable Name"
                                        :rules="[(val) => !!val || 'Field is required']"
                                        class="q-mb-md col"
                                    />

                                    <!-- ────────── Status ────────── -->
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

                                    <!-- ────────── Color ────────── -->
                                    <q-select
                                        v-model="form.color"
                                        filled
                                        :readonly="readonly"
                                        label="Color"
                                        :options="page.props.colors"
                                        emit-value
                                        map-options
                                        class="q-mb-md col-3"
                                    />
                                </div>

                                <!-- ────────── Tags ────────── -->
                                <div>Write each tag and then press Enter:</div>
                                <q-input v-model="tagInput" filled :readonly="readonly" label="Tags" @keydown.enter="addTag" />
                                <div class="q-mb-xl text-gray-400">tags: {{ form.tags?.join('; ') || '' }}</div>

                                <!-- ────────── Description ────────── -->
                                <q-input
                                    v-model="form.description"
                                    filled
                                    :readonly="readonly"
                                    label="Description"
                                    type="textarea"
                                    class="q-mb-md"
                                    :rows="2"
                                />

                                <!-- ------------------------ Actions ------------------------ -->
                                <div class="q-mt-md">
                                    <!-- Submit Button -->
                                    <q-card-actions v-if="!readonly" align="right">
                                        <q-btn label="Submit" type="submit" icon="send" color="primary" />
                                    </q-card-actions>

                                    <!-- Back Button -->
                                    <q-card-actions v-if="['show'].includes(routeMethod)" align="right">
                                        <q-btn label="Back" @click="router.visit(route('dashboard.cables.index'))" icon="arrow_back" color="brown" />
                                    </q-card-actions>
                                </div>

                                <!-- ────────── Created / Updated Info ────────── -->
                                <div class="q-mt-md">
                                    <q-badge
                                        v-if="routeMethod != 'create' && page.props.columns?.created_by"
                                        outline
                                        color="secondary"
                                        :label="`created by : ${page.props.record.created_by_full_name}  ${page.props.record.created_at}`"
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
/*                                   Imports                                  */
/* -------------------------------------------------------------------------- */
import { router, useForm, usePage } from '@inertiajs/vue3';
import { useQuasar } from 'quasar';
import { computed, ref } from 'vue';
import { route } from 'ziggy-js';

// Layouts & Components
import PanelLayout from '@/Layouts/PanelLayout.vue';
import Clg from '@/components/Clg.vue';
import TitleInPanel from '@/components/TitleInPanel.vue';

// Composables
import { useSafeRoute } from '@/Composables/useSafeRoute';
import { useRouteInfo } from '@/composables/useRouteInfo';

/* -------------------------------------------------------------------------- */
/*                                   Consts & State                          */
/* -------------------------------------------------------------------------- */
const { routeMethod, baseRouteWithDot } = useRouteInfo();
const page = usePage();
const { flash } = page.props;
const $q = useQuasar();
const { safeRoute } = useSafeRoute();

/* -------------------------------------------------------------------------- */
/*                                   Readonly / Status Options                */
/* -------------------------------------------------------------------------- */
const readonly = computed(() => !['edit', 'create'].includes(routeMethod));
const statusOptions = [
    { label: 'Active', value: true },
    { label: 'Inactive', value: false },
];

/* -------------------------------------------------------------------------- */
/*                                   Form Setup                               */
/* -------------------------------------------------------------------------- */
const form = useForm({
    name: page.props.record?.name ?? null,
    description: page.props.record?.description ?? '',
    tags: page.props.record?.tags ?? [],
    status: page.props.record?.status ?? true,
    color: page.props.record?.color ?? '',
});

/* -------------------------------------------------------------------------- */
/*                                   Tags Handling                            */
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
/*                                   Submit Form                              */
/* -------------------------------------------------------------------------- */
const submitForm = () => {
    switch (routeMethod) {
        case 'create':
            form.post('/dashboard/cables', {
                onSuccess: () => console.log('Cable created successfully'),
                onError: (errors) => console.log('Errors:', errors),
            });
            break;
        case 'edit':
            if (!page.props.record?.id) return console.error('Cable ID is missing');
            form.put(`/dashboard/cables/${page.props.record.id}`, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => console.log('Cable updated successfully'),
                onError: (errors) => console.log('Errors:', errors),
            });
            break;
        default:
            alert('routeMethod is not create nor edit');
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
