<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
</script>
<script></script>
<template>
    <AppLayout>
        <template #header>แจ้งของหาย</template>
        <div class="form-control mb-5">
            <label class="input-group">
                <span class="material-symbols-rounded">
                    search
                </span>
                <input type="text" placeholder="ค้นหาของหายได้ที่นี่" class="input input-bordered w-full" />
            </label>
        </div>
        <div class="grid grid-cols-4 gap-5 overflow-auto" style="max-height: calc(100vh - 210px)">
            <div class="rounded-xl bg-white p-5" v-for="n in 10" :key="n">
                <p class="text-xl font-bold">โทรศัพท์มือถือยี่ห้อ Oppo</p>
                <p class="text-gray-500 mb-3">ใส่เคสสีฟ้าดำ พับครึ่งได้ ติดต่อรับได้ที่ห้องประชาสันพันธ์ค่ะ</p>
                <img src="/img/oppo.jpeg" alt="">
            </div>
        </div>
    </AppLayout>
</template>
