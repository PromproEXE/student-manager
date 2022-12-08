<script setup>
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
const logout = () => {
    Inertia.post(route('logout'));
};
</script>

<script>
</script>

<template>
    <nav class="p-5">
        <div class="bg-white text-neutral rounded-xl p-5 h-full">
            <div class="mb-5">
                <a class="btn btn-ghost hover:bg-transparent" :href="route('index')">
                    <img src="/img/logo/student-manager-logo.png" style="width: 200px;" alt="logo">
                </a>
            </div>
            <ul class="menu p-0 rounded-xl">
                <li class="rounded-xl text-xl"><a :href="route('index')"><span class="material-symbols-rounded">
                            home
                        </span>หน้าหลัก</a></li>
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
                        <li><a :href="route('homework_list_view')">ห้องเรียน</a></li>
                        <li><a :href="route('timetable_list_view')">ตารางเรียน</a></li>
                    </ul>
                </li>
                <li class="rounded-xl text-xl"><a :href="route('doograde_grade_view')"><span
                            class="material-symbols-rounded">
                            format_list_numbered
                        </span>ดูเกรด</a></li>
                <li class="rounded-xl text-xl"><a :href="route('missing_items_list_view')"><span
                            class="material-symbols-rounded">
                            device_unknown
                        </span>แจ้งของหาย</a></li>
            </ul>
        </div>
    </nav>
</template>
<style>
ul.menu>li>ul {
    z-index: 2000;
}
</style>
