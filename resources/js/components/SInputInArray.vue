<script setup>
import QuantityField from '@/Components/QuantityField.vue';
import { ref } from 'vue';

const arr = defineModel('arr', { type: Array, default: () => [] });

const props = defineProps({
    readonly: { type: Boolean, default: false },
    label: { type: String, default: 'Tags' },
    parentId: { type: String, default: null },
});

const inputed = ref('');
const initial2 = 300;
const inputed2 = ref(initial2);

// @keydown.tab="addInput"
    // if (event.key === 'Tab') {
    //     event.preventDefault();
    const addInput = (event) => {
        const value = inputed.value.trim();
        if (value) {
            arr.value.push(`${value}-${inputed2.value}`);
            inputed.value = '';
            inputed2.value = initial2;
        }
};

// حذف تگ با index
const removeInput = (index) => {
    arr.value = arr.value.filter((_, i) => i !== index);
};
</script>

<template>
    <div class="row q-gutter-sm items-center">
        <q-input v-model="inputed" filled :readonly="readonly" :label="label" dense class="col" />
        <QuantityField v-model:quantity="inputed2" filled dense label="Quantity" :step="25" :readonly="readonly" class="col-5" />
        <q-btn icon="add" flat color="blue" style="border: 2px solid #1976d2" class="col-1" @click="addInput" />
    </div>
</template>

<style scoped></style>
