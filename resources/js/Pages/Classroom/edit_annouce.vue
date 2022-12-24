<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
</script>
<script>
export default {
    data() {
        return {
            title: 'ไม่มีชื่อ',
            score: 'ไม่มีคะแนน',
        }
    },
    methods: {

    }
}
</script>
<template>
    <AppLayout>
        <div class="overflow-auto" style="max-height: calc(100vh - 140px)">
            <div class="grid grid-cols-3 gap-5">
                <!-- MAIN -->
                <div class="col-span-2">
                    <div class="rounded-xl bg-white p-5 mb-5">
                        <div class="flex items-center justify-between">
                            <p class="text-3xl font-bold">{{ title }}</p>
                            <button class="btn btn-warning text-lg">ประกาศ</button>
                        </div>
                    </div>
                    <div class="rounded-xl bg-white p-5 mb-5">
                        <label for="" class="text-gray-500">
                            <p class="text-lg">หัวข้อประกาศ</p>
                        </label>
                        <input type="text" class="input input-bordered w-full" v-model="title">
                    </div>
                    <div class="rounded-xl bg-white p-5 mb-5">
                        <label for="" class="text-gray-500">
                            <p class="text-lg">รายละเอียด</p>
                        </label>
                        <textarea class="input input-bordered w-full" style="height: 100px"
                            placeholder="รายละเอียดของประกาศ"></textarea>
                    </div>
                    <div class="rounded-xl bg-white p-5">
                        <label for="" class="text-gray-500">
                            <p class="text-lg">แนบไฟล์/ลิงก์</p>
                        </label>
                        <div class="flex justify-center">
                            <button class="btn btn-outline btn-neutral mr-3"><span
                                    class="material-symbols-rounded mr-3">
                                    description
                                </span>ไฟล์</button>
                            <button class="btn btn-outline btn-neutral"><span class="material-symbols-rounded mr-3">
                                    link
                                </span>ลิงก์</button>
                        </div>
                    </div>
                </div>

                <!-- SIDEBAR -->
                <div class="rounded-xl bg-white p-5 h-fit">
                    <label for="">
                        <p class="mb-3">สำหรับวิชา</p>
                    </label>
                    <select class="select select-bordered w-full mb-3">
                        <option value="">ภาษาไทย</option>
                        <option value="">ภาษาอังกฤษ</option>
                        <option value="">คณิตศาสตร์</option>
                        <option value="">คอมพิวเตอร์</option>
                        <option value="">วิทยาศาสตร์</option>
                    </select>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
