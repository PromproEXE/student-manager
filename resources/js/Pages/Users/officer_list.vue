<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
</script>
<script>
export default {
    data() {
        return {
            officers: []
        }
    }
}
</script>
<template>

    <Head title="จัดการเจ้าหน้าที่"></Head>
    <AppLayout>
        <template #header>จัดการเจ้าหน้าที่</template>
        <div class="bg-white rounded-xl p-5" v-if="$page.props.user.admin">
            <div class="form-control mb-3">
                <label class="input-group">
                    <span class="material-symbols-rounded">
                        search
                    </span>
                    <input type="text" placeholder="ค้นหาด้วยชื่อเจ้าหน้าที่" class="input input-bordered w-full" />
                </label>
            </div>
            <!-- <div class="flex mb-3">
                <div class="mr-3">
                    <label for="" class="mr-2">ระดับชั้น</label>
                    <select class="select select-bordered">
                        <option>ทั้งหมด</option>
                        <option v-for="n in 6" :key="n">ม.{{ n }}</option>
                    </select>
                </div>
                <div class="mr-3">
                    <label for="" class="mr-2">ห้อง</label>
                    <select class="select select-bordered">
                        <option>ทั้งหมด</option>
                        <option v-for="n in 14" :key="n">{{ n }}</option>
                    </select>
                </div>
            </div> -->
            <table class="table table-zebra w-full">
                <thead>
                    <tr>
                        <th></th>
                        <th class="text-lg">ชื่อ-นามสกุล</th>
                        <th class="text-right">
                            <button class="btn btn-sm btn-success"><span class="material-symbols-rounded mr-2">
                                    add
                                </span>เพิ่มนักเรียน</button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="hover" v-for="officer in officers" :key="officer.id">
                        <td class="text-center">
                            <input type="checkbox" class="checkbox checkbox-sm">
                        </td>
                        <td>{{ officer.name }}</td>
                        <td>
                            <button class="btn btn-warning mr-2"><span class="material-symbols-rounded mr-2">
                                    edit
                                </span>แก้ไขข้อมูล</button>
                            <button class="btn btn-error"><span class="material-symbols-rounded mr-2">
                                    delete
                                </span>ลบเจ้าหน้าที่</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
