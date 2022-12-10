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
            <div class="rounded-xl bg-white p-5 mb-5">
                <div class="flex items-center justify-between">
                    <p class="text-3xl font-bold">{{ title }}</p>
                    <button class="btn btn-warning text-lg">ประกาศ</button>
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
                    <p class="text-lg">ชื่อสิ่งของที่หาย</p>
                </label>
                <input type="text" class="input input-bordered w-full" v-model="title">
            </div>
            <div class="rounded-xl bg-white p-5 mb-5">
                <label for="" class="text-gray-500">
                    <p class="text-lg">รายละเอียด</p>
                </label>
                <textarea class="input input-bordered w-full" style="height: 100px"
                    placeholder="รายละเอียดของสิ่งที่หาย"></textarea>
            </div>
            <div class="rounded-xl bg-white p-5">
                <label for="" class="text-gray-500">
                    <p class="text-lg">แนบไฟล์/ลิงก์</p>
                </label>
                <div class="flex justify-center">
                    <button class="btn btn-outline btn-neutral mr-3"><span class="material-symbols-rounded mr-3">
                            description
                        </span>ไฟล์</button>
                    <button class="btn btn-outline btn-neutral"><span class="material-symbols-rounded mr-3">
                            link
                        </span>ลิงก์</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
