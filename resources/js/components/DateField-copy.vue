<script setup>
//import DateField from "@/Components/DateField.vue";
import { defineProps, defineEmits } from "vue";

// Props received from parent
const props = defineProps({
  modelValue: String, // the v-model binding
  label: {
    type: String,
    default: "Date",
  },
  readonly: {
    type: Boolean,
    default: false,
  },
  classes: {
    type: String,
    default: "",
  },
});

// Emits value updates
const emit = defineEmits(["update:modelValue"]);
</script>

<template>
  <q-input
    :model-value="props.modelValue"
    @update:model-value="emit('update:modelValue', $event)"
    filled
    :readonly="props.readonly"
    :label="props.label"
    mask="####/##/##"
    :rules="['date'] || 'input as: YYYY/MM/DD'"
    :class="props.classes"
  />
</template>
