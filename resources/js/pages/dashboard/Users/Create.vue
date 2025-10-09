<template>
  <main-layout>
    <q-page class="q-pa-md">
      <q-card class="q-pa-lg" flat bordered>
        <q-card-section>
          <div class="text-h6">Create User</div>
        </q-card-section>

        <q-card-section>
          <q-form @submit.prevent="submit">
            <!-- Gender -->
            <q-select
              v-model="form.gender"
              :options="genders"
              option-value="value"
              option-label="label"
              label="Gender"
              outlined
              dense
              class="q-mb-md"
              :error="!!form.errors.gender"
              :error-message="form.errors.gender"
            />

            <!-- First Name -->
            <q-input
              v-model="form.first_name"
              label="First Name"
              outlined
              dense
              class="q-mb-md"
              :error="!!form.errors.first_name"
              :error-message="form.errors.first_name"
            />

            <!-- Last Name -->
            <q-input
              v-model="form.last_name"
              label="Last Name"
              outlined
              dense
              class="q-mb-md"
              :error="!!form.errors.last_name"
              :error-message="form.errors.last_name"
            />

            <!-- Email -->
            <q-input
              v-model="form.email"
              label="Email"
              outlined
              dense
              class="q-mb-md"
              :error="!!form.errors.email"
              :error-message="form.errors.email"
            />

            <!-- Phone (optional) -->
            <q-input
              v-model="form.phone"
              label="Phone"
              outlined
              dense
              class="q-mb-md"
              :error="!!form.errors.phone"
              :error-message="form.errors.phone"
            />

            <!-- Description (optional) -->
            <q-input
              v-model="form.description"
              label="Description"
              type="textarea"
              autogrow
              outlined
              dense
              class="q-mb-md"
              :error="!!form.errors.description"
              :error-message="form.errors.description"
            />

            <!-- Buttons -->
            <div class="row q-gutter-sm">
              <q-btn label="Save" color="primary" type="submit" />
              <q-btn label="Cancel" color="secondary" flat :to="route('users.index')" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-page>
  </main-layout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const props = defineProps({
  genders: Array,
});

// inertia form
const form = useForm({
  gender: null,
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  description: '',
});

// submit handler
const submit = () => {
  form.post(route('users.store'), {
    onSuccess: () => {
      form.reset();
    },
  });
};
</script>
