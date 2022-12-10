<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
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
        },
    },
    mounted() {
        this.initChart()
    }
}
</script>
<template>
    <AppLayout>
        <template #header>ประวัติการมาโรงเรียน</template>
        <div class="grid grid-cols-2 gap-5 bg-white rounded-xl p-5" v-if="$page.props.user.student">
            <div>
                <p class="text-2xl text-primary font-bold mb-3">สถิติการมาเรียนในแต่ละเดือน</p>
                <div style="height:300px">
                    <canvas id="chart"></canvas>
                </div>
            </div>
            <div>
                <p class="text-2xl text-primary font-bold">สถิติการมาเรียนในแต่ละรายวิชา</p>
                <div style="height:300px">
                    <canvas id="chart2"></canvas>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-5 bg-white rounded-xl p-5" v-if="$page.props.user.teacher">
            <div>
                <p class="text-2xl text-primary font-bold mb-3">สถิติการมาเรียนของนักเรียนในห้อง</p>
                <div class="grid grid-cols-5 items-center mb-3">
                    <label for="" class="mr-3">ชื่อนักเรียน: </label>
                    <select class="select select-bordered w-full col-span-4">
                        <option>ชาญวิช มาศมัณฑนะ</option>
                        <option>รักษิต รุ่งรัตนไชย</option>
                        <option>เกียรติศักดิ์ มากมีทรัพย์</option>
                    </select>
                </div>
                <div style="height:300px">
                    <canvas id="chart"></canvas>
                </div>
            </div>
            <div>
                <p class="text-2xl text-primary font-bold">สถิติการมาเรียนของทั้งห้อง</p>
                <div style="height:300px">
                    <canvas id="chart2"></canvas>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5" v-if="$page.props.user.admin">
            <p class="text-2xl text-primary font-bold mb-3">สถิติการมาเรียนของนักเรียนในห้อง</p>
            <div class="flex">
                <div class="mr-3">
                    <label for="" class="mr-2">ระดับชั้น: </label>
                    <select class="select select-bordered">
                        <option>ทั้งหมด</option>
                        <option v-for="n in 6" :key="n">ม.{{ n }}</option>
                    </select>
                </div>
                <div class="mr-3">
                    <label for="" class="mr-2">ห้อง: </label>
                    <select class="select select-bordered">
                        <option>ทั้งหมด</option>
                        <option v-for="n in 6" :key="n">ม.{{ n }}</option>
                    </select>
                </div>
                <div>
                    <label for="" class="mr-2">ชื่อนักเรียน: </label>
                    <select class="select select-bordered">
                        <option>ทั้งหมด</option>
                        <option>ชาญวิช มาศมัณฑนะ</option>
                        <option>รักษิต รุ่งรัตนไชย</option>
                        <option>เกียรติศักดิ์ มากมีทรัพย์</option>
                    </select>
                </div>
            </div>
            <div style="height:400px;" class="flex justify-center">
                <canvas id="chart"></canvas>
            </div>
        </div>
    </AppLayout>
</template>
