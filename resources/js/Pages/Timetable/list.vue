<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { isStudent, isTeacher, isAdmin, isSystem } from '@/role'
import { cloneDeep } from 'lodash';
import { Inertia } from '@inertiajs/inertia';
import { Head } from '@inertiajs/inertia-vue3';
import { validateTimetable } from '@/data-validator'
import { isEmpty } from '@/helper'
</script>
<script>
export default {
    data() {
        return {
            time: ['08.10 - 09.00', '09.00 - 09.50', '09.50 - 10.40', '10.40 - 11.30', '11.30 - 12.20', '12.20 - 13.10', '13.10 - 14.00', '14.00 - 14.50', '14.50 - 15.40', '15.40 - 16.30'],
            days: ['จันทร์', 'อังคาร', 'พุธ', 'พฤหัส', 'ศุกร์'],
            timeTables: [],
            filter: {
                class: 'all',
                room: 'all'
            },
            academicYear: null,
            academicTerm: null,
            selectedTimetable: {},
            addTimetableData: {},
            deleteTimetableData: {},
            rootUrl: route('timetable_list_view') + '#',
            errorBag: {
                status: true,
                message: {}
            }
        }
    },
    methods: {
        async getTimeTable() {
            try {
                let res = null
                if (isAdmin(Inertia.page.props.user.role)) {
                    res = await axios('/api/timetable/')
                }
                else if (isTeacher(Inertia.page.props.user.role)) {
                    res = await axios('/api/timetable/class/' + Inertia.page.props.user.data.advisor.class + '/room/' + Inertia.page.props.user.data.advisor.room)
                }
                else {
                    res = await axios('/api/timetable/class/' + Inertia.page.props.user.class + '/room/' + Inertia.page.props.user.room)
                }
                if (res.status == 200) {
                    this.timeTables = res.data

                    if (this.timeTables.length != 0) {
                        this.selectedTimetable = this.timeTables[0]
                    }
                }
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างดึงข้อมูลตารางเรียน')
                console.log(err)
            }
        },
        getAcademicYear() {
            let date = new Date()
            if (date.getMonth() >= 0 && date.getMonth() < 4) {
                this.academicYear = date.getFullYear() - 1
            }
            else {
                this.academicYear = date.getFullYear()
            }
        },
        getAcademicYear() {
            let date = new Date()
            if (date.getMonth() >= 0 && date.getMonth() < 4) {
                this.academicYear = date.getFullYear() - 1
            }
            else {
                this.academicYear = date.getFullYear()
            }
        },
        getAcademicTerm() {
            let date = new Date()
            if (date.getMonth() >= 4 && date.getMonth() < 10) {
                this.academicTerm = 1
            }
            else {
                this.academicTerm = 2
            }
        },
        selectTimetable(data) {
            this.selectedTimetable = cloneDeep(data)
        },
        pushAddTimetable() {
            this.addTimetableData = {
                class: null,
                room: null,
            }
        },
        clearAddTimetableData() {
            window.location.href = this.rootUrl
            setTimeout(() => this.addTimetableData = {}, 200)
        },
        clearDeleteTimetableData() {
            window.location.href = this.rootUrl
            setTimeout(() => this.deleteTimetableData = {}, 200)
        },
        async addTimetable() {
            try {
                //VALIDATE
                this.errorBag = validateTimetable(this.addTimetableData)
                if (!this.errorBag.status) {
                    return
                }

                let res = await axios.post('/api/timetable/create', this.addTimetableData)
                if (res.status == 201) {
                    alert('เพิ่มตารางเรียนสำเร็จ')

                    this.clearAddTimetableData()
                    this.getTimeTable()
                }
            }
            catch (err) {
                alert('มีปัญหาระหว่างเพิ่มตารางเรียน')
                console.log(err)
            }
        },
        async deleteTimetable() {
            try {
                let res = await axios.delete('/api/timetable/delete/' + this.deleteTimetableData.id)
                if (res.status == 200) {
                    alert('ลบตารางเรียนสำเร็จ')

                    this.clearDeleteTimetableData()
                    this.getTimeTable()
                }
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างการลบตารางเรียน')
                console.log(err)
            }
        }
    },
    computed: {
        filteredTimetable() {
            let data = this.timeTables

            //FILTER CLASS
            if (this.filter.class != 'all') {
                data = data.filter((timetable) => timetable.class == this.filter.class)
            }

            //FILTER ROOM
            if (this.filter.room != 'all') {
                data = data.filter((timetable) => timetable.room == this.filter.room)
            }

            return data
        }
    },
    mounted() {
        this.getTimeTable()
        this.getAcademicYear()
        this.getAcademicTerm()
    }
}
</script>
<template>

    <Head>
        <title>ตารางเรียน</title>
    </Head>
    <AppLayout>
        <template #header>ตารางเรียน</template>
        <div class="overflow-auto" style="max-height: calc(100vh - 140px)">
            <div class="rounded-xl bg-white p-5 mb-5">
                <template v-if="Object.keys(selectedTimetable).length != 0">
                    <div class="flex justify-between items-center mb-5">
                        <p class="text-4xl text-primary font-bold" v-if="Object.keys(selectedTimetable).length > 0">
                            ตารางเรียนห้องม.{{ selectedTimetable.class + '/' + selectedTimetable.room }}</p>
                        <a role="button"
                            :href="route('timetable_edit_view') + '/?academic=' + academicYear + '&term=' + academicTerm + '&class=' + selectedTimetable.class + '&room=' + selectedTimetable.room"
                            class="btn btn-warning text-lg"
                            v-if="isAdmin($page.props.user.role) && selectedTimetable.data[academicYear]"><span
                                class="material-symbols-rounded mr-3">
                                edit
                            </span>แก้ไขตารางเรียน</a>
                    </div>
                    <div class="grid grid-cols-11" v-if="selectedTimetable.data[academicYear]">
                        <div class="bg-gray-300 text-center font-bold p-3 text-xl border border-neutral"></div>
                        <div class="col-span-10 grid grid-cols-10">
                            <p class="bg-gray-300 text-center font-bold px-3 py-2 text-xl border border-neutral"
                                v-for="n in 10" :key="n">
                                คาบที่ {{ n
                                }}</p>
                            <p class="bg-gray-100 text-center px-1 py-2 text-sm border border-neutral" v-for="n in time"
                                :key="n">{{ n
                                }}</p>
                        </div>
                        <template v-for="(day, i) in days" :key="day">
                            <div>
                                <p class="text-center py-3 border border-neutral h-full font-bold">{{ day }}</p>
                            </div>
                            <div class="col-span-10 grid grid-cols-10">
                                <label class="text-center py-3 border border-neutral cursor-pointer hover:bg-gray-200"
                                    for="timetable-edit-drawer"
                                    v-for="(subject, subjecti) in selectedTimetable.data[academicYear][academicTerm][i]"
                                    :key="i + '-' + subjecti">
                                    <p class="text-lg font-medium" v-if="!isEmpty(subject.name)">{{
                                            subject.name
                                    }}
                                    </p>
                                    <div v-if="subject.teacher.length > 0">
                                        <p class="text-md" v-for="teacher in subject.teacher"
                                            :key="day + subjecti + teacher">{{
                                                    teacher.split(' ')[0]
                                            }}</p>
                                    </div>
                                    <p class="text-md" v-if="!isEmpty(subject.room)">{{ subject.room }}</p>
                                </label>
                            </div>
                        </template>
                    </div>
                    <div v-else class="text-center">
                        <p class="text-2xl mb-3 text-gray-400">ยังไม่มีตารางเรียนของปีการศึกษา {{ academicYear + 543 }}
                            เทอมที่ {{ academicTerm }}
                        </p>
                        <a :href="route('timetable_edit_view') + '/?academic=' + academicYear + '&term=' + academicTerm + '&class=' + selectedTimetable.class + '&room=' + selectedTimetable.room"
                            role="button"
                            class="btn btn-warning text-lg">คลิกที่นี่เพื่อเพิ่มตารางเรียนของปีการศึกษาในเทอมนี้</a>
                    </div>
                </template>
            </div>

            <div class="rounded-xl p-5 bg-white" v-if="isAdmin($page.props.user.role)">
                <div class="flex items-center mb-3">
                    <label for="filter-class" class="mr-2">ระดับชั้น:</label>
                    <select id="filter-class" class="select select-bordered select-sm text-xs mr-3"
                        v-model="filter.class">
                        <option value="all">ทั้งหมด</option>
                        <option v-for="classes in 6" :key="classes" :value="classes">ม.{{ classes }}</option>
                    </select>
                    <label for="filter-room" class="mr-2">ห้อง:</label>
                    <select id="filter-room" class="select select-bordered select-sm text-xs" v-model="filter.room">
                        <option value="all">ทั้งหมด</option>
                        <option v-for="room in 14" :key="room" :value="room">{{ room }}</option>
                    </select>
                </div>
                <div class="overflow-auto">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr>
                                <th></th>
                                <th class="text-lg text-center">ระดับชั้น</th>
                                <th class="text-lg text-center">ห้อง</th>
                                <th class="text-right">
                                    <a role="button" href="#add-timetable-form-modal" class="btn btn-sm btn-success"
                                        @click="pushAddTimetable()"><span class="material-symbols-rounded mr-2">
                                            add
                                        </span>เพิ่มตารางเรียน</a>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(timetable, i) in filteredTimetable" :key="timetable.id">
                                <th class="text-center">{{ i + 1 }}</th>
                                <td class="text-center">{{ timetable.class }}</td>
                                <td class="text-center">{{ timetable.room }}</td>
                                <td>
                                    <button class="btn btn-secondary mr-3" type="button"
                                        @click="selectTimetable(timetable)"><span class="material-symbols-rounded mr-2">
                                            done
                                        </span>เลือกห้องนี้</button>
                                    <a role="button" href="#delete-modal" class="btn btn-error" type="button"
                                        @click="deleteTimetableData = timetable"><span
                                            class="material-symbols-rounded mr-2">
                                            delete
                                        </span>ลบตารางเรียน</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <!-- ADD BY FORM MODAL -->
        <div class="modal" id="add-timetable-form-modal" v-if="Object.keys(addTimetableData).length > 0">
            <form @submit.prevent="addTimetable()">
                <div class="modal-box w-full max-w-5xl">
                    <div class="flex justify-between items-center mb-3">
                        <div class="flex">
                            <a href="#add-modal" role="button"
                                class="btn btn-sm text-primary hover:bg-transparent hover:border hover:border-b-2 text-xl btn-ghost mr-3">
                                <span class="material-symbols-rounded">
                                    arrow_back
                                </span>
                                ย้อนกลับ</a>
                            <h3 class="text-gray-400 text-xl pt-0.5">เพิ่มตารางเรียนด้วยฟอร์ม</h3>
                        </div>
                        <a href="#" class="btn btn-circle btn-ghost" role="button" @click="clearAddTimetableData()">
                            <span class="material-symbols-rounded">
                                close
                            </span>
                        </a>
                    </div>

                    <div class="mb-3 pl-4">
                        <p class="text-xl font-bold">ข้อมูลทั่วไป</p>
                        <p class="text-gray-500">ข้อมูลทั่วไปที่เกี่ยวกับตารางเรียน</p>
                    </div>
                    <div class="grid grid-cols-2 gap-5 gap-y-3 px-4 mb-7">
                        <div class="form-control">
                            <label for="edit-class" class="mb-1 required">ระดับชั้น</label>
                            <input type="number" min="1" id="edit-class" class="input input-sm input-bordered"
                                :class="{ 'border-error': errorBag.message.class }" placeholder="ระดับชั้น"
                                v-model="addTimetableData.class">
                            <small class="text-error mt-1">{{ errorBag.message.class }}</small>
                        </div>
                        <div class="form-control">
                            <label for="edit-room" class="mb-1 required">ห้อง</label>
                            <input type="number" min="1" id="edit-room" class="input input-sm input-bordered"
                                :class="{ 'border-error': errorBag.message.room }" placeholder="ห้อง"
                                v-model="addTimetableData.room">
                            <small class="text-error mt-1">{{ errorBag.message.room }}</small>
                        </div>
                    </div>
                    <div class="modal-action">
                        <button type="submit" class="btn btn-secondary">เพิ่มตารางเรียน</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- DELETE MODAL -->
        <div class="modal" id="delete-modal" v-if="Object.keys(deleteTimetableData).length > 0">
            <div class="modal-box">
                <div class="flex justify-end items-center">
                    <a href="#" class="btn btn-circle btn-ghost" role="button" @click="clearDeleteTimetableData()">
                        <span class="material-symbols-rounded">
                            close
                        </span>
                    </a>
                </div>
                <div class="text-center">
                    <p class="text-xl">คุณต้องการลบตารางเรียนห้อง<span class="font-bold">ม.{{ deleteTimetableData.class
                            + '/' +
                            deleteTimetableData.room
                    }}</span> หรือไม่</p>
                </div>
                <div class="modal-action grid grid-cols-2 gap-5">
                    <button type="button" class="btn btn-gray text-lg"
                        @click="clearDeleteTimetableData()">ยกเลิก</button>
                    <button type="button" class="btn btn-error text-lg" @click="deleteTimetable()">ลบตารางเรียน</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
