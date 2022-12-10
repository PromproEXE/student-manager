<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
</script>
<script></script>
<template>
    <AppLayout>
        <div style="max-height: calc(100vh - 140px)" class="overflow-auto">
            <div class="rounded-xl bg-yellow-200 px-5 pt-40 pb-3 mb-5">
                <p class="text-4xl font-bold">ม.6/3</p>
            </div>
            <div class="grid grid-cols-4 gap-5">
                <div class="col-span-3">
                    <div class="rounded-lg bg-white p-5">
                        <p class="text-3xl font-bold mb-5">รายชื่อนักเรียนในห้อง</p>
                        <div class="rounded-lg border mb-3 p-5 flex hover:bg-gray-50 cursor-pointer" v-for="n in 3"
                            :key="n">
                            <img src="/img/account.png" style="width: 64px" alt="std_img" class="mr-5">
                            <div>
                                <p class="text-2xl font-bold">นายชาญวิช มาศมัณฑนะ</p>
                                <p>วันเกิด: <span class="text-gray-400">27/02/2548</span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center rounded-xl p-5 bg-white h-fit">
                    <p class="text-2xl font-bold">อาจารย์ที่ปรึกษา</p>
                    <img src="" class="w-full" alt="">
                    <p>นางสาวสุธาทิพย์ ผลไสว</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
