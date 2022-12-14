<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Inertia } from '@inertiajs/inertia'
import axios from 'axios';
import * as role from '@/role'
axios.defaults.withCredentials = true
</script>
<script>
export default {
    data() {
        return {
            absentPostData: {
                type: '',
                from: new Date(),
                to: new Date(),
                details: '',
            },
            data: [],
        }
    },
    methods: {
        formatDate(date) {
            let dateObj = new Date(date)
            return Intl.DateTimeFormat('th-TH').format(dateObj)
        },
        hasUnapprove() {
            for (let absent of this.data) {
                if (!absent.approve) {
                    return true
                }
            }
            return false
        },
        async submitAbsent() {
            try {
                //VALIDATE DATA
                if (this.absentPostData.from == '' || this.absentPostData.from == null) {
                    return alert('กรุณาเลือกวันลาเริ่มต้น')
                }

                if (this.absentPostData.to == '' || this.absentPostData.to == null) {
                    return alert('กรุณาเลือกวันลาสุดท้าย')
                }
                else if (new Date(this.absentPostData.to).getTime() < new Date(this.absentPostData.from)) {
                    return alert('วันลาสุดท้ายไม่สามารถเป็นวะนก่อนวันลาเริ่มต้นได้')
                }

                if (this.absentPostData.type == '' || this.absentPostData.type == null) {
                    return alert('กรุณาเลือกประเภทการลา')
                }

                if (this.absentPostData.details == '' || this.absentPostData.details == null) {
                    return alert('กรุณาใส่รายละเอียดการลา')
                }

                let postData = await axios.post('/api/absent/create', this.absentPostData)
                if (postData.status == 200) {
                    alert('ส่งคำขอสำเร็จ')
                    try {
                        let res = await axios('/api/absent/user_id/' + Inertia.page.props.user.id)
                        this.data = res.data
                    }
                    catch (err) {
                        alert('เกิดข้อผิดพลาดระหว่างดึงข้อมูล')
                        console.log(err)
                    }
                }
            }
            catch (err) {
                alert('เกิดข้อผิดพลาดระหว่างเพิ่มข้อมูล')
                console.log(err)
            }
        }
    },
    computed: {
        waitApproveData() {
            return this.data.filter((datas) => datas.approve == null)
        }
    },
    async mounted() {
        try {
            let res = await axios('/api/absent/user_id/' + Inertia.page.props.user.id)
            if (res.status == 403) {
                throw 'ERROR'
            }
            this.data = res.data
        }
        catch (err) {
            alert(err)
        }
    },
}
</script>
<template>

    <AppLayout title="แจ้งการลา">
        <template #header>{{ !role.isAdmin($page.props.user.role) ? 'แจ้งการลา' : 'ประวัติการลา' }}</template>
        <div class="overflow-auto" style="max-height: calc(100vh - 140px)">
            <form @submit.prevent="submitAbsent()" v-if="role.isStudent($page.props.user.role)">
                <div class="grid grid-cols-3 gap-5 mb-5">
                    <div class="rounded-xl bg-white p-4">
                        <p class="text-primary text-2xl font-bold mb-3 text-center">เลือกวันลา</p>
                        <label for="absent_from" class="font-medium">ลาตั้งแต่</label>
                        <input type="date" id="absent_from" class="input input-bordered w-full mt-1 mb-2"
                            v-model="absentPostData.from">
                        <label for="absent_to" class="font-medium">ลาจนถึง</label>
                        <input type="date" id="absent_to" class="input input-bordered w-full mt-1"
                            :min="absentPostData.from" v-model="absentPostData.to">
                    </div>
                    <div class="rounded-xl bg-white p-4">
                        <p class="text-primary text-2xl font-bold mb-3 text-center">เลือกประเภทการลา</p>
                        <select id="" class="select select-bordered w-full" v-model="absentPostData.type">
                            <option value="" disabled selected>--ประเภทการลา--</option>
                            <option value="ลากิจ">ลากิจ</option>
                            <option value="ลาป่วย">ลาป่วย</option>
                            <option value="ลาไปทำกิจกรรม">ลาไปทำกิจกรรม</option>
                            <option value="อื่น ๆ">อื่น ๆ</option>
                        </select>
                    </div>
                    <div class="rounded-xl bg-white p-4">
                        <p class="text-primary text-2xl font-bold mb-3 text-center">รายละเอียดการลา</p>
                        <textarea rows="6" placeholder="รายละเอียด" style="height: fit-content"
                            class="w-full input input-bordered" v-model="absentPostData.details"></textarea>
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
                    <template v-if="waitApproveData.length > 0">
                        <table class="table table-zebra w-full">
                            <!-- head -->
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>ประเภทการลา</th>
                                    <th>สาเหตุการลา</th>
                                    <th>เริ่มต้น</th>
                                    <th>สิ้นสุด</th>
                                    <th>อนุมัติ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="(absent, i) in waitApproveData" :key="absent.id">
                                    <tr class="hover">
                                        <th>{{ i + 1 }}</th>
                                        <td>{{ absent.type }}</td>
                                        <td>{{ absent.details }}</td>
                                        <td>{{ formatDate(absent.from) }}</td>
                                        <td>{{ formatDate(absent.to) }}</td>
                                        <td
                                            :class="{ 'text-success': absent.approve, 'text-warning': absent.approve == null, 'text-error': !absent.approve }">
                                            {{ absent.approve ? 'อนุมัติแล้ว' : !absent.approve ? 'ไม่ได้รับการอนุมัติ'
                                                    : 'รออนุมัติ'
                                            }}</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </template>
                    <template v-else>
                        <p class="text-6xl text-gray-400 mb-3">¯\_(ツ)_/¯</p>
                        <p class="text-xl">คุณไม่มีประวัติการขอลา</p>
                        <p class="text-gray-400">พยายามอย่าให้มีวันลาล่ะ</p>
                    </template>
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
                    <template v-if="data.length == 0">
                        <p class="text-6xl text-gray-400 mb-3">¯\_(ツ)_/¯</p>
                        <p class="text-xl">คุณไม่มีประวัติการลา</p>
                        <p class="text-gray-400">พยายามอย่าให้มีวันลาล่ะ</p>
                    </template>
                    <template v-else>
                        <table class="table table-zebra w-full">
                            <!-- head -->
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>ประเภทการลา</th>
                                    <th>สาเหตุการลา</th>
                                    <th>เริ่มต้น</th>
                                    <th>สิ้นสุด</th>
                                    <th>อนุมัติ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(absent, i) in data" :key="absent.id" class="hover">
                                    <th>{{ i + 1 }}</th>
                                    <td>{{ absent.type }}</td>
                                    <td>{{ absent.details }}</td>
                                    <td>{{ formatDate(absent.from) }}</td>
                                    <td>{{ formatDate(absent.to) }}</td>
                                    <td
                                        :class="{ 'text-success': absent.approve, 'text-warning': absent.approve == null, 'text-error': !absent.approve }">
                                        {{ absent.approve ? 'อนุมัติแล้ว' : !absent.approve ? 'ไม่ได้รับการอนุมัติ'
        : 'รออนุมัติ'
                                        }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
