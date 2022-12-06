<script setup>
import AppLayout from '../Layouts/AppLayout.vue';
</script>

<template>
    <AppLayout>
        <div class="p-5">
            <div class="bg-white rounded-xl text-neutral p-3">
                <p class="text-4xl font-bold text-primary">สวัสดีคุณ{{ $page.props.user.name }}</p>
            </div>
        </div>
    </AppLayout>
</template>

<style>

</style>
