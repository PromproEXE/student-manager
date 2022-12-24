<script setup>
import AppLayout from '../Layouts/AppLayout.vue';
import SkeletonBar from '@/Components/SkeletonBar.vue'
import Chart from 'chart.js/auto';
import { Head } from '@inertiajs/inertia-vue3';
import { isAdmin, isStudent, isTeacher, isSystem } from '@/role'
import { Inertia } from '@inertiajs/inertia';
import axios from 'axios';
</script>

<script>
export default {
    data() {
        return {
            student_amount: -1,
            teacher_amount: -1,
            officer_amount: -1,
            todayClass: -1,
        }
    },
    methods: {
        async getUserAmount() {
            try {
                //REQUEST
                let amountUrl = ['/api/users/student/count/', '/api/users/teacher/count/', '/api/users/officer/count/']
                let res = await axios.all(amountUrl.map((url) => axios(url)))

                //GET STUDENT AMOUNT
                if (res[0].status == 200) {
                    this.student_amount = res[0].data
                }

                //GET TEACHER AMOUNT
                if (res[1].status == 200) {
                    this.teacher_amount = res[1].data
                }

                //GET OFFICER AMOUNT
                if (res[2].status == 200) {
                    this.officer_amount = res[2].data
                }
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างดึงจำนวนผู้ใช้')
                console.log(err)
            }
        },
        async getTodayClass() {
            try {
                let res = await axios('/api/timetable/today-class/count')
                if (res.status == 200) {
                    this.todayClass = res.data
                }
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างดึงข้อมูลตารางเรียน')
                console.log(err)
            }
        },
        initChart() {
            const labels1 = [
                'มกราคม',
                'กุมภาพันธ์',
                'มีนาคม',
                'เมษายน',
                'พฤษภาคม',
                'มิถุนายน',
                'กรกฎาคม',
                'สิงหาคม',
                'กันยายน',
                'ตุลาคม',
                'พฤศจิกายน',
                'ธันวาคม',
            ];
            const data1 = {
                labels: labels1,
                datasets: [{
                    label: 'My First Dataset',
                    data: [65, 59, 80, 81, 56, 55, 40, 65, 59, 80, 81, 56],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(255, 205, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(201, 203, 207, 0.2)'
                    ],
                    borderColor: [
                        'rgb(255, 99, 132)',
                        'rgb(255, 159, 64)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(54, 162, 235)',
                        'rgb(153, 102, 255)',
                        'rgb(201, 203, 207)'
                    ],
                    borderWidth: 1
                }]
            };

            const config1 = {
                type: 'bar',
                data: data1,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                },
            };

            new Chart(document.getElementById('chart'), config1)

            const labels2 = [
                'ภาษาไทย',
                'ภาษาอังกฤษ',
                'คณิตศาสตร์',
                'คอมพิวเตอร์',
                'วิทยาศาสตร์',
            ];
            const data2 = {
                labels: labels2,
                datasets: [{
                    label: 'My First Dataset',
                    data: [65, 59, 80, 81, 56],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(255, 205, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                    ],
                    borderColor: [
                        'rgb(255, 99, 132)',
                        'rgb(255, 159, 64)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(54, 162, 235)',
                    ],
                    borderWidth: 1
                }]
            };

            const config2 = {
                type: 'bar',
                data: data2,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                },
            };
            new Chart(document.getElementById('chart2'), config2)
        }
    },
    mounted() {
        if (!isAdmin(Inertia.page.props.user.role)) {
            this.initChart()
        }

        if (isStudent(Inertia.page.props.user.role)) {
            this.getTodayClass()
        }

        if (isAdmin(Inertia.page.props.user.role)) {
            this.getUserAmount()
        }
    }
}
</script>

