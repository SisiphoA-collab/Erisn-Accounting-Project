<template>
    <div class="col-md-4">
        <div class="d-flex gap-3">
            <div :class="`text-${color} card flex-grow-1 rounded-2 p-1 position-relative overflow-hidden`">
                <h1 class="card-title">{{ title }}</h1>
                <p class="card-text">{{ value }}</p>

                <div class="position-absolute top-0 end-0 opacity-25 display-1">
                    <span class="pe-2"><i :class="`fa fa-${icon}`" /></span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    icon: String,
    title: String,
    value: [String, Number],
    color: String
});
</script>
