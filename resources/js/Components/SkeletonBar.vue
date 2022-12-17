<script setup>
defineProps({
    bg: {
        type: String,
        default: 'bg-gray-200'
    }
})
</script>

<template>
    <div role="status" class="rounded-xl animate-pulse" :class="bg"></div>
</template>
