<!-- ----------------------------------------------------------------------- -->
<!--                not componene selections and filterables                 -->
<!-- ----------------------------------------------------------------------- -->
// const selections = ref({});
// watch(
//     selections,
//     (newSelections) => {
//         const payload = cloneDeep(newSelections);
//         router.get(
//             '/orders',
//             {
//                 page: 1,
//                 perPage: pagination.rowsPerPage,
//                 selections: newSelections,
//             },
//             { preserveState: true },
//         );
//     },
//     { deep: true },
// );
<div v-for="(filter, key) in page.props.filterables" :key="key">
                    <q-select
                        v-model="selections[key]"
                        :label="`Filter ${key}`"
                        :options="filter.options"
                        :multiple="filter.multiple"
                        use-input
                        outlined
                        dense
                        input-debounce="300"
                        clearable
                        use-chips
                        stack-label
                    />
                </div> 
                <hr />
                <hr />
                <hr />
                <hr />
                <hr />

const productOptions = ref([]);
productOptions.value = page.props.productOptions;
const monthOptions = ref([]);
monthOptions.value = page.props.monthOptions;
const statusOptions = ref([]);
statusOptions.value = page.props.filterables['status']['options'];
const seenOptions = [
    { label: 'Seen', value: true },
    { label: 'unSeen', value: false },
];


const filterables = reactive(page.props.filterables);

const filters = reactive({
    searchProduct: page.props.searchProduct || '',
    searchDescription: page.props.searchDescription || '',
    productFilter: page.props.productFilter || [],
    monthFilter: page.props.monthFilter || [],
    statusFilter: page.props.statusFilter || [],
    seenFilter: page.props.seenFilter || [],
});