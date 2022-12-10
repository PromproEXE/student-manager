<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
</script>
<script></script>
<template>
    <AppLayout>
        <div class="overflow-auto" style="max-height: calc(100vh - 140px)">
            <div class="rounded-xl bg-white p-5 flex items-center justify-between mb-5">
                <div class="flex items-center">
                    <span class="material-symbols-rounded text-5xl mr-5">
                        ballot
                    </span>
                    <div>
                        <p class="text-3xl font-bold">โครงงานคอมพิวเตอร์ครั้งที่ 9999999999999</p>
                        <p class="text-gray-400">สุธาทิพย์ ผลไสว | 2 ธันวาคม</p>
                    </div>
                </div>
                <div class="rounded-lg p-3 bg-green-100">
                    <p class="text-green-500 font-bold text-2xl">15 คะแนน</p>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-5">
                <div class="rounded-xl p-5 bg-white col-span-2">
                    <p class="font-bold text-2xl">รายละเอียด</p>
                    <p class="mb-5">ให้นักเรียนทำโครงงาน 1 เรื่องภายใน 1 วัน</p>
                    <p class="font-bold text-2xl">ไฟล์ที่แนบมาด้วย</p>
                    <p class="mb-5">ให้นักเรียนทำโครงงาน 1 เรื่องภายใน 1 วัน</p>
                    <p class="font-bold text-2xl">ลิงก์ที่แนบมาด้วย</p>
                    <p>ให้นักเรียนทำโครงงาน 1 เรื่องภายใน 1 วัน</p>
                </div>
                <div class="rounded-xl bg-white p-5">
                    <div class="flex items-end justify-between mb-3">
                        <p class="text-2xl font-bold">งานของคุณ</p>
                        <p class="text-error">ยังไม่ส่ง</p>
                    </div>
                    <button
                        class="btn btn-ghost font-normal btn-outline border-gray-300 text-lg w-full mb-3">เพิ่มงาน</button>
                    <button class="btn btn-success text-lg w-full">เสร็จสิ้น</button>
                </div>
                <div class="col-span-2 rounded-xl p-5 bg-white">
                    <p class="font-bold mb-3">คอมเมนต์ของงานนี้</p>
                    <div class="grid grid-cols-5 gap-5">
                        <textarea type="text" class="input input-bordered col-span-4 w-full"
                            placeholder="เขียนคอมเมนต์ที่นี่"></textarea>
                        <button class="btn btn-primary">คอมเมนต์</button>
                    </div>
                </div>
                <div class="rounded-xl p-5 bg-white">
                    <p class="font-bold mb-3">คอมเมนต์ส่วนตัว</p>
                    <input type="text" class="input input-bordered w-full mb-3" placeholder="เขียนคอมเมนต์ที่นี่">
                    <button class="btn btn-secondary w-full">คอมเมนต์</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
