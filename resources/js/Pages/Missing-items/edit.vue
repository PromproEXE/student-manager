<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { isAdmin } from '@/role'
import axios from 'axios';
import { Head } from '@inertiajs/inertia-vue3';
import { uploadFile } from '@/helper'
</script>
<script>
function getURLParameter(param) {
    const url = window.location.search
    return new URLSearchParams(url).get(param)
}

export default {
    data() {
        return {
            itemData: {
                title: null,
                details: null,
                img: [],
            },
            uploadImgInput: 'upload-img-input',
            uploadImg: {},
            mode: getURLParameter('mode'),
            rootUrl: route('missing_items_edit_view') + window.location.search + '#'
        }
    },
    methods: {
        async getItemData() {
            try {
                let res = await axios('/api/missing-items/' + getURLParameter('id'))
                if (res.status == 200) {
                    if (Object.keys(res.data).length != 0) {
                        this.itemData = res.data
                    }
                    else {
                        this.mode = 'add'
                    }
                }
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างดึงข้อมูลสิ่งของ')
                console.log(err)
            }
        },
        async addItem() {
            try {
                let res = await axios.post('/api/missing-items/create', this.itemData)
                if (res.status == 201) {
                    alert('ประกาศของหายแล้ว')
                    window.history.replaceState(null, null, "?mode=edit&id=" + res.data.id);
                    this.mode = 'edit'
                }
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างเพิ่มประกาศของหาย')
                console.log(err)
            }
        },
        async UpdateItem() {
            try {
                let res = await axios.put('/api/missing-items/update/' + getURLParameter('id'), this.itemData)
                if (res.status == 200) {
                    alert('แก้ไขประกาศแล้ว')
                }
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างแก้ไขประกาศ')
                console.log(err)
            }
        },
        openUploadInput() {
            document.getElementById(this.uploadImgInput).click()
        },
        assignUploadImg() {
            let fileInput = document.getElementById(this.uploadImgInput)
            this.uploadImg = fileInput.files[0]
            document.getElementById('upload-img-preview').src = URL.createObjectURL(this.uploadImg)
        },
        async uploadImage() {
            let formData = new FormData()
            formData.append('file', this.uploadImg)

            let uploadData = await uploadFile(formData)
            if (uploadData.status) {
                this.itemData.img.push(uploadData.message)
                this.clearUploadImg()
            }
            else {
                alert('อัปโหลดภาพไม่สำเร็จ')
            }
        },
        clearUploadImg() {
            window.location.href = this.rootUrl
            setTimeout(() => {
                document.getElementById(this.uploadImgInput).value = null
                document.getElementById('upload-img-preview').src = ''
                this.uploadImg = {}
            }, 200);
        },
        async transactionItem() {
            if (this.mode == 'add') {
                this.addItem()
            }
            else if (this.mode == 'edit') {
                this.UpdateItem()
            }
            else {
                alert('โหมดไม่ถูกต้อง')
            }
        }
    },
    mounted() {
        if (this.mode == 'edit') {
            this.getItemData()
        }
    }
}
</script>
<template>

    <Head>
        <title>{{ mode == 'add' ? 'เพิ่มประกาศของหาย' : 'แก้ไขรายละเอียดของหาย' }}</title>
    </Head>
    <AppLayout v-if="isAdmin($page.props.user.role)">
        <div class="overflow-auto" style="max-height: calc(100vh - 140px)">
            <form @submit.prevent="transactionItem()">
                <div class="rounded-xl bg-white p-5 mb-5">
                    <div class="flex items-center justify-between">
                        <p class="text-3xl font-bold">{{ itemData.title || 'ไม่มีหัวข้อ' }}</p>
                        <button class="btn btn-warning text-lg" type="submit">{{ mode == 'add' ? 'ประกาศ' :
                                'แก้ไขประกาศ'
                        }}</button>
                    </div>
                </div>
                <div class="rounded-xl bg-white p-5 mb-5 form-control">
                    <label for="edit-item-title" class="font-medium mb-1">ชื่อสิ่งของที่หาย</label>
                    <input type="text" id="edit-item-title" class="input input-bordered"
                        placeholder="หัวข้อของสิ่งที่หาย" v-model="itemData.title">
                </div>
                <div class="rounded-xl bg-white p-5 mb-5 form-control">
                    <label for="edit-item-details" class="font-medium mb-1">รายละเอียด</label>
                    <textarea class="input input-bordered" id="edit-item-details" style="height: 100px"
                        placeholder="รายละเอียดของสิ่งที่หาย" v-model="itemData.details"></textarea>
                </div>
                <div class="rounded-xl bg-white p-5 form-control">
                    <label class="font-medium mb-1">แนบไฟล์ภาพ</label>
                    <div class="grid grid-cols-4 gap-5">
                        <div class="overlay-container rounded-lg" v-for="(img, i) in itemData.img" :key="'img-' + i">
                            <img :src="img" class="w-full rounded-lg">
                            <div class="overlay justify-center items-center rounded-lg">
                                <button class="btn btn-ghost h-fit" type="button"
                                    @click="itemData.img.splice(i, 1)"><span class="material-symbols-rounded"
                                        style="font-size: 4rem;">
                                        delete
                                    </span></button>

                            </div>
                        </div>

                        <a href="#upload-img-modal" role="button" class="btn btn-outline btn-neutral mr-3 h-full"><span
                                class="material-symbols-rounded mr-3">
                                description
                            </span>ไฟล์</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- UPLOAD IMG MODAL -->
        <div class="modal" id="upload-img-modal">
            <div class="modal-box w-full max-w-lg">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-bold text-3xl">อัปโหลดรูปภาพ</h3>
                    <button class="btn btn-circle btn-ghost" type="button" @click="clearUploadImg()">
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
                <img id="upload-img-preview" src="" alt="" class="w-full">
                <input aria-label="upload-img-input" type="file" id="upload-img-input" accept="image/jpeg, image/png"
                    class="file-input file-input-bordered file-input-primary w-full" @change="assignUploadImg()"
                    :class="{ 'mt-5': (typeof this.uploadImg.name == 'string') }">
                <div class="text-right mt-5">
                    <button class="btn btn-secondary" @click="uploadImage()">อัปโหลดรูปภาพ</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>
/* The overlay effect - lays on top of the container and over the image */
.overlay {
    display: flex;
    position: absolute;
    bottom: 0;
    border: 5px dashed #b18ae3;
    background: rgba(177, 138, 227, 0.3);
    /* Black see-through */
    color: #f1f1f1;
    width: 100%;
    height: 100%;
    transition: .5s ease;
    opacity: 0;
    color: white;
    font-size: 20px;
    padding: 20px;
    text-align: center;
}

/* When you mouse over the container, fade in the overlay title */
.overlay-container:hover .overlay {
    opacity: 1;
}

.overlay-container {
    position: relative;
}
</style>
