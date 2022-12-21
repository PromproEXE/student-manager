<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import Multiselect from '@vueform/multiselect'
import { isAdmin } from '@/role'
import { Head } from '@inertiajs/inertia-vue3';
import { validateSubject } from '@/data-validator';
import { cloneDeep, compact } from 'lodash'
import { isEmpty } from '@/helper'
</script>
<script>
export default {
    data() {
        return {
            filter: {
                department: 'all',
                class: 'all',
                room: 'all',
            },
            subjects: [],
            roomAmount: [],
            addSubjectData: [],
            deleteSubjectData: [],
            editSubjectData: {},
            searchText: '',

            selectedSubject: [],
            departments: ['ภาษาไทย', 'คณิตศาสตร์', 'วิทยาศาสตร์และเทคโนโลยี', 'ภาษาต่างประเทศ', 'สังคมศึกษา', 'ศิลปะ', 'สุขศึกษาและพลศึกษา', 'การงานอาชีพ', 'แนะแนว'],
            errorBag: {
                status: true,
                message: {}
            },
            selectAll: false,
            rootUrl: route('subject_list_view') + '#',
            mode: 'single'
        }
    },
    methods: {
        async getRoomAmount() {
            try {
                let res = await axios('/api/classroom/room-amount')
                if (res.status == 200) {
                    if (Array.isArray(res.data) && res.data.length == 6) {
                        this.roomAmount = res.data
                    }
                    else {
                        throw 'ระดับชั้นไม่ครบ'
                    }

                }
                this.$forceUpdate()
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างดึงข้อมูลจำนวนห้องเรียน')
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
                alert('เกิดปัญหาระหว่างดึงข้อมูลรายวิชา')
                console.log(err)
            }
        },
        pushAddSubject() {
            let for_class = Array.from(this.roomAmount.map((amount) => {
                let roomArr = []
                for (let room = 1; room <= amount; room++) {
                    roomArr.push(room)
                }
                return roomArr
            }))

            this.addSubjectData.push({
                for_class: [[], [], [], [], [], []],
                department: null,
                teacher: [],
                department: null,
                subject_id: null,
                name: null,

            })
        },
        clearAddSubject() {
            window.location.href = this.rootUrl
            document.getElementById('add-subject-csv-file').value = null
            setTimeout(() => this.addSubjectData = [], 200)
        },
        clearEditSubjectData() {
            window.location.href = this.rootUrl
            setTimeout(() => this.editSubjectData = {}, 200)
        },
        async addSubject() {
            this.errorBag = { status: true, message: {} }
            try {
                //VALIDATE
                if (this.addSubjectData.length == 1) {
                    this.errorBag = validateSubject(this.addSubjectData[0])

                    if (!this.errorBag.status) {
                        return
                    }
                }

                //CREATE TUPLE
                let res = await axios.post('/api/subject/create/', this.addSubjectData)
                if (res.status == 200) {
                    alert('เพิ่มรายวิชาสำเร็จ')

                    //CLEAR DATA
                    this.clearAddSubject()

                    //FETCH DATA
                    let new_res = await axios('/api/subject/')
                    if (new_res.status == 200) {
                        this.subjects = new_res.data
                    }
                }
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างเพิ่มรายวิชา')
                console.log(err)
            }
        },
        async updateSubject() {
            try {
                //VALIDATE
                this.errorBag = validateSubject(this.editSubjectData)
                if (!this.errorBag.status) {
                    return
                }

                //CREATE TUPLE
                let res = await axios.put('/api/subject/update/' + this.editSubjectData.id, this.editSubjectData)
                if (res.status == 200) {
                    alert('แก้ไขข้อมูลสำเร็จ')

                    //FETCH DATA
                    let new_res = await axios('/api/subject/')
                    if (new_res.status == 200) {
                        this.subjects = new_res.data
                    }
                }
            }
            catch {
                alert('เกิดปัญหาระหว่างแก้ไขข้อมูลรายวิชา')
                console.log(err)
            }
        },
        async deleteSubject() {
            try {
                for (let subject of this.deleteSubjectData) {
                    let res = await axios.delete('/api/subject/delete/' + subject.id)

                    if (res.status != 200) {
                        throw res.data
                    }
                }

                alert('ลบรายวิชาสำเร็จ')
                this.clearDeleteSubject()
                let new_res = await axios('/api/subject')
                if (new_res.status == 200) {
                    this.subjects = new_res.data
                }
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างลบรายวิชา')
                console.log(err)
            }
        },
        clearDeleteSubject() {
            window.location.href = this.rootUrl
            setTimeout(() => this.deleteSubjectData = [], 200)
        },
        selectAllClass(classes, value) {
            let index = classes - 1
            if (value === true) {
                this.addSubjectData[0].for_class[index] = Array.from(Array(this.roomAmount[index]).keys())
            }
            else {
                this.addSubjectData[0].for_class[index] = []
            }
        },
        pushEditSubjectData(data) {
            this.editSubjectData = cloneDeep(data)
        },
        pushDeleteSubjectData(data) {
            this.deleteSubjectData.push(data)
        },
        getClassesDropdownData(classes) {
            let dropdownData = []
            for (let room = 1; room <= this.roomAmount[classes - 1]; room++) {
                dropdownData.push({
                    label: 'ม.' + classes + '/' + room,
                    value: room
                })
            }
            return dropdownData
        },
        readAddSubjectCsv() {
            let reader = new FileReader()
            let fileElement = document.getElementById('add-subject-csv-file').files
            reader.addEventListener('load', (event) => {
                //SPLIT \n TO ARRAY
                let rawData = reader.result.split('\n')
                rawData.splice(0, 1)

                for (let data of rawData) {
                    let subject = data.split(',')

                    //GET FOR CLASS
                    let for_class = []
                    for (let classes = 3; classes <= 8; classes++) {
                        if (!isEmpty(subject[classes])) {
                            if (subject[classes].indexOf('/') > -1) {
                                for_class.push(compact(subject[classes].split('/')))
                            }
                            else {
                                for_class.push([subject[classes]])
                            }
                        }
                        else {
                            for_class.push([])
                        }
                    }

                    //GET TEACHER
                    let teacher = []
                    if (subject[9].indexOf('/') > -1) {
                        teacher = compact(subject[9].split('/'))
                    }
                    else {
                        teacher = [subject[9]]
                    }
                    this.addSubjectData.push({
                        subject_id: subject[0],
                        name: subject[1],
                        department: subject[2],
                        for_class: for_class,
                        teacher: teacher,
                    })
                }
            })

            for (let file of fileElement) {
                reader.readAsText(file)
            }
        },
        filteredSubject() {
            let data = this.subjects

            //FILTER DEPARTMENT
            if (this.filter.department != 'all') {
                data = data.filter((data) => data.department.indexOf(this.filter.department) > -1)
            }

            //FILTER CLASS
            if (this.filter.class != 'all') {
                data = data.filter((data) => data.for_class[this.filter.class - 1].length > 0)
            }

            //FILTER ROOM
            if (this.filter.room != 'all' && this.filter.class == 'all') {
                data = data.filter((data) => {
                    let newArr = data.for_class[0].concat(data.for_class[1], data.for_class[2], data.for_class[3], data.for_class[4], data.for_class[5])
                    return newArr.indexOf(this.filter.room) > -1
                })
            }
            else if (this.filter.room != 'all' && this.filter.class != 'all') {
                data = data.filter((data) => data.for_class[this.filter.class - 1].indexOf(this.filter.room) > -1)
            }

            //FILTER SEARCH
            if (!isEmpty(this.searchText)) {
                data = data.filter((data) => data.subject_id.indexOf(this.searchText) > -1 || data.name.indexOf(this.searchText) > -1)
            }

            return data
        }
    },
    components: {
        Multiselect
    },
    watch: {
        selectedSubject() {
            if (this.selectedSubject.length > 0) {
                this.mode = 'multiple'
            }
            else {
                this.mode = 'single'
                this.selectAll = false
            }

            if (this.selectedSubject.length == this.filteredSubject().length && this.filteredSubject().length != 0) {
                this.selectAll = true
            }
            else {
                this.selectAll = false
            }
        },
        selectAll() {
            if (this.selectAll) {
                this.selectedSubject = this.filteredSubject()
            }
            else if (this.selectedSubject.length == this.filteredSubject().length) {
                this.selectedSubject = []
            }
        }
    },
    computed: {
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
        this.getRoomAmount()
        this.getSubject()
    }
}
</script>
<template>

    <Head>
        <title>จัดการรายวิชา</title>
    </Head>

    <AppLayout>
        <template #header>จัดการรายวิชา</template>
        <div class="overflow-auto" style="max-height: calc(100vh - 140px);">
            <div class="rounded-xl p-5 bg-white">
                <div class="form-control mb-3">
                    <label class="input-group">
                        <span class="material-symbols-rounded">
                            search
                        </span>
                        <input type="text" placeholder="ค้นหาด้วยรหัสวิชาหรือชื่อวิชา"
                            class="input input-bordered w-full" v-model="searchText" />
                    </label>
                </div>
                <div class="flex items-center mb-3">
                    <div class="dropdown mr-3" v-if="mode == 'multiple'">
                        <label tabindex="0" class="btn btn-secondary">จัดการ
                            <span class="material-symbols-rounded">
                                expand_more
                            </span>
                        </label>
                        <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52">
                            <li class="text-error">
                                <a href="#delete-modal" @click="deleteSubjectData = selectedSubject"><span
                                        class="material-symbols-rounded mr-2">
                                        delete
                                    </span>ลบรายวิชา</a>
                            </li>
                        </ul>
                    </div>
                    <span class="mr-3">
                        <label for="filter-department" class="mr-2 font-medium">กลุ่มสาระการเรียนรู้:</label>
                        <select id="filter-department" class="select select-sm text-xs select-bordered"
                            v-model="filter.department">
                            <option value="all">ทั้งหมด</option>
                            <option v-for="department in departments" :key="department" :value="department">{{
                                    department
                            }}
                            </option>
                        </select>
                    </span>
                    <span class="mr-3">
                        <label for="filter-class" class="mr-2 font-medium">ระดับชั้น:</label>
                        <select id="filter-class" class="select select-sm text-xs select-bordered"
                            v-model="filter.class">
                            <option value="all">ทั้งหมด</option>
                            <option v-for="classes in 6" :key="classes" :value="classes">ม.{{ classes }}
                            </option>
                        </select>
                    </span>
                    <span class="">
                        <label for="filter-room" class="mr-2 font-medium">ห้อง:</label>
                        <select id="filter-room" class="select select-sm text-xs select-bordered" v-model="filter.room">
                            <option value="all">ทั้งหมด</option>
                            <option v-for="room in filteredRoom" :key="room" :value="room">{{ room }}
                            </option>
                        </select>
                    </span>
                </div>

                <!-- SUBJECT TABLE -->
                <div class="overflow-auto">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr>
                                <th class="text-lg text-center">
                                    <input type="checkbox" class="checkbox checkbox-sm checkbox-bordered"
                                        v-model="selectAll">
                                </th>
                                <th class="text-lg">รหัสวิชา</th>
                                <th class="text-lg">ชื่อวิชา</th>
                                <th class="text-lg">กลุ่มสาระการเรียนรู้</th>
                                <th class="text-lg">อาจารย์ผู้สอน</th>
                                <th class="text-right">
                                    <a role="button" href="#add-modal" class="btn btn-sm btn-success"
                                        v-if="mode == 'single'"><span class="material-symbols-rounded mr-2">
                                            add
                                        </span>เพิ่มรายวิชา</a>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="subject in filteredSubject()" :key="subject.subject_id" class="hover">
                                <td class="text-center">
                                    <input type="checkbox" class="checkbox checkbox-sm checkbox-bordered"
                                        v-model="selectedSubject" :value="subject">
                                </td>
                                <td>{{ subject.subject_id }}</td>
                                <td>{{ subject.name }}</td>
                                <td>{{ subject.department }}</td>
                                <td>
                                    <p v-for="teacher in subject.teacher"
                                        :key="'teacher-' + subject.subject_id + '-' + teacher">{{
                                                teacher
                                        }}</p>
                                </td>
                                <td v-if="mode == 'single'">
                                    <a role="button" href="#edit-modal" class="btn btn-warning mr-3"
                                        @click="pushEditSubjectData(subject)"><span
                                            class="material-symbols-rounded mr-2">
                                            edit
                                        </span>แก้ไขข้อมูล</a>
                                    <a role="button" href="#delete-modal" class="btn btn-error"
                                        @click="pushDeleteSubjectData(subject)"><span
                                            class="material-symbols-rounded mr-2">
                                            delete
                                        </span>ลบรายวิชา</a>
                                </td>
                                <td v-else></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ADD MODAL -->
        <div class="modal" id="add-modal">
            <div class="modal-box w-full max-w-5xl">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-bold text-3xl">เพิ่มรายวิชา</h3>
                    <a href="#" class="btn btn-circle btn-ghost" role="button" @click="clearAddSubject()">
                        <span class="material-symbols-rounded">
                            close
                        </span>
                    </a>
                </div>
                <div class="grid grid-cols-2 gap-5">
                    <a role="button" href="#add-subject-csv-modal"
                        class="rounded-lg bg-secondary p-5 py-36 text-center bg-opacity-50 border-4 border-primary">
                        <p class="text-primary text-2xl font-bold">เพิ่มรายวิชาด้วยไฟล์ .csv</p>
                        <p class="text-primary">เพิ่มโดยการใช้ไฟล์ .csv ของระบบไปทำการเพิ่มข้อมูลรายวิชา
                            สามารถเพิ่มข้อมูลได้หลายวิชาในครั้งเดียว</p>
                    </a>
                    <a role="button" href="#add-user-form-modal"
                        class="rounded-lg bg-gray-100 p-5 py-36 text-center bg-opacity-50 border-4 border-gray-400"
                        @click="pushAddSubject()">
                        <p class="text-primary text-2xl font-bold">เพิ่มรายชื่อด้วยฟอร์มของระบบ</p>
                        <p class="text-primary">เพิ่มโดยการกรอกฟอร์มลงในระบบ เหมาะกับการเพิ่มรายวิชาจำนวนน้อย ๆ</p>
                    </a>
                </div>
            </div>
        </div>

        <!-- ADD BY CSV MODAL -->
        <div class="modal" id="add-subject-csv-modal">
            <div class="modal-box w-full max-w-5xl">
                <form @submit.prevent="addSubject()">
                    <div class="flex justify-between items-center mb-3">
                        <div class="flex">
                            <a href="#add-modal" role="button"
                                class="btn btn-sm text-primary hover:bg-transparent hover:border hover:border-b-2 text-xl btn-ghost mr-3">
                                <span class="material-symbols-rounded">
                                    arrow_back
                                </span>
                                ย้อนกลับ</a>
                            <h3 class="text-gray-400 text-xl pt-0.5">เพิ่มรายวิชาด้วยไฟล์ CSV</h3>
                        </div>
                        <a href="#" class="btn btn-circle btn-ghost" role="button" @click="clearAddSubject()">
                            <span class="material-symbols-rounded">
                                close
                            </span>
                        </a>
                    </div>
                    <div class="px-4">
                        <div class="form-control mb-3">
                            <div class="flex mb-1">
                                <label for="add-subject-csv-file" class="mr-3 text-lg">เลือกไฟล์</label>
                                <a href="/download/example-subject-csv.csv" role="button"
                                    class="btn btn-sm btn-warning">ดาวน์โหลดไฟล์เตรียมข้อมูล</a>
                            </div>
                            <input type="file" id="add-subject-csv-file"
                                class="file-input file-input-bordered file-input-primary w-full mb-5"
                                @change="readAddSubjectCsv()" accept=".csv" />
                            <div class="grid grid-cols-2 gap-5">
                                <button type="submit" class="btn btn-success text-lg">เพิ่มรายวิชา</button>
                                <button type="reset" class="btn btn-error text-lg"
                                    @click="addSubjectData = []">ล้างข้อมูล</button>
                            </div>
                        </div>
                        <p class="text-lg font-medium mb-1">รายวิชาที่จะถูกเพิ่ม</p>
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th class="text-lg text-center"></th>
                                    <th class="text-lg">รหัสวิชา</th>
                                    <th class="text-lg">ชื่อวิชา</th>
                                    <th class="text-lg">กลุ่มสาระการเรียนรู้</th>
                                    <th class="text-lg">อาจารย์ผู้สอน</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(subject, i) in addSubjectData" :key="subject.subject_id" class="hover">
                                    <td class="text-center">{{ i + 1 }}</td>
                                    <td>{{ subject.subject_id }}</td>
                                    <td>{{ subject.name }}</td>
                                    <td>{{ subject.department }}</td>
                                    <td>
                                        <p v-for="teacher in subject.teacher"
                                            :key="'teacher-' + subject.subject_id + '-' + teacher">{{ teacher }}
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>

        <!-- ADD BY FORM MODAL -->
        <div class="modal" id="add-user-form-modal" v-if="addSubjectData.length > 0">
            <form @submit.prevent="addSubject()">
                <div class="modal-box w-full max-w-5xl">
                    <div class="flex justify-between items-center mb-3">
                        <div class="flex">
                            <a href="#add-modal" role="button"
                                class="btn btn-sm text-primary hover:bg-transparent hover:border hover:border-b-2 text-xl btn-ghost mr-3">
                                <span class="material-symbols-rounded">
                                    arrow_back
                                </span>
                                ย้อนกลับ</a>
                            <h3 class="text-gray-400 text-xl pt-0.5">เพิ่มรายวิชาด้วยฟอร์ม</h3>
                        </div>
                        <a href="#" class="btn btn-circle btn-ghost" role="button" @click="clearAddSubject()">
                            <span class="material-symbols-rounded">
                                close
                            </span>
                        </a>
                    </div>

                    <div class="mb-3 pl-4">
                        <p class="text-xl font-bold">ข้อมูลทั่วไป</p>
                        <p class="text-gray-500">ข้อมูลทั่วไปที่เกี่ยวกับรายวิชา</p>
                    </div>
                    <div class="grid grid-cols-3 gap-5 gap-y-3 px-4 mb-7">
                        <div class="form-control">
                            <label for="edit-subject-id" class="mb-1 font-medium required">รหัสวิชา</label>
                            <input type="text" id="edit-subject-id" class="input input-sm input-bordered"
                                :class="{ 'border-error': errorBag.message.subject_id }" placeholder="รหัสวิชา"
                                v-model="addSubjectData[0].subject_id">
                            <small class="text-error mt-1" v-if="errorBag.message.subject_id">{{
                                    errorBag.message.subject_id
                            }}</small>
                        </div>
                        <div class="form-control">
                            <label for="edit-subject-name" class="mb-1 font-medium required">ชื่อวิชา</label>
                            <input type="text" id="edit-subject-name" class="input input-sm input-bordered"
                                :class="{ 'border-error': errorBag.message.name }" placeholder="ชื่อวิชา"
                                v-model="addSubjectData[0].name">
                            <small class="text-error mt-1" v-if="errorBag.message.name">{{ errorBag.message.name
                            }}</small>
                        </div>
                        <div class="form-control">
                            <label for="edit-subject-department"
                                class="mb-1 font-medium required">กลุ่มสาระการเรียนรู้</label>
                            <select id="edit-subject-department" class="select select-bordered select-sm text-xs"
                                v-model="addSubjectData[0].department"
                                :class="{ 'border-error': errorBag.message.department }">
                                <option :value="null" disabled>--กลุ่มสาระการเรียนรู้--</option>
                                <option v-for="department in departments"
                                    :key="'edit-subject-department-key-' + department" :value="department">
                                    กลุ่มสาระการเรียนรู้{{ department
                                    }}</option>
                            </select>
                            <small class="text-error mt-1" v-if="errorBag.message.department">{{
                                    errorBag.message.department
                            }}</small>
                        </div>
                    </div>
                    <div class="mb-3 pl-4">
                        <p class="text-xl font-bold">ข้อมูลอาจารย์ผู้สอน</p>
                        <p class="text-gray-500">ข้อมูลรายชื่อของอาจารย์ผู้สอนในรายวิชานั้น ๆ</p>
                    </div>
                    <div class="grid grid-cols-3 gap-5 gap-y-3 px-4 mb-7">
                        <div class="flex justify-between items-center" v-for="(teacher, i) in addSubjectData[0].teacher"
                            :key="'teacher-key-' + i">
                            <div class="mr-2">
                                <div class="form-control">
                                    <label :for="'edit-teacher-' + i" class="mb-1 font-medium required">ชื่อ-นามสกุล
                                        อาจารย์ผู้สอนคนที่
                                        {{ i
                                                + 1
                                        }}</label>
                                    <input type="text" :id="'edit-teacher-' + i"
                                        class="input input-sm input-bordered mb-0"
                                        :class="{ 'border-error': errorBag.message.subject_id }"
                                        placeholder="ชื่ออาจารย์" v-model="addSubjectData[0].teacher[i]">
                                </div>
                                <small class="text-error mt-1" v-if="errorBag.message['teacher' + i]">{{
                                        errorBag.message['teacher' + i]
                                }}</small>
                            </div>
                            <div class="tooltip tooltip-secondary ml-2 mt-2" data-tip="ลบอาจารย์">
                                <button class="btn btn-sm btn-error w-fit" type="button"
                                    @click="addSubjectData[0].teacher.splice(i, 1)"><span
                                        class="material-symbols-rounded">
                                        delete
                                    </span></button>
                            </div>
                        </div>
                        <div class="flex items-center" data-tip="เพิ่มอาจารย์">
                            <div class="tooltip tooltip-secondary w-full mt-2" data-tip="เพิ่มอาจารย์">
                                <button class="btn btn-sm btn-success w-full" type="button"
                                    @click="addSubjectData[0].teacher.push('')"><span class="material-symbols-rounded">
                                        add
                                    </span></button>
                            </div>
                        </div>
                        <small class="text-error col-span-3" v-if="errorBag.message.teacher">{{
                                errorBag.message.teacher
                        }}</small>
                    </div>
                    <div class="mb-3 pl-4">
                        <p class="text-xl font-bold">ข้อมูลระดับชั้นและห้องที่เปิดสอน</p>
                        <p class="text-gray-500">ข้อมูลเกี่ยวกับระดับชั้นและห้องที่เปิดสอน</p>
                    </div>
                    <div class="px-4 mb-7 grid grid-cols-3 gap-5 gap-y-3">
                        <div v-for="(classes, i) in 6" :key="'for-class-collapse-key-' + classes">
                            <label :for="'edit-for-class-' + classes">
                                <p class="mb-1 font-medium">ระดับชั้นม.{{ classes }}</p>
                            </label>
                            <Multiselect :placeholder="'ระดับชั้นม.' + classes" :id="'edit-for-class-' + classes"
                                v-model="addSubjectData[0].for_class[i]" mode="tags" :close-on-select="false"
                                :searchable="true" :options="getClassesDropdownData(classes)" />
                        </div>
                    </div>
                    <div class="modal-action">
                        <button type="submit" class="btn btn-secondary">เพิ่มรายวิชา</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- EDIT MODAL -->
        <div class="modal" id="edit-modal" v-if="Object.keys(editSubjectData).length > 0">
            <form @submit.prevent="updateSubject()">
                <div class="modal-box w-full max-w-5xl">
                    <div class="flex justify-between items-center mb-3">
                        <p class="text-3xl font-bold">แก้ไขข้อมูล</p>
                        <a href="#" class="btn btn-circle btn-ghost" role="button" @click="clearAddSubject()">
                            <span class="material-symbols-rounded">
                                close
                            </span>
                        </a>
                    </div>

                    <div class="mb-3 pl-4">
                        <p class="text-xl font-bold">ข้อมูลทั่วไป</p>
                        <p class="text-gray-500">ข้อมูลทั่วไปที่เกี่ยวกับรายวิชา</p>
                    </div>
                    <div class="grid grid-cols-3 gap-5 gap-y-3 px-4 mb-7">
                        <div class="form-control">
                            <label for="edit-subject-id" class="mb-1 font-medium required">รหัสวิชา</label>
                            <input type="text" id="edit-subject-id" class="input input-sm input-bordered"
                                :class="{ 'border-error': errorBag.message.subject_id }" placeholder="รหัสวิชา"
                                v-model="editSubjectData.subject_id">
                            <small class="text-error mt-1" v-if="errorBag.message.subject_id">{{
                                    errorBag.message.subject_id
                            }}</small>
                        </div>
                        <div class="form-control">
                            <label for="edit-subject-name" class="mb-1 font-medium required">ชื่อวิชา</label>
                            <input type="text" id="edit-subject-name" class="input input-sm input-bordered"
                                :class="{ 'border-error': errorBag.message.name }" placeholder="ชื่อวิชา"
                                v-model="editSubjectData.name">
                            <small class="text-error mt-1" v-if="errorBag.message.name">{{ errorBag.message.name
                            }}</small>
                        </div>
                        <div class="form-control">
                            <label for="edit-subject-department"
                                class="mb-1 font-medium required">กลุ่มสาระการเรียนรู้</label>
                            <select id="edit-subject-department" class="select select-bordered select-sm text-xs"
                                v-model="editSubjectData.department"
                                :class="{ 'border-error': errorBag.message.department }">
                                <option :value="null" disabled>--กลุ่มสาระการเรียนรู้--</option>
                                <option v-for="department in departments"
                                    :key="'edit-subject-department-key-' + department" :value="department">
                                    กลุ่มสาระการเรียนรู้{{ department
                                    }}</option>
                            </select>
                            <small class="text-error mt-1" v-if="errorBag.message.department">{{
                                    errorBag.message.department
                            }}</small>
                        </div>
                    </div>
                    <div class="mb-3 pl-4">
                        <p class="text-xl font-bold">ข้อมูลอาจารย์ผู้สอน</p>
                        <p class="text-gray-500">ข้อมูลรายชื่อของอาจารย์ผู้สอนในรายวิชานั้น ๆ</p>
                    </div>
                    <div class="grid grid-cols-3 gap-5 gap-y-3 px-4 mb-7">
                        <div class="flex justify-between items-center" v-for="(teacher, i) in editSubjectData.teacher"
                            :key="'teacher-key-' + i">
                            <div class="mr-2">
                                <div class="form-control">
                                    <label :for="'edit-teacher-' + i" class="mb-1 font-medium required">ชื่อ-นามสกุล
                                        อาจารย์ผู้สอนคนที่
                                        {{ i
                                                + 1
                                        }}</label>
                                    <input type="text" :id="'edit-teacher-' + i"
                                        class="input input-sm input-bordered mb-0"
                                        :class="{ 'border-error': errorBag.message.subject_id }"
                                        placeholder="ชื่ออาจารย์" v-model="editSubjectData.teacher[i]">
                                </div>
                                <small class="text-error mt-1" v-if="errorBag.message['teacher' + i]">{{
                                        errorBag.message['teacher' + i]
                                }}</small>
                            </div>
                            <div class="tooltip tooltip-secondary ml-2 mt-2" data-tip="ลบอาจารย์">
                                <button class="btn btn-sm btn-error w-fit"
                                    @click="editSubjectData.teacher.splice(i, 1)"><span
                                        class="material-symbols-rounded">
                                        delete
                                    </span></button>
                            </div>
                        </div>
                        <div class="flex items-center" data-tip="เพิ่มอาจารย์">
                            <div class="tooltip tooltip-secondary w-full mt-2" data-tip="เพิ่มอาจารย์">
                                <button class="btn btn-sm btn-success w-full"
                                    @click="editSubjectData.teacher.push('')"><span class="material-symbols-rounded">
                                        add
                                    </span></button>
                            </div>
                        </div>
                        <small class="text-error col-span-3" v-if="errorBag.message.teacher">{{
                                errorBag.message.teacher
                        }}</small>
                    </div>
                    <div class="mb-3 pl-4">
                        <p class="text-xl font-bold">ข้อมูลระดับชั้นและห้องที่เปิดสอน</p>
                        <p class="text-gray-500">ข้อมูลเกี่ยวกับระดับชั้นและห้องที่เปิดสอน</p>
                    </div>
                    <div class="px-4 mb-7 grid grid-cols-3 gap-5 gap-y-3">
                        <div v-for="(classes, i) in 6" :key="'for-class-collapse-key-' + classes">
                            <label :for="'edit-for-class-' + classes">
                                <p class="mb-1 font-medium">ระดับชั้นม.{{ classes }}</p>
                            </label>
                            <Multiselect :placeholder="'ระดับชั้นม.' + classes" :id="'edit-for-class-' + classes"
                                v-model="editSubjectData.for_class[i]" mode="tags" :close-on-select="false"
                                :searchable="true" :options="getClassesDropdownData(classes)" />
                        </div>
                    </div>
                    <div class="modal-action">
                        <button type="submit" class="btn btn-secondary"><span class="material-symbols-rounded mr-2">
                                edit
                            </span>แก้ไขข้อมูล</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- DELETE MODAL -->
        <div class="modal" id="delete-modal" v-if="deleteSubjectData.length > 0">
            <div class="modal-box">
                <div class="flex justify-end items-center">
                    <a href="#" class="btn btn-circle btn-ghost" role="button" @click="clearDeleteSubject()">
                        <span class="material-symbols-rounded">
                            close
                        </span>
                    </a>
                </div>
                <div class="text-center">
                    <p class="text-xl">คุณต้องการลบวิชาเหล่านี้หรือไม่</p>
                    <p class="text-xl font-bold" v-for="subject in deleteSubjectData"
                        :key="'delete-subject-key-' + subject.id">{{ subject.subject_id + ' - ' + subject.name
                        }}</p>
                </div>
                <div class="modal-action grid grid-cols-2 gap-5">
                    <button type="button" class="btn btn-gray text-lg" @click="clearDeleteSubject()">ยกเลิก</button>
                    <button type="button" class="btn btn-error text-lg" @click="deleteSubject()">ลบรายวิชา</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>
@import url('@vueform/multiselect/themes/default.css');

.required::after {
    content: '*';
    color: red;
}
</style>
