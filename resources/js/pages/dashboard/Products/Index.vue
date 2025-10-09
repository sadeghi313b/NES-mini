<template>
  <app-layout title="Products">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Products
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <!-- Header with Create Button -->
          <div class="p-6 bg-white border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Product List</h3>
            <Link
              :href="route('products.create')"
              class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
            >
              Create Product
            </Link>
          </div>

          <!-- Search and Filters -->
          <div class="p-6 bg-gray-50 border-b border-gray-200">
            <div class="flex space-x-4">
              <div class="flex-1">
                <input
                  v-model="searchFilters.search"
                  type="text"
                  placeholder="Search products..."
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                  @input="search"
                />
              </div>
              <select
                v-model="searchFilters.status"
                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                @change="search"
              >
                <option value="">All Status</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
              </select>
            </div>
          </div>

          <!-- Products Table -->
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    ID
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Name
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Customer
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Cable Name
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Created By
                  </th>
                  <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="product in products.data" :key="product.id">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ product.id }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                    <div class="text-sm text-gray-500">{{ product.cable_color }} - {{ product.cable_length }}m</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ product.customer?.name || '-' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ product.cable_name }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="product.status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                          class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                      {{ product.status ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ product.createdBy?.name || '-' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <Link
                      :href="route('products.show', product.id)"
                      class="text-indigo-600 hover:text-indigo-900 mr-3"
                    >
                      View
                    </Link>
                    <Link
                      :href="route('products.edit', product.id)"
                      class="text-blue-600 hover:text-blue-900 mr-3"
                    >
                      Edit
                    </Link>
                    <button
                      @click="deleteProduct(product)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Delete
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="p-6 bg-white border-t border-gray-200">
            <pagination :links="products.links" />
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { router, Link } from '@inertiajs/vue3'
import { ref, reactive } from 'vue'
import { debounce } from 'lodash'

// Props from controller
const props = defineProps({
  products: Object,
  filters: Object,
})

// Reactive search filters
const searchFilters = reactive({
  search: props.filters?.search || '',
  status: props.filters?.status || '',
})

// Debounced search function to prevent excessive requests
const search = debounce(() => {
  router.get(
    route('products.index'),
    {
      search: searchFilters.search,
      status: searchFilters.status,
    },
    {
      preserveState: true,
      replace: true,
    }
  )
}, 300)

// Delete product with confirmation
const deleteProduct = (product) => {
  if (confirm('Are you sure you want to delete this product?')) {
    router.delete(route('products.destroy', product.id))
  }
}
</script>
