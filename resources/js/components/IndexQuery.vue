// IndexQuery.vue
<script setup>
const props = defineProps({
    filterables: Object,
});
const criteria = defineModel('criteria', Object);

function resetSearches() {
    for (const key in criteria.value.keywords) {
        criteria.value.keywords[key] = '';
    }
}

function resetFilters() {
    if (Object.keys(criteria.value.selections).length) {
        Object.keys(criteria.value.selections).forEach((key) => delete criteria.value.selections[key]);
    }
}
</script>

<template>
    <!-- -------------------------------- searches -------------------------------- -->
    <div>
        <q-icon name="search" /> Searches:
        <div v-for="(value, key) in criteria.keywords" :key="key">
            <q-input outlined dense v-model="criteria.keywords[key]" :label="key" clearable />
        </div>
        <!-- Reset Button -->
        <div class="">
            <q-btn label="Reset Searches" color="negative" flat icon="refresh" @click="resetSearches" />
        </div>
    </div>
    <!-- --------------------------------- filters -------------------------------- -->
    <div>
        <q-icon name="filter_alt" /> Filters:
        <div v-for="(value, key) in filterables" :key="key">
            <q-select
                v-model="criteria.selections[key]"
                :label="key"
                :options="value.options"
                :multiple="value.multiple"
                use-input
                outlined
                dense
                input-debounce="300"
                clearable
                use-chips
                stack-label
            >
            </q-select>
        </div>
        <!-- Reset Button -->
        <div class="">
            <q-btn label="Reset Filters" color="negative" flat icon="refresh" @click="resetFilters" />
        </div>
    </div>
</template>

<style scoped></style>
