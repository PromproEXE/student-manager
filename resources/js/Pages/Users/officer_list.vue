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
            officers: [],
            searchText: '',
            filter: {},
            selectedOfficer: [],
            selectAll: false,
            editUserData: {},
            deleteUserData: [],
            addUserData: [],
            errorBag: {
                status: true,
                message: {}
            },
            mode: 'single',
            rootUrl: route('users_officer_list_view') + '#',
        }
    },
    methods: {
        async getOfficerList() {
            try {
                let res = await axios('/api/users/officer')
                if (res.status == 200) {
                    this.officers = res.data
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
                role: ['admin']
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
                    let officer = data.split(',')

                    this.addUserData.push({
                        name: officer[0],
                        eng_name: officer[1],
                        role: ['admin']
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
                    alert('เพิ่มเจ้าหน้าที่สำเร็จ')

                    window.location.href = this.rootUrl
                    document.getElementById('add-user-csv-file').value = null
                    this.addUserData = []

                    let res_officer = await axios('/api/users/officer')
                    this.officers = res_officer.data
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
                    let new_res = await axios('/api/users/officer')

                    if (new_res.status == 200) {
                        this.officers = new_res.data
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
                alert('ลบเจ้าหน้าที่เรียบร้อยแล้ว')
                let res_officer = await axios('/api/users/officer')
                this.officers = res_officer.data

            }
            catch (err) {
                alert('มีปัญหาระหว่างลบข้อมูล')
                console.log(err)
            }
            this.deleteUserData = []
            this.selectedOfficer = []
            window.location.href = this.rootUrl
        },
        searchedData() {
            //SEARCH FILTER
            let data = this.officers
            if (this.searchText != '' || this.searchText != null) {
                data = this.officers.filter((officer) => officer.name.toLowerCase().indexOf(this.searchText) > -1 || '')
            }

            return data
        }
    },
    watch: {
        selectedOfficer() {
            if (this.selectedOfficer.length > 0) {
                this.mode = 'multiple'
            }
            else {
                this.mode = 'single'
                this.selectAll = false
            }

            if (this.selectedOfficer.length == this.searchedData().length && this.searchedData().length != 0) {
                this.selectAll = true
            }
            else {
                this.selectAll = false
            }
        },
        selectAll() {
            if (this.selectAll) {
                this.selectedOfficer = this.searchedData()
            }
            else if (this.selectedOfficer.length == this.searchedData().length) {
                this.selectedOfficer = []
            }
        }
    },
    mounted() {
        this.getOfficerList()
    }
}
</script>
<template>

    <Head>
        <title>จัดการเจ้าหน้าที่</title>
    </Head>
    <AppLayout>
        <template #header>จัดการเจ้าหน้าที่</template>
        <div class="bg-white rounded-xl p-5" v-if="role.isAdmin($page.props.user.role)">
            <div class="form-control mb-3">
                <label class="input-group">
                    <span class="material-symbols-rounded">
                        search
                    </span>
                    <input type="text" placeholder="ค้นหาด้วยชื่อเจ้าหน้าที่" class="input input-bordered w-full"
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
                            <a href="#delete-modal" @click="deleteUserData = selectedOfficer"><span
                                    class="material-symbols-rounded mr-2">
                                    delete
                                </span>ลบเจ้าหน้าที่</a>
                        </li>
                    </ul>
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
                            <th class="text-right" v-if="mode == 'single'">
                                <a href="#add-modal" class="btn btn-sm btn-success"><span
                                        class="material-symbols-rounded mr-2">
                                        add
                                    </span>เพิ่มเจ้าหน้าที่</a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="hover" v-for="officer in searchedData()" :key="officer.id">
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm" :value="officer"
                                    v-model="selectedOfficer">
                            </td>
                            <td>{{ officer.name }}</td>
                            <td v-if="mode == 'single'">
                                <a href="#edit-modal" @click="getEditUserData(officer)"
                                    class="btn btn-warning mr-2"><span class="material-symbols-rounded mr-2">
                                        edit
                                    </span>แก้ไขข้อมูล</a>
                                <a href="#delete-modal" class="btn btn-error" @click="pushDeleteUser(officer)"><span
                                        class="material-symbols-rounded mr-2">
                                        delete
                                    </span>ลบเจ้าหน้าที่</a>
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
                        <h3 class="font-bold text-3xl">แก้ไขข้อมูลเจ้าหน้าที่</h3>
                        <a href="#" class="btn btn-circle btn-ghost" role="button">
                            <span class="material-symbols-rounded">
                                close
                            </span>
                        </a>
                    </div>

                    <div class="mb-3">
                        <p class="text-xl font-bold">ข้อมูลทั่วไป</p>
                        <p class="text-gray-500">ข้อมูลทั่วไปที่เกี่ยวกับเจ้าหน้าที่</p>
                    </div>
                    <div class="grid grid-cols-3 gap-5 gap-y-3 px-5 mb-7">
                        <div class="form-control">
                            <label for="edit-name" class="mb-1 required">ชื่อ-นามสกุล (ภาษาไทย)</label>
                            <input type="text" id="edit-name" class="input input-sm input-bordered"
                                :class="{ 'border-error': errorBag.message.name }" placeholder="ชื่อ-นามสกุล"
                                v-model="editUserData.name">
                            <small class="text-error mt-1">{{ errorBag.message.name }}</small>
                        </div>
                        <div class="form-control">
                            <label for="edit-name" class="mb-1 required">ชื่อ-นามสกุล (ภาษาอังกฤษ)</label>
                            <input type="text" id="edit-name" class="input input-sm input-bordered"
                                :class="{ 'border-error': errorBag.message.eng_name }"
                                placeholder="ชื่อ-นามสกุล ภาษาอังกฤษ" v-model="editUserData.eng_name">
                            <small class="text-error mt-1">{{ errorBag.message.eng_name }}</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <p class="text-xl font-bold">ข้อมูลการใช้งาน</p>
                        <p class="text-gray-500">ข้อมูลที่เกี่ยวกับการใข้งานระบบของเจ้าหน้าที่</p>
                    </div>
                    <div class="grid grid-cols-3 gap-5 gap-y-3 px-5">
                        <div class="form-control">
                            <label for="edit-std-id" class="mb-1 required">อีเมล</label>
                            <input type="email" id="edit-std-id" class="input input-sm input-bordered"
                                :class="{ 'border-error': errorBag.message.email }" placeholder="officer@example.com"
                                v-model="editUserData.email" required>
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
                    <h3 class="font-bold text-3xl">เพิ่มรายชื่อเจ้าหน้าที่</h3>
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
                        <p class="text-primary">เพิ่มโดยการใช้ไฟล์ .csv ของระบบไปทำการเพิ่มข้อมูลเจ้าหน้าที่
                            สามารถเพิ่มข้อมูลเจ้าหน้าที่ได้หลายคนในครั้งเดียว</p>
                    </a>
                    <a role="button" href="#add-user-form-modal"
                        class="rounded-lg bg-gray-100 p-5 py-36 text-center bg-opacity-50 border-4 border-gray-400"
                        @click="pushAddUser()">
                        <p class="text-primary text-2xl font-bold">เพิ่มรายชื่อด้วยฟอร์มของระบบ</p>
                        <p class="text-primary">เพิ่มโดยการกรอกฟอร์มลงในระบบ เหมาะกับการเพิ่มเจ้าหน้าที่จำนวนน้อย ๆ</p>
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
                            <a href="#add-modal" role="button" @click="addUserData = []"
                                class="btn btn-sm text-primary hover:bg-transparent hover:border hover:border-b-2 text-xl btn-ghost mr-3">
                                <span class="material-symbols-rounded">
                                    arrow_back
                                </span>
                                ย้อนกลับ</a>
                            <h3 class="text-gray-400 text-xl pt-0.5">เพิ่มรายชื่อเจ้าหน้าที่ด้วยไฟล์ CSV</h3>
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
                                <a href="/download/example-officer-csv.csv" role="button"
                                    class="btn btn-sm btn-warning">ดาวน์โหลดไฟล์เตรียมข้อมูล</a>
                            </div>
                            <input type="file" id="add-user-csv-file"
                                class="file-input file-input-bordered file-input-primary w-full mb-5" multiple
                                @change="readAdduserCsv()" accept=".csv" />
                            <div class="grid grid-cols-2 gap-5">
                                <button type="submit" class="btn btn-success text-lg">เพิ่มเจ้าหน้าที่</button>
                                <button type="reset" class="btn btn-error text-lg"
                                    @click="clearAddUser()">ล้างข้อมูล</button>
                            </div>
                        </div>
                        <p class="text-lg font-medium mb-1">รายชื่อที่จะถูกเพิ่ม</p>
                        <div class="overflow-auto">
                            <table class="table table-zebra w-full">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th class="text-lg">ชื่อ-นามสกุล (ภาษาไทย)</th>
                                        <th class="text-lg">ชื่อ-นามสกุล (ภาษาอังกฤษ)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="hover" v-for="(officer, i) in addUserData" :key="i">
                                        <td>{{ i + 1 }}</td>
                                        <td>{{ officer.name || 'ไม่ได้ระบุ' }}</td>
                                        <td>{{ officer.eng_name || 'ไม่ได้ระบุ' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
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
                            <a href="#add-modal" role="button" @click="addUserData = []"
                                class="btn btn-sm text-primary hover:bg-transparent hover:border hover:border-b-2 text-xl btn-ghost mr-3">
                                <span class="material-symbols-rounded">
                                    arrow_back
                                </span>
                                ย้อนกลับ</a>
                            <h3 class="text-gray-400 text-xl pt-0.5">เพิ่มรายชื่อเจ้าหน้าที่ด้วยฟอร์ม</h3>
                        </div>
                        <a href="#" class="btn btn-circle btn-ghost" role="button" @click="clearAddUser()">
                            <span class="material-symbols-rounded">
                                close
                            </span>
                        </a>
                    </div>

                    <div class="mb-3 pl-4">
                        <p class="text-xl font-bold">ข้อมูลทั่วไป</p>
                        <p class="text-gray-500">ข้อมูลทั่วไปที่เกี่ยวกับเจ้าหน้าที่</p>
                    </div>
                    <div class="grid grid-cols-2 gap-5 gap-y-3 px-4 mb-7">
                        <div class="form-control">
                            <label for="edit-name" class="mb-1 required">ชื่อ-นามสกุล (ภาษาไทย)</label>
                            <input type="text" id="edit-name" class="input input-sm input-bordered"
                                :class="{ 'border-error': errorBag.message.name }" placeholder="ชื่อ-นามสกุล ภาษาไทย"
                                v-model="addUserData[0].name">
                            <small class="text-error mt-1">{{ errorBag.message.name }}</small>
                        </div>
                        <div class="form-control">
                            <label for="edit-name" class="mb-1 required">ชื่อ-นามสกุล (ภาษาอังกฤษ)</label>
                            <input type="text" id="edit-name" class="input input-sm input-bordered"
                                :class="{ 'border-error': errorBag.message.eng_name }"
                                placeholder="ชื่อ-นามสกุล ภาษาอังกฤษ" v-model="addUserData[0].eng_name">
                            <small class="text-error mt-1">{{ errorBag.message.eng_name }}</small>
                        </div>
                    </div>
                    <div class="modal-action">
                        <button type="submit" class="btn btn-secondary">เพิ่มเจ้าหน้าที่</button>
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
                    <p class="text-xl">คุณต้องการลบเจ้าหน้าที่เหล่านี้หรือไม่</p>
                    <p class="text-xl font-bold" v-for="officer in deleteUserData" :key="officer.id">{{ officer.name
                    }}</p>
                </div>
                <div class="modal-action grid grid-cols-2 gap-5">
                    <button type="button" class="btn btn-gray text-lg" @click="clearDeleteUser()">ยกเลิก</button>
                    <button type="button" class="btn btn-error text-lg" @click="deleteUser()">ลบเจ้าหน้าที่</button>
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
