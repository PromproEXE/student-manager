<script setup>
import AppLayout from '../Layouts/AppLayout.vue';
import Chart from 'chart.js/auto';
</script>

<script>
export default {
    data() {
        return {

        }
    },
    methods: {
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
        this.initChart()
    }
}
</script>

<template>
    <AppLayout>
        <template #header>หน้าหลัก</template>
        <div class="overflow-auto" style="max-height: calc(100vh - 140px)">
            <div class="bg-white rounded-xl text-neutral p-5 mb-5">
                <div class="flex items-center mb-5 justify-between">
                    <div class="flex items-center">
                        <div></div>
                        <img src="/img/account.png" class="mr-5" style="width: 100px" alt="">
                        <div>
                            <p class="text-4xl font-bold text-primary">{{ $page.props.user.name }}</p>
                            <p class="text-xl text-gray-400">{{
                                    $page.props.user.student ? 'นักเรียน' : ''
                            }} {{ $page.props.user.admin ? '/ แอดมิน' : '' }}
                                {{
                                        $page.props.user.super_admin ? '/ ผู้ดูแลระบบ' : ''
                                }}</p>
                        </div>
                    </div>
                    <div>
                        <button class="btn btn-warning text-lg mr-5">แจ้งลา</button>
                        <button class="btn btn-success text-lg">การบ้าน</button>
                    </div>
                </div>

                <template v-if="$page.props.user.student">
                    <div class="grid grid-cols-3 gap-5 mb-5">
                        <div class="bg-red-200 text-red-600 rounded-lg p-3 px-5">
                            <p class="text-xl font-bold mb-3">การบ้านที่ต้องส่งวันนี้
                                <span class="material-symbols-rounded">
                                    google_plus_reshare
                                </span>
                            </p>
                            <p class="text-4xl font-bold">2 งาน</p>
                        </div>
                        <div class="bg-amber-200 text-amber-600 rounded-lg p-3 px-5">
                            <p class="text-xl font-bold mb-3">การบ้านที่ต้องส่งพรุ่งนี้</p>
                            <p class="text-4xl font-bold">3 งาน</p>
                        </div>
                        <div class="bg-sky-200 text-sky-600 rounded-lg p-3 px-5">
                            <p class="text-xl font-bold mb-3">วันนี้เรียนทั้งหมด</p>
                            <p class="text-4xl font-bold">5 คาบ</p>
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
            <template v-if="$page.props.user.admin">
                <div class="rounded-xl bg-white p-5">

                </div>
            </template>
        </div>

    </AppLayout>
</template>

<style>

</style>
