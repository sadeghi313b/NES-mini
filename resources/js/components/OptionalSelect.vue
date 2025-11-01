<template>
    <q-select
        :rules="[requiredRule]"
        v-model="modelValue"
        :readonly="readonly"
        :options="filteredOptions"
        option-value="value"
        option-label="label"
        filled
        stack-label
        hide-bottom-space
        clearable
        use-input
        input-debounce="100"
        label="Product"
        emit-value
        map-options
        @filter="qselectFilter"
        @blur="fillFirstOption"
    />
</template>

<script setup>
/*
import OptionalSelect from '@/Components/OptionalSelect.vue';
<OptionalSelect
  v-model="form.product_id"
  :options="products"
  :readonly="readonly"
/>
*/
import { defineEmits, defineModel, defineProps, ref, watch } from 'vue';

// ----------------------
// Props & v-model
// ----------------------
const modelValue = defineModel({
    name: 'modelValue',
    type: [String, Number, null],
});

const props = defineProps({
    readonly: {
        type: Boolean,
        default: false,
    },
    options: {
        type: Array,
        required: true,
        default: () => [],
    },
    optionValue: {
        type: String,
        default: 'value', // مقدار پیشفرض
    },
    optionLabel: {
        type: String,
        default: 'label', // مقدار پیشفرض
    },
});

const emit = defineEmits(['update:modelValue']);

// ----------------------
// Reactive filtered options
// ----------------------
const filteredOptions = ref([...props.options]);

// Watch for changes in original options
watch(
    () => props.options,
    (newOptions) => {
        filteredOptions.value = [...newOptions];
    },
);

// ----------------------
// Validation rule
// ----------------------
const requiredRule = (val) => !!val || 'Field is required';

// ----------------------
// Filter function
// ----------------------
const typedValue = ref('');
function qselectFilter(val, update) {
    typedValue.value = val;
    if (!val) {
        update(() => {
            filteredOptions.value = [...props.options]; // reset all
        });
        return;
    }

    update(() => {
        const needle = val.toLowerCase();
        filteredOptions.value = props.options.filter((v) => String(v.label).toLowerCase().includes(needle));
    });
}

// ----------------------
// Fill first option if empty
// ----------------------
function fillFirstOption() {
    if (typedValue.value.trim() !== '' && !props.modelValue && filteredOptions.value.length > 0) {
        emit('update:modelValue', filteredOptions.value[0].value);
    }
}
</script>
