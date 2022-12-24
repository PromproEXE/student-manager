<script setup>
import { Head } from '@inertiajs/inertia-vue3';
import { isAdmin, isTeacher } from '@/role'
import axios from 'axios';
import { cloneDeep, isEqual } from 'lodash';
import AppLayout from '../../Layouts/AppLayout.vue';
import { isEmpty } from '@/helper'
</script>

<script>
function getUrlParameter(param) {
    let url = window.location.search
    let urlParam = new URLSearchParams(url)

    return urlParam.get(param)
}
export default {
    data() {
        return {
            classes: getUrlParameter('class'),
            room: getUrlParameter('room'),
            academicYear: getUrlParameter('academic'),
            academicTerm: getUrlParameter('term'),
            timetable: {},
            timetable_temp: {},
            time: ['08.10 - 09.00', '09.00 - 09.50', '09.50 - 10.40', '10.40 - 11.30', '11.30 - 12.20', '12.20 - 13.10', '13.10 - 14.00', '14.00 - 14.50', '14.50 - 15.40', '15.40 - 16.30'],
            days: ['จันทร์', 'อังคาร', 'พุธ', 'พฤหัส', 'ศุกร์'],
            selectedSubject: {},
            roomAmount: [],
            selectedDay: null,
            selectedPeriod: null,
            loading: true,
            subjects: [],
            filter: {
                class: 'all',
                room: 'all',
                search: '',
            },
            showSaveBtn: false,
        }
    },
    methods: {
        async getTimetable() {
            try {
                let res = await axios('/api/timetable/class/' + this.classes + '/room/' + this.room)
                if (res.status == 200) {
                    this.timetable = res.data[0]
                    this.processTimetable()
                    this.timetable_temp = cloneDeep(this.timetable)
                    this.loading = false
                }
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างดึงข้อมูลตารางเรียน')
                console.log(err)
            }
        },
        async getSubject() {
            try {
                let res = await axios('/api/subject/')
                if (res.status == 200) {
                    this.subjects = res.data
                }
            }
            catch (err) {
                console.log('เกิดปัญหาระหว่างดึงข้อมูลรายวิชา')
                console.log(err)
            }
        },
        async getRoomAmount() {
            try {
                let res = await axios('/api/classroom/room/amount')
                if (res.status == 200) {
                    this.roomAmount = res.data
                }
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างดึงข้อมูลจำนวนห้อง')
                console.log(err)
            }
        },
        async processTimetable() {
            try {
                let hasChange = false
                let timetableData = []
                let templateData = {
                    subject_id: 'ว000000',
                    name: 'ว่าง',
                    room: '',
                    teacher: [],
                    type: 'study'
                }

                for (let i = 0; i < 5; i++) {
                    timetableData.push([])
                    for (let j = 0; j < 10; j++) {
                        timetableData[i].push(templateData)
                    }
                }

                //CHECK DATA IS ARRAY
                if (Array.isArray(this.timetable.data)) {
                    this.timetable.data = {}
                }

                //CHECK HAS ACADEMIC YEAR DATA
                if (!this.timetable.data.hasOwnProperty(this.academicYear)) {
                    this.timetable.data[this.academicYear] = {}
                    hasChange = true
                }

                //CHECK HAS TERM IN ACADEMIC YEAR
                if (!this.timetable.data[this.academicYear][this.academicTerm]) {
                    this.timetable.data[this.academicYear][this.academicTerm] = timetableData
                    hasChange = true
                }

                if (hasChange) {
                    let res = await axios.put('/api/timetable/update/' + this.timetable.id, this.timetable)
                    if (!res.status == 200) {
                        throw 'UPDATE ERROR'
                    }
                }
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างประมวลผลตารางเรียน')
                console.log(err)
            }
        },
        selectPeriod(day_i, subject_i, data) {
            this.selectedDay = day_i
            this.selectedPeriod = subject_i
            this.selectedSubject = cloneDeep(data)
        },
        selectSubject(data) {
            this.selectedSubject.subject_id = data.subject_id
            this.selectedSubject.name = data.name
            this.selectedSubject.teacher = data.teacher
        },
        assignSubjectToPeriod() {
            this.timetable.data[this.academicYear][this.academicTerm][this.selectedDay][this.selectedPeriod] = this.selectedSubject
            document.getElementById('timetable-edit-drawer').checked = false
        },
        async updateTimetable() {
            try {
                let res = await axios.put('/api/timetable/update/' + this.timetable.id, this.timetable)
                if (res.status == 200) {
                    alert('บันทึกข้อมูลแล้ว')
                    this.getTimetable()
                }
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างบันทึกข้อมูลตารางเรียน')
                console.log(err)
            }
        }
    },
    watch: {
        'selectedSubject.type'() {
            if (this.selectedSubject.type == 'study') {
                this.selectedSubject.subject_id = ''
                this.selectedSubject.name = ''
                this.selectedSubject.teacher = []
            }
            else if (this.selectedSubject.type == 'break') {
                this.selectedSubject.subject_id = ''
                this.selectedSubject.name = 'พักกลางวัน'
                this.selectedSubject.teacher = []
            }
            else {
                this.selectedSubject.subject_id = ''
                this.selectedSubject.name = ''
                this.selectedSubject.teacher = []
            }
        },
        timetable: {
            handler() {
                if (!isEqual(this.timetable, this.timetable_temp)) {
                    this.showSaveBtn = true
                }
                else {
                    this.showSaveBtn = false
                }
            },
            deep: true
        }
    },
    computed: {
        filteredSubject() {
            let data = this.subjects
            let classes = this.filter.class
            let room = this.filter.room
            let searchText = this.filter.search

            //FILTER CLASS
            if (classes != 'all') {
                data = data.filter(subject => subject.for_class[classes - 1].length > 0)
            }

            //FILTER ROOM
            if (room != 'all' && classes == 'all') {
                data = data.filter(subject => {
                    let newArr = subject.for_class[0].concat(subject.for_class[1], subject.for_class[2], subject.for_class[3], subject.for_class[4], subject.for_class[5])

                    return newArr.indexOf(room) > -1
                })
            }
            else if (room != 'all' && classes != 'all') {
                data = data.filter(subject => subject.for_class[classes - 1].indexOf(room) > -1)
            }

            //FILTER SEARCH
            if (!isEmpty(searchText)) {
                data = data.filter(subject => subject.subject_id.indexOf(searchText) > -1 || subject.name.indexOf(searchText) > -1)
            }

            return data
        },
        filteredRoom() {
            if (this.filter.class != 'all') {
                return Array.from(Array(this.roomAmount[this.filter.class - 1]).keys()).map((room) => room + 1)
            }
            else {
                if (isFinite(Math.max.apply(1, this.roomAmount))) {
                    return Array.from(Array(Math.max.apply(1, this.roomAmount)).keys()).map((room) => room + 1)
                }
            }
        }
    },
    mounted() {
        this.getTimetable()
        this.getSubject()
        this.getRoomAmount()
    }
}
</script>

