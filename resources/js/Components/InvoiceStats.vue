<template>
    <div class="d-flex gap-3">
        <div v-for="stat in invoiceStats" :key="stat.status"
            :class="statusColors[stat.status] + ' card flex-grow-1 rounded-2 shadow p-1 position-relative overflow-hidden'">
            <h1 class="display-5 fw-bold">{{ stat.invoices_stats }}</h1>
            <p class="fs-5 text-uppercase">{{ stat.status }} Invoices</p>

            <div class="position-absolute top-0 end-0 opacity-25 display-1">
                <span class="pe-2">{{ stat.status }}</span>
            </div>
        </div>
    </div>
</template>



<script setup>
defineProps({
    invoiceStats: Array
});

const statusColors = {
    'Draft': 'bg-custom-blue',
    'Paid': 'bg-custom-green',
    'Sent': 'bg-custom-yellow',
    'Overdue': 'bg-custom-red'
};
</script>

