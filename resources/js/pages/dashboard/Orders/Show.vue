<template>
    <panel-layout>
        <!-- ---------------------------------------------------------------- -->
        <!--                        Header                                    -->
        <!-- ---------------------------------------------------------------- -->
        <template #header> </template>
        <!-- ---------------------------------------------------------------- -->
        <!--                        Left Sidebar                              -->
        <!-- ---------------------------------------------------------------- -->
        <template #left-drawer>
            <q-card>
                <q-card-section>
                    <div class="text-h5">Order Details</div>
                </q-card-section>
            </q-card>
        </template>
        <!-- ---------------------------------------------------------------- -->
        <!--                        Right Sidebar                             -->
        <!-- ---------------------------------------------------------------- -->
        <template #right-drawer> </template>

        <q-page padding>
            <div class="row q-mb-md">
                <div class="col-12">
                    <q-card>
                        <q-card-section>
                            <!-- ──────────────────────
                            ├   Notification Date
                            └─────────────────────── -->
                            <div class="row q-gutter-md">
                                <q-input
                                    v-model="order.notification_date"
                                    filled
                                    label="Notification Date"
                                    mask="####/##/##"
                                    readonly
                                    class="col q-mb-md"
                                />
                                <q-space />
                                <q-input
                                    v-model="order.month.name"
                                    filled
                                    label="Month"
                                    readonly
                                    class="q-mb-md col-5"
                                />
                            </div>

                            <!-- ──────────────────────
                            ├   Product & Quantity & Status
                            └─────────────────────── -->
                            <div class="row q-gutter-sm">
                                <q-input
                                    v-model="order.product.name"
                                    filled
                                    label="Product"
                                    readonly
                                    class="q-mb-md col-5"
                                />
                                <q-input
                                    v-model.number="order.quantity"
                                    filled
                                    type="number"
                                    label="Quantity"
                                    readonly
                                    class="q-mb-md col"
                                />
                                <q-input
                                    v-model="order.status"
                                    filled
                                    label="Status"
                                    readonly
                                    class="q-mb-md col-3"
                                />
                            </div>

                            <!-- ──────────────────────
                            ├   Description 
                            └─────────────────────── -->
                            <q-input
                                v-model="order.description"
                                filled
                                label="Description"
                                type="textarea"
                                :rows="2"
                                readonly
                                class="q-mb-md"
                            />

                            <!-- ──────────────────────
                            ├   Deadlines 
                            └─────────────────────── -->
                            <div class="deadline-container q-mt-xl">
                                <div class="deadline-label">Deadlines</div>
                                <q-list separator class="q-mx-md q-pb-md q-gutter-y-md">
                                    <q-item v-for="(deadline, index) in order.deadlines" :key="deadline.id">
                                        <q-item-section>
                                            <div class="row q-gutter-md">
                                                <!-- ──────────────────────
                                                ├   Part Quantity
                                                └─────────────────────── -->
                                                <q-input
                                                    v-model.number="deadline.part_quantity"
                                                    filled
                                                    type="number"
                                                    label="Part Quantity"
                                                    readonly
                                                    class="col"
                                                />
                                                <!-- ──────────────────────
                                                ├   Due Date
                                                └─────────────────────── -->
                                                <q-input
                                                    v-model="deadline.due_date"
                                                    filled
                                                    label="Due Date"
                                                    mask="####/##/##"
                                                    readonly
                                                    class="col-6"
                                                />
                                            </div>
                                            <!-- ──────────────────────
                                            ├   Description
                                            └─────────────────────── -->
                                            <q-input
                                                v-model="deadline.description"
                                                filled
                                                label="Description"
                                                readonly
                                                class="q-mt-md"
                                            />
                                        </q-item-section>
                                    </q-item>
                                </q-list>
                            </div>
                        </q-card-section>
                    </q-card>
                </div>
            </div>
        </q-page>
    </panel-layout>
</template>

<script setup>
//. Layouts
import PanelLayout from '@/Layouts/PanelLayout.vue';

defineProps({
  order: Object,
});
</script>

<style scoped>
.deadline-container {
  position: relative;
  border: 1px solid rgba(0, 0, 0, 0.12);
  border-radius: 8px;
  padding-top: 24px;
}

.deadline-label {
  position: absolute;
  top: -10px;
  left: 16px;
  padding: 0 8px;
  font-weight: bold;
  background: var(--q-color-page, white);
  color: var(--q-color-text, #9e9e9e);
}
</style>