<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Inertia } from '@inertiajs/inertia'
import axios from 'axios';
import { cloneDeep } from 'lodash';
import { isAdmin, isStudent, isTeacher } from '@/role'
import { validateAbsent } from '@/data-validator'
</script>
<script>
function padTo2Digits(num) {
    return num.toString().padStart(2, '0');
}
let date = new Date()
let dateWithFormat = date.getFullYear() + '-' + padTo2Digits(date.getMonth() + 1) + '-' + date.getDate()
export default {
    data() {
        return {
            absentPostData: {
                type: '',
                from: dateWithFormat,
                to: dateWithFormat,
                details: '',
            },
            errorBag: {
                status: true,
                message: {}
            },
            filter_absent_request: {
                class: 'all',
                room: 'all',
                name: 'all'
            },
            filter_absent_history: {
                class: 'all',
                room: 'all',
                name: 'all'
            },
            data: [],
            students: [],
            selectedAbsent: {},
            rootUrl: route('absent_view') + '#'
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
            this.errorBag = {
                status: true,
                message: {}
            }
            try {
                //VALIDATE DATA
                this.errorBag = validateAbsent(this.absentPostData)

                if (!this.errorBag.status) {
                    return
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
        },
        selectAbsent(data) {
            this.selectedAbsent = cloneDeep(data)
        },
        clearSelectAbsent() {
            window.location.href = this.rootUrl
            setTimeout(() => this.selectedAbsent = {}, 150)
        },
        async getAbsentData() {
            try {
                let res = null
                if (isStudent(Inertia.page.props.user.role)) {
                    res = await axios('/api/absent/user_id/' + Inertia.page.props.user.id)
                }
                else if (isTeacher(Inertia.page.props.user.role)) {
                    res = await axios('/api/absent/class/' + Inertia.page.props.user.data.advisor.class + '/room/' + Inertia.page.props.user.data.advisor.room)
                }
                else if (isAdmin(Inertia.page.props.user.role)) {
                    res = await axios('/api/absent/')
                }
                if (res.status == 403) {
                    throw 'ERROR'
                }
                this.data = res.data
            }
            catch (err) {
                alert(err)
            }
        },
        async getStudent() {
            try {
                let res = null

                if (isTeacher(Inertia.page.props.user.role)) {
                    let classes = Inertia.page.props.user.data.advisor.class
                    let room = Inertia.page.props.user.data.advisor.room
                    res = await axios('/api/users/student/class/' + classes + '/room/' + room + '/name')
                }
                else {
                    res = await axios('/api/users/student/name/')
                }
                if (res.status == 200) {
                    this.students = res.data
                }
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างดึงข้อมูลนักเรียน')
                console.log(err)
            }
        },
        clearAbsentData() {
            this.absentPostData = {
                type: '',
                from: dateWithFormat,
                to: dateWithFormat,
                details: '',
            }
        },
        formatDate(date) {
            let dateObj = new Date(date)
            return Intl.DateTimeFormat('th-TH').format(dateObj)
        },
        filteredStudentName(filter) {
            let data = this.students

            //FILTER CLASS
            if (filter.class != 'all') {
                data = data.filter((datas) => datas.class == filter.class)
            }

            //FILTER ROOM
            if (filter.room != 'all') {
                data = data.filter((datas) => datas.room == filter.room)
            }

            return data
        },
        async approveAbsent(approve = true) {
            try {
                this.selectedAbsent.approve = approve
                let res = await axios.put('/api/absent/update/' + this.selectedAbsent.id, this.selectedAbsent)
                if (res.status == 200) {
                    if (approve) {
                        alert('อนุมัติแล้ว')
                    }
                    else {
                        alert('ไม่อนุมัติแล้ว') //STRANGE
                    }

                    this.clearSelectAbsent()
                    this.getAbsentData()
                }
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างการอนุมัติ / ไม่อนุมัติคำขอการลา')
            }
        }
    },
    computed: {
        waitApproveData() {
            //FILTER UNAPPROVE
            let data = this.data.filter((datas) => datas.approve == null)

            //FILTER CLASS
            if (this.filter_absent_request.class != 'all') {
                data = data.filter((datas) => datas.user_class.toLowerCase().indexOf(this.filter_absent_request.class) > -1)
            }

            //FILTER ROOM
            if (this.filter_absent_request.room != 'all') {
                data = data.filter((datas) => datas.user_room.toLowerCase().indexOf(this.filter_absent_request.room) > -1)
            }

            //FILTER NAME
            if (this.filter_absent_request.name != 'all') {
                data = data.filter((datas) => datas.user_name.toLowerCase().indexOf(this.filter_absent_request.name) > -1)
            }
            return data
        },
        absentHistoryData() {
            //FILTER UNAPPROVE
            let data = this.data

            //FILTER CLASS
            if (this.filter_absent_history.class != 'all') {
                data = data.filter((datas) => datas.user_class.toLowerCase().indexOf(this.filter_absent_history.class) > -1)
            }

            //FILTER ROOM
            if (this.filter_absent_history.room != 'all') {
                data = data.filter((datas) => datas.user_room.toLowerCase().indexOf(this.filter_absent_history.room) > -1)
            }

            //FILTER NAME
            if (this.filter_absent_history.name != 'all') {
                data = data.filter((datas) => datas.user_name.toLowerCase().indexOf(this.filter_absent_history.name) > -1)
            }
            return data
        },
    },
    async mounted() {
        this.getAbsentData()
        if (isTeacher(Inertia.page.props.user.role) || isAdmin(Inertia.page.props.user.role)) {
            this.getStudent()
        }
    },
}
</script>
<template>

    <AppLayout title="แจ้งการลา">
        <template #header>{{ !isAdmin($page.props.user.role) ? 'แจ้งการลา' : 'ประวัติการลา' }}</template>
        <div class="overflow-auto" style="max-height: calc(100vh - 140px)">

            <!-- ABSENT FORM -->
            <form @submit.prevent="submitAbsent()" v-if="isStudent($page.props.user.role)">
                <div class="grid grid-cols-3 gap-5 mb-5">
                    <div class="rounded-xl bg-white p-4">
                        <p class="text-primary text-2xl font-bold mb-3 text-center">เลือกวันลา</p>
                        <label for="absent_from" class="font-medium">ลาตั้งแต่</label>
                        <input type="date" id="absent_from" class="input input-bordered w-full mt-1 mb-2"
                            :class="{ 'border-error': errorBag.message.from }" v-model="absentPostData.from">
                        <small class="text-error" v-if="errorBag.message.from">{{ errorBag.message.from }}</small>
                        <label for="absent_to" class="font-medium">ลาจนถึง</label>
                        <input type="date" id="absent_to" class="input input-bordered w-full mt-1"
                            :class="{ 'border-error': errorBag.message.to }" :min="absentPostData.from"
                            v-model="absentPostData.to">
                        <small class="text-error" v-if="errorBag.message.to">{{ errorBag.message.to }}</small>
                    </div>
                    <div class="rounded-xl bg-white p-4">
                        <p class="text-primary text-2xl font-bold mb-3 text-center">เลือกประเภทการลา</p>
                        <select id="" class="select select-bordered w-full" v-model="absentPostData.type"
                            :class="{ 'border-error': errorBag.message.type }">
                            <option value="" disabled>--ประเภทการลา--</option>
                            <option value="ลากิจ">ลากิจ</option>
                            <option value="ลาป่วย">ลาป่วย</option>
                            <option value="ลาไปทำกิจกรรม">ลาไปทำกิจกรรม</option>
                            <option value="อื่น ๆ">อื่น ๆ</option>
                        </select>
                        <small class="text-error" v-if="errorBag.message.type">{{ errorBag.message.type }}</small>
                    </div>
                    <div class="rounded-xl bg-white p-4">
                        <p class="text-primary text-2xl font-bold mb-3 text-center">รายละเอียดการลา</p>
                        <textarea rows="6" placeholder="รายละเอียด" style="height: fit-content"
                            class="w-full input input-bordered" v-model="absentPostData.details"
                            :class="{ 'border-error': errorBag.message.details }"></textarea>
                        <small class="text-error" v-if="errorBag.message.details">{{ errorBag.message.details }}</small>
                    </div>
                </div>
                <div class="mb-5 grid grid-cols-2 gap-5">
                    <button class="btn btn-success w-full text-xl" type="submit">ส่งคำขอการลา</button>
                    <button class="btn btn-error w-full text-xl" type="reset"
                        @click="clearAbsentData()">ล้างข้อมูล</button>
                </div>
            </form>

            <!-- ABSENT REQUEST -->
            <div class="bg-white p-5 rounded-xl mb-5">
                <p class="text-2xl text-primary font-bold mb-3" v-if="isStudent($page.props.user.role)">
                    คำขอการลา</p>
                <p class="text-2xl text-primary font-bold mb-3" v-else>คำขอการลาห้องม.{{
                        $page.props.user.data.advisor.class +
                        '/' + $page.props.user.data.advisor.room
                }}</p>
                <div class="flex mb-3" v-if="isTeacher($page.props.user.role) || isAdmin($page.props.user.role)">
                    <template v-if="isAdmin($page.props.user.role)">
                        <div class="mr-3">
                            <label for="filter-class" class="mr-2">ระดับชั้น:</label>
                            <select class="select select-bordered select-sm text-xs" id="filter-class"
                                v-model="filter_absent_request.class">
                                <option value="all">ทั้งหมด</option>
                                <option v-for="classes in 6" :key="classes" :value="classes">ม.{{ classes }}</option>
                            </select>
                        </div>
                        <div class="mr-3">
                            <label for="filter-room" class="mr-2">ห้อง:</label>
                            <select class="select select-bordered text-xs select-sm" id="filter-room"
                                v-model="filter_absent_request.room">
                                <option value="all">ทั้งหมด</option>
                                <option v-for="room in 14" :key="room">{{ room }}</option>
                            </select>
                        </div>
                    </template>
                    <div>
                        <label for="filter-std-name" class="mr-2 font-medium">ชื่อนักเรียน:</label>
                        <select class="select select-bordered select-sm text-xs" id="filter-std-name"
                            v-model="filter_absent_request.name">
                            <option value="all">ทั้งหมด</option>
                            <option v-for="(student, i) in filteredStudentName(filter_absent_request)" :key="i"
                                :value="student.name">{{
                                        student.name
                                }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="text-center">
                    <template v-if="waitApproveData.length > 0">
                        <div class="overflow-auto">
                            <table class="table table-zebra w-full">
                                <thead>
                                    <tr>
                                        <th class="text-lg"></th>
                                        <th class="text-lg"
                                            v-if="isTeacher($page.props.user.role) || isAdmin($page.props.user.role)">
                                            ชื่อ-นามสกุล</th>
                                        <th class="text-lg text-center" v-if="isAdmin($page.props.user.role)">ระดับชั้น
                                        </th>
                                        <th class="text-lg text-center" v-if="isAdmin($page.props.user.role)">ห้อง</th>
                                        <th class="text-lg">ประเภทการลา</th>
                                        <th class="text-lg">สาเหตุการลา</th>
                                        <th class="text-lg">เริ่มต้น</th>
                                        <th class="text-lg">สิ้นสุด</th>
                                        <th class="text-lg">อนุมัติ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template v-for="(absent, i) in waitApproveData" :key="absent.id">
                                        <tr class="hover">
                                            <th>{{ i + 1 }}</th>
                                            <td
                                                v-if="isTeacher($page.props.user.role) || isAdmin($page.props.user.role)">
                                                {{
                                                        absent.user_name
                                                }}</td>
                                            <td class="text-center" v-if="isAdmin($page.props.user.role)">{{
                                                    absent.user_class
                                            }}</td>
                                            <td class="text-center" v-if="isAdmin($page.props.user.role)">{{
                                                    absent.user_room
                                            }}</td>
                                            <td>{{ absent.type }}</td>
                                            <td>{{ absent.details }}</td>
                                            <td>{{ formatDate(absent.from) }}</td>
                                            <td>{{ formatDate(absent.to) }}</td>
                                            <td v-if="isStudent($page.props.user.role)"
                                                :class="{ 'text-success': absent.approve, 'text-warning': absent.approve == null, 'text-error': !absent.approve }">
                                                {{ absent.approve ? 'อนุมัติแล้ว' : absent.approve == null ? 'รออนุมัติ'
                                                        : 'ไม่ได้รับการอนุมัติ'
                                                }}</td>
                                            <td v-else>
                                                <a role="button" href="#approve-modal" @click="selectAbsent(absent)"
                                                    class="btn btn-secondary mr-3" type="button">อนุมัติ /
                                                    ไม่อนุมัติ</a>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>
                    <template v-else>
                        <p class="text-6xl text-gray-400 mb-3 mt-10">¯\_(ツ)_/¯</p>
                        <p class="text-xl">คุณไม่มีประวัติการขอลา</p>
                        <p class="text-gray-400">พยายามอย่าให้มีล่ะ</p>
                    </template>
                </div>
            </div>

            <!-- ABSENT HISTORY -->
            <div class="bg-white p-5 rounded-xl">
                <p class="text-2xl text-primary font-bold mb-3" v-if="isTeacher($page.props.user.role)">
                    ประวัติการลาของห้องม.{{ $page.props.user.data.advisor.class + '/' +
                            $page.props.user.data.advisor.room
                    }}</p>
                <p class="text-2xl text-primary font-bold mb-3" v-else>ประวัติการลา</p>
                <div class="flex mb-3" v-if="isAdmin($page.props.user.role) || isTeacher($page.props.user.role)">
                    <div class="mr-3" v-if="isAdmin($page.props.user.role)">
                        <label for="filter-history-class" class="mr-2">ระดับชั้น:</label>
                        <select class="select select-bordered select-sm text-xs" id="filter-history-class"
                            v-model="filter_absent_history.class">
                            <option value="all">ทั้งหมด</option>
                            <option v-for="classes in 6" :key="classes">ม.{{ classes }}</option>
                        </select>
                    </div>
                    <div class="mr-3" v-if="isAdmin($page.props.user.role)">
                        <label for="filter-history-room" class="mr-2">ห้อง:</label>
                        <select class="select select-bordered select-sm text-xs" id="filter-history-room"
                            v-model="filter_absent_history.class">
                            <option value="all">ทั้งหมด</option>
                            <option v-for="room in 14" :key="room">{{ room }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="filter-history-std-name" class="mr-2 font-medium">ชื่อนักเรียน:</label>
                        <select class="select select-bordered select-sm text-xs" id="filter-history-std-name"
                            v-model="filter_absent_history.name">
                            <option value="all">ทั้งหมด</option>
                            <option v-for="(student, i) in filteredStudentName(filter_absent_history)" :key="i"
                                :value="student.name">{{
                                        student.name
                                }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="text-center">
                    <template v-if="absentHistoryData.length > 0">
                        <div class="overflow-auto">
                            <table class="table table-zebra w-full">
                                <thead>
                                    <tr>
                                        <th class="text-lg"></th>
                                        <th class="text-lg"
                                            v-if="isAdmin($page.props.user.role) || isTeacher($page.props.user.role)">
                                            ชื่อ-นามสกุล</th>
                                        <th class="text-lg text-center" v-if="isAdmin($page.props.user.role)">
                                            ระดับชั้น</th>
                                        <th class="text-lg text-center" v-if="isAdmin($page.props.user.role)">
                                            ห้อง</th>
                                        <th class="text-lg">ประเภทการลา</th>
                                        <th class="text-lg">สาเหตุการลา</th>
                                        <th class="text-lg">เริ่มต้น</th>
                                        <th class="text-lg">สิ้นสุด</th>
                                        <th class="text-lg">
                                            อนุมัติ</th>
                                        <th class="text-lg">
                                            อนุมัติโดย</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(absent, i) in absentHistoryData" :key="absent.id" class="hover">
                                        <th>{{ i + 1 }}</th>
                                        <td v-if="isAdmin($page.props.user.role) || isTeacher($page.props.user.role)">{{
                                                absent.user_name
                                        }}</td>
                                        <td class="text-center" v-if="isAdmin($page.props.user.role)">{{
                                                absent.user_class
                                        }}</td>
                                        <td class="text-center" v-if="isAdmin($page.props.user.role)">{{
                                                absent.user_room
                                        }}</td>
                                        <td>{{ absent.type }}</td>
                                        <td>{{ absent.details }}</td>
                                        <td>{{ formatDate(absent.from) }}</td>
                                        <td>{{ formatDate(absent.to) }}</td>
                                        <td
                                            :class="{ 'text-success': absent.approve, 'text-warning': absent.approve == null, 'text-error': !absent.approve }">
                                            {{ absent.approve ? 'อนุมัติแล้ว' : absent.approve == null ?
                                                    'รออนุมัติ'
                                                    : 'ไม่ได้รับการอนุมัติ'
                                            }}</td>
                                        <td>{{ absent.approve_by || '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </template>
                    <template v-else>
                        <p class="text-6xl text-gray-400 mb-3 mt-10">¯\_(ツ)_/¯</p>
                        <p class="text-xl">คุณไม่มีประวัติการลา</p>
                        <p class="text-gray-400">พยายามอย่าให้มีวันลาล่ะ</p>
                    </template>
                </div>
            </div>
        </div>

        <!-- APPROVE MODAL -->
        <div class="modal" id="approve-modal"
            v-if="Object.keys(selectedAbsent).length > 0 && (isAdmin($page.props.user.role) || isTeacher($page.props.user.role))">
            <div class="modal-box text-center">
                <div class="flex justify-end items-center">
                    <a href="#" class="btn btn-circle btn-ghost" role="button" @click="clearSelectAbsent()">
                        <span class="material-symbols-rounded">
                            close
                        </span>
                    </a>
                </div>
                <h3 class="font-bold text-xl">คุณต้องการอนุมัติการลาของ <span class="underline">{{
                        selectedAbsent.user_name
                }}</span></h3>
                <h3 class="font-bold text-xl mb-3">หรือไม่</h3>
                <div
                    class="rounded-md bg-secondary bg-opacity-50 p-3 pl-5 border-l-8 border-primary text-primary text-left">
                    <p class="font-bold">ประเภทการลา: <span class="font-normal">{{ selectedAbsent.type }}</span></p>
                    <p class="font-bold">ลาตั้งแต่: <span class="font-normal">{{ formatDate(selectedAbsent.from)
                    }}</span></p>
                    <p class="font-bold">ลาจนถึง: <span class="font-normal">{{ formatDate(selectedAbsent.to) }}</span>
                    </p>
                    <p class="font-bold">รายละเอียด: <span class="font-normal">{{ selectedAbsent.details }}</span></p>
                </div>
                <div class="modal-action grid grid-cols-2 gap-5">
                    <button type="button" class="btn btn-error text-lg"
                        @click="approveAbsent(false)">ไม่อนุมัติ</button>
                    <button type="button" class="btn btn-success text-lg" @click="approveAbsent()">อนุมัติ</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
