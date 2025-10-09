<!-- ------------------- fileName: resources\js\pages\Users\Index.vue ------------------- -->

<template>
    <main-layout>
        <template #left-drawer>
            <div class="row q-mb-md">
                <div class="col">
                    <q-toolbar class="rounded-borders text-white">
                        <!-- title -->
                        <q-toolbar-title>
                            <q-icon name="people" class="q-mr-sm" />
                            Users Management
                        </q-toolbar-title>
                    </q-toolbar>

                    <q-toolbar>
                        <!-- search -->
                        <q-input class="col" v-model="filters.search" label="Search" outlined dense clearable @update:model-value="search">
                            <template v-slot:append>
                                <q-icon name="search" />
                            </template>
                        </q-input>
                    </q-toolbar>

                    <q-toolbar>
                        <!-- + add user -->
                        <q-btn color="secondary" icon="add">
                            <Link :href="safeRoute('users.create')">add user</Link>
                        </q-btn>
                        <q-space />
                        <q-btn color="negative" icon="delete" @click="deleteRecords()">
                            Delete
                        </q-btn>
                    </q-toolbar>

                    <q-toolbar>
                        <!-- filters -->
                        <!-- hide/show columns -->
                        <q-select
                            v-model="visibleColumns"
                            multiple
                            outlined
                            dense
                            options-dense
                            :display-value="$q.lang.table.columns"
                            emit-value
                            map-options
                            :options="columns"
                            option-value="name"
                            options-cover
                            style="min-width: 150px"
                        />
                        <q-space />
                        <!-- filters.status -->
                        <div class="aaa col-auto" style="min-width: 100px; color: blue">
                            <q-select
                                v-model="filters.status"
                                :options="statusOptions"
                                label="Status"
                                outlined
                                dense
                                emit-value
                                map-options
                                @update:model-value="search"
                            >
                                <template v-slot:option="scope">
                                    <q-item v-bind="scope.itemProps" v-on="scope.itemEvents ?? {}">
                                        <q-item-section>
                                            <q-item-label>
                                                {{ scope.opt.label }}
                                            </q-item-label>
                                        </q-item-section>
                                    </q-item>
                                </template>
                            </q-select>
                        </div>
                    </q-toolbar>
                </div>
            </div>
        </template>

        <template #right-drawer> </template>

        <template #header> </template>

        <template #footer></template>

        <!-- Users Table -->
        <q-page class="q-pa-md">
            <div class="row">
                <div class="col">
                    <q-card>
                        <q-card-section>
                            <q-table
                                :rows="rows"
                                :columns="columns"
                                row-key="id"
                                :loading="loading"
                                :pagination="pagination"
                                flat
                                bordered
                                dense
                                v-model:selected="selected" selection="multiple" :selected-rows-label="getSelectedString"
                                virtual-scroll
                                :rows-per-page-options="[0, 5, 10, 15]"
                                :separator="separator"
                                :visible-columns="visibleColumns"
                                class="my-sticky-header-table"
                            >
                                <template v-slot:top> </template>
                                <template v-slot:body-cell-status="props">
                                    <q-td :props="props">
                                        <q-chip :color="props.row.status ? 'positive' : 'negative'" text-color="white" dense>
                                            {{ props.row.status ? 'Active' : 'Inactive' }}
                                        </q-chip>
                                    </q-td>
                                </template>
                                <template v-slot:body-cell-actions="props">
                                    <q-td :props="props">
                                        <q-btn
                                            flat
                                            dense
                                            round
                                            icon="visibility"
                                            color="primary"
                                            size="sm"
                                            :to="safeRoute('users.show', props.row.id)"
                                        >
                                            <q-tooltip>Show</q-tooltip>
                                        </q-btn>
                                        <q-btn
                                            flat
                                            dense
                                            round
                                            icon="edit"
                                            color="warning"
                                            size="sm"
                                            class="q-ml-sm"
                                            :to="safeRoute('users.edit', props.row.id)"
                                        >
                                            <q-tooltip>Edit</q-tooltip>
                                        </q-btn>
                                        <q-btn
                                            flat
                                            dense
                                            round
                                            icon="delete"
                                            color="negative"
                                            size="sm"
                                            class="q-ml-sm"
                                            @click="deleteRecords(props.row)"
                                        >
                                            <q-tooltip>Delete</q-tooltip>
                                        </q-btn>
                                    </q-td>
                                </template>
                                <template v-slot:bottom>
                                    <div class="row full-width items-center justify-between">
                                        <div class="col-auto">
                                            <span class="text-caption"> Showing {{ users.from }} to {{ users.to }} of {{ users.total }} users </span>
                                        </div>
                                        <div class="col-auto">
                                            <q-pagination
                                                v-model="pagination.page"
                                                :max="users.meta.last_page"
                                                input
                                                size="sm"
                                                @update:model-value="onPaginationChange"
                                            />
                                        </div>
                                    </div>
                                </template>
                            </q-table>
                        </q-card-section>
                    </q-card>
                </div>
            </div>
        </q-page>
        {{ pagination }}
    </main-layout>
