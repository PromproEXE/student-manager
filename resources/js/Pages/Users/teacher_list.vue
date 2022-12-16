<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import axios from 'axios';
import { cloneDeep } from 'lodash';
import * as validate from '@/data-validator'
import * as role from '@/role'
import { isEmpty } from '@/helper'
</script>
<script>
export default {
    data() {
        return {
            teachers: [],
            searchText: '',
            departments: ['ภาษาไทย', 'คณิตศาสตร์', 'วิทยาศาสตร์และเทคโนโลยี', 'ภาษาต่างประเทศ', 'สังคมศึกษา', 'ศิลปะ', 'สุขศึกษาและพลศึกษา', 'การงานอาชีพ', 'แนะแนว'],
            filter: {
                department: 'all',
                class: 'all'
            },
            selectedTeacher: [],
            selectAll: false,
            editUserData: {},
            deleteUserData: [],
            addUserData: [],
            errorBag: {
                status: true,
                message: {}
            },
            mode: 'single',
            rootUrl: route('users_teacher_list_view') + '#',
        }
    },
    methods: {
        async getTeacherList() {
            try {
                let res = await axios('/api/users/teacher')
                if (res.status == 200) {
                    this.teachers = res.data
                }
                else {
                    return alert('มีปัญหาระหว่างดึงข้อมูล')
                }
            }
            catch (err) {
                alert('มีปัญหาระหว่างดึงข้อมูล')
                console.log(err)
            }
        },
        getEditUserData(data) {
            this.editUserData = cloneDeep(data)
        },
        pushAddUser() {
            this.addUserData.push({
                name: null,
                eng_name: null,
                class: null,
                data: {
                    department: null,
                    advisor: {
                        class: null,
                        room: null,
                    }
                },
                role: ['teacher']
            })
        },
        clearAddUser() {
            this.addUserData = []
        },
        pushDeleteUser(data) {
            this.deleteUserData.push(data)
        },
        clearDeleteUser() {
            this.deleteUserData = []
        },
        readAdduserCsv() {
            let reader = new FileReader()
            let fileElement = document.getElementById('add-user-csv-file').files
            reader.addEventListener('load', (event) => {
                //SPLIT \n TO ARRAY
                let rawData = reader.result.split('\n')
                rawData.splice(0, 1)

                for (let data of rawData) {
                    let teacher = data.split(',')
                    let advisor = {
                        class: null,
                        room: null,
                    }

                    if (!isEmpty(teacher[4])) {
                        let splitClass = teacher[4].split('/')
                        advisor.class = splitClass[0]
                        advisor.room = splitClass[1]
                    }
                    this.addUserData.push({
                        name: teacher[0],
                        eng_name: teacher[1],
                        class: teacher[2],
                        data: {
                            department: teacher[3],
                            advisor: advisor
                        },
                        role: ['teacher']
                    })
                }

                console.log(this.addUserData)
            })

            for (let file of fileElement) {
                reader.readAsText(file)
            }
        },
        async addUser() {
            //VALIDATE DATA
            this.errorBag = { status: true, message: {} }
            for (let user of this.addUserData) {
                this.errorBag = validate.validateUser(user)
            }
            if (!this.errorBag.status) {
                return
            }

            //ADD USER
            try {
                let res = await axios.post('/api/users/create', this.addUserData)
                if (res.status == 200) {
                    alert('เพิ่มอาจารย์สำเร็จ')

                    window.location.href = this.rootUrl
                    document.getElementById('add-user-csv-file').value = null
                    this.addUserData = []

                    let res_student = await axios('/api/users/teacher')
                    this.teachers = res_student.data
                }
            }
            catch (err) {
                alert('มีปัญหาระหว่างเพิ่มผู้ใช้')
                console.log(err)
            }

            //CLEAR ERROR BAG
            this.errorBag = { status: true, message: {} }
        },
        async updateUserData() {
            try {
                //VALIDATE
                this.errorBag = { status: true, message: {} }
                this.errorBag = validate.validateUser(this.editUserData)
                if (!this.errorBag.status) {
                    return
                }

                let res = await axios.put('/api/users/update/' + this.editUserData.id, this.editUserData)
                if (res.status == 200) {
                    alert('แก้ไขข้อมูลเรียบร้อยแล้ว')
                }
            }
            catch (err) {
                alert('มีปัญหาระหว่างการบันทึกข้อมูล')
                console.log(err)
            }
            this.errorBag = { status: true, message: {} }
        },
        async deleteUser() {
            try {
                for (let user of this.deleteUserData) {
                    let res = await axios.delete('/api/users/delete/' + user.id)
                }
                alert('ลบอาจารย์เรียบร้อยแล้ว')
                let res_student = await axios('/api/users/teacher')
                this.teachers = res_student.data

            }
            catch (err) {
                alert('มีปัญหาระหว่างลบข้อมูล')
                console.log(err)
            }
            this.deleteUserData = []
            window.location.href = this.rootUrl
        }
    },
    computed: {
        searchedData() {
            //SEARCH FILTER
            let data = this.teachers
            if (this.searchText != '' || this.searchText != null) {
                data = this.teachers.filter((teacher) => teacher.name?.toLowerCase().indexOf(this.searchText) > -1 || '')
            }

            //DEPARTMENT FILTER
            if (this.filter.department != 'all') {
                data = data.filter((teacher) => teacher.data.department == this.filter.department)
            }

            //DEPARTMENT FILTER
            if (this.filter.class != 'all') {
                data = data.filter((teacher) => teacher.class == this.filter.class)
            }

            return data
        }
    },
    watch: {
        selectedTeacher() {
            if (this.selectedTeacher.length > 0) {
                this.mode = 'multiple'
            }
            else {
                this.mode = 'single'
                this.selectAll = false
            }

            if (this.selectedTeacher.length == this.teachers.length && this.teachers.length != 0) {
                this.selectAll = true
            }
            else {
                this.selectAll = false
            }
        },
        selectAll() {
            if (this.selectAll) {
                this.selectedTeacher = this.teachers
            }
            else {
                this.selectedTeacher = []
            }
        }
    },
    mounted() {
        this.getTeacherList()
    }
}
</script>
<template>

    <Head>
        <title>จัดการอาจารย์</title>
    </Head>
    <AppLayout>
        <template #header>จัดการอาจารย์</template>
        <div class="bg-white rounded-xl p-5" v-if="role.isAdmin($page.props.user.role)">
            <div class="form-control mb-3">
                <label class="input-group">
                    <span class="material-symbols-rounded">
                        search
                    </span>
                    <input type="text" placeholder="ค้นหาด้วยชื่ออาจารย์" class="input input-bordered w-full"
                        v-model="searchText" />
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
                            <a href="#delete-modal" @click="deleteUserData = selectedTeacher"><span
                                    class="material-symbols-rounded mr-2">
                                    delete
                                </span>ลบอาจารย์</a>
                        </li>
                    </ul>
                </div>
                <div class="mr-3">
                    <label for="filter-class" class="mr-2">กลุ่มสาระการเรียนรู้</label>
                    <select class="select select-bordered" id="filter-class" v-model="filter.department">
                        <option value="all">ทั้งหมด</option>
                        <option v-for="department in departments" :key="department" :value="department">
                            กลุ่มสาระการเรียนรู้{{ department }}
                        </option>
                    </select>
                </div>
                <div class="mr-3">
                    <label for="filter-class" class="mr-2">ระดับชั้น</label>
                    <select class="select select-bordered" id="filter-class" v-model="filter.class">
                        <option value="all">ทั้งหมด</option>
                        <option v-for="classes in 6" :key="classes" :value="classes">
                            ม.{{ classes }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="overflow-auto" style="max-height: calc(100vh - 300px)">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th class="text-center">
                                <input type="checkbox" class="checkbox checkbox-bordered checkbox-sm"
                                    v-model="selectAll">
                            </th>
                            <th class="text-lg">ชื่อ-นามสกุล</th>
                            <th class="text-lg">กลุ่มสาระการเรียนรู้</th>
                            <th class="text-lg text-center">ระดับชั้น</th>
                            <th class="text-lg text-center">สอนรายวิชา</th>
                            <th class="text-lg text-center">อาจารย์ที่ปรึกษา</th>
                            <th class="text-right" v-if="mode == 'single'">
                                <a href="#add-modal" class="btn btn-sm btn-success"><span
                                        class="material-symbols-rounded mr-2">
                                        add
                                    </span>เพิ่มอาจารย์</a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="hover" v-for="teacher in searchedData" :key="teacher.id">
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm" :value="teacher"
                                    v-model="selectedTeacher">
                            </td>
                            <td>{{ teacher.name }}</td>
                            <td>{{ teacher.data.department }}</td>
                            <td class="text-center">{{ teacher.class }}</td>
                            <td class="text-center">{{ teacher.data.classroom || '-' }}</td>
                            <td class="text-center">{{ teacher.data.advisor.class + '/' + teacher.data.advisor.room ||
                                    '-'
                            }}</td>
                            <td v-if="mode == 'single'">
                                <a href="#edit-modal" @click="getEditUserData(teacher)"
                                    class="btn btn-warning mr-2"><span class="material-symbols-rounded mr-2">
                                        edit
                                    </span>แก้ไขข้อมูล</a>
                                <a href="#delete-modal" class="btn btn-error" @click="pushDeleteUser(teacher)"><span
                                        class="material-symbols-rounded mr-2">
                                        delete
                                    </span>ลบอาจารย์</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- EDIT MODAL -->
        <div class="modal" id="edit-modal" v-if="Object.keys(editUserData).length > 0">
            <form @submit.prevent="updateUserData()">
                <div class="modal-box w-full max-w-5xl">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-bold text-3xl">แก้ไขข้อมูลอาจารย์</h3>
                        <a href="#" class="btn btn-circle btn-ghost" role="button">
                            <span class="material-symbols-rounded">
                                close
                            </span>
                        </a>
                    </div>

                    <div class="mb-3">
                        <p class="text-xl font-bold">ข้อมูลทั่วไป</p>
                        <p class="text-gray-500">ข้อมูลทั่วไปที่เกี่ยวกับอาจารย์</p>
                    </div>
                    <div class="grid grid-cols-3 gap-5 gap-y-3 px-5 mb-7">
                        <div class="form-control">
                            <label for="edit-name" class="mb-1 required">ชื่อ-นามสกุล ภาษาไทย</label>
                            <input type="text" id="edit-name" class="input input-sm input-bordered"
                                :class="{ 'border-error': errorBag.message.name }" placeholder="ชื่อ-นามสกุล"
                                v-model="editUserData.name">
                            <small class="text-error mt-1">{{ errorBag.message.name }}</small>
                        </div>
                        <div class="form-control">
                            <label for="edit-name" class="mb-1 required">ชื่อ-นามสกุล ภาษาอังกฤษ</label>
                            <input type="text" id="edit-name" class="input input-sm input-bordered"
                                :class="{ 'border-error': errorBag.message.eng_name }"
                                placeholder="ชื่อ-นามสกุล ภาษาอังกฤษ" v-model="editUserData.eng_name">
                            <small class="text-error mt-1">{{ errorBag.message.eng_name }}</small>
                        </div>
                        <div class="form-control">
                            <label for="edit-class" class="mb-1 required">ระดับชั้น</label>
                            <input type="number" min="1" id="edit-class" class="input input-sm input-bordered"
                                :class="{ 'border-error': errorBag.message.class }" placeholder="ระดับชั้น"
                                v-model="editUserData.class">
                            <small class="text-error mt-1">{{ errorBag.message.class }}</small>
                        </div>
                        <div>
                            <label for="edit-department" class="required">กลุ่มสาระการเรียนรู้</label>
                            <select id="edit-department" class="select select-sm text-xs select-bordered w-full mt-1"
                                v-model="editUserData.data.department"
                                :class="{ 'border-error': errorBag.message.department }">
                                <option v-for="department in departments" :key="department" :value="department">{{
                                        department
                                }}</option>
                            </select>
                            <small class="text-error mt-1">{{ errorBag.message.department }}</small>
                        </div>
                        <div>
                            <label for="">อาจารย์ที่ปรึกษา</label>
                            <div class="grid grid-cols-2 gap-5 mt-1">
                                <div class="form-control">
                                    <input type="number" min="1" id="edit-advisor-class"
                                        class="input input-sm input-bordered" placeholder="ระดับชั้น"
                                        :class="{ 'border-error': errorBag.message.advisor_class }"
                                        v-model="editUserData.data.advisor.class">
                                    <small class="text-error mt-1">{{ errorBag.message.advisor_class }}</small>
                                </div>
                                <div class="form-control">
                                    <input type="number" min="1" id="edit-advisor-room"
                                        class="input input-sm input-bordered" placeholder="ห้อง"
                                        :class="{ 'border-error': errorBag.message.advisor_room }"
                                        v-model="editUserData.data.advisor.room">
                                    <small class="text-error mt-1">{{ errorBag.message.advisor_room }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <p class="text-xl font-bold">ข้อมูลการใช้งาน</p>
                        <p class="text-gray-500">ข้อมูลที่เกี่ยวกับการใข้งานระบบของอาจารย์</p>
                    </div>
                    <div class="grid grid-cols-3 gap-5 gap-y-3 px-5">
                        <div class="form-control">
                            <label for="edit-std-id" class="mb-1 required">อีเมล</label>
                            <input type="email" id="edit-std-id" class="input input-sm input-bordered"
                                :class="{ 'border-error': errorBag.message.email }" placeholder="student@example.com"
                                v-model="editUserData.email">
                            <small class="text-error mt-1">{{ errorBag.message.email }}</small>
                        </div>
                    </div>
                    <div class="modal-action">
                        <button type="submit" class="btn btn-secondary">บันทึกข้อมูล</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- ADD MODAL -->
        <div class="modal" id="add-modal">
            <div class="modal-box w-full max-w-5xl">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-bold text-3xl">เพิ่มรายชื่ออาจารย์</h3>
                    <a href="#" class="btn btn-circle btn-ghost" role="button">
                        <span class="material-symbols-rounded">
                            close
                        </span>
                    </a>
                </div>
                <div class="grid grid-cols-2 gap-5">
                    <a role="button" href="#add-user-csv-modal"
                        class="rounded-lg bg-secondary p-5 py-36 text-center bg-opacity-50 border-4 border-primary">
                        <p class="text-primary text-2xl font-bold">เพิ่มรายชื่อด้วยไฟล์ .csv</p>
                        <p class="text-primary">เพิ่มโดยการใช้ไฟล์ .csv ของระบบไปทำการเพิ่มข้อมูลอาจารย์
                            สามารถเพิ่มข้อมูลอาจารย์ได้หลายคนในครั้งเดียว</p>
                    </a>
                    <a role="button" href="#add-user-form-modal"
                        class="rounded-lg bg-gray-100 p-5 py-36 text-center bg-opacity-50 border-4 border-gray-400"
                        @click="pushAddUser()">
                        <p class="text-primary text-2xl font-bold">เพิ่มรายชื่อด้วยฟอร์มของระบบ</p>
                        <p class="text-primary">เพิ่มโดยการกรอกฟอร์มลงในระบบ เหมาะกับการเพิ่มอาจารย์จำนวนน้อย ๆ</p>
                    </a>
                </div>
            </div>
        </div>

        <!-- ADD BY CSV MODAL -->
        <div class="modal" id="add-user-csv-modal">

            <div class="modal-box w-full max-w-5xl">
                <form @submit.prevent="addUser()">
                    <div class="flex justify-between items-center mb-3">
                        <div class="flex">
                            <a href="#add-modal" role="button"
                                class="btn btn-sm text-primary hover:bg-transparent hover:border hover:border-b-2 text-xl btn-ghost mr-3">
                                <span class="material-symbols-rounded">
                                    arrow_back
                                </span>
                                ย้อนกลับ</a>
                            <h3 class="text-gray-400 text-xl pt-0.5">เพิ่มรายชื่ออาจารย์ด้วยไฟล์ CSV</h3>
                        </div>
                        <a href="#" class="btn btn-circle btn-ghost" role="button" @click="clearAddUser()">
                            <span class="material-symbols-rounded">
                                close
                            </span>
                        </a>
                    </div>
                    <div class="px-4">
                        <div class="form-control mb-3">
                            <div class="flex mb-1">
                                <label for="add-csv-file" class="mr-3 text-lg">เลือกไฟล์</label>
                                <a href="/download/example-teacher-csv.csv" role="button"
                                    class="btn btn-sm btn-warning">ดาวน์โหลดไฟล์เตรียมข้อมูล</a>
                            </div>
                            <input type="file" id="add-user-csv-file"
                                class="file-input file-input-bordered file-input-primary w-full mb-5" multiple
                                @change="readAdduserCsv()" accept=".csv" />
                            <div class="grid grid-cols-2 gap-5">
                                <button type="submit" class="btn btn-success text-lg">เพิ่มอาจารย์</button>
                                <button type="reset" class="btn btn-error text-lg"
                                    @click="clearAddUser()">ล้างข้อมูล</button>
                            </div>
                        </div>
                        <p class="text-lg font-medium mb-1">รายชื่อที่จะถูกเพิ่ม</p>
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th class="text-lg">ชื่อ-นามสกุล</th>
                                    <th class="text-lg">กลุ่มสาระการเรียนรู้</th>
                                    <th class="text-lg text-center">ระดับชั้น</th>
                                    <th class="text-lg text-center">สอนรายวิชา</th>
                                    <th class="text-lg text-center">อาจารย์ที่ปรึกษา</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="hover" v-for="(teacher, i) in addUserData" :key="i">
                                    <td>{{ i + 1 }}</td>
                                    <td>{{ teacher.name || 'ไม่ได้ระบุ' }}</td>
                                    <td>{{ teacher.data.department || 'ไม่ได้ระบุ' }}</td>
                                    <td class="text-center">{{ teacher.class || 'ไม่ได้ระบุ' }}</td>
                                    <td class="text-center">{{ teacher.data.classroom || 'ไม่ได้ระบุ' }}</td>
                                    <td class="text-center">{{ teacher.data.advisor.class + '/' +
                                            teacher.data.advisor.room || 'ไม่ได้ระบุ'
                                    }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>

        <!-- ADD BY FORM MODAL -->
        <div class="modal" id="add-user-form-modal" v-if="addUserData.length > 0">
            <form @submit.prevent="addUser()">
                <div class="modal-box w-full max-w-5xl">
                    <div class="flex justify-between items-center mb-3">
                        <div class="flex">
                            <a href="#add-modal" role="button"
                                class="btn btn-sm text-primary hover:bg-transparent hover:border hover:border-b-2 text-xl btn-ghost mr-3">
                                <span class="material-symbols-rounded">
                                    arrow_back
                                </span>
                                ย้อนกลับ</a>
                            <h3 class="text-gray-400 text-xl pt-0.5">เพิ่มรายชื่ออาจารย์ด้วยฟอร์ม</h3>
                        </div>
                        <a href="#" class="btn btn-circle btn-ghost" role="button" @click="clearAddUser()">
                            <span class="material-symbols-rounded">
                                close
                            </span>
                        </a>
                    </div>

                    <div class="mb-3 pl-4">
                        <p class="text-xl font-bold">ข้อมูลทั่วไป</p>
                        <p class="text-gray-500">ข้อมูลทั่วไปที่เกี่ยวกับอาจารย์</p>
                    </div>
                    <div class="grid grid-cols-3 gap-5 gap-y-3 px-4 mb-7">
                        <div class="form-control">
                            <label for="edit-name" class="mb-1 required">ชื่อ-นามสกุล ภาษาไทย</label>
                            <input type="text" id="edit-name" class="input input-sm input-bordered"
                                :class="{ 'border-error': errorBag.message.name }" placeholder="ชื่อ-นามสกุล ภาษาไทย"
                                v-model="addUserData[0].name">
                            <small class="text-error mt-1">{{ errorBag.message.name }}</small>
                        </div>
                        <div class="form-control">
                            <label for="edit-name" class="mb-1 required">ชื่อ-นามสกุล ภาษาอังกฤษ</label>
                            <input type="text" id="edit-name" class="input input-sm input-bordered"
                                :class="{ 'border-error': errorBag.message.eng_name }"
                                placeholder="ชื่อ-นามสกุล ภาษาอังกฤษ" v-model="addUserData[0].eng_name">
                            <small class="text-error mt-1">{{ errorBag.message.eng_name }}</small>
                        </div>
                        <div class="form-control">
                            <label for="edit-class" class="mb-1 required">ระดับชั้น</label>
                            <input type="number" min="1" id="edit-class" class="input input-sm input-bordered"
                                :class="{ 'border-error': errorBag.message.class }" placeholder="ระดับชั้น"
                                v-model="addUserData[0].class">
                            <small class="text-error mt-1">{{ errorBag.message.class }}</small>
                        </div>
                        <div>
                            <label for="edit-department" class="required">กลุ่มสาระการเรียนรู้</label>
                            <select id="edit-department" class="select text-xs select-sm select-bordered w-full mt-1"
                                v-model="addUserData[0].data.department"
                                :class="{ 'border-error': errorBag.message.department }">
                                <option value="" disabled>--กลุ่มสาระการเรียนรู้--</option>
                                <option v-for="department in departments" :key="department" :value="department">{{
                                        department
                                }}</option>
                            </select>
                            <small class="text-error mt-1">{{ errorBag.message.department }}</small>
                        </div>
                        <div>
                            <label for="">อาจารย์ที่ปรึกษา</label>
                            <div class="grid grid-cols-2 gap-5 mt-1">
                                <div class="form-control">
                                    <input type="number" min="1" id="edit-advisor-class"
                                        class="input input-sm input-bordered" placeholder="ระดับชั้น"
                                        :class="{ 'border-error': errorBag.message.advisor_class }"
                                        v-model="addUserData[0].data.advisor.class">
                                    <small class="text-error mt-1">{{ errorBag.message.advisor_class }}</small>
                                </div>
                                <div class="form-control">
                                    <input type="number" min="1" id="edit-advisor-room"
                                        class="input input-sm input-bordered" placeholder="ห้อง"
                                        :class="{ 'border-error': errorBag.message.advisor_room }"
                                        v-model="addUserData[0].data.advisor.room">
                                    <small class="text-error mt-1">{{ errorBag.message.advisor_room }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-action">
                        <button type="submit" class="btn btn-secondary">เพิ่มอาจารย์</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- DELETE MODAL -->
        <div class="modal" id="delete-modal" v-if="deleteUserData.length > 0">
            <div class="modal-box">
                <div class="flex justify-end items-center">
                    <a href="#" class="btn btn-circle btn-ghost" role="button" @click="clearDeleteUser()">
                        <span class="material-symbols-rounded">
                            close
                        </span>
                    </a>
                </div>
                <div class="text-center">
                    <p class="text-xl">คุณต้องการลบอาจารย์เหล่านี้หรือไม่</p>
                    <p class="text-xl font-bold" v-for="student in deleteUserData" :key="student.id">{{ student.name
                    }}</p>
                </div>
                <div class="modal-action grid grid-cols-2 gap-5">
                    <button type="button" class="btn btn-gray text-lg" @click="clearDeleteUser()">ยกเลิก</button>
                    <button type="button" class="btn btn-error text-lg" @click="deleteUser()">ลบอาจารย์</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
<style>
label {
    font-weight: 500;
}

.required::after {
    content: '*';
    color: red;
}
</style>
