<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
</script>
<script>
export default {
    data() {
        return {
            teachers: [{
                id: 1,
                std_id: '09362',
                name: 'ชาญวิช มาศมัณฑนะ',
                class: '6',
                room: '3',
                class_responsible: 'ภาษาไทย, ภาษาอังกฤษ, คณิตศาสตร์, คอมพิวเตอร์, วิทยาศาสตร์',

            },]
        }
    }
}
</script>
<template>
    <AppLayout>
        <template #header>จัดการอาจารย​์</template>
        <div class="bg-white rounded-xl p-5" v-if="$page.props.user.admin">
            <div class="form-control mb-3">
                <label class="input-group">
                    <span class="material-symbols-rounded">
                        search
                    </span>
                    <input type="text" placeholder="ค้นหาด้วยชื่ออาจารย์" class="input input-bordered w-full" />
                </label>
            </div>
            <div class="flex mb-3">
                <div class="mr-3">
                    <label for="" class="mr-2">กลุ่มสาระ</label>
                    <select class="select select-bordered">
                        <option>ทั้งหมด</option>
                        <option v-for="n in 6" :key="n">ม.{{ n }}</option>
                    </select>
                </div>
            </div>
            <table class="table table-zebra w-full">
                <thead>
                    <tr>
                        <th></th>
                        <th class="text-lg">ชื่อ-นามสกุล</th>
                        <th class="text-lg text-center">วิชาที่สอน</th>
                        <th class="text-right">
                            <button class="btn btn-sm btn-success"><span class="material-symbols-rounded mr-2">
                                    add
                                </span>เพิ่มอาจารย์</button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="hover" v-for="teacher in teachers" :key="teacher.id">
                        <td class="text-center">
                            <input type="checkbox" class="checkbox checkbox-sm">
                        </td>
                        <td>{{ teacher.name }}</td>
                        <td class="text-center">{{ teacher.class_responsible }}</td>
                        <td>
                            <button class="btn btn-warning mr-2"><span class="material-symbols-rounded mr-2">
                                    edit
                                </span>แก้ไขข้อมูล</button>
                            <button class="btn btn-error"><span class="material-symbols-rounded mr-2">
                                    delete
                                </span>ลบอาจารย์</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
