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
                            <button class="btn btn-success text-lg">มอบหมายงาน</button>
                        </div>
                        <!-- <div>
                            <div class="btn-group">
                                <button class="btn btn-success">มอบหมายงาน</button>
                                <div class="dropdown">
                                    <label tabindex="0" class="btn btn-success px-1"
                                        style="border-radius: 0px 0.5rem 0.5rem 0px !important;"><span
                                            class="material-symbols-rounded">
                                            expand_more
                                        </span></label>
                                    <ul tabindex="0"
                                        class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52">
                                        <li><a>Item 1</a></li>
                                        <li><a>Item 2</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div> -->
                    </div>
                    <div class="rounded-xl bg-white p-5 mb-5">
                        <label for="" class="text-gray-500">
                            <p class="text-lg">ชื่องาน</p>
                        </label>
                        <input type="text" class="input input-bordered w-full" v-model="title">
                    </div>
                    <div class="rounded-xl bg-white p-5 mb-5">
                        <label for="" class="text-gray-500">
                            <p class="text-lg">รายละเอียด</p>
                        </label>
                        <textarea class="input input-bordered w-full" style="height: 100px"
                            placeholder="รายละเอียดของงาน"></textarea>
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
                    <label for="">
                        <p class="mb-3">คะแนน</p>
                    </label>
                    <input list="score" class="input input-bordered w-full mb-3" v-model="score">
                    <datalist id="score">
                        <option value="ไม่มีคะแนน"></option>
                    </datalist>
                    <label for="">
                        <p class="mb-3">กำหนดส่ง</p>
                    </label>
                    <input type="date" class="input input-bordered w-full">
                </div>
            </div>
        </div>
    </AppLayout>
</template>
