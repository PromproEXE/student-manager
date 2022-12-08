<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
</script>
<script>
export default {
    data() {
        return {
            startAbsent: new Date(),
            endAbsent: "",
            absentType: ""
        }
    }
}
</script>
<template>
    <AppLayout>
        <template #header>แจ้งการลา</template>
        <form @submit.prevent="">
            <div class="grid grid-cols-3 gap-5 mb-5">
                <div class="rounded-xl bg-white p-4">
                    <p class="text-primary text-2xl font-bold mb-3">เลือกวันลา</p>
                    <input type="date" class="input input-bordered w-full" v-model="startAbsent">
                </div>
                <div class="rounded-xl bg-white p-4">
                    <p class="text-primary text-2xl font-bold mb-3">เลือกประเภทการลา</p>
                    <select id="" class="select select-bordered w-full" v-model="absentType">
                        <option value="" disabled selected>--ประเภทการลา--</option>
                        <option value="ลากิจ">ลากิจ</option>
                        <option value="ลาป่วย">ลาป่วย</option>
                        <option value="ลาไปทำกิจกรรม">ลาไปทำกิจกรรม</option>
                        <option value="อื่น ๆ">อื่น ๆ</option>
                    </select>
                </div>
                <div class="rounded-xl bg-white p-4">
                    <p class="text-primary text-2xl font-bold mb-3">รายละเอียดการลา</p>
                    <textarea rows="10" placeholder="รายละเอียด" class="w-full input input-bordered"></textarea>
                </div>
            </div>
            <div class="mb-5 grid grid-cols-2 gap-5">
                <button class="btn btn-success w-full text-xl" type="submit">ส่งคำขอการลา</button>
                <button class="btn btn-error w-full text-xl" type="reset">ล้างข้อมูล</button>
            </div>
        </form>
        <div class="bg-white p-4 rounded-xl mb-5">
            <p class="text-2xl text-primary font-bold mb-5">คำขอการลา</p>
            <div class="text-center my-8">
                <p class="text-6xl text-gray-400 mb-3">¯\_(ツ)_/¯</p>
                <p class="text-xl">คุณไม่มีประวัติการขอลา</p>
                <p class="text-gray-400">พยายามอย่าให้มีวันลาล่ะ</p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl">
            <p class="text-2xl text-primary font-bold mb-5">ประวัติการลา</p>
            <div class="text-center my-8">
                <p class="text-6xl text-gray-400 mb-3">¯\_(ツ)_/¯</p>
                <p class="text-xl">คุณไม่มีประวัติการลา</p>
                <p class="text-gray-400">พยายามอย่าให้มีวันลาล่ะ</p>
            </div>
        </div>
    </AppLayout>
</template>
