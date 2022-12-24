<script setup>
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import { find } from 'lodash';
import { uploadFile } from '@/helper';
import { Head } from '@inertiajs/inertia-vue3';
import { validatePost } from '@/data-validator'
import { isEmpty } from '@/helper';
</script>
<script>
function getURLParameter(param) {
    const url = window.location.search
    return new URLSearchParams(url).get(param)
}
export default {
    data() {
        return {
            postData: {
                title: null,
                type: getURLParameter('type') == 'work' ? 'work' : 'annouce',
                details: null,
                file: [],
                score: 'ไม่มีคะแนน',
                due: '',
                can_submit: true,
                user_data: {},
                comment: [],
                private_comment: {}
            },
            addLink: {},
            subjects: [],
            selectedSubject: {},
            fileUpload: {},
            fileInput: 'upload-img-input',
            errorBag: {
                status: true,
                message: {}
            },
            rootUrl: route('classroom_edit_view') + window.location.search + '#'
        }
    },
    methods: {
        getURLParameter,
        async getSubject() {
            try {
                let teacherName = Inertia.page.props.user.name.split(' ').join('-')
                let res = await axios('/api/subject/teacher/' + teacherName)
                if (res.status == 200) {
                    this.subjects = res.data

                    if (this.subjects.length > 0) {
                        this.selectedSubject = find(this.subjects, (subject) => subject.subject_id == getURLParameter('subject'))
                    }
                }
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างดึงข้อมูลวิชา')
                console.log(err)
            }
        },
        async getPost() {
            try {
                let res = await axios('/api/classroom/' + getURLParameter('subject') + '/post/' + getURLParameter('post_id'))
                if (res.status == 200) {
                    this.postData = res.data
                }
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างดึงข้อมูลโพสต์')
                console.log(err)
            }
        },
        openUploadInput() {
            document.getElementById(this.fileInput).click()
        },
        assignUploadFile() {
            let fileInput = document.getElementById(this.fileInput)
            this.fileUpload = fileInput.files[0]
        },
        assignLink() {
            this.postData.file.push({
                type: 'link',
                title: !isEmpty(this.addLink.title) ? this.addLink.title : this.addLink.path,
                path: this.addLink.path
            })

            this.clearLink()
        },
        clearLink() {
            window.location.href = this.rootUrl
            setTimeout(() => {
                this.addLink = {}
            }, 200);
        },
        async uploadFileData() {
            let formData = new FormData()
            formData.append('file', this.fileUpload)

            let uploadData = await uploadFile(formData)
            if (uploadData.status) {
                this.postData.file.push({
                    type: 'file',
                    title: this.fileUpload.name,
                    path: uploadData.message
                })
                this.clearUploadFile()
                console.log(this.postData)
            }
            else {
                alert('อัปโหลดภาพไม่สำเร็จ')
            }
        },
        clearUploadFile() {
            window.location.href = this.rootUrl
            setTimeout(() => {
                document.getElementById(this.fileInput).value = null
                this.fileUpload = {}
            }, 200);
        },
        async addPost() {
            this.errorBag = {
                status: true,
                message: {}
            }
            try {
                this.errorBag = validatePost(this.postData)
                if (!this.errorBag.status) {
                    return
                }
                let res = await axios.post('/api/classroom/' + this.selectedSubject.subject_id + '/create/post', this.postData)
                if (res.status == 200) {
                    alert('เพิ่ม' + (getURLParameter('type') == 'work' ? 'การบ้าน' : 'ประกาศ') + 'เรียบร้อยแล้ว')
                    window.location.href = getURLParameter('back')
                }
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างเพิ่ม' + (getURLParameter('type') == 'work' ? 'การบ้าน' : 'ประกาศ'))
                console.log(err)
            }
        },
        async updatePost() {
            try {
                let res = await axios.put('/api/classroom/' + getURLParameter('subject') + '/post/' + getURLParameter('post_id') + '/update', this.postData)
                if (res.status == 200) {
                    alert('แก้ไขโพสต์สำเร็จ')
                }
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างอัปเดทโพสต์')
                console.log(err)
            }
        },
        transactionPost() {
            if (getURLParameter('mode') == 'add') {
                this.addPost()
            }

            if (getURLParameter('mode') == 'edit') {
                this.updatePost()
            }
        }
    },
    mounted() {
        if (getURLParameter('mode') == 'add') {
            this.getSubject()
            let date = new Date()
            this.postData.due = date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate()
        }

        if (getURLParameter('mode') == 'edit') {
            this.getPost()
        }
    }
}
</script>
<template>

    <Head>
        <title>{{ getURLParameter('type') == 'work' ? 'เพิ่มการบ้าน' : 'เพิ่มประกาศ' }}</title>
    </Head>
    <AppLayout>
        <template #header>{{ getURLParameter('type') == 'work' ? 'เพิ่มการบ้าน' : 'เพิ่มประกาศ' }}</template>
        <div class="overflow-auto" style="max-height: calc(100vh - 140px)">
            <div class="grid grid-cols-3 gap-5" v-if="getURLParameter('type') == 'work'">
                <!-- MAIN -->
                <div class="col-span-2">
                    <div class="rounded-xl bg-white p-5 mb-5">
                        <div class="flex items-center justify-between">
                            <p class="text-3xl font-bold">{{ postData.title || 'ไม่มีชื่อ' }}</p>
                            <button class="btn btn-success text-lg" @click="transactionPost()">{{
                                    getURLParameter('mode') == 'add' ? 'มอบหมายงาน' : 'แก้ไขงาน'
                            }}</button>
                        </div>
                    </div>
                    <div class="rounded-xl bg-white p-5 mb-5">
                        <label for="edit-title" class="text-gray-500">
                            <p class="text-lg required">ชื่องาน</p>
                        </label>
                        <input type="text" id="edit-title" class="input input-bordered w-full" placeholder="ชื่องาน"
                            v-model="postData.title" :class="{ 'border-error': errorBag.message.title }">
                        <small class="text-error mt-1" v-if="errorBag.message.title">{{ errorBag.message.title
                        }}</small>
                    </div>
                    <div class="rounded-xl bg-white p-5 mb-5">
                        <label for="edit-details" class="text-gray-500">
                            <p class="text-lg">รายละเอียด</p>
                        </label>
                        <textarea id="edit-details" class="input input-bordered w-full" style="height: 100px"
                            v-model="postData.details" placeholder="รายละเอียดของงาน"></textarea>
                    </div>
                    <div class="rounded-xl bg-white p-5">
                        <label for="" class="text-gray-500">
                            <p class="text-lg mb-1">แนบไฟล์/ลิงก์</p>
                        </label>
                        <div>
                            <div class="grid grid-cols-4 gap-3">
                                <div class="border rounded-lg p-3 bg-gray-100 flex items-center justify-between w-full"
                                    v-for="(file, i) in postData.file" :key="'file-' + i">
                                    <div class="flex">
                                        <span class="material-symbols-rounded mr-3">
                                            {{ file.type == 'file' ? 'description' : 'link' }}
                                        </span>
                                        <p class="mr-2">{{ file.title }}</p>
                                    </div>
                                    <button class="btn btn-error btn-xs btn-circle"
                                        @click="postData.file.splice(i, 1)"><span
                                            class="material-symbols-rounded text-sm">
                                            close
                                        </span></button>
                                </div>
                            </div>
                            <div class="text-right">
                                <a href="#upload-img-modal" role="button" class="btn btn-outline btn-neutral mr-3"><span
                                        class="material-symbols-rounded mr-3">
                                        description
                                    </span>ไฟล์</a>
                                <a role="button" href="#add-link-modal" class="btn btn-outline btn-neutral"><span
                                        class="material-symbols-rounded mr-3">
                                        link
                                    </span>ลิงก์</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SIDEBAR -->
                <div class="rounded-xl bg-white p-5 h-fit">
                    <template v-if="getURLParameter('mode') == 'add'">
                        <label for="select-subject">
                            <p class="mb-3">สำหรับวิชา</p>
                        </label>
                        <select class="select select-bordered w-full mb-3" id="select-subject"
                            v-model="selectedSubject">
                            <option v-for="subject in subjects" :key="'subject-' + subject.id" :value="subject">{{
                                    subject.subject_id + ' - ' + subject.name
                            }}</option>
                        </select>
                    </template>
                    <label for="edit-score">
                        <p class="mb-3">คะแนน</p>
                    </label>
                    <input list="score" id="edit-score" class="input input-bordered w-full mb-3"
                        v-model="postData.score">
                    <datalist id="score">
                        <option value="ไม่มีคะแนน"></option>
                    </datalist>
                    <label for="edit-due">
                        <p class="mb-3">กำหนดส่ง</p>
                    </label>
                    <input type="date" id="edit-due" class="input input-bordered w-full" v-model="postData.due">
                </div>
            </div>

            <!-- ANNOUCE -->
            <div class="grid grid-cols-3 gap-5" v-if="getURLParameter('type') == 'annouce'">
                <!-- MAIN -->
                <div class="col-span-2">
                    <div class="rounded-xl bg-white p-5 mb-5">
                        <div class="flex items-center justify-between">
                            <p class="text-3xl font-bold">{{ postData.title || 'ไม่มีชื่อ' }}</p>
                            <button class="btn btn-warning text-lg" @click="transactionPost()">{{
                                    getURLParameter('mode') == 'add' ? 'ประกาศ' : 'แก้ไขประกาศ'
                            }}</button>
                        </div>
                    </div>
                    <div class="rounded-xl bg-white p-5 mb-5">
                        <label for="edit-title" class="text-gray-500">
                            <p class="text-lg required">หัวข้อประกาศ</p>
                        </label>
                        <input type="text" id="edit-title" class="input input-bordered w-full"
                            placeholder="หัวข้อประกาศ" v-model="postData.title">
                    </div>
                    <div class="rounded-xl bg-white p-5 mb-5">
                        <label for="edit-details" class="text-gray-500">
                            <p class="text-lg">รายละเอียด</p>
                        </label>
                        <textarea id="edit-details" class="input input-bordered w-full" style="height: 100px"
                            placeholder="รายละเอียดของประกาศ" v-model="postData.details"></textarea>
                    </div>
                    <div class="rounded-xl bg-white p-5">
                        <label for="" class="text-gray-500">
                            <p class="text-lg">แนบไฟล์/ลิงก์</p>
                        </label>
                        <div>
                            <div class="grid grid-cols-4 gap-3">
                                <div class="border rounded-lg p-3 bg-gray-100 flex items-center justify-between w-full"
                                    v-for="(file, i) in postData.file" :key="'file-' + i">
                                    <div class="flex">
                                        <span class="material-symbols-rounded mr-3">
                                            {{ file.type == 'file' ? 'description' : 'link' }}
                                        </span>
                                        <p class="mr-2">{{ file.title }}</p>
                                    </div>
                                    <button class="btn btn-error btn-xs btn-circle"
                                        @click="postData.file.splice(i, 1)"><span
                                            class="material-symbols-rounded text-sm">
                                            close
                                        </span></button>
                                </div>
                            </div>
                            <div class="text-right">
                                <a href="#upload-img-modal" role="button" class="btn btn-outline btn-neutral mr-3"><span
                                        class="material-symbols-rounded mr-3">
                                        description
                                    </span>ไฟล์</a>
                                <a role="button" href="#add-link-modal" class="btn btn-outline btn-neutral"><span
                                        class="material-symbols-rounded mr-3">
                                        link
                                    </span>ลิงก์</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SIDEBAR -->
                <div class="rounded-xl bg-white p-5 h-fit">
                    <template v-if="getURLParameter('mode') == 'add'">
                        <label for="select-subject">
                            <p class="mb-3">สำหรับวิชา</p>
                        </label>
                        <select class="select select-bordered w-full mb-3" id="select-subject"
                            v-model="selectedSubject">
                            <option v-for="subject in subjects" :key="'subject-' + subject.id" :value="subject">{{
                                    subject.subject_id + ' - ' + subject.name
                            }}</option>
                        </select>
                    </template>
                </div>
            </div>
        </div>

        <!-- UPLOAD IMG MODAL -->
        <div class="modal" id="upload-img-modal">
            <div class="modal-box w-full max-w-lg">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-bold text-3xl">อัปโหลดไฟล์</h3>
                    <button class="btn btn-circle btn-ghost" type="button" @click="clearUploadFile()">
                        <span class="material-symbols-rounded">
                            close
                        </span>
                    </button>
                </div>
                <input aria-label="upload-img-input" type="file" id="upload-img-input"
                    class="file-input file-input-bordered file-input-primary w-full mt-5" @change="assignUploadFile()">
                <div class="text-right mt-5">
                    <button class="btn btn-secondary" @click="uploadFileData()">อัปโหลดไฟล์</button>
                </div>
            </div>
        </div>

        <!-- ADD LINK MODAL -->
        <div class="modal" id="add-link-modal">
            <div class="modal-box w-full max-w-lg">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-bold text-3xl">แนบลิงก์</h3>
                    <button class="btn btn-circle btn-ghost" type="button" @click="clearLink()">
                        <span class="material-symbols-rounded">
                            close
                        </span>
                    </button>
                </div>
                <div class="form-control mb-3">
                    <label for="edit-link-title" class="font-medium">ชื่อลิงก์</label>
                    <input type="text" class="input input-bordered mt-1" placeholder="ชื่อลิงก์"
                        v-model="addLink.title">
                </div>
                <div class="form-control">
                    <label for="edit-link-path" class="font-medium">ที่อยู่ลิงก์</label>
                    <input type="text" class="input input-bordered mt-1" placeholder="ที่อยู่ลิงก์"
                        v-model="addLink.path">
                </div>
                <div class="text-right mt-5">
                    <button class="btn btn-secondary" @click="assignLink()">แนบลิงก์</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>
.required::after {
    content: '*';
    color: red;
}
</style>
