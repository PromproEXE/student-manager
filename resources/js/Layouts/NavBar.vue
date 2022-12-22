<script setup>
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { isAdmin, isStudent, isTeacher, isSystem } from '@/role';
const logout = () => {
    Inertia.post(route('logout'));
};
</script>

<script>
export default {
    data() {
        return {

        }
    },
    methods: {
        isEmpty(str) {
            return !str || str.length === 0
        }
    }
}
</script>

<template>
    <nav class="p-5">
        <div class="bg-white text-neutral rounded-xl p-5 h-full">
            <div class="mb-5">
                <a class="btn btn-ghost hover:bg-transparent" :href="route('index')">
                    <img src="/img/logo/student-manager-logo.png" style="width: 200px;" alt="logo">
                </a>
            </div>
            <div class="flex flex-col justify-between items-stretch" style="height: calc(100vh - 163px)">
                <ul class="menu p-0 rounded-xl">
                    <li class="rounded-xl text-xl"><a :href="route('index')"><span class="material-symbols-rounded">
                                home
                            </span>หน้าหลัก</a></li>
                    <template v-if="isStudent($page.props.user.role)">
                        <li class="rounded-xl text-xl" tabindex="0">
                            <a>
                                <span class="material-symbols-rounded">
                                    schedule
                                </span>เวลาเรียน
                                <svg class="fill-current" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24">
                                    <path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z" />
                                </svg>
                            </a>
                            <ul class="p-2 bg-white drop-shadow-xl">
                                <li><a :href="route('absent_view')">แจ้งการลา</a></li>
                                <li><a :href="route('coming_history_view')">ประวัติการมาโรงเรียน</a></li>
                            </ul>
                        </li>
                        <li class="rounded-xl text-xl" tabindex="0">
                            <a>
                                <span class="material-symbols-rounded">
                                    calendar_month
                                </span>การเรียน
                                <svg class="fill-current" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24">
                                    <path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z" />
                                </svg>
                            </a>
                            <ul class="p-2 bg-white drop-shadow-xl">
                                <li><a :href="route('classroom_list_view')">ห้องเรียน</a></li>
                                <li><a :href="route('timetable_list_view')">ตารางเรียน</a></li>
                            </ul>
                        </li>
                        <li class="rounded-xl text-xl" v-if="isStudent($page.props.user.role)"><a
                                :href="route('doograde_grade_view')"><span class="material-symbols-rounded">
                                    format_list_numbered
                                </span>ดูเกรด</a></li>
                    </template>

                    <!-- NAVBAR FOR TEACHER -->
                    <template v-if="isTeacher($page.props.user.role)">
                        <li class="rounded-xl text-xl" tabindex="0"
                            v-if="!isEmpty($page.props.user.data.advisor.class) || !isEmpty($page.props.user.data.advisor.room)">
                            <a>
                                <span class="material-symbols-rounded">
                                    schedule
                                </span>เวลาเรียน
                                <svg class="fill-current" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24">
                                    <path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z" />
                                </svg>
                            </a>
                            <ul class="p-2 bg-white drop-shadow-xl">
                                <li><a :href="route('absent_view')">แจ้งการลา</a></li>
                                <li><a :href="route('coming_history_view')">ประวัติการมาโรงเรียน</a></li>
                            </ul>
                        </li>
                        <li class="rounded-xl text-xl" tabindex="0">
                            <a>
                                <span class="material-symbols-rounded">
                                    calendar_month
                                </span>การเรียน
                                <svg class="fill-current" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24">
                                    <path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z" />
                                </svg>
                            </a>
                            <ul class="p-2 bg-white drop-shadow-xl">
                                <li><a :href="route('classroom_list_view')">ห้องเรียน</a></li>
                                <li><a :href="route('timetable_list_view')">ตารางเรียน</a></li>
                            </ul>
                        </li>
                        <li class="rounded-xl text-xl" v-if="isStudent($page.props.user.role)"><a
                                :href="route('doograde_grade_view')"><span class="material-symbols-rounded">
                                    format_list_numbered
                                </span>ดูเกรด</a></li>
                    </template>

                    <!-- NAVBAR FOR ADMIN -->
                    <template v-if="isAdmin($page.props.user.role)">
                        <li class="rounded-xl text-xl" tabindex="0">
                            <a>
                                <span class="material-symbols-rounded">
                                    schedule
                                </span>ข้อมูลเวลาเรียน
                                <svg class="fill-current" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24">
                                    <path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z" />
                                </svg>
                            </a>
                            <ul class="p-2 bg-white drop-shadow-xl">
                                <li><a :href="route('absent_view')">ประวัติการลา</a></li>
                                <li><a :href="route('coming_history_view')">ประวัติการมาโรงเรียน</a></li>
                            </ul>
                        </li>
                        <li class="rounded-xl text-xl" tabindex="0">
                            <a>
                                <span class="material-symbols-rounded">
                                    calendar_month
                                </span>ข้อมูลการเรียน
                                <svg class="fill-current" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24">
                                    <path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z" />
                                </svg>
                            </a>
                            <ul class="p-2 bg-white drop-shadow-xl">
                                <li><a :href="route('classroom_list_view')">จัดการห้องเรียน</a></li>
                                <li><a :href="route('subject_list_view')">จัดการรายวิชา</a></li>
                                <li><a :href="route('timetable_list_view')">จัดการตารางเรียน</a></li>
                            </ul>
                        </li>
                        <li class="rounded-xl text-xl" tabindex="0">
                            <a>
                                <span class="material-symbols-rounded">
                                    group
                                </span>จัดการบุคคล
                                <svg class="fill-current" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24">
                                    <path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z" />
                                </svg>
                            </a>
                            <ul class="p-2 bg-white drop-shadow-xl">
                                <li><a :href="route('users_student_list_view')">จัดการรายชื่อนักเรียน</a></li>
                                <li><a :href="route('users_teacher_list_view')">จัดการรายชื่ออาจารย์</a></li>
                                <li><a :href="route('users_officer_list_view')">จัดการรายชื่อเจ้าหน้าที่</a></li>
                            </ul>
                        </li>

                        <li class="rounded-xl text-xl"><a :href="route('missing_items_list_view')"><span
                                    class="material-symbols-rounded">
                                    device_unknown
                                </span>จัดการของหาย</a></li>
                    </template>
                    <li class="rounded-xl text-xl" v-if="!isAdmin($page.props.user.role)"><a
                            :href="route('missing_items_list_view')"><span class="material-symbols-rounded">
                                device_unknown
                            </span>แจ้งของหาย</a></li>
                </ul>
                <ul class="menu p-0 rounded-xl">
                    <li class="rounded-xl text-xl"><a :href="route('index')"><span class="material-symbols-rounded">
                                settings
                            </span>ตั้งค่า</a></li>
                </ul>
            </div>
        </div>
    </nav>
</template>
<style>
ul.menu>li>ul {
    z-index: 2000;
}
</style>
