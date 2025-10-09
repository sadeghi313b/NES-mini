// FormSubmit.vue
<script setup></script>

<template>
    <div class="q-mt-md">
        <q-card-actions v-if="!readonly" align="right">
            <q-btn label="Submit" type="submit" icon="send" color="primary" />
        </q-card-actions>
        <q-card-actions v-if="['show'].includes(routeMethod)" align="right">
            <q-btn label="Back" @click="router.visit(route('dashboard.cuts.index'))" icon="arrow_back" color="brown" />
        </q-card-actions>
    </div>
</template>

<style scoped></style>