</template>

<script setup>
/* -------------------------------------------------------------------------- */
/*                                   script                                   */
/* -------------------------------------------------------------------------- */
// [import]
import MainLayout from '@/Layouts/MainLayout.vue';

import { reactive, ref, shallowRef, computed, watch } from 'vue';
import { router, useForm, usePage, Link } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import { Quasar, useQuasar, Dialog } from 'quasar';

// ziggy route()
import { route } from 'ziggy-js';

//composables
import { useSafeRoute } from '@/Composables/useSafeRoute';

const page = usePage();
const { flash } = usePage().props;
const $q = useQuasar(); // Quasar instance
const { safeRoute } = useSafeRoute();
// [/]

// [Props]
const props = defineProps({
    users: Object,
    filters: Object,
});
// [/]

// [Columns & Rows]
import columns from './columns';

// const rows = props.users.data; 
// const rows = ref([...props.users.data]);
const rows = computed(() => props.users.data || []);
// [/]

// [QTable Attributes]
const separator = ref('cell');
const loading = ref(false);
const visibleColumns = ref(columns.map(col => col.name));
// [/]

// [Search]
const search = debounce(() => {
    router.get(
        route('users.index'),
        {
            search: filters.search,
            status: filters.status,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
}, 300); // [/]

// [Filters]
const filters = reactive({
    search: props.filters?.search || '',
    status: props.filters?.status !== undefined ? props.filters.status : null,
});

const statusOptions = [
    { label: 'All', value: null },
    { label: 'Active', value: '1' },
    { label: 'Inactive', value: '0' },
]; // [/]

// [Pagination]
const pagination = reactive({
    page: props.users.meta.current_page,
    rowsPerPage: props.users.meta.per_page,
    rowsNumber: props.users.meta.total,
});

const onPaginationChange = (page) => {
    const url = route('users.index');
    if (url) {
        router.get(
            url,
            {
                page: page,
                search: filters.search,
                status: filters.status,
            },
            {
                preserveState: true,
                replace: true,
            },
        );
    }
}; // [/]

// [selection]
const selected = ref([]);
const getSelectedString = () => {
    const count = selected.value.length; // تعداد رکوردهای انتخاب‌شده
    const total = rows.value.length; // تعداد کل رکوردها

    if (count === 0) return '';

    const recordWord = count > 1 ? 'records' : 'record';
    return `${count} ${recordWord} selected of ${total}`;
};
// [/]

// [CRUD: Delete]
const deleteRecords = () => {
    const selectedRows = selected.value;
    if (selectedRows.length === 0) return;
    const s = selectedRows.length > 1 ? 's' : '';
    $q.dialog({
        title: 'Confirm Delete',
        message: `Are you sure you want to delete selected row${s}?\n ${selectedRows}`,
        cancel: true,
        persistent: true,
    }).onOk(() => {
        const ids=selectedRows.map(r => r.id);
        router.delete(
            route('users.bulkDestroy'),
            {
                data: {ids},
                onSuccess: () => {
                    $q.notify({
                        color: 'positive',
                        // message: `row${s} deleted successfully` ,
                        message: flash?.success || `row${s} deleted successfully` ,
                    });
                    
                },
            }
        );
    });
}


// [/]

</script>

<style lang="sass">
.my-sticky-header-table
    height: 70vh

    .q-table__top,
    .q-table__bottom,
    thead tr:first-child th
        background-color: #00b4ff

    thead tr th
        position: sticky
        z-index: 1
    thead tr:first-child th
        top: 0

    &.q-table--loading thead tr:last-child th
        top: 48px

    tbody
        scroll-margin-top: 48px
</style>

<!-- 

-->
