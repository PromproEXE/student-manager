<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
</script>
<script>
export default {
    data() {
        return {
            time: ['08.10 - 09.00', '09.00 - 09.50', '09.50 - 10.40', '10.40 - 11.30', '11.30 - 12.20', '12.20 - 13.10', '13.10 - 14.00', '14.00 - 14.50', '14.50 - 15.40', '15.40 - 16.30'],
            days: ['จันทร์', 'อังคาร', 'พุธ', 'พฤหัส', 'ศุกร์']
        }
    },
    methods: {

    }
}
</script>
<template>
    <AppLayout>
        <template #header>ตารางเรียน</template>
        <div class="rounded-xl bg-white p-5" v-if="$page.props.user.student || $page.props.user.teacher">
            <div class="flex justify-between items-center mb-5">
                <p class="text-4xl text-primary font-bold">ตารางเรียนห้องม.6/3</p>
                <button class="btn btn-success text-lg"><span class="material-symbols-rounded mr-3">
                        edit
                    </span>แก้ไขตารางเรียน</button>
            </div>
            <div class="grid grid-cols-11">
                <div class="bg-gray-300 text-center font-bold p-3 text-xl border border-neutral"></div>
                <div class="col-span-10 grid grid-cols-10">
                    <p class="bg-gray-300 text-center font-bold px-3 py-2 text-xl border border-neutral" v-for="n in 10"
                        :key="n">คาบที่ {{ n
                        }}</p>
                    <p class="bg-gray-100 text-center px-1 py-2 text-sm border border-neutral" v-for="n in time"
                        :key="n">{{ n }}</p>
                </div>
                <template v-for="day in days" :key="day">
                    <div>
                        <p class="text-center py-5 border border-neutral">{{ day }}</p>
                    </div>
                    <div class="col-span-10 grid grid-cols-10">
                        <p class="text-center py-5 border border-neutral" v-for="n in 10" :key="n">คอมพิวเตอร์</p>
                    </div>
                </template>
            </div>
        </div>
        <div class="rounded-xl bg-white p-5" v-if="$page.props.user.admin">
            <div class="flex justify-between items-center mb-5">
                <p class="text-4xl text-primary font-bold">จัดการตารางเรียน</p>
            </div>
            <div class="flex justify-between mb-3">
                <div class="flex">
                    <div class="mr-3">
                        <label for="" class="mr-2">ระดับชั้น</label>
                        <select class="select select-bordered">
                            <option v-for="n in 6" :key="n">ม.{{ n }}</option>
                        </select>
                    </div>
                    <div class="mr-3">
                        <label for="" class="mr-2">ห้อง</label>
                        <select class="select select-bordered">
                            <option v-for="n in 14" :key="n">{{ n }}</option>
                        </select>
                    </div>
                </div>
                <button class="btn btn-success text-lg"><span class="material-symbols-rounded mr-3">
                        edit
                    </span>แก้ไขตารางเรียน</button>
            </div>
            <div class="grid grid-cols-11">
                <div class="bg-gray-300 text-center font-bold p-3 text-xl border border-neutral"></div>
                <div class="col-span-10 grid grid-cols-10">
                    <p class="bg-gray-300 text-center font-bold px-3 py-2 text-xl border border-neutral" v-for="n in 10"
                        :key="n">คาบที่ {{ n
                        }}</p>
                    <p class="bg-gray-100 text-center px-1 py-2 text-sm border border-neutral" v-for="n in time"
                        :key="n">{{ n
                        }}</p>
                </div>
                <template v-for="day in days" :key="day">
                    <div>
                        <p class="text-center py-5 border border-neutral">{{ day }}</p>
                    </div>
                    <div class="col-span-10 grid grid-cols-10">
                        <p class="text-center py-5 border border-neutral" v-for="n in 10" :key="n">คอมพิวเตอร์</p>
                    </div>
                </template>
            </div>
        </div>
    </AppLayout>
</template>
