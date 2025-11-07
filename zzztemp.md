<!-- ----------------------------------------------------------------------- -->
<!--                                   q-date                                -->
<!-- ----------------------------------------------------------------------- -->
<q-input
                                        v-model="form.notification_date"
                                        filled
                                        :readonly="readonly"
                                        label="Notification Date"
                                        mask="####/##/##"
                                        :rules="['date']"
                                        class="q-mb-md col-5"
                                    >
                                        <template v-slot:append>
                                            <q-icon name="event" class="cursor-pointer">
                                                <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                                    <!-- -------------------------------- QDate -------------------------------- -->
                                                    <q-date
                                                        v-model="form.notification_date"
                                                        mask="YYYY/MM/DD"
                                                        title="تاریخ ثبت سفارش"
                                                        subtitle="email date"
                                                        calendar="persian"
                                                        locale="fa-ir"
                                                    >
                                                        <div class="row items-center justify-end">
                                                            <q-btn v-close-popup label="Close" color="primary" flat />
                                                        </div>
                                                    </q-date>
                                                </q-popup-proxy>
                                            </q-icon>
                                        </template>
                                    </q-input>
<!-- ----------------------------------------------------------------------- -->
<!--                               back botton                               -->
<!-- ----------------------------------------------------------------------- -->
v-if="['show','edit'].includes(routeMethod)"
<!-- ----------------------------------------------------------------------- -->
<!--                                 options                                 -->
<!-- ----------------------------------------------------------------------- -->
const activityOptions = computed(() =>
    activities.map((a) => ({
        label: a.name,
        value: a.id,
    })),
);

const selectedZone = ref('');
const zoneOptions = computed(() =>
    activities.map((a) => ({
        label: a.zone,
        value: a.id,
    })),
);

const batchOptions = computed(() =>
    batches.map((b) => ({
        label: `Batch ${b.id} - Size: ${b.size}`,
        value: b.id,
    })),
);
<!-- ----------------------------------------------------------------------- -->
<!--                                aggregate                                -->
<!-- ----------------------------------------------------------------------- -->
// Example: fetch aggregated orders per month
        $months = Month::with(['orders.product', 'orders.cuts', 'orders.productions'])->get();

        $aggregatedOrders = $months->map(function ($month) {
            return $month->orders->map(function ($order) use ($month) {
                return [
                    'month_name' => $month->month_name,
                    'order_id' => $order->id,
                    'product_name' => $order->product?->name,
                    'order_quantity' => $order->quantity,
                    'total_cuts' => $order->cuts->sum('quantity'),
                    'total_productions' => $order->productions->sum('quantity'),
                ];
            });
        })->flatten();