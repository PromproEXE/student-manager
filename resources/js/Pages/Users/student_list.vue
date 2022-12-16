<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import axios from 'axios';
import { cloneDeep } from 'lodash';
import * as validate from '@/data-validator'
import * as role from '@/role'
</script>
<script>
export default {
    data() {
        return {
            students: [],
            searchText: '',
            filter: {
                class: 'all',
                room: 'all'
            },
            selectedStudent: [],
            selectAll: false,
            editUserData: {
            },
            deleteUserData: [],
            addUserData: [],
            errorBag: {
                status: true,
                message: {}
            },
            mode: 'single',
            rootUrl: route('users_student_list_view') + '#',
        }
    },
    methods: {
        async getStudentList() {
            try {
                let res = await axios('/api/users/student')
                if (res.status == 200) {
                    this.students = res.data
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
                std_id: null,
                name: null,
                class: null,
                room: null,
                birth_day: null,
                role: ['student']
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
                    let student = data.split(',')
                    this.addUserData.push({
                        std_id: student[0],
                        name: student[1],
                        class: student[2],
                        room: student[3],
                        birth_day: student[4].length != 0 ? student[4] : null,
                        role: ['student']
                    })
                }

                console.log(this.addUserData)
            })

            for (let file of fileElement) {
                reader.readAsText(file)
            }
        },
        async addUser() {
            try {
                let res = await axios.post('/api/users/create', this.addUserData)
                if (res.status == 200) {
                    alert('เพิ่มนักเรียนสำเร็จ')

                    window.location.href = this.rootUrl
                    document.getElementById('add-user-csv-file').value = null
                    this.addUserData = []

                    let res_student = await axios('/api/users/student')
                    this.students = res_student.data
                }
            }
            catch (err) {
                alert('มีปัญหาระหว่างเพิ่มผู้ใช้')
                console.log(err)
            }
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
                    let new_res = await axios('/api/users/student')
                    if (new_res.status == 200) {
                        this.students = new_res.data
                    }
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
                alert('ลบนักเรียนเรียบร้อยแล้ว')
                let res_student = await axios('/api/users/student')
                this.students = res_student.data

            }
            catch (err) {
                alert('มีปัญหาระหว่างลบข้อมูล')
                console.log(err)
            }
            this.deleteUserData = []
            this.selectedStudent = []
            window.location.href = this.rootUrl
        }
    },
    computed: {
        searchedData() {
            //SEARCH FILTER
            let data = this.students
            if (this.searchText != '' || this.searchText != null) {
                data = this.students.filter((std) => (std.std_id?.toLowerCase().indexOf(this.searchText) > -1 || '') || (std.name?.toLowerCase().indexOf(this.searchText) > -1 || ''))
            }

            //CLASS FILTER
            if (this.filter.class != 'all') {
                data = data.filter((std) => std.class == this.filter.class)
            }

            //ROOM FILTER
            if (this.filter.room != 'all') {
                data = data.filter((std) => std.room == this.filter.room)
            }

            return data
        }
    },
    watch: {
        selectedStudent() {
            if (this.selectedStudent.length > 0) {
                this.mode = 'multiple'
            }
            else {
                this.mode = 'single'
                this.selectAll = false
            }

            if (this.selectedStudent.length == this.students.length && this.students.length != 0) {
                this.selectAll = true
            }
            else {
                this.selectAll = false
            }
        },
        selectAll() {
            if (this.selectAll) {
                this.selectedStudent = this.students
            }
            else if (this.selectedStudent.length == this.students.length) {
                this.selectedStudent = []
            }
        }
    },
    mounted() {
        this.getStudentList()
    }
}
</script>
<template>

    <Head>
        <title>จัดการนักเรียน</title>
    </Head>
    <AppLayout>
        <template #header>จัดการนักเรียน</template>
        <div class="bg-white rounded-xl p-5" v-if="role.isAdmin($page.props.user.role)">
            <div class="form-control mb-3">
                <label class="input-group">
                    <span class="material-symbols-rounded">
                        search
                    </span>
                    <input type="text" placeholder="ค้นหาด้วยเลขประจำตัวหรือชื่อนักเรียน"
                        class="input input-bordered w-full" v-model="searchText" />
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
                            <a href="#delete-modal" @click="deleteUserData = selectedStudent"><span
                                    class="material-symbols-rounded mr-2">
                                    delete
                                </span>ลบนักเรียน</a>
                        </li>
                    </ul>
                </div>
                <div class="mr-3">
                    <label for="filter-class" class="mr-2">ระดับชั้น</label>
                    <select class="select select-bordered" id="filter-class" v-model="filter.class">
                        <option value="all">ทั้งหมด</option>
                        <option v-for="classes in 6" :key="classes" :value="classes">ม.{{ classes }}</option>
                    </select>
                </div>
                <div>
                    <label for="filter-room" class="mr-2">ห้อง</label>
                    <select class="select select-bordered" id="filter-room" v-model="filter.room">
                        <option value="all">ทั้งหมด</option>
                        <option v-for="room in 14" :key="room" :value="room">{{ room }}</option>
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
                            <th class="text-lg">เลขประจำตัว</th>
                            <th class="text-lg">ชื่อ-นามสกุล</th>
                            <th class="text-lg text-center">ระดับชั้น</th>
                            <th class="text-lg text-center">ห้อง</th>
                            <th class="text-right" v-if="mode == 'single'">
                                <a href="#add-modal" class="btn btn-sm btn-success"><span
                                        class="material-symbols-rounded mr-2">
                                        add
                                    </span>เพิ่มนักเรียน</a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="hover" v-for="student in searchedData" :key="student.id">
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm" :value="student"
                                    v-model="selectedStudent">
                            </td>
                            <td>{{ student.std_id }}</td>
                            <td>{{ student.name }}</td>
                            <td class="text-center">{{ student.class }}</td>
                            <td class="text-center">{{ student.room }}</td>
                            <td v-if="mode == 'single'">
                                <a href="#edit-modal" @click="getEditUserData(student)"
                                    class="btn btn-warning mr-2"><span class="material-symbols-rounded mr-2">
                                        edit
                                    </span>แก้ไขข้อมูล</a>
                                <a href="#delete-modal" class="btn btn-error" @click="pushDeleteUser(student)"><span
                                        class="material-symbols-rounded mr-2">
                                        delete
                                    </span>ลบนักเรียน</a>
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
                        <h3 class="font-bold text-3xl">แก้ไขข้อมูลนักเรียน</h3>
                        <a href="#" class="btn btn-circle btn-ghost" role="button">
                            <span class="material-symbols-rounded">
                                close
                            </span>
                        </a>
                    </div>

                    <div class="mb-3">
                        <p class="text-xl font-bold">ข้อมูลทั่วไป</p>
                        <p class="text-gray-500">ข้อมูลทั่วไปที่เกี่ยวกับนักเรียน</p>
                    </div>
                    <div class="grid grid-cols-3 gap-5 gap-y-3 px-5 mb-7">
                        <div class="form-control">
                            <label for="edit-std-id" class="mb-1 required">เลขประจำตัวนักเรียน</label>
                            <input type="text" id="edit-std-id" class="input input-sm input-bordered"
                                :class="{ 'border-error': errorBag.message.std_id }" placeholder="เลขประจำตัว"
                                v-model="editUserData.std_id">
                            <small class="text-error mt-1">{{ errorBag.message.std_id }}</small>
                        </div>
                        <div class="form-control">
                            <label for="edit-name" class="mb-1 required">ชื่อ-นามสกุล</label>
                            <input type="text" id="edit-name" class="input input-sm input-bordered"
                                :class="{ 'border-error': errorBag.message.name }" placeholder="ชื่อ-นามสกุล"
                                v-model="editUserData.name">
                            <small class="text-error mt-1">{{ errorBag.message.name }}</small>
                        </div>
                        <div class="grid grid-cols-2 gap-5">
                            <div class="form-control">
                                <label for="edit-class" class="mb-1 required">ระดับชั้น</label>
                                <input type="number" min="1" id="edit-class" class="input input-sm input-bordered"
                                    :class="{ 'border-error': errorBag.message.class }" placeholder="ระดับชั้น"
                                    v-model="editUserData.class">
                                <small class="text-error mt-1">{{ errorBag.message.class }}</small>
                            </div>
                            <div class="form-control">
                                <label for="edit-room" class="mb-1 required">ห้อง</label>
                                <input type="number" min="1" id="edit-room" class="input input-sm input-bordered"
                                    :class="{ 'border-error': errorBag.message.room }" placeholder="ห้อง"
                                    v-model="editUserData.room">
                                <small class="text-error mt-1">{{ errorBag.message.room }}</small>
                            </div>
                        </div>
                        <div class="form-control">
                            <label for="edit-birth-day" class="mb-1">วันเกิด</label>
                            <input type="date" id="edit-birth-day" class="input input-sm input-bordered"
                                :class="{ 'border-error': errorBag.message.birth_day }"
                                v-model="editUserData.birth_day">
                            <small class="text-error mt-1">{{ errorBag.message.birth_day }}</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <p class="text-xl font-bold">ข้อมูลการใช้งาน</p>
                        <p class="text-gray-500">ข้อมูลที่เกี่ยวกับการใข้งานระบบของนักเรียน</p>
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
                    <h3 class="font-bold text-3xl">เพิ่มรายชื่อนักเรียน</h3>
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
                        <p class="text-primary">เพิ่มโดยการใช้ไฟล์ .csv ของระบบไปทำการเพิ่มข้อมูลนักเรียน
                            สามารถเพิ่มข้อมูลนักเรียนได้หลายคนในครั้งเดียว</p>
                    </a>
                    <a role="button" href="#add-user-form-modal"
                        class="rounded-lg bg-gray-100 p-5 py-36 text-center bg-opacity-50 border-4 border-gray-400"
                        @click="pushAddUser()">
                        <p class="text-primary text-2xl font-bold">เพิ่มรายชื่อด้วยฟอร์มของระบบ</p>
                        <p class="text-primary">เพิ่มโดยการกรอกฟอร์มลงในระบบ เหมาะกับการเพิ่มนักเรียนจำนวนน้อย ๆ</p>
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
                            <h3 class="text-gray-400 text-xl pt-0.5">เพิ่มรายชื่อนักเรียนด้วยไฟล์ CSV</h3>
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
                                <a href="/download/example-user-csv.csv" role="button"
                                    class="btn btn-sm btn-warning">ดาวน์โหลดไฟล์เตรียมข้อมูล</a>
                            </div>
                            <input type="file" id="add-user-csv-file"
                                class="file-input file-input-bordered file-input-primary w-full mb-5" multiple
                                @change="readAdduserCsv()" accept=".csv" />
                            <div class="grid grid-cols-2 gap-5">
                                <button type="submit" class="btn btn-success text-lg">เพิ่มนักเรียน</button>
                                <button type="reset" class="btn btn-error text-lg"
                                    @click="clearAddUser()">ล้างข้อมูล</button>
                            </div>
                        </div>
                        <p class="text-lg font-medium mb-1">รายชื่อที่จะถูกเพิ่ม</p>
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th class="text-lg">เลขประจำตัว</th>
                                    <th class="text-lg">ชื่อ-นามสกุล</th>
                                    <th class="text-lg">ระดับชั้น</th>
                                    <th class="text-lg">ห้อง</th>
                                    <th class="text-lg">วันเกิด</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="hover" v-for="(student, i) in addUserData" :key="i">
                                    <td>{{ i + 1 }}</td>
                                    <td>{{ student.std_id || 'ไม่ได้ระบุ' }}</td>
                                    <td>{{ student.name || 'ไม่ได้ระบุ' }}</td>
                                    <td>{{ student.class || 'ไม่ได้ระบุ' }}</td>
                                    <td>{{ student.room || 'ไม่ได้ระบุ' }}</td>
                                    <td>{{ student.birth_day || 'ไม่ได้ระบุ' }}</td>
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
                            <h3 class="text-gray-400 text-xl pt-0.5">เพิ่มรายชื่อนักเรียนด้วยฟอร์ม</h3>
                        </div>
                        <a href="#" class="btn btn-circle btn-ghost" role="button" @click="clearAddUser()">
                            <span class="material-symbols-rounded">
                                close
                            </span>
                        </a>
                    </div>

                    <div class="mb-3 pl-4">
                        <p class="text-xl font-bold">ข้อมูลทั่วไป</p>
                        <p class="text-gray-500">ข้อมูลทั่วไปที่เกี่ยวกับนักเรียน</p>
                    </div>
                    <div class="grid grid-cols-3 gap-5 gap-y-3 px-4 mb-7">
                        <div class="form-control">
                            <label for="edit-std-id" class="mb-1 required">เลขประจำตัวนักเรียน</label>
                            <input type="text" id="edit-std-id" class="input input-sm input-bordered"
                                :class="{ 'border-error': errorBag.message.std_id }" placeholder="เลขประจำตัว"
                                v-model="addUserData[0].std_id">
                            <small class="text-error mt-1">{{ errorBag.message.std_id }}</small>
                        </div>
                        <div class="form-control">
                            <label for="edit-name" class="mb-1 required">ชื่อ-นามสกุล</label>
                            <input type="text" id="edit-name" class="input input-sm input-bordered"
                                :class="{ 'border-error': errorBag.message.name }" placeholder="ชื่อ-นามสกุล"
                                v-model="addUserData[0].name">
                            <small class="text-error mt-1">{{ errorBag.message.name }}</small>
                        </div>
                        <div class="grid grid-cols-2 gap-5">
                            <div class="form-control">
                                <label for="edit-class" class="mb-1 required">ระดับชั้น</label>
                                <input type="number" min="1" id="edit-class" class="input input-sm input-bordered"
                                    :class="{ 'border-error': errorBag.message.class }" placeholder="ระดับชั้น"
                                    v-model="addUserData[0].class">
                                <small class="text-error mt-1">{{ errorBag.message.class }}</small>
                            </div>
                            <div class="form-control">
                                <label for="edit-room" class="mb-1 required">ห้อง</label>
                                <input type="number" min="1" id="edit-room" class="input input-sm input-bordered"
                                    :class="{ 'border-error': errorBag.message.room }" placeholder="ห้อง"
                                    v-model="addUserData[0].room">
                                <small class="text-error mt-1">{{ errorBag.message.room }}</small>
                            </div>
                        </div>
                        <div class="form-control">
                            <label for="edit-birth-day" class="mb-1">วันเกิด</label>
                            <input type="date" id="edit-birth-day" class="input input-sm input-bordered"
                                :class="{ 'border-error': errorBag.message.birth_day }"
                                v-model="addUserData[0].birth_day">
                            <small class="text-error mt-1">{{ errorBag.message.birth_day }}</small>
                        </div>
                    </div>
                    <div class="modal-action">
                        <button type="submit" class="btn btn-secondary">เพิ่มนักเรียน</button>
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
                    <p class="text-xl">คุณต้องการลบนักเรียนเหล่านี้หรือไม่</p>
                    <p class="text-xl font-bold" v-for="student in deleteUserData" :key="student.id">{{ student.name
                    }}</p>
                </div>
                <div class="modal-action grid grid-cols-2 gap-5">
                    <button type="button" class="btn btn-gray text-lg" @click="clearDeleteUser()">ยกเลิก</button>
                    <button type="button" class="btn btn-error text-lg" @click="deleteUser()">ลบนักเรียน</button>
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
