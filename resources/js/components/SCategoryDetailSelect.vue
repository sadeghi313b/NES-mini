<script setup>
import { ref, computed, watch } from 'vue';
import OptionalSelect from '@/Components/OptionalSelect.vue';


// -------------------------------------------------
// Model synced with parent using defineModel
// -------------------------------------------------
const model = defineModel({  
  type: Object,  
  default: () => ({ category: null, detail: null })  
});

// -------------------------------------------------
// Props
// -------------------------------------------------
const props = defineProps({
  relatedData: {
    type: Array,
    required: true,
    default: () => [],
    // Each element: { id, category, detail }
  },
  readonly: { type: Boolean, default: false },
  label1: { type: String, default: 'Category' },
  label2: { type: String, default: 'Detail' },
  class1: { type: String, default: 'col-3' },
  class2: { type: String, default: 'col-3' },
  categoryField: { type: String, default: 'category' },  
  detailField: { type: String, default: 'detail' },
});

// -------------------------------------------------
// Extract unique categories and details
// -------------------------------------------------
const categories = computed(() => {
  const unique = new Set(props.relatedData.map(i => i[props.categoryField]));
  return [...unique].map(cat => ({ label: cat, value: cat }));
});

const allDetails = computed(() => {
  const unique = new Set(props.relatedData.map(i => i[props.detailField]));
  return [...unique].map(det => ({ label: det, value: det }));
});

// -------------------------------------------------
// Filtered details based on selected category
// -------------------------------------------------
const filteredDetails = computed(() => {
  if (!model.value.category) return allDetails.value;
  const related = props.relatedData.filter(i => i[props.categoryField] === model.value.category);
  const unique = new Set(related.map(i => i[props.detailField]));
  return [...unique].map(det => ({ label: det, value: det }));
});

// -------------------------------------------------
// Clear detail if category changes
// -------------------------------------------------
watch(
  () => model.value.category,
  () => {
    model.value.detail = null;
  }
);
</script>

<template>
  <div class="row q-gutter-x-sm">
    <!-- Category select -->
    <optional-select
      v-model="model.category"
      :options="categories"
      :label="label1"
      :readonly="readonly"
      dense
      :class="class1"
    />

    <!-- Detail select -->
    <optional-select
      v-model="model.detail"
      :options="filteredDetails"
      :label="label2"
      :readonly="readonly"
      dense
      :class="class2"
    />
  </div>
</template>

<style scoped>
/* keep layout simple */
</style>
