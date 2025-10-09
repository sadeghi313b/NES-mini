// -----------------------------
// Fetch Orders function (server-side)
// -----------------------------
const fetchOrders = async () => {
    loading.value = true;

    // Compose query parameters
    const params = {
        page: pagination.page,
        perPage: pagination.rowsPerPage,
        searchProduct: filters.product,
        searchDescription: filters.description,
        filterProduct: filters.productFilter,
        filterMonth: filters.monthFilter,
        filterStatus: filters.statusFilter,
        filterSeen: filters.seenFilter,
    };

    // Inertia get request to server with query params
    await router.get('/orders', params, {
        preserveState: true,
        replace: true,
        onSuccess: (page) => {
            // Assign orders and pagination meta from server response
            orders.value = page.props.orders.data;
            ordersMeta.total = page.props.orders.meta.total;
            ordersMeta.last_page = page.props.orders.meta.last_page;
            loading.value = false;
        },
    });
};
fetchOrders(); //initial fetch

watch(
    () => [pagination.page, pagination.rowsPerPage, filters],
    () => {
        fetchOrders();
    },
    { deep: true },
);

<!-- ----------------------------------------------------------------------- -->
<!--                             v-slot:botttom                              -->
<!-- ----------------------------------------------------------------------- -->
<template v-slot:bottom>
                    <div class="row q-pa-sm items-center justify-between">
                        <div>
                            <!-- Sum of quantity in current page -->
                            Total Quantity: {{ sumQuantity.toLocaleString() }}
                        </div>
                        <div>
                            <!-- Selected rows banner -->
                            Selected Rows: {{ selectedRows.length }}
                        </div>
                        <!-- <div class="col-auto">
                            <span class="text-caption"> Showing {{ ordersMeta.from }} to {{ ordersMeta.to }} of {{ ordersMeta.total }} orders </span>
                        </div>
                        <div class="col-auto">
                            <q-pagination
                                v-model="pagination.page"
                                :max="users.meta.last_page"
                                input
                                size="sm"
                                @update:model-value="onPaginationChange"
                            />
                        </div> -->
                    </div>
                </template>
<!-- ----------------------------------------------------------------------- -->
<!--                             if dark styling                             -->
<!-- ----------------------------------------------------------------------- -->
<div :class="['row justify-between full-width',
                        <!-- $q.dark.isActive && 'text-yellow' -->
                        {'text-yellow': $q.dark.isActive}
                    ]" >
<!-- ----------------------------------------------------------------------- -->
<!--                               dark class                                -->
<!-- ----------------------------------------------------------------------- -->
<body :class="{ dark: $q.dark.isActive }">

<!-- ----------------------------------------------------------------------- -->
<!--                              v-slot-bottom                              -->
<!-- ----------------------------------------------------------------------- -->
                <template v-slot:bottom>
                    <div :class="['row full-width justify-between', { 'text-yellow': $q.dark.isActive }]">
                        <div>
                            <!-- Selected rows banner -->
                            Selected Rows: {{ selectedRows.length }}
                        </div>
                        <div>
                            <div class="row justify-center q-mt-md">
                                <q-pagination
                                    v-model="pagination.page"
                                    :max="pagination.lastPage"
                                    input
                                    size="sm"
                                    @update:model-value="onPaginationChange"
                                />
                            </div>
                        </div>
                        <div>
                            <!-- Sum of quantity in current page -->
                            Total Quantity: {{ sumQuantity.toLocaleString() }}
                        </div>
                    </div>
                </template>
<!-- ----------------------------------------------------------------------- -->
<!--                               pagination                                -->
<!-- ----------------------------------------------------------------------- -->
const pagesNumber = computed(() => {
    return Math.ceil(pagination.rowsNumber / pagination.rowsPerPage);
});
const props = defineProps({
    orders: Object,
});
const initialPagination = reactive({
    page: props.orders.meta.current_page,
    rowsPerPage: props.orders.meta.per_page,
    rowsNumber: props.orders.meta.total,
});
@request="onPaginationChange"
<!-- ----------------------------------------------------------------------- -->
<!--                                 others                                  -->
<!-- ----------------------------------------------------------------------- -->

    // height: 70vh;
    // &.q-table--loading thead tr:last-child th {
    //     top: 48px;
    // }

    // tbody {
    //     scroll-margin-top: 48px;
    // }
<!-- ----------------------------------------------------------------------- -->
<!--                     DynamicFilters-Component usage                      -->
<!-- ----------------------------------------------------------------------- -->
<script setup>
import { ref } from "vue";
import DynamicFilters from "@/Components/DynamicFilters.vue";

const props = defineProps({
  records: Array,
  filterables: Object,
});

const appliedFilters = ref({});

function handleFiltersUpdate(filters) {
  appliedFilters.value = filters;
  console.log("Filters applied:", filters);
  // اینجا می‌تونی request جدید بزنی یا records رو فیلتر کنی
}
</script>

<template>
  <div>
    <DynamicFilters
      :filterables="filterables"
      @update="handleFiltersUpdate"
    />

    <!-- نمایش رکوردها -->
    <div v-for="record in records" :key="record.id">
      {{ record }}
    </div>
  </div>
</template>
<!-- ----------------------------- with lable ------------------------------ -->
<script setup>
import { ref } from "vue";
import DynamicFilters from "@/Components/DynamicFilters.vue";

const props = defineProps({
  records: Array,
  filterables: Object,
});

const appliedFilters = ref({});

function handleFiltersUpdate(filters) {
  appliedFilters.value = filters;
  console.log("Filters applied:", filters);
}
</script>

<template>
  <div>
    <DynamicFilters
      :filterables="filterables"
      @update="handleFiltersUpdate"
    />

    <div v-for="record in records" :key="record.id">
      {{ record }}
    </div>
  </div>
</template>
<!-- ----------------------------------------------------------------------- -->
<!--                               selections                                -->
<!-- ----------------------------------------------------------------------- -->
array:3 [▼ // app\Http\Controllers\OrderController.php:33
  "product" => array:2 [▼
    0 => array:1 [▼
      "label" => "1"
    ]
    1 => array:1 [▼
      "value" => "1"
    ]
  ]
  "status" => array:2 [▼
    0 => array:1 [▼
      "label" => "Hold"
    ]
    1 => array:1 [▼
      "value" => "hold"
    ]
  ]
  "seen" => "Seen"
]


