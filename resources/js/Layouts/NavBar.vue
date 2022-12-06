<script setup>
</script>

<script>
</script>

<template>
    <nav class="p-5 pb-0">
        <div class="navbar bg-white text-neutral rounded-xl">
            <div class="navbar-start">
                <div class="dropdown">
                    <label tabindex="0" class="btn btn-ghost lg:hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h8m-8 6h16" />
                        </svg>
                    </label>
                    <ul tabindex="0"
                        class="menu menu-compact dropdown-content mt-3 p-2 shadow bg-white rounded-box w-52">
                        <li class="rounded-xl"><a>หน้าหลัก</a></li>
                        <li class="rounded-xl" tabindex="0">
                            <a>
                                เวลาเรียน
                                <svg class="fill-current" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24">
                                    <path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z" />
                                </svg>
                            </a>
                            <ul class="p-2 bg-white drop-shadow-xl">
                                <li><a>แจ้งลา</a></li>
                                <li><a>ประวัติการมาโรงเรียน</a></li>
                            </ul>
                        </li>
                        <li class="rounded-xl" tabindex="0">
                            <a>
                                การเรียน
                                <svg class="fill-current" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24">
                                    <path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z" />
                                </svg>
                            </a>
                            <ul class="p-2 bg-white drop-shadow-xl">
                                <li><a>การบ้าน</a></li>
                                <li><a>ตารางเรียน</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <a class="btn btn-ghost">
                    <img src="/img/logo/student-manager-logo.png" style="width: 150px;" alt="logo">
                </a>
            </div>
            <div class="navbar-center hidden lg:flex">
                <ul class="menu menu-horizontal p-0">
                    <li class="rounded-xl"><a>หน้าหลัก</a></li>
                    <li class="rounded-xl" tabindex="0">
                        <a>
                            เวลาเรียน
                            <svg class="fill-current" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24">
                                <path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z" />
                            </svg>
                        </a>
                        <ul class="p-2 bg-white drop-shadow-xl">
                            <li><a>แจ้งลา</a></li>
                            <li><a>ประวัติการมาโรงเรียน</a></li>
                        </ul>
                    </li>
                    <li class="rounded-xl" tabindex="0">
                        <a>
                            การเรียน
                            <svg class="fill-current" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24">
                                <path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z" />
                            </svg>
                        </a>
                        <ul class="p-2 bg-white drop-shadow-xl">
                            <li><a>การบ้าน</a></li>
                            <li><a>ตารางเรียน</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="navbar-end">
                <!-- <a class="btn">Get started</a> -->
            </div>
        </div>
    </nav>
</template>
