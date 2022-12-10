<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
</script>
<script>
export default {
    data() {
        return {
            subjects: [{
                id: 'asdasdasd',
                name: 'ภาษาอังกฤษ',
                class_id: 'อ13101',
                for_class: '6',
                teacher: 'สุธาทิพย์ ผลไสว'
            },
            {
                id: 'sdgdgethgseth',
                name: 'ภาษาไทย',
                class_id: 'ท13101',
                for_class: '6',
                teacher: 'สุธาทิพย์ ผลไสว'
            },
            {
                id: 'qwiejiqwjiejiqwoer',
                name: 'คณิตศาสตร์',
                class_id: 'ค13101',
                for_class: '6',
                teacher: 'สุธาทิพย์ ผลไสว'
            },
            {
                id: 'mnvkmkdfvnfjdnjg',
                name: 'คอมพิวเตอร์',
                class_id: 'ว13201',
                for_class: '6',
                teacher: 'สุธาทิพย์ ผลไสว'
            },
            {
                id: 'qiwjeioqjwiej',
                name: 'วิทยาศาสตร์',
                class_id: 'ว13101',
                for_class: '6',
                teacher: 'สุธาทิพย์ ผลไสว'
            }]
        }
    }
}
</script>
<template>
    <AppLayout>
        <template #header>ห้องเรียน</template>
        <div class="bg-white rounded-xl grid grid-cols-3 p-5 gap-5" v-if="$page.props.user.student">
            <div class="rounded-lg drop-shadow-lg bg-white" v-for="subject in subjects" :key="subject.id">
                <div
                    class="px-5 py-4 flex items-center justify-between border-b-4 rounded-t-lg bg-sky-200 border-sky-400">
                    <div>
                        <a :href="route('homework_classroom_view')" class="text-2xl font-bold underline">{{ subject.name
                        }}</a>
                        <p class="text-md">{{ subject.teacher }}</p>
                    </div>
                    <img src="/img/account.png" style="width: 64px" alt="">
                </div>
                <div class="px-1 py-2 text-right">
                    <button class="btn btn-sm btn-ghost">เข้าห้องเรียน<span class="ml-2 material-symbols-rounded">
                            google_plus_reshare
                        </span></button>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5" v-if="$page.props.user.teacher">
            <p class="text-4xl font-bold text-primary mb-5">ห้องเรียนที่ดูแล</p>
            <div class="grid grid-cols-3 gap-5">
                <div class="rounded-lg drop-shadow-lg bg-white" v-for="subject in subjects" :key="subject.id">
                    <div
                        class="px-5 py-4 flex items-center justify-between border-b-4 rounded-t-lg bg-sky-200 border-sky-400">
                        <div>
                            <a :href="route('homework_classroom_view')" class="text-2xl font-bold underline">{{
                                    subject.name
                            }}</a>
                            <p class="text-md">{{ subject.teacher }}</p>
                        </div>
                        <img src="/img/account.png" style="width: 64px" alt="">
                    </div>
                    <div class="px-1 py-2 text-right">
                        <button class="btn btn-sm btn-ghost">เข้าห้องเรียน<span class="ml-2 material-symbols-rounded">
                                google_plus_reshare
                            </span></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5" v-if="$page.props.user.admin">
            <p class="text-4xl font-bold text-primary mb-5">ห้องเรียนทั้งหมด</p>
            <div class="form-control mb-3">
                <label class="input-group">
                    <span class="material-symbols-rounded">
                        search
                    </span>
                    <input type="text" placeholder="ค้นหาด้วยรหัสวิชาหรือชื่อวิชา"
                        class="input input-bordered w-full" />
                </label>
            </div>
            <div class="flex mb-3">
                <div class="mr-3">
                    <label for="" class="mr-2">ระดับชั้น</label>
                    <select class="select select-bordered">
                        <option>ทั้งหมด</option>
                        <option v-for="n in 6" :key="n">ม.{{ n }}</option>
                    </select>
                </div>
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
                        <th></th>
                        <th class="text-lg">รหัสวิชา</th>
                        <th class="text-lg">ชื่อวิชา</th>
                        <th class="text-lg">ระดับชั้น</th>
                        <th class="text-lg">ผู้สอน</th>
                        <th class="text-right">
                            <button class="btn btn-sm btn-info mr-2"><span class="material-symbols-rounded mr-2">
                                    library_add
                                </span>เพิ่มห้องเรียนอัตโนมัติ</button>
                            <button class="btn btn-sm btn-success"><span class="material-symbols-rounded mr-2">
                                    add
                                </span>เพิ่มห้องเรียน</button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="hover" v-for="(subject, i) in subjects" :key="subject.id">
                        <td class="text-center">
                            <input type="checkbox" class="checkbox checkbox-sm">
                        </td>
                        <th>{{ i + 1 }}</th>
                        <td>{{ subject.class_id }}</td>
                        <td>{{ subject.name }}</td>
                        <td>{{ subject.for_class }}</td>
                        <td>{{ subject.teacher }}</td>
                        <td>
                            <button class="btn btn-warning mr-2"><span class="material-symbols-rounded mr-2">
                                    edit
                                </span>แก้ไข</button>
                            <button class="btn btn-secondary mr-2"><span class="material-symbols-rounded mr-2">
                                    link
                                </span>คัดลอกลิงก์</button>
                            <button class="btn btn-error"><span class="material-symbols-rounded mr-2">
                                    delete
                                </span>ลบห้อง</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
