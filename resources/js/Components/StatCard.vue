<template>
    <div :class="`p-2 text-${color} bg-light rounded-lg col text-center items-center`">
        <i  :class="`fas fa-${icon} fs-3 mb-1`"></i>
        <h3 class="fs-3">{{ title }}</h3>
        <p class="p-0 m-auto">{{ description }}</p>
        <span class="fs-3 p-0 m-auto"><strong>{{ value }}</strong></span>
    </div>
</template>

<script setup>
defineProps({
    icon: String,
    title: String,
    description: String,
    value: [String, Number],
    color: String
});
</script>
