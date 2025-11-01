<template>
  <!-- Use PanelLayout instead of AppLayout -->
  <panel-layout>
    <template #header>
      <div class="text-h6">Dashboard</div>
    </template>

    <q-page class="q-pa-md">
      <q-table
        :rows="safeRows"
        :columns="columns"
        row-key="order_id"
        dense
        flat
        separator="cell"
      >
        <template v-slot:body-cell="props">
          <q-td :props="props">{{ props.value }}</q-td>
        </template>
      </q-table>
    </q-page>
  </panel-layout>
</template>

<script setup lang="ts">
// ---------------------------------------------------------------------------
// Imports
// ---------------------------------------------------------------------------
import PanelLayout from '@/layouts/PanelLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

// ---------------------------------------------------------------------------
// Props from Laravel backend (Inertia)
// ---------------------------------------------------------------------------
const page = usePage();
const aggregatedOrders = computed(() => page.props.aggregatedOrders || []);

//! Defensive check: make sure aggregatedOrders is a non-null array
const safeRows = computed(() => {
  const rows = page.props.aggregatedOrders;
  if (!Array.isArray(rows)) return [];
  return rows.filter((r) => r && typeof r === 'object');
});

const loading = ref(false);

// ---------------------------------------------------------------------------
// Columns definition for QTable
// ---------------------------------------------------------------------------
const columns = [
  { name: 'month_name', label: 'Month', field: 'month_name', align: 'center' },
  { name: 'order_id', label: 'Order ID', field: 'order_id', align: 'center' },
  { name: 'product_id', label: 'Product', field: 'product_id', align: 'center' },
  { name: 'order_quantity', label: 'Order', field: 'order_quantity', align: 'center' },
  { name: 'total_cuts', label: 'Cuts', field: 'total_cuts', align: 'center' },
  { name: 'total_productions', label: 'Produced', field: 'total_productions', align: 'center' },
];
</script>

<style scoped>
.q-page {
  /* background-color: white; */
}
</style>
