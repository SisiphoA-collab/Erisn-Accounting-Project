<script setup>
import { ref, watch, defineProps } from 'vue'

const props = defineProps({
  message: String,
  messageType: String
})

const localMessage = ref('')
const flash = ref('')
const visible = ref(false)
const emit = defineEmits(['cleared','close']) // or ['close', 'clear'] depending on what you want

watch(
  () => props.message,
  (newVal, _, onCleanup) => {
    if (!newVal) return

    localMessage.value = newVal
    visible.value = true

    flash.value =
      props.messageType === 'error'
        ? 'bg-danger-subtle text-danger-emphasis'
        : props.messageType === 'success'
        ? 'bg-success-subtle text-success-emphasis'
        : 'bg-primary-subtle text-primary-emphasis'

    const timeout = setTimeout(() => {
      visible.value = false
      localMessage.value = ''
	  emit('cleared')
    }, 3000)

    // Cleanup in case message changes before 3s is up
    onCleanup(() => clearTimeout(timeout));
  },
  { immediate: true }
)
</script>

<template>
	<Transition name="fade">
		<div v-if="localMessage !== ''" :class="['p-3 rounded d-flex mx-4 text-sm', flash]" role="alert">
			<span class="flex-fill">{{ localMessage }}</span>
			<button type="button" class="btn-close" aria-label="Close" @click="$emit('close')"></button>
		</div>
	</Transition>
</template>
