<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { isStudent, isTeacher } from '@/role'
import { Head } from '@inertiajs/inertia-vue3';
import axios from 'axios';
</script>
<script>
function getURLParameter(param) {
    const url = window.location.search
    return new URLSearchParams(url).get(param)
}
export default {
    data() {
        return {
            data: {
                data: {},
            },
            selectedPost: {},
            rootUrl: route('classroom_room_view') + window.location.search
        }
    },
    methods: {
        formatDate(date) {
            let dateObj = new Date(date)
            return Intl.DateTimeFormat('th-TH', { dateStyle: 'full' }).format(dateObj)
        },
        async getClassroom() {
            try {
                let res = await axios('/api/classroom/' + getURLParameter('id'))
                if (res.status == 200) {
                    this.data = res.data
                }
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างดึงข้อมูลห้องเรียน')
                console.log(err)
            }
        },
        clearSelectedPost() {
            window.location.href = this.rootUrl + '#'
            setTimeout(() => this.selectedPost = {}, 200)
        },
        async deletePost() {
            try {
                let res = await axios.delete('/api/classroom/' + getURLParameter('id') + '/post/' + this.selectedPost.id + '/delete')
                if (res.status == 200) {
                    alert('ลบโพสต์แล้ว')
                    this.clearSelectedPost()
                    this.getClassroom()
                }
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างลบโพสต์')
                console.log(err)
            }
        }
    },
    mounted() {
        this.getClassroom()


    }
}
</script>
<template>

    <Head>
        <title>{{ data.name }}</title>
    </Head>
    <AppLayout>
        <div style="max-height: calc(100vh - 140px)" class="overflow-auto">
            <div class="rounded-xl bg-orange-200 px-5 pt-40 pb-3 mb-5">
                <p class="text-4xl font-bold">{{ data.name }}</p>
                <div class="flex">
                    <p v-for="(teacher, i) in data.teacher" :key="'teacher-' + i">{{ teacher }}</p>
                </div>
            </div>
            <div class="grid grid-cols-4 gap-5">
                <div class="bg-white rounded-xl p-5 h-fit border" v-if="isStudent($page.props.user.role)">
                    <p class="font-bold mb-3">งานที่จะต้องส่ง</p>
                    <p class="text-gray-400 mb-3 text-center">ดีใจด้วย! คุณไม่มีงานที่จะต้องส่ง</p>
                    <div class="text-right">
                        <button class="btn btn-sm btn-ghost">ดูทั้งหมด</button>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-5 h-fit border" v-if="isTeacher($page.props.user.role)">
                    <p class="font-bold mb-3">แท็บด่วน</p>
                    <a :href="route('classroom_edit_view') + '?subject=' + data.class_id + '&type=work&mode=add&back=' + rootUrl"
                        role="button" class="btn btn-success w-full mb-3">เพิ่มการบ้าน</a>
                    <a :href="route('classroom_edit_view') + '?subject=' + data.class_id + '&type=annouce&mode=add&back=' + rootUrl"
                        role="button" class="btn btn-warning w-full">เพิ่มประกาศ</a>

                </div>
                <div class="col-span-3">
                    <template v-if="data.data.post">
                        <template v-for="post in data.data.post" :key="post.id">
                            <div class="w-full flex rounded-lg bg-white hover:bg-gray-50 hover:cursor-pointer mb-5 items-center border justify-between"
                                v-if="post.can_submit">
                                <a :href="route('classroom_task_view') + '?class_id=' + getURLParameter('id') + '&post=' + post.id"
                                    class="flex w-full px-5 py-4">
                                    <span class="material-symbols-rounded text-5xl">
                                        {{ post.type == 'work' ? 'description' : 'campaign' }}
                                    </span>
                                    <div class="ml-3">
                                        <p class="text-xl font-medium mb-0">{{ post.title }}</p>
                                        <small class="text-gray-500">{{ formatDate(post.due) }}</small>
                                    </div>
                                </a>
                                <div class="dropdown dropdown-left px-5 py-4">
                                    <label tabindex="0" class="btn btn-ghost btn-circle"><span
                                            class="material-symbols-rounded">
                                            more_vert
                                        </span></label>
                                    <ul tabindex="0"
                                        class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52">
                                        <li v-if="post.type == 'work'"><a
                                                :href="route('classroom_edit_view') + '?subject=' + data.id + '&type=work&mode=edit&back=' + rootUrl + '&post_id=' + post.id">แก้ไข</a>
                                        </li>
                                        <li v-if="post.type == 'annouce'"><a
                                                :href="route('classroom_edit_view') + '?subject=' + data.id + '&type=annouce&mode=edit&back=' + rootUrl + '&post_id=' + post.id">แก้ไข</a>
                                        </li>
                                        <li class="text-error" @click="selectedPost = post"><a href="#delete-modal"
                                                class="w-full">ลบโพสต์</a></li>
                                    </ul>
                                </div>
                            </div>
                        </template>
                    </template>
                    <div class="text-center" v-else>
                        <p>¯\_(ツ)_/¯</p>
                        <p>ไม่มีประกาศหรืองาน</p>
                        <p>เมื่อมีงานหรือประกาศจะขึ้นตรงนี้</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- DELETE MODAL -->
        <div class="modal" id="delete-modal">
            <div class="modal-box">
                <div class="flex justify-end items-center">
                    <a href="#" class="btn btn-circle btn-ghost" role="button" @click="clearSelectedPost()">
                        <span class="material-symbols-rounded">
                            close
                        </span>
                    </a>
                </div>
                <div class="text-center">
                    <p class="text-xl">คุณต้องการลบโพสต์นี้หรือไม่</p>
                    <p class="text-xl font-bold">{{ selectedPost.title }}</p>
                </div>
                <div class="modal-action grid grid-cols-2 gap-5">
                    <button type="button" class="btn btn-gray text-lg" @click="clearSelectedPost()">ยกเลิก</button>
                    <button type="button" class="btn btn-error text-lg" @click="deletePost()">ลบโพสต์</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
