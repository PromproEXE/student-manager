<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import * as role from '@/role'
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

    <AppLayout title="แจ้งการลา">
        <template #header>{{ !role.isAdmin($page.props.user.role) ? 'แจ้งการลา' : 'ประวัติการลา' }}</template>
        <div class="overflow-auto" style="max-height: calc(100vh - 140px)">
            <form @submit.prevent="" v-if="role.isStudent($page.props.user.role)">
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
                <div class="flex mb-5" v-if="role.isAdmin($page.props.user.role)">
                    <div class="mr-3">
                        <label for="" class="mr-2">ระดับชั้น:</label>
                        <select class="select select-bordered">
                            <option>ทั้งหมด</option>
                            <option v-for="n in 6" :key="n">ม.{{ n }}</option>
                        </select>
                    </div>
                    <div class="mr-3">
                        <label for="" class="mr-2">ห้อง:</label>
                        <select class="select select-bordered">
                            <option>ทั้งหมด</option>
                            <option v-for="n in 14" :key="n">{{ n }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="" class="mr-2">ชื่อนักเรียน:</label>
                        <select class="select select-bordered">
                            <option>ทั้งหมด</option>
                            <option>ชาญวิช มาศมัณฑนะ</option>
                            <option>รักษิต รุ่งรัตนไชย</option>
                            <option>เกียรติศักดิ์ มากมีทรัพย์</option>
                        </select>
                    </div>
                </div>
                <div class="text-center my-8">
                    <p class="text-6xl text-gray-400 mb-3">¯\_(ツ)_/¯</p>
                    <p class="text-xl">คุณไม่มีประวัติการขอลา</p>
                    <p class="text-gray-400">พยายามอย่าให้มีวันลาล่ะ</p>
                </div>
            </div>
            <div class="bg-white p-4 rounded-xl">
                <p class="text-2xl text-primary font-bold mb-5">ประวัติการลา</p>
                <div class="flex mb-5" v-if="role.isAdmin($page.props.user.role)">
                    <div class="mr-3">
                        <label for="" class="mr-2">ระดับชั้น:</label>
                        <select class="select select-bordered">
                            <option>ทั้งหมด</option>
                            <option v-for="n in 6" :key="n">ม.{{ n }}</option>
                        </select>
                    </div>
                    <div class="mr-3">
                        <label for="" class="mr-2">ห้อง:</label>
                        <select class="select select-bordered">
                            <option>ทั้งหมด</option>
                            <option v-for="n in 14" :key="n">{{ n }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="" class="mr-2">ชื่อนักเรียน:</label>
                        <select class="select select-bordered">
                            <option>ทั้งหมด</option>
                            <option>ชาญวิช มาศมัณฑนะ</option>
                            <option>รักษิต รุ่งรัตนไชย</option>
                            <option>เกียรติศักดิ์ มากมีทรัพย์</option>
                        </select>
                    </div>
                </div>
                <div class="text-center my-8">
                    <p class="text-6xl text-gray-400 mb-3">¯\_(ツ)_/¯</p>
                    <p class="text-xl">คุณไม่มีประวัติการลา</p>
                    <p class="text-gray-400">พยายามอย่าให้มีวันลาล่ะ</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
