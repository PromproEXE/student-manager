<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
</script>
<script></script>
<template>
    <AppLayout>
        <div style="max-height: calc(100vh - 140px)" class="overflow-auto">
            <div class="rounded-xl bg-orange-200 px-5 pt-40 pb-3 mb-5">
                <p class="text-4xl font-bold">คอมพิวเตอร์</p>
            </div>
            <div class="grid grid-cols-4 gap-5">
                <div class="bg-white rounded-xl p-5 h-fit border">
                    <p class="font-bold mb-3">งานที่จะต้องส่ง</p>
                    <p class="text-gray-400 mb-3 text-center">ดีใจด้วย! คุณไม่มีงานที่จะต้องส่ง</p>
                    <div class="text-right">
                        <button class="btn btn-sm btn-ghost">ดูทั้งหมด</button>
                    </div>
                </div>
                <div class="col-span-3">
                    <a :href="route('homework_task_view')">
                        <div class="rounded-lg bg-white items-center border flex px-5 py-4 hover:bg-gray-50 hover:cursor-pointer mb-5"
                            v-for="n in 10" :key="n">
                            <span class="material-symbols-rounded text-5xl">
                                ballot
                            </span>
                            <div class="ml-3">
                                <p class="text-xl font-medium mb-0">โครงงานคอมพิวเตอร์ครั้งที่ 9999999999</p>
                                <small class="text-gray-500">2 ธันวาคม</small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
