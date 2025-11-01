<script setup>
import { ref } from 'vue'

const date = defineModel({ type: String })
const localValue = ref(date.value || '')

// -------------------------------------------
// ✅Validate Jalali date (simple structure & logic)
// -------------------------------------------
function validateDate(value) {
  if (!value) return true

  const parts = value.split('/')
  if (parts.length !== 3) return 'Invalid format (use YYYY/MM/DD)'

  let [y, m, d] = parts.map(Number)

  // numeric validation
  if (isNaN(y) || isNaN(m) || isNaN(d)) return 'Invalid numbers'

  // month check
  if (m < 1 || m > 12) return 'Month must be between 1 and 12'

  // day check depending on month
  const daysInMonth = m <= 6 ? 31 : 30
  if (d < 1 || d > daysInMonth) return `Day must be between 1 and ${daysInMonth}`

  return true
}

// -------------------------------------------
// ✅Normalize (format correction) function
// -------------------------------------------
function normalizeDate(value) {
  if (!value) return ''

  let [y, m, d] = value.split('/').map(x => x.trim())

  // pad month/day
  if (m?.length === 1) m = '0' + m
  if (d?.length === 1) d = '0' + d

  // normalize year
  if (y?.length === 1) y = '140' + y
  else if (y?.length === 2) y = '14' + y

  return `${y}/${m}/${d}`
}

// -------------------------------------------
// ✅Triggered when user leaves the input field
// -------------------------------------------
function handleBlur() {
  const valid = validateDate(localValue.value)
  if (valid !== true) return // do not modify if invalid

  const formatted = normalizeDate(localValue.value)
  localValue.value = formatted
  date.value = formatted // emit to parent
}

//mask="####/##/##"
</script>

<template>
  <q-input
    v-model="localValue"
    filled
    label="Date"
    :rules="[validateDate]"
    placeholder="Format: Year/Month/Day"
    class=""
    @blur="handleBlur"
  />
</template>
