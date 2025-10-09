<!-- Orders/Index.vue -->
<template>
    <panel-layout>
        <!-- -----------------------------------------------------------------------
        ├                                 Header                                  
        └ ---------------------------------------------------------------------- -->
        <template #header>
            <div class="">
                <q-pagination
                    v-model="pagination.page"
                    :max="lastPageNumber"
                    input
                    size="sm"
                    @update:model-value="onPaginationChange"
                    color
                    icon-first="skip_previous"
                    icon-last="skip_next"
                    icon-prev="fast_rewind"
                    icon-next="fast_forward"
                />
            </div>
            <div class="">
                <select v-model.number="pagination.rowsPerPage" @change="onRowsPerPageChange" class="q-ml-md perPage">
                    <option :value="5">5</option>
                    <option :value="10">10</option>
                    <option :value="25">25</option>
                    <option :value="50">50</option>
                    <option :value="100">100</option>
                    <option :value="pagination.rowsNumber">All</option>
                </select>
            </div>
            <q-btn flat round icon="view_comfy" @click="toggleGrid" />
            <q-btn flat round icon="file_download" @click="exportCsv" />
        </template>
        <!-- -----------------------------------------------------------------------
        ├                                 left-drawer
        └ ---------------------------------------------------------------------- -->
        <template #left-drawer>
            <div class="q-pa-md">
                <!-- -------------------------------- Title -------------------------------- -->
                <TitleInPanel />

                <!-- -------------------------------- Add & Delete buttons inline -------------------------------- -->
                <div class="q-mb-md row q-gutter-sm items-center">
                    <q-btn color="primary" label="Add" icon="add" @click="onAdd" />
                    <q-space />
                    <q-btn color="negative" label="Delete" icon="delete" @click="onDelete" :disable="selectedRows.length === 0" />
                </div>
                <!---------------------------- Searches & Filters  --------------------------------->
                <IndexQuery :filterables="page.props.filterables" v-model:criteria="criteria" />
            </div>
        </template>
        <!-- ---------------------------------- page ---------------------------------- -->
        <q-page class="">
            <!-- Table -->
            <q-table
                :rows="rows"
                :columns="columns"
                row-key="id"
                :loading="loading"
                dense
                flat
                v-model:selected="selectedRows"
                selection="multiple"
                :selected-rows-label="getSelectedString"
                :separator="separator"
                :visible-columns="visibleColumns"
                :grid="gridMode"
                :pagination="pagination"
                class="my-sticky-header-table"
            >
                <!-- -------------------------------- body-cell-actions -------------------------------- -->
                <template v-slot:body-cell-actions="props">
                    <q-td :props="props">
                        <q-btn
                            flat
                            dense
                            round
                            icon="visibility"
                            color="primary"
                            size="sm"
                            :href="safeRoute(baseRouteWithDot + 'show', props.row.id)"
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
                            :href="safeRoute(baseRouteWithDot + 'edit', props.row.id)"
                        >
                            <q-tooltip>Edit</q-tooltip>
                        </q-btn>
                    </q-td>
                </template>
                <template v-slot:bottom>
                    <!-- Rows per page selector -->
                </template>
            </q-table>
            <!-- temp -->
            <clg
                :vars="{
                    
                }"
            />
        </q-page>
    </panel-layout>
</template>

<script setup>
//. --------------------------------------------------------------------------
//.                                   script
//. --------------------------------------------------------------------------

/* -------------------------------------------------------------------------- */
/*                                   imports                                  */
/* -------------------------------------------------------------------------- */
//. Imports
import { router, usePage } from '@inertiajs/vue3';
import Cookies from 'js-cookie';
import cloneDeep from 'lodash/cloneDeep';
import { useQuasar } from 'quasar';
import { computed, reactive, ref, watch } from 'vue';
import { route } from 'ziggy-js';
//. Layouts
import PanelLayout from '@/layouts/PanelLayout.vue';
//. Components
import TitleInPanel from '@/components/TitleInPanel.vue';
import IndexQuery from '@/components/IndexQuery.vue';
//. Composables
import { useSafeRoute } from '@/Composables/useSafeRoute';
import { useRouteInfo } from '@/composables/useRouteInfo';
const { routeMethod, baseRouteWithDot, routeName } = useRouteInfo();
//. Consts
const page = usePage();
const { flash } = usePage().props;
const $q = useQuasar(); // Quasar instance
const { safeRoute } = useSafeRoute();
//. Temp
import Clg from '@/components/Clg.vue';
const vars = ref([]); //temp

/* -------------------------------------------------------------------------- */
/*                                 definitions                                */
/* -------------------------------------------------------------------------- */
const url = route(routeName);

/* -------------------------------------------------------------------------- */
/*                               Rows & Columns                               */
/* -------------------------------------------------------------------------- */
const columns = page.props.columns;
const rows = computed(() => page.props.records.data || []);

/* -------------------------------------------------------------------------- */
/*                                  Criteria                                  */
/* -------------------------------------------------------------------------- */
const criteria = ref(cloneDeep(page.props.criteria));

