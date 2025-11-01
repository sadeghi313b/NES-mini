<template>
Lorem ipsum dolor sit amet consectetur adipisicing elit. Officia, harum!
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4">
      <table class="min-w-full border border-gray-300">
        <thead>
          <tr>
            <th class="border px-2 py-1">Month</th>
            <th class="border px-2 py-1">Order ID</th>
            <th class="border px-2 py-1">Product</th>
            <th class="border px-2 py-1">Order Quantity</th>
            <th class="border px-2 py-1">Total Cuts</th>
            <th class="border px-2 py-1">Total Productions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in aggregatedOrders" :key="item.order_id">
            <td class="border px-2 py-1">{{ item.month_name }}</td>
            <td class="border px-2 py-1">{{ item.order_id }}</td>
            <td class="border px-2 py-1">{{ item.product_name }}</td>
            <td class="border px-2 py-1">{{ item.order_quantity }}</td>
            <td class="border px-2 py-1">{{ item.total_cuts }}</td>
            <td class="border px-2 py-1">{{ item.total_productions }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';


const page = usePage();
const aggregatedOrders = page.props.aggregatedOrders;

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: dashboard().url }
];
</script>
