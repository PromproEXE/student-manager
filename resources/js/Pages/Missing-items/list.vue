<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
</script>
<script></script>
<template>
    <AppLayout>
        <template #header>แจ้งของหาย</template>
        <div class="grid grid-cols-6 gap-5 mb-5">
            <div class="form-control"
                :class="{ 'col-span-6': $page.props.user.student, 'col-span-5': !$page.props.user.student }">
                <label class="input-group">
                    <span class="material-symbols-rounded">
                        search
                    </span>
                    <input type="text" placeholder="ค้นหาของหายได้ที่นี่" class="input input-bordered w-full" />
                </label>
            </div>
            <div class="text-right" v-if="!$page.props.user.student">
                <a :href="route('missing_items_edit_view')" role="button" class="btn btn-warning text-lg"><span
                        class="material-symbols-rounded mr-2">
                        add
                    </span>เพิ่มประกาศ</a>
            </div>
        </div>
        <div class="grid grid-cols-4 gap-5 overflow-auto" style="max-height: calc(100vh - 210px)">
            <div class="rounded-xl bg-white p-5" v-for="n in 10" :key="n">
                <div class="flex justify-between">
                    <p class="text-xl font-bold">โทรศัพท์มือถือยี่ห้อ Oppo</p>
                    <div class="dropdown" v-if="!$page.props.user.student">
                        <label tabindex="0" class="btn btn-ghost btn-circle">
                            <span class="material-symbols-rounded text-2xl font-bold">
                                menu
                            </span>
                        </label>
                        <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52">
                            <li><a>พบเจ้าของแล้ว</a></li>
                            <li><a :href="route('missing_items_edit_view')">แก้ไข</a></li>
                            <li><a>ลบ</a></li>
                        </ul>
                    </div>
                </div>
                <p class="text-gray-500 mb-3">ใส่เคสสีฟ้าดำ พับครึ่งได้ ติดต่อรับได้ที่ห้องประชาสันพันธ์ค่ะ</p>
                <img src="/img/oppo.jpeg" alt="" class="mb-3">
                <button class="btn btn-sm btn-success w-full text-lg"
                    v-if="!$page.props.user.student">พบเจ้าของแล้ว</button>
            </div>
        </div>

        <template v-if="false">
            <div class="rounded-xl bg-white p-5">
                <div class="form-control mb-3">
                    <label class="input-group">
                        <span class="material-symbols-rounded">
                            search
                        </span>
                        <input type="text" placeholder="ค้นหาด้วยชื่อสิ่งของที่หาย"
                            class="input input-bordered w-full" />
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
                            <th class="text-lg">ชื่อสิ่งของ</th>
                            <th class="text-lg">รายละเอียด</th>
                            <th class="text-lg text-center">สถานะ</th>
                            <th class="text-right">
                                <button class="btn btn-sm btn-success"><span class="material-symbols-rounded mr-2">
                                        add
                                    </span>เพิ่มของหาย</button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="hover" v-for="student in students" :key="student.id">
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm">
                            </td>
                            <td>{{ student.std_id }}</td>
                            <td>{{ student.name }}</td>
                            <td class="text-center">{{ student.class }}</td>
                            <td>
                                <button class="btn btn-warning mr-2"><span class="material-symbols-rounded mr-2">
                                        edit
                                    </span>แก้ไขข้อมูล</button>
                                <button class="btn btn-error"><span class="material-symbols-rounded mr-2">
                                        delete
                                    </span>ลบนักเรียน</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>
    </AppLayout>
</template>
