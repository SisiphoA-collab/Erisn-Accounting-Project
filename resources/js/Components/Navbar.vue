<template>
    <!-- navbar  -->
    <div class="border flex-grow-1 py-3 bg-nav d-flex"
        :style="{ marginLeft: props.collapsed ? '65px' : '250px', transition: 'margin-left 0.3s' }">
        <button @click.prevent="props.collapsed = !props.collapsed" class="btn btn-outline-primary border-none">
            <i class="fas fa-home"></i>
            <span class=""> {{ props.page }} </span></button>
        <div class="col text-end border text-white">
            agsg
        </div>
    </div>
</template>
<script setup>
const props = defineProps({
    page: {
        type: String,
        default: 'Dashboard'
    },
    collapsed: {
        type: Boolean,
        default: true
    }
})
</script>

