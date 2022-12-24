<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { isStudent, isTeacher, isAdmin, isSystem } from '@/role'
import { Head } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import axios from 'axios';
import { isEmpty } from '@/helper'
</script>
<script>
export default {
    data() {
        return {
            classrooms: [],
            departments: ['ภาษาไทย', 'คณิตศาสตร์', 'วิทยาศาสตร์และเทคโนโลยี', 'ภาษาต่างประเทศ', 'สังคมศึกษา', 'ศิลปะ', 'สุขศึกษาและพลศึกษา', 'การงานอาชีพ', 'แนะแนว'],
            filter: {
                classes: 'all',
                department: 'all',
                room: 'all',
                search: ''
            },
            selectedClassroom: [],
            deleteClassroomData: [],
            selectAll: false,
            mode: 'single',
            rootUrl: route('classroom_list_view') + '#'
        }
    },
    methods: {
        async getClassroom() {
            try {
                let res = null
                if (isStudent(Inertia.page.props.user.role)) {
                    res = await axios('/api/classroom/users/' + Inertia.page.props.user.id)
                }
                else if (isTeacher(Inertia.page.props.user.role)) {
                    let splitName = Inertia.page.props.user.name.split(' ')
                    let name = splitName[0] + '-' + splitName[1]
                    res = await axios('/api/classroom/teacher/' + name)
                }
                else if (isAdmin(Inertia.page.props.user.role)) {
                    res = await axios('/api/classroom/')
                }

                if (res.status == 200) {
                    this.classrooms = res.data
                }
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างดึงข้อมูลห้องเรียน')
                console.log(err)
            }
        },
        async autoCreateClassroom() {
            try {
                let res = await axios.post('/api/classroom/auto-create')
                if (res.status == 200) {
                    alert('สร้างห้องเรียนเสร็จสิ้น')
                    this.getClassroom()
                }
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างสร้างห้องเรียนอัตโนมัติ')
                console.log(err)
            }
        },
        clearDeleteClassroom() {
            window.location.href = this.rootUrl
            setTimeout(() => {
                this.deleteClassroomData = []
                this.selectedClassroom = []
            }, 200)
        },
        pushDeleteData(data) {
            this.deleteClassroomData.push(data)
        },
        async deleteClassroom() {
            try {
                for (let classes of this.deleteClassroomData) {
                    let res = await axios.delete('/api/classroom/delete/' + classes.id)
                    if (res.status != 200) {
                        alert('เกิดปัญหาระหว่างลบห้องเรียน "' + classes.class_id + ' - ' + classes.name + '"')
                        this.getClassroom()
                        return
                    }
                }
                alert('ลบห้องเรียนเรียบร้อยแล้ว')
                this.clearDeleteClassroom()
                this.getClassroom()
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างลบห้องเรียน')
                console.log(err)
            }
        },
        filteredClassroom() {
            let data = this.classrooms

            if (!isEmpty(this.filter.search)) {
                data = data.filter((classroom) => classroom.class_id.indexOf(this.filter.search) > -1 || classroom.name.indexOf(this.filter.search) > -1)
            }

            if (this.filter.classes != 'all') {
                data = data.filter((classroom) => classroom.for_class[this.filter.classes - 1].length > 0)
            }

            if (this.filter.department != 'all') {
                data = data.filter((classroom) => classroom.department == this.filter.department)
            }

            // if (this.filter.class != 'all') {
            //     data = data.filter((classroom) => {
            //         let newArr = classroom.for_class[0].concat(classroom.for_class[1], classroom.for_class[2], classroom.for_class[3], classroom.for_class[4], classroom.for_class[5])
            //         // return newArr.indexOf('room')
            //     })
            // }

            return data
        }
    },
    watch: {
        selectedClassroom() {
            if (this.selectedClassroom.length > 0) {
                this.mode = 'multiple'
            }
            else {
                this.mode = 'single'
            }

            if (this.selectedClassroom.length == this.filteredClassroom().length) {
                this.selectAll = true
            }
            else if (this.selectedClassroom.length > 0 && this.selectedClassroom.length != this.filteredClassroom().length) {
                this.selectAll = false
            }
        },
        selectAll() {
            if (this.selectAll) {
                this.selectedClassroom = this.filteredClassroom()
            }
            else if (!this.selectAll && this.selectedClassroom.length == this.filteredClassroom().length) {
                this.selectedClassroom = []
            }

            if (this.selectedClassroom.length != this.filteredClassroom().length && this.selectedClassroom.length == 0) {
                this.selectAll = false
                this.mode = 'single'
            }
        },
    },
    mounted() {
        this.getClassroom()
    }
}
</script>
<template>

    <Head>
        <title>{{ isStudent($page.props.user.role) || isTeacher($page.props.user.role) ? 'ห้องเรียน' :
                'จัดการห้องเรียน'
        }}</title>
    </Head>
    <AppLayout>
        <template #header>{{ isStudent($page.props.user.role) || isTeacher($page.props.user.role) ? 'ห้องเรียน' :
                'จัดการห้องเรียน'
        }}</template>

        <!-- STUDENT CLASSROOM UI -->
        <div class="bg-white rounded-xl overflow-auto" style="max-height: calc(100vh - 140px)">
            <div class="grid grid-cols-3 gap-5 p-5" v-if="isStudent($page.props.user.role)">
                <div class="rounded-lg hover:drop-shadow-lg transition border flex flex-col cursor-pointer bg-white"
                    v-for="classroom in classrooms" :key="classroom.id">
                    <div
                        class="px-5 py-4 flex items-center justify-between border-b-4 rounded-t-lg bg-sky-200 border-sky-400">
                        <a :href="route('classroom_room_view') + '?id=' + classroom.id">
                            <p class="text-2xl font-bold underline">{{
                                    classroom.name
                            }}</p>
                            <p class="text-md" v-for="(teacher, i) in classroom.teacher"
                                :key="'classroom-' + classroom.id + '-' + i">{{ teacher }}</p>
                        </a>
                        <img src="/img/account.png" style="width: 64px" alt="">
                    </div>
                    <div class="px-1 py-2 text-right h-full flex justify-end items-end">
                        <a :href="route('classroom_room_view')" class="btn btn-sm btn-ghost">เข้าห้องเรียน<span
                                class="ml-2 material-symbols-rounded">
                                google_plus_reshare
                            </span></a>
                    </div>
                </div>
            </div>

            <!-- TEACHER CLASSROOM UI -->
            <div class="bg-white rounded-xl p-5" v-if="isTeacher($page.props.user.role)">
                <p class="text-4xl font-bold text-primary mb-5">ห้องเรียนที่ดูแล</p>
                <div class="grid grid-cols-3 gap-5">
                    <div class="rounded-lg hover:drop-shadow-lg transition border cursor-pointer bg-white"
                        v-for="classroom in classrooms" :key="classroom.id">
                        <div
                            class="px-5 py-4 flex items-center justify-between border-b-4 rounded-t-lg bg-sky-200 border-sky-400">
                            <a :href="route('classroom_room_view') + '?id=' + classroom.id">
                                <p class="text-2xl font-bold underline">{{
                                        classroom.name
                                }}</p>
                                <p class="text-md" v-for="(teacher, i) in classroom.teacher"
                                    :key="'classroom-' + classroom.id + '-' + i">{{ teacher }}</p>
                            </a>
                            <img src="/img/account.png" style="width: 64px" alt="">
                        </div>
                        <div class="px-1 py-2 text-right">
                            <a :href="route('classroom_room_view')" class="btn btn-sm btn-ghost">เข้าห้องเรียน<span
                                    class="ml-2 material-symbols-rounded">
                                    google_plus_reshare
                                </span></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ADMIN CLASSROOM UI -->
            <div class="bg-white rounded-xl p-5" v-if="isAdmin($page.props.user.role)">
                <p class="text-4xl font-bold text-primary mb-5">ห้องเรียนทั้งหมด</p>
                <div class="form-control mb-3">
                    <label class="input-group">
                        <span class="material-symbols-rounded">
                            search
                        </span>
                        <input type="text" placeholder="ค้นหาด้วยรหัสวิชาหรือชื่อวิชา"
                            class="input input-bordered w-full" v-model="filter.search" />
                    </label>
                </div>
                <div class="flex mb-3">
                    <div class="dropdown mr-3" v-if="mode == 'multiple'">
                        <label tabindex="0" class="btn btn-secondary">จัดการ
                            <span class="material-symbols-rounded">
                                expand_more
                            </span>
                        </label>
                        <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52">
                            <li class="text-error">
                                <a href="#delete-modal" @click="deleteClassroomData = selectedClassroom"><span
                                        class="material-symbols-rounded mr-2">
                                        delete
                                    </span>ลบห้องเรียน</a>
                            </li>
                        </ul>
                    </div>
                    <div class="mr-3">
                        <label for="filter-class" class="mr-2 font-medium">ระดับชั้น:</label>
                        <select class="select select-sm text-xs select-bordered" id="filter-class"
                            v-model="filter.classes">
                            <option value="all">ทั้งหมด</option>
                            <option v-for="classes in 6" :key="classes" :value="classes">ม.{{ classes }}</option>
                        </select>
                    </div>
                    <div class="mr-3">
                        <label for="filter-department" class="mr-2 font-medium">กลุ่มสาระการเรียนรู้:</label>
                        <select class="select select-sm text-xs select-bordered" id="filter-department"
                            v-model="filter.department">
                            <option value="all">ทั้งหมด</option>
                            <option v-for="department in departments" :key="department">{{ department }}</option>
                        </select>
                    </div>
                </div>
                <div class="overflow-auto">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr>
                                <th class="text-center">
                                    <input type="checkbox" class="checkbox checkbox-sm" v-model="selectAll">
                                </th>
                                <th class="text-lg">รหัสวิชา</th>
                                <th class="text-lg">ชื่อวิชา</th>
                                <th class="text-lg">อาจารย์ผู้สอน</th>
                                <th class="text-right" v-if="mode == 'single'">
                                    <button class="btn btn-sm btn-info" @click="autoCreateClassroom()"><span
                                            class="material-symbols-rounded mr-2">
                                            library_add
                                        </span>เพิ่มห้องเรียนอัตโนมัติ</button>
                                    <!-- <button class="btn btn-sm btn-success"><span class="material-symbols-rounded mr-2">
                                            add
                                        </span>เพิ่มห้องเรียน</button> -->
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="hover" v-for="(classroom, i) in filteredClassroom()" :key="classroom.id">
                                <td class="text-center">
                                    <input type="checkbox" class="checkbox checkbox-sm" :value="classroom"
                                        v-model="selectedClassroom">
                                </td>
                                <td>{{ classroom.class_id }}</td>
                                <td>{{ classroom.name }}</td>
                                <td>
                                    <p v-for="(teacher, teacheri) in classroom.teacher"
                                        :key="'teacher-' + i + '-' + teacheri">
                                        {{ teacher }}</p>
                                </td>
                                <td v-if="mode == 'single'">
                                    <!-- <button class="btn btn-warning mr-2"><span class="material-symbols-rounded mr-2">
                                            edit
                                        </span>แก้ไข</button> -->
                                    <button class="btn btn-secondary mr-2"><span class="material-symbols-rounded mr-2">
                                            link
                                        </span>คัดลอกลิงก์</button>
                                    <a href="#delete-modal" class="btn btn-error"
                                        @click="pushDeleteData(classroom)"><span class="material-symbols-rounded mr-2">
                                            delete
                                        </span>ลบห้องเรียน</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- DELETE MODAL -->
        <div class="modal" id="delete-modal" v-if="deleteClassroomData.length > 0">
            <div class="modal-box">
                <div class="flex justify-end items-center">
                    <a href="#" class="btn btn-circle btn-ghost" role="button" @click="clearDeleteClassroom()">
                        <span class="material-symbols-rounded">
                            close
                        </span>
                    </a>
                </div>
                <div class="text-center">
                    <p class="text-xl">คุณต้องการลบห้องเรียนเหล่านี้หรือไม่</p>
                    <p class="text-xl font-bold" v-for="classroom in deleteClassroomData"
                        :key="'classroom-' + classroom.id">{{
                                classroom.name
                        }}</p>
                </div>
                <div class="modal-action grid grid-cols-2 gap-5">
                    <button type="button" class="btn btn-gray text-lg" @click="clearDeleteClassroom()">ยกเลิก</button>
                    <button type="button" class="btn btn-error text-lg" @click="deleteClassroom()">ลบห้องเรียน</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
