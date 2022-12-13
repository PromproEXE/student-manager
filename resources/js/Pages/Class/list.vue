<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
</script>
<script>
export default {
    data() {
        return {
            students: [{
                id: 1,
                std_id: '09362',
                name: 'ชาญวิช มาศมัณฑนะ',
                class: '6',
                room: '3',
            },
            {
                id: 2,
                std_id: '09362',
                name: 'รักษิต รุ่งรัตนไชย',
                class: '6',
                room: '5',
            },
            {
                id: 3,
                std_id: '09362',
                name: 'เกียรติศักดิ์ มากมีทรัพย์',
                class: '5',
                room: '4',
            }]
        }
    }
}
</script>
<template>

    <head title="ห้องเรียน"></head>
    <AppLayout>
        <template #header>จัดการห้องเรียน</template>
        <div class="bg-white rounded-xl p-5" v-if="$page.props.user.admin">
            <div class="form-control mb-3">
                <label class="input-group">
                    <span class="material-symbols-rounded">
                        search
                    </span>
                    <input type="text" placeholder="ค้นหาด้วยระดับชั้นหรือห้อง" class="input input-bordered w-full" />
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
                    <label for="" class="mr-2">ห้อง</label>
                    <select class="select select-bordered">
                        <option>ทั้งหมด</option>
                        <option v-for="n in 14" :key="n">{{ n }}</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-4 gap-5">
                <a :href="route('class_details_view')"
                    class="rounded-lg p-5 bg-yellow-200 cursor-pointer hover:bg-yellow-300" v-for="n in 11" :key="n">
                    <p class="text-2xl font-bold">ม.6/{{ n }}</p>
                    <p>สุธาทิพย์ ผลไสว</p>
                </a>
            </div>
        </div>
    </AppLayout>
</template>
