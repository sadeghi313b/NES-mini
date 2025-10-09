// Clg.vue
<script setup>
const props=defineProps({
    vars: Object,
    where: String,
});
if (props.where == 'console') {
    Object.entries(props.vars).forEach(([key,value]) => {
        console.log(`${key}:` , value);
    });
}
</script>



<template>
    <div v-if="where != 'console'">
        <div v-for="(value,key) in vars" :key="key">
            <pre>{{ key }} &lt;{{ typeof(vars) }}&gt; = {{ value }}</pre>
            <hr>
        </div>
    </div>
</template>



<style scoped></style>