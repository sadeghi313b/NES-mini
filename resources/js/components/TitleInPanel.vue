<script setup>
import { usePage } from '@inertiajs/vue3';
const page = usePage();
const icons = page.props.title.icons; 
const titleTexts = page.props.title.texts;
</script>

<template>
    <div class="q-mb-md q-gutter-sm row items-center">
        <q-icon :name="icons[0]" />
        <span v-for="(title, index) in titleTexts" class="text-h6" :key="index"> {{ title }} <span v-if="index != titleTexts.length - 1"> - </span> </span>
        <q-icon v-for="(icon, index) in icons.slice(1)" :key="index" :name="icon" />
    </div>
</template>

<style scoped></style>