watch(
    criteria,
    (updatedCriteria) => {
        router.get(
            url,
            { page: 1, perPage: pagination.rowsPerPage, criteria: cloneDeep(updatedCriteria) },
            { preserveState: true, preserveScroll: true },
        );
        Cookies.set('criteria', JSON.stringify(updatedCriteria), { expires: undefined });
        Cookies.set('pagination', JSON.stringify({ page: 1, perPage: pagination.rowsPerPage }));
    },
    { deep: true },
);

/* -------------------------------------------------------------------------- */
/*                                 Pagination                                 */
/* -------------------------------------------------------------------------- */
const pagination = reactive({
    page: page.props.records.meta.current_page,
    rowsPerPage: page.props.records.meta.per_page,
    rowsNumber: page.props.records.meta.total,
    lastPage: page.props.records.meta.last_page,
});

const lastPageNumber = computed(
    //used in QPagination:max="lastPageNumber"
    () => page.props.records.meta.last_page,
);

const onPaginationChange = (newPage) => {
    if (url) {
        router.get(
            url,
            {
                page: newPage,
                perPage: pagination.rowsPerPage,
                criteria: cloneDeep(criteria.value),
            },
            { preserveState: true, replace: true },
        );
        Cookies.set('pagination', JSON.stringify({ page: newPage, perPage: pagination.rowsPerPage }));
    }
};

const onRowsPerPageChange = () => {
    router.get(
        url,
        {
            page: 1,
            perPage: pagination.rowsPerPage,
            criteria: cloneDeep(criteria.value),
        },
        { preserveState: true, replace: true },
    );
    Cookies.set('pagination', JSON.stringify({ page: 1, perPage: pagination.rowsPerPage }));
};

/* -------------------------------------------------------------------------- */
/*                                 QSelection                                 */
/* -------------------------------------------------------------------------- */
const selectedRows = ref([]);
const getSelectedString = () => {
    const count = selectedRows.value.length;
    const total = rows.value.length;

    if (count === 0) return '';

    const recordWord = count > 1 ? 'records' : 'record';
    return `${count} ${recordWord} selected of ${total}`;
};

/* -------------------------------------------------------------------------- */
/*                                QTable Props                                */
/* -------------------------------------------------------------------------- */
const separator = ref('cell');
const visibleColumns = ref(columns.map((c) => c.name));
//todo const visibleColumns = computed(() => {
//   return columns.value ? columns.value.map(c => c.name) : [];
// });
const gridMode = ref(false);
const loading = ref(false);
const toggleGrid = () => {
    gridMode.value = !gridMode.value;
};

/* -------------------------------------------------------------------------- */
/*                                   States                                   */
/* -------------------------------------------------------------------------- */
const sumQuantity = computed(() => {
    return rows.value.reduce((sum, row) => sum + row.quantity, 0);
});

/* -------------------------------------------------------------------------- */
/*                                    CRUD                                    */
/* -------------------------------------------------------------------------- */
const onAdd = () => {
    router.get(route(baseRouteWithDot + 'create'));
};

const onDelete = () => {
    if (selectedRows.value.length === 0) return;
    const s = selectedRows.value.length > 1 ? 's' : '';
    const ids = selectedRows.value.map((r) => r.id);
    $q.dialog({
        title: 'Confirm Delete',
        message: `Are you sure you want to delete selected row${s}?\n${ids}`,
        cancel: true,
        persistent: true,
    }).onOk(() => {
        router.delete(route(baseRouteWithDot + 'bulk-destroy'), {
            data: {
                ids,
                page: pagination.page,
                perPage: pagination.rowsPerPage,
            },
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                $q.notify({
                    color: 'positive',
                    // message: `row${s} deleted successfully` ,
                    message: flash?.success || `row${s} ${ids} deleted successfully`,
                });
                selectedRows.value = [];
            },
        });
    });
};

/* -------------------------------------------------------------------------- */
/*                                     CSV                                    */
/* -------------------------------------------------------------------------- */
const exportCsv = () => {
    // Build CSV string from visible columns and selected rows
    const rowsToExport = selectedRows.value.length ? selectedRows.value : rows.value;
    const csvContent = [
        visibleColumns.value.join(','), // Header row
        ...rowsToExport.map((row) => visibleColumns.value.map((col) => `"${row[col]}"`).join(',')),
    ].join('\n');

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.setAttribute('download', 'rows.csv');
    link.click();
};
</script>

<style lang="scss">
.my-sticky-header-table {
    height: 90vh;
    .q-table__top,
    thead tr:first-child th {
        background-color: #00b4ff;
    }

    thead tr th {
        position: sticky;
        z-index: 1;
    }

    thead tr:first-child th {
        top: 0;
    }

    &.q-table--loading thead tr:last-child th {
        top: 48px;
    }

    tbody {
        scroll-margin-top: 48px;
    }
}

.perPage {
    border: 1px solid Navy;
    border-radius: 8px;
    padding: 4px 8px;
    background-color: Azure;
    color: black;
    font-size: 14px;
}

.rounded-border {
    border: 1px solid Navy;
    border-radius: 8px;
    padding: 0px;
}
</style>
