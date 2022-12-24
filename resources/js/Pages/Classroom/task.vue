<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import axios from 'axios';
import { Head } from '@inertiajs/inertia-vue3';
import { isEmpty } from '@/helper';
import { isStudent, isTeacher } from '@/role'
import { Inertia } from '@inertiajs/inertia';
import { uploadFile } from '@/helper';
</script>
<script>
function getURLParameter(param) {
    const url = window.location.search
    return new URLSearchParams(url).get(param)
}
export default {
    data() {
        return {
            post: {
                due: new Date(),
                user_data: {}
            },
            teachers: [],
            fileInput: 'upload-img-input',
            uploadFileData: {},
            selectedStd: {},
            rootUrl: route('classroom_task_view') + window.location.search + '#'
        }
    },
    methods: {
        async getPost() {
            try {
                let res = await axios('/api/classroom/' + getURLParameter('class_id') + '/post/' + getURLParameter('post'))
                if (res.status == 200) {
                    this.post = res.data

                    if (Array.isArray(this.post.user_data)) {
                        this.post.user_data = {}
                    }

                    if (isStudent(Inertia.page.props.user.role)) {
                        this.processPost()
                    }

                    console.log(this.post)
                }
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างดึงข้อมูลโพสต์')
                console.log(err)
            }
        },
        async getTeacherName() {
            try {
                let res = await axios('/api/classroom/' + getURLParameter('class_id') + '/teacher/name')
                if (res.status == 200) {
                    this.teachers = res.data.teacher
                }
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างดึงข้อมูลอาจารย์')
                console.log(err)
            }
        },
        async updatePost(silent = false) {
            try {
                let res = await axios.put('/api/classroom/' + getURLParameter('class_id') + '/post/' + getURLParameter('post') + '/update', this.post)
                if (res.status == 200) {
                    if (!silent) {
                        alert('บันทึกข้อมูลสำเร็จ')
                    }
                }
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างบันทึกข้อมูล')
                console.log(err)
            }
        },
        changeWorkStatus(data, status) {
            data.submit = status
            data.user = {
                id: Inertia.page.props.user.id,
                name: Inertia.page.props.user.name,
                class: Inertia.page.props.user.class,
                room: Inertia.page.props.user.room
            }
            this.updatePost(true)
        },
        formatDate(date) {
            let dateObj = new Date(date)
            return Intl.DateTimeFormat('th-TH', { dateStyle: 'full' }).format(dateObj)
        },
        getRealFileName(path) {
            return path.split('/')[2]
        },
        processPost() {
            if (!this.post.user_data[Inertia.page.props.user.id]) {
                this.post.user_data[Inertia.page.props.user.id] = {
                    submit: false,
                    file: [],
                    user: {
                        id: Inertia.page.props.user.id,
                        name: Inertia.page.props.user.name,
                        class: Inertia.page.props.user.class,
                        room: Inertia.page.props.user.room
                    }
                }
            }
        },
        openUploadInput() {
            document.getElementById(this.fileInput).click()
        },
        assignUploadFile() {
            let fileInput = document.getElementById(this.fileInput)
            this.uploadFileData = fileInput.files[0]
        },
        async uploadFileToServer() {
            let formData = new FormData()
            formData.append('file', this.uploadFileData)

            let uploadData = await uploadFile(formData)
            console.log(this.uploadFileData)
            if (uploadData.status) {
                this.post.user_data[Inertia.page.props.user.id].file.push({
                    name: this.uploadFileData.name,
                    size: (this.uploadFileData.size / 1024).toFixed(2) + ' KB',
                    mime_type: this.uploadFileData.type,
                    path: uploadData.message
                })
                this.clearUploadFile()
                this.updatePost(true)
            }
            else {
                alert('อัปโหลดไฟล์ไม่สำเร็จ')
            }
        },
        clearUploadFile() {
            window.location.href = this.rootUrl
            setTimeout(() => {
                document.getElementById(this.fileInput).value = null
                this.uploadFileData = {}
            }, 200);
        },
        grading() {
            this.selectedStd.grading = true
            this.post.user_data[this.selectedStd.user.id] = this.selectedStd

            this.updatePost(false)
        },
        statusColor(data) {
            if (data.grading) {
                return 'text-warning'
            }

            if (data.submit) {
                return 'text-success'
            }
            else {
                return 'text-error'
            }
        }
    },
    computed: {
        getFileData() {
            try {
                let file = this.post.file.filter((item) => item.type == 'file')
                return file.length
            }
            catch (err) {
                return 0
            }
        },
        getLinkData() {
            try {
                let file = this.post.file.filter((item) => item.type == 'link')
                return file.length
            }
            catch (err) {
                return 0
            }
        },
    },
    mounted() {
        this.getTeacherName()
        this.getPost()
    }
}
</script>
<template>
    <AppLayout>
        <div class="overflow-auto" style="max-height: calc(100vh - 140px)">
            <div class="rounded-xl bg-white p-5 flex items-center justify-between mb-5">
                <div class="flex items-center">
                    <span class="material-symbols-rounded text-5xl mr-5">
                        {{ post.type == 'work' ? 'description' : 'campaign' }}
                    </span>
                    <div>
                        <p class="text-3xl font-bold">{{ post.title }}</p>
                        <p class="text-gray-400"><span v-for="(teacher, i) in teachers" :key="'teacher-' + i">{{
                                teacher
                        }}</span> | {{ formatDate(post.due) }}</p>
                    </div>
                </div>
                <div class="rounded-lg p-3 bg-green-100" v-if="post.type == 'work'">
                    <p class="text-green-500 font-bold text-2xl">{{ post.score == 'ไม่มีคะแนน' ? post.score : post.score
                            + ' คะแนน'
                    }}</p>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-5">
                <div class="rounded-xl p-5 bg-white h-fit"
                    :class="{ 'col-span-2': post.type == 'work', 'col-span-3': post.type == 'annouce' }">
                    <template v-if="!isEmpty(post.details)">
                        <p class="font-bold text-2xl">รายละเอียด</p>
                        <p class="mb-5">{{ post.details }}</p>
                    </template>
                    <template v-if="getFileData > 0">
                        <p class="font-bold text-2xl mb-1">ไฟล์ที่แนบมาด้วย</p>
                        <div class="grid grid-cols-4 gap-3 mb-5">
                            <template v-for="(file, i) in post.file" :key="'file-' + i">
                                <a :href="'/download/' + getRealFileName(file.path)"
                                    class="rounded-lg border bg-gray-50 p-3 cursor-pointer"
                                    v-if="file.type == 'file'">{{ file.title }}</a>
                            </template>
                        </div>
                    </template>
                    <template v-if="getLinkData > 0">
                        <p class="font-bold text-2xl mb-1">ลิงก์ที่แนบมาด้วย</p>
                        <div class="grid grid-cols-4 gap-3 mb-5">
                            <template v-for="(link, i) in post.file" :key="'link-' + i">
                                <a :href="link.path" target="_blank"
                                    class="rounded-lg border bg-gray-50 p-3 cursor-pointer"
                                    v-if="link.type == 'link'">{{ link.title }}</a>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- SIDEBAR FOR STUDENT -->
                <div class="rounded-xl bg-white p-5" v-if="post.type == 'work' && isStudent($page.props.user.role)">
                    <div class="flex items-end justify-between mb-3">
                        <p class="text-2xl font-bold">งานของคุณ</p>
                        <p class="" :class="statusColor(post.user_data[$page.props.user.id])"
                            v-if="post.user_data[$page.props.user.id]">{{
                                    post.user_data[$page.props.user.id].grading ? 'ส่งกลับแล้ว' :
                                        post.user_data[$page.props.user.id].submit ? 'ส่งแล้ว' : 'ยังไม่ส่ง'
                            }}</p>
                    </div>
                    <template v-if="post.user_data[$page.props.user.id]">
                        <div class="p-3 border rounded-lg flex items-center justify-between mb-3"
                            v-for="(file, i) in post.user_data[$page.props.user.id].file" :key="'submit-file-' + i">
                            <div class="flex items-center w-full">
                                <span class="material-symbols-rounded mr-3 text-4xl">
                                    description
                                </span>
                                <div>
                                    <p class="font-medium text-lg">{{ file.name }}</p>
                                    <small class="text-gray-400">{{ file.size }}</small>
                                </div>
                            </div>
                            <button class="btn btn-ghost btn-circle"
                                @click="post.user_data[$page.props.user.id].file.splice(i, 1)"
                                v-if="post.user_data[$page.props.user.id] && !post.user_data[$page.props.user.id].submit">
                                <span class="material-symbols-rounded">
                                    close
                                </span>
                            </button>
                        </div>
                    </template>
                    <a href="#upload-file-modal"
                        class="btn btn-ghost font-normal btn-outline border-gray-300 text-lg w-full mb-3"
                        v-if="post.user_data[$page.props.user.id] && !post.user_data[$page.props.user.id].submit && !post.user_data[$page.props.user.id].grading">เพิ่มงาน</a>
                    <button class="btn btn-success text-lg w-full"
                        v-if="post.user_data[$page.props.user.id] && !post.user_data[$page.props.user.id].submit && !post.user_data[$page.props.user.id].grading"
                        @click="changeWorkStatus(post.user_data[$page.props.user.id], true)">เสร็จสิ้น</button>
                    <button class="btn btn-error text-lg w-full"
                        v-if="post.user_data[$page.props.user.id] && post.user_data[$page.props.user.id].submit && !post.user_data[$page.props.user.id].grading"
                        @click="changeWorkStatus(post.user_data[$page.props.user.id], false)">ยกเลิกการส่ง</button>
                </div>

                <!-- SIDEBAR FOR TEACHER -->
                <div class="rounded-xl bg-white p-5" v-if="post.type == 'work' && isTeacher($page.props.user.role)">
                    <div class="flex items-end justify-between mb-3">
                        <p class="text-2xl font-bold">งานของนักเรียน</p>
                        <p class=""
                            :class="{ 'text-success': post.user_data[$page.props.user.id].submit, 'text-error': !post.user_data[$page.props.user.id].submit }"
                            v-if="post.user_data[$page.props.user.id]">{{
                                    post.user_data[$page.props.user.id].submit ? 'ส่งแล้ว' : 'ยังไม่ส่ง'
                            }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-5 gap-y-3 mb-3">
                        <!-- <div class="form-control">
                            <label for="filter-class" class="font-medium">ระดับชั้น</label>
                            <select id="filter-class" class="select select-bordered select-sm text-xs">
                                <option value="all">ทั้งหมด</option>
                                <option v-for="classes in 6" :value="classes" :key="'classes-' + classes">ม.{{
                                        classes
                                }}</option>
                            </select>
                        </div>
                        <div class="form-control">
                            <label for="filter-room" class="font-medium">ห้อง</label>
                            <select id="filter-room" class="select select-bordered select-sm text-xs">
                                <option value="all">ทั้งหมด</option>
                                <option v-for="room in 14" :value="room" :key="'room-' + room">{{
                                        room
                                }}</option>
                            </select>
                        </div> -->
                        <div class="form-control col-span-2">
                            <label for="filter-std" class="font-medium">ชื่อนักเรียน</label>
                            <select id="filter-std" class="select select-bordered select-sm text-xs"
                                v-model="selectedStd">
                                <template v-for="std in post.user_data" :key="'std-' + std.user.id">
                                    <option v-if="!std.grading" :value="std">{{
                                            std.user.name
                                    }}</option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div class="p-3 border rounded-lg flex items-center justify-between mb-3"
                        v-for="(file, i) in selectedStd.file" :key="'submit-file-' + i">
                        <a :href="'/download/' + getRealFileName(file.path)" class="flex items-center w-full">
                            <span class="material-symbols-rounded mr-3 text-4xl">
                                description
                            </span>
                            <div>
                                <p class="font-medium text-lg">{{ file.name }}</p>
                                <small class="text-gray-400">{{ file.size }}</small>
                            </div>
                        </a>
                        <button class="btn btn-ghost btn-circle"
                            @click="post.user_data[$page.props.user.id].file.splice(i, 1)"
                            v-if="post.user_data[$page.props.user.id] && !post.user_data[$page.props.user.id].submit">
                            <span class="material-symbols-rounded">
                                close
                            </span>
                        </button>
                    </div>
                    <button class="btn btn-warning text-lg w-full" @click="grading()"
                        v-if="Object.keys(selectedStd).length > 0">ส่งกลับ</button>
                </div>
                <!-- <div class="col-span-2 rounded-xl p-5 bg-white">
                    <p class="font-bold mb-3">คอมเมนต์ของงานนี้</p>
                    <div class="grid grid-cols-5 gap-5">
                        <textarea type="text" class="input input-bordered col-span-4 w-full"
                            placeholder="เขียนคอมเมนต์ที่นี่"></textarea>
                        <button class="btn btn-primary">คอมเมนต์</button>
                    </div>
                </div>
                <div class="rounded-xl p-5 bg-white">
                    <p class="font-bold mb-3">คอมเมนต์ส่วนตัว</p>
                    <input type="text" class="input input-bordered w-full mb-3" placeholder="เขียนคอมเมนต์ที่นี่">
                    <button class="btn btn-secondary w-full">คอมเมนต์</button>
                </div> -->
            </div>
        </div>

        <!-- UPLOAD FILE MODAL -->
        <div class="modal" id="upload-file-modal">
            <div class="modal-box w-full max-w-lg">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-bold text-3xl">อัปโหลดไฟล์</h3>
                    <button class="btn btn-circle btn-ghost" type="button" @click="clearUploadFile()">
                        <span class="material-symbols-rounded">
                            close
                        </span>
                    </button>
                </div>
                <!-- <div class="form-control">
                                    <div class="rounded-lg border-dashed border-4 border-primary p-5 bg-secondary bg-opacity-30 text-primary text-center cursor-pointer"
                                        v-if="(typeof this.uploadImg.name != 'string')" @click="openUploadInput()">
                                        <span class="material-symbols-rounded" style="font-size: 4rem; font-weight: 500;">
                                            upload
                                        </span>
                                        <p class="text-xl">คลิกที่นี่เพื่อเลือกภาพ</p>
                                    </div>
                                </div> -->
                <input aria-label="upload-img-input" type="file" id="upload-img-input"
                    class="file-input file-input-bordered file-input-primary w-full" @change="assignUploadFile()"
                    :class="{ 'mt-5': (typeof this.uploadFileData.name == 'string') }">
                <div class="text-right mt-5">
                    <button class="btn btn-secondary" @click="uploadFileToServer()">อัปโหลดไฟล์</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