<template>
    <AppLayout>

        <Head>
            <title>แก้ไขตารางเรียน</title>
        </Head>

        <template #header>แก้ไขตารางเรียน</template>

        <div class="overflow-auto">
            <div class="drawer drawer-end rounded-xl" style="max-height: calc(100vh - 140px);">
                <input id="timetable-edit-drawer" type="checkbox" class="drawer-toggle" />
                <div class="drawer-content overflow-auto" style="max-height: calc(100vh - 140px);">
                    <!-- Page content here -->
                    <!-- <label for="my-drawer" class="btn btn-primary drawer-button">Open drawer</label> -->
                    <div class="rounded-xl p-5 bg-white">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <p class="text-3xl text-primary font-bold">ตารางเรียนห้องม.{{ classes + '/' + room }}
                                </p>
                                <p class="text-gray-500 ">ปีการศึกษา {{ parseInt(academicYear) + 543 }} | เทอมที่ {{
                                        academicTerm
                                }}
                                </p>
                            </div>
                            <button class="btn btn-success text-lg" v-if="showSaveBtn" @click="updateTimetable()"><span
                                    class="material-symbols-rounded mr-2">
                                    save
                                </span>บันทึกข้อมูลตารางเรียน</button>
                        </div>
                        <div class="grid grid-cols-11" v-if="!loading">
                            <div class="bg-gray-300 text-center font-bold p-3 text-xl border border-neutral"></div>
                            <div class="col-span-10 grid grid-cols-10">
                                <p class="bg-gray-300 text-center font-bold px-3 py-2 text-xl border border-neutral"
                                    v-for="n in 10" :key="n">
                                    คาบที่ {{ n
                                    }}</p>
                                <p class="bg-gray-100 text-center px-1 py-2 text-sm border border-neutral"
                                    v-for="n in time" :key="n">{{ n
                                    }}</p>
                            </div>
                            <template v-for="(day, i) in days" :key="day">
                                <div>
                                    <p class="text-center py-3 border border-neutral h-full font-bold">{{ day }}</p>
                                </div>
                                <div class="col-span-10 grid grid-cols-10">
                                    <label
                                        class="text-center py-3 border border-neutral cursor-pointer hover:bg-gray-200"
                                        for="timetable-edit-drawer"
                                        v-for="(subject, subjecti) in timetable.data[academicYear][academicTerm][i]"
                                        :key="i + '-' + subjecti" @click="selectPeriod(i, subjecti, subject)">
                                        <p class="text-lg font-medium" v-if="!isEmpty(subject.name)">{{
                                                subject.name
                                        }}
                                        </p>
                                        <div v-if="subject.teacher.length > 0">
                                            <p class="text-md" v-for="teacher in subject.teacher"
                                                :key="day + subjecti + teacher">{{ teacher.split(' ')[0] }}</p>
                                        </div>
                                        <p class="text-md" v-if="!isEmpty(subject.room)">{{ subject.room }}</p>
                                    </label>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="drawer-side">
                    <label for="timetable-edit-drawer" class="drawer-overlay"></label>
                    <ul class="menu p-5 w-5/12 bg-base-100 text-base-content">
                        <!-- Sidebar content here -->
                        <p class="text-2xl font-bold text-primary mb-2">คาบที่ {{ selectedPeriod + 1 }} ของวัน{{
                                days[selectedDay]
                        }}</p>
                        <p class="mb-2">ตอนนี้กำลังเลือก <span class="font-bold underline">{{ selectedSubject.type ==
                                'study' ? selectedSubject.subject_id + ' - ' + selectedSubject.name :
                                selectedSubject.type == 'break' ? 'พักกลางวัน' : 'คาบว่าง'
                        }}</span></p>
                        <div class="flex mb-2">
                            <label class="label cursor-pointer mr-3">
                                <input type="radio" name="edit-period-attribute"
                                    class="radio radio-sm radio-warning checked:text-warning focus:text-warning active:text-warning"
                                    value="study" v-model="selectedSubject.type" />
                                <span class="ml-2">คาบเรียน</span>
                            </label>
                            <label class="label cursor-pointer mr-3">
                                <input type="radio" name="edit-period-attribute"
                                    class="radio radio-sm radio-warning checked:text-warning focus:text-warning active:text-warning"
                                    value="break" v-model="selectedSubject.type" />
                                <span class="ml-2">คาบพักกลางวัน</span>
                            </label>
                            <div class="tooltip tooltip-secondary" data-tip="คาบที่นักเรียนไม่มีเรียน">
                                <label class="label cursor-pointer mr-3">
                                    <input type="radio" name="edit-period-attribute"
                                        class="radio radio-sm radio-warning checked:text-warning focus:text-warning active:text-warning"
                                        value="no_study" v-model="selectedSubject.type" />
                                    <span class="ml-2">คาบว่าง<span class="badge ml-2 badge-secondary">?</span></span>
                                </label>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-3" v-if="selectedSubject.type == 'study'">
                            <div class="form-control">
                                <label for="filter-subject-class" class="mb-1 font-medium">ระดับชั้น</label>
                                <select id="filter-subject-class" class="select select-sm text-xs select-bordered"
                                    v-model="filter.class">
                                    <option value="all">ทั้งหมด</option>
                                    <option v-for="classes in 6" :key="classes" :value="classes">ม.{{ classes }}
                                    </option>
                                </select>
                            </div>
                            <div class="form-control">
                                <label for="filter-subject-room" class="mb-1 font-medium">ห้อง</label>
                                <select id="filter-subject-room" class="select select-sm text-xs select-bordered"
                                    v-model="filter.room">
                                    <option value="all">ทั้งหมด</option>
                                    <option v-for="room in filteredRoom" :key="room" :value="room">{{ room }}
                                    </option>
                                </select>
                            </div>
                            <div class="form-control col-span-2">
                                <input type="text" class="input input-bordered" id="filter-subject-search"
                                    placeholder="ค้นหาโดยใช้รหัสวิชาหรือชื่อวิชา" v-model="filter.search">
                            </div>
                        </div>
                        <div class="overflow-auto rounded-lg" v-if="selectedSubject.type == 'study'">
                            <div class="grid grid-cols-1 gap-3" style="max-height: calc(100vh - 530px)">
                                <button class="btn btn-secondary block h-fit text-left p-3"
                                    @click="selectSubject(subject)" v-for="subject in filteredSubject"
                                    :key="subject.subject_id">
                                    <p class="text-xl font-bold capitalize">{{ subject.subject_id }} - {{ subject.name
                                    }}
                                    </p>
                                    <p class="font-normal" v-for="teacher in subject.teacher"
                                        :key="subject.subject_id + teacher">{{ teacher
                                        }}</p>
                                </button>
                            </div>
                        </div>
                        <div class="grid grid-col-2 gap-3 mt-auto">
                            <button class="btn btn-success text-lg" @click="assignSubjectToPeriod()"><span
                                    class="material-symbols-rounded mr-2">
                                    save
                                </span>บันทึก</button>
                        </div>
                        <!-- <div class="form-control mb-3">
                            <label for="edit-subject" class="mb-1 font-medium">วิชา</label>
                            <select id="edit-subject" class="select select-bordered" v-model="selectedSubject">
                                <option value="all">ทั้งหมด</option>
                                <option v-for="subject in filteredSubject" :key="subject.id" :value="subject">{{
                                        subject.id
                                }} - {{ subject.name }}
                                </option>
                            </select>
                        </div> -->
                    </ul>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
<style>
[type='radio']:checked {
    background-image: none;
}
</style>
