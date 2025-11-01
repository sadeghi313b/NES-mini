<script setup>
/**
 * import SIndexedRelatedListPicker from '@/Components/SIndexedRelatedListPicker.vue';
 * <SIndexedRelatedListPicker
      v-model="selectedValues"       
      :data="myData"                  
      :index-value1="fixedIndex1"    
      :index-value2="fixedIndex2"     
      label1="Category"               
      label2="Detail"                  
      label3="Extra"                   
      class1="col-3"
      class2="col-3"
      class3="col-3"
    />
 */
// SIndexedRelatedListPicker.vue
// data → id, set1, set2, set3
// dataField1 from set1, dataField2 from set2, dataField3 from set3
// fixedValue1, fixedValue2
// selected1(default: indexValue), selected2(default: fixedValue2), selected3
// label1, label2, label3
// class1, class2, class3
// filteredOptions2, filteredOptions3
// if fixedValue1 is passed from parent then dont show its field : selected1=fixedValue1
// if fixedValue2 is passed from parent then dont show its field : selected2=fixedValue2
// const selected1, selected2, selected3

import OptionalSelect from '@/Components/OptionalSelect.vue';
import { computed, defineModel, defineProps, onMounted, ref, watch } from 'vue';

// -------------------------------------------------
// Model synced with parent using defineModel
// -------------------------------------------------
const selected1 = defineModel('selected1');
const selected2 = defineModel('selected2');
const selected3 = defineModel('selected3');

// -------------------------------------------------
// Props
// -------------------------------------------------
const props = defineProps({
    readonly: { type: Boolean, default: false },

    data: {
        type: Array,
        required: true,
        default: () => [],
        // Each element: { id, set1, set2, set3 }
    },
    dataField1: { type: String, default: 'set1' }, //date.month_name
    dataField2: { type: String, default: 'set2' }, //data.product_id
    dataField3: { type: String, default: 'set3' }, //data.id

    label1: { type: String, default: 'dataField1' },
    label2: { type: String, default: 'dataField2' },
    label3: { type: String, default: 'dataField3' },

    class1: { type: String, default: 'col-3' },
    class2: { type: String, default: 'col-3' },
    class3: { type: String, default: 'col-3' },

    fixedValue1: { type: [String, Number], default: null },
    fixedValue2: { type: [String, Number], default: null },
    fixedValue3: { type: [String, Number], default: null },
});
// console.log(props.data);

// -------------------------------------------------
// Computed: unique options for dataField1
// -------------------------------------------------
const options1 = computed(() => {
    if (props.label1 == 'dont show') {
      return [{ label: props.fixedValue1, value: props.fixedValue1 }];
    }
    const unique = new Set(props.data.map((i) => i[props.dataField1]));
    return [...unique].map((v) => ({ label: v, value: v }));
});

// -------------------------------------------------
// Computed: filtered options for dataField2
// -------------------------------------------------
const options2 = computed(() => {
    if (props.label2 == 'dont show') {
        selected2.value = fixedValue2;
        return [{ label: props.fixedValue2, value: props.fixedValue2 }];
    }
    const related = props.data.filter((i) => selected1.value === i[props.dataField1]);
    const unique = new Set(related.map((i) => i[props.dataField2]));
    return [...unique].map((v) => ({ label: v, value: v }));
});

// -------------------------------------------------
// Computed: filtered options for dataField3
// -------------------------------------------------
const options3 = computed(() => {
    const related = props.data.filter((i) => {
        let match = true;
        let cell1 = selected1.value;
        let cell2 = selected2.value;
        if (cell1 && cell1.value) match = match && i[props.dataField1] == cell1.value;
        if (cell2 && cell2.value) match = match && i[props.dataField2] == cell2.value;
        return match;
    });
    const unique = new Set(related.map((i) => i[props.dataField3]));
    return [...unique].map((v) => ({ label: v, value: v }));
});
// console.log(options3);

// -------------------------------------------------
// Watchers: clear dependent fields
// -------------------------------------------------
switch (true) {
    case props.label1.value === 'dont show':
        selected1.value = fixedValue1.value;
        break;
    case props.label2.value === 'dont show':
        selected2.value = fixedValue2.value;
        break;
    default:
        break;
}

watch(
    () => selected1.value,
    () => {
        if (props.fixedValue2 === null) selected2.value = null;
        selected3.value = null;
    },
);

watch(
    () => selected2.value,
    () => {
        selected3.value = null;
    },
);

const clgTarget = ref(null);
onMounted(() => {
    // Make sure the element exists in DOM
    const el = document.getElementById('clg');
    if (el) clgTarget.value = '#clg';
});
</script>

<template>
    <div class="q-gutter-x-sm flex items-center">
        <!-- dataField1 -->
        <q-select
            v-if="label1 != 'dont show'"
            v-model="selected1"
            :options="options1"
            :label="label1" emit-value map-options
            :readonly="readonly"
            dense
            outlined
            :class="class1"
        />
        <!-- <div v-else class="col">{{ fixedValue1 }}</div> -->

        <!-- dataField2 -->
        <q-select v-if="label2 != 'dont show'" v-model="selected2" :options="options2" :label="label2" :readonly="readonly" dense :class="class2" />
        <!-- <div v-else class="col">{{ fixedValue2 }}</div> -->

        <!-- dataField3 -->
        <optional-select v-model="selected3" :options="options3" :label="label3" :readonly="readonly" dense :class="class3" />
    </div>
</template>