<template>

    <Head>
        <title>หน้าหลัก</title>
    </Head>
    <AppLayout>
        <template #header>หน้าหลัก</template>
        <div class="overflow-auto" style="max-height: calc(100vh - 140px)">
            <div class="bg-white rounded-xl text-neutral p-5 mb-5">
                <div class="flex items-center justify-between" :class="{ 'mb-5': isStudent($page.props.user.role) }">
                    <div class="flex items-center">
                        <div></div>
                        <img src="/img/account.png" class="mr-5" style="width: 100px" alt="">
                        <div>
                            <p class="text-4xl font-bold text-primary">{{ $page.props.user.name }}</p>
                            <p class="text-xl text-gray-400">{{
                                    isStudent($page.props.user.role) ? 'นักเรียน' : ''
                            }} {{ isTeacher($page.props.user.role) ? 'อาจารย์' : '' }}
                                {{
                                        isAdmin($page.props.user.role) ? 'เจ้าหน้าที่' : ''
                                }} {{
        isSystem($page.props.user.role) ? 'ผู้ดูแลระบบ' : ''
}}</p>
                        </div>
                    </div>
                    <div v-if="isStudent($page.props.user.role)">
                        <a :href="route('absent_view')" role="button" class="btn btn-warning text-lg mr-5">แจ้งลา</a>
                        <a role="button" class="btn btn-success text-lg">การบ้าน</a>
                    </div>
                    <div v-if="isTeacher($page.props.user.role)">
                        <a :href="route('absent_view')" role="button"
                            class="btn btn-warning text-lg mr-5">ยืนยันคำขอการลา</a>
                        <a role="button" class="btn btn-success text-lg">เพิ่มการบ้าน</a>
                    </div>
                </div>

                <template v-if="isStudent($page.props.user.role)">
                    <div class="grid grid-cols-3 gap-5 mb-5">
                        <div class="bg-red-200 text-red-600 rounded-lg p-3 px-5">
                            <p class="text-xl font-bold mb-3">การบ้านที่ต้องส่งวันนี้
                                <span class="material-symbols-rounded">
                                    google_plus_reshare
                                </span>
                            </p>
                            <p class="text-4xl font-bold">0 งาน</p>
                        </div>
                        <div class="bg-amber-200 text-amber-600 rounded-lg p-3 px-5">
                            <p class="text-xl font-bold mb-3">การบ้านที่ต้องส่งพรุ่งนี้</p>
                            <p class="text-4xl font-bold">0 งาน</p>
                        </div>
                        <div class="bg-sky-200 text-sky-600 rounded-lg p-3 px-5">
                            <p class="text-xl font-bold mb-3">วันนี้เรียนทั้งหมด</p>
                            <p class="text-4xl font-bold" v-if="todayClass > -1">{{ todayClass }} คาบ</p>
                            <SkeletonBar bg="bg-sky-600" class="w-1/2 h-8" v-else></SkeletonBar>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <p class="text-2xl font-bold">สถิติการมาเรียนแต่ละเดือน</p>
                            <div style="height:300px">
                                <canvas id="chart"></canvas>
                            </div>
                        </div>
                        <div>
                            <p class="text-2xl font-bold">สถิติการเข้าเรียนแต่ละรายวิชา</p>
                            <div style="height:300px">
                                <canvas id="chart2"></canvas>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <template v-if="isTeacher($page.props.user.role)">
                <div class="rounded-xl bg-white p-5">
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <p class="text-2xl font-bold">สถิติการมาเรียนของนักเรียนทั้งหมดในแต่ละเดือน</p>
                            <div style="height:300px">
                                <canvas id="chart"></canvas>
                            </div>
                        </div>
                        <div>
                            <p class="text-2xl font-bold">สถิติการเข้าเรียนแต่ละรายวิชา</p>
                            <div style="height:300px">
                                <canvas id="chart2"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- ADMIN DASHBOARD -->
            <template v-if="isAdmin($page.props.user.role)">
                <div class="rounded-xl bg-white p-5 mb-5 grid grid-cols-3 gap-5">
                    <div class="bg-sky-200 text-sky-600 rounded-lg p-3 px-5">
                        <p class="text-xl font-bold mb-3">จำนวนนักเรียนทั้งหมด</p>
                        <p class="text-4xl font-bold" v-if="student_amount > -1">{{ student_amount }} คน</p>
                        <SkeletonBar bg="bg-sky-600" class="w-1/2 h-8" v-else></SkeletonBar>
                    </div>
                    <div class="bg-amber-200 text-amber-600 rounded-lg p-3 px-5 h-full">
                        <p class="text-xl font-bold mb-3">จำนวนอาจารย์ทั้งหมด</p>
                        <p class="text-4xl font-bold" v-if="teacher_amount > -1">{{ teacher_amount }} คน</p>
                        <SkeletonBar bg="bg-amber-600" class="w-1/2 h-8" v-else></SkeletonBar>
                    </div>
                    <div class="bg-pink-200 text-pink-600 rounded-lg p-3 px-5">
                        <p class="text-xl font-bold mb-3">จำนวนเจ้าหน้าที่</p>
                        <p class="text-4xl font-bold" v-if="officer_amount > -1">{{ officer_amount }} คน</p>
                        <SkeletonBar bg="bg-pink-600" class="w-1/2 h-8" v-else></SkeletonBar>
                    </div>
                </div>
                <div class="rounded-xl bg-white p-5">
                    <p class="font-bold text-4xl mb-3">เมนูลัด</p>
                    <div class="grid grid-cols-3 gap-5">
                        <a :href="route('users_student_list_view')" role="button"
                            class="btn btn-secondary block h-fit py-5">
                            <span class="material-symbols-rounded" style="font-size: 5rem;">
                                person
                            </span>
                            <p class="text-2xl">จัดการนักเรียน</p>
                        </a>
                        <a :href="route('users_teacher_list_view')" role="button"
                            class="btn btn-secondary block h-fit py-5">
                            <span class="material-symbols-rounded" style="font-size: 5rem;">
                                manage_accounts
                            </span>
                            <p class="text-2xl">จัดการอาจารย์</p>
                        </a>
                        <a :href="route('classroom_list_view')" role="button"
                            class="btn btn-secondary block h-fit py-5">
                            <span class="material-symbols-rounded" style="font-size: 5rem;">
                                school
                            </span>
                            <p class="text-2xl">จัดการห้องเรียน</p>
                        </a>
                    </div>
                </div>
            </template>
        </div>

    </AppLayout>
</template>

<style>

</style>
