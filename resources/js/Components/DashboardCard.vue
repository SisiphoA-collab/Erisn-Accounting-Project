<template>
    <div class="col-md-4">
        <div class="flex gap-3">
            <div
                :class="`text-${color} bg-white shadow rounded-2 p-4 relative overflow-hidden w-full`"
            >
                <h1 class="text-xl font-bold mb-2">{{ title }}</h1>
                <p class="text-lg">{{ value }}</p>

                <div class="absolute top-2 right-2 opacity-20 text-6xl">
                    <i :class="`fa fa-${icon}`"></i>
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
