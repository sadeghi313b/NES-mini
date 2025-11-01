<script setup>
// import QuantityField from '@/Components/QuantityField.vue';

const quantity = defineModel('quantity', Number);
const props = defineProps({
    readonly: {
        type: Boolean,
        default: false,
    },
    step: {
        type: Number,
        default: 500,
    },
    label: {
        type: String,
        default: 'Quantity',
    },
    bottomSize: {
        type: String,
        default: 'sm',
    },
});

const initial_quantity = quantity.value;

function increase() {
    quantity.value = (quantity.value || 0) + props.step;
}

function decrease() {
    quantity.value = (quantity.value || 0) - props.step;
}
</script>

<template>
    <q-input v-model.number="quantity" filled :readonly="readonly" :label="label">
        <template v-if="!readonly" v-slot:append>
            <q-btn flat dense round :size="bottomSize" icon="remove" @click="decrease" />
            <q-btn flat dense round :size="bottomSize" icon="add" @click="increase" />
            <q-btn flat dense round :size="bottomSize" icon="refresh" @click="quantity = initial_quantity" />
        </template>
    </q-input>
</template>
