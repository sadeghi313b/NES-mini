<script setup>
import { Link } from '@inertiajs/vue3'

// Props مشابه QBtn + href مخصوص Inertia
const props = defineProps({
  href: {
    type: String,
    required: true
  },
  color: {
    type: String,
    default: 'primary'
  },
  label: {
    type: String,
    default: ''
  },
  icon: {
    type: String,
    default: ''
  },
  flat: {
    type: Boolean,
    default: false
  },
  round: {
    type: Boolean,
    default: false
  },
  dense: {
    type: Boolean,
    default: false
  },
  unelevated: {
    type: Boolean,
    default: false
  },
  outline: {
    type: Boolean,
    default: false
  }
})
</script>

<template>
  <!-- Link از Inertia -->
  <Link :href="href" style="text-decoration: none">
    <q-btn
      :color="color"
      :label="label"
      :icon="icon"
      :flat="flat"
      :round="round"
      :dense="dense"
      :unelevated="unelevated"
      :outline="outline"
    />
  </Link>
</template>
