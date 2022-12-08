<script setup>
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { Head, Link } from '@inertiajs/inertia-vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import NavBar from '@/Layouts/NavBar.vue'

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);

const logout = () => {
    Inertia.post(route('logout'));
};
</script>

<template>
    <div>

        <Head :title="title" />

        <Banner />

        <div class="min-h-screen bg-gray-100">
            <div class="grid grid-cols-5">
                <NavBar class="h-screen"></NavBar>

                <!-- Page Content -->
                <main class="p-5 pl-0 col-span-4 h-screen">
                    <div class="rounded-xl bg-white mb-5 p-3 flex justify-between items-center">
                        <div class="flex items-center">
                            <button class="btn btn-ghost hover:bg-transparent">
                                <span class="material-symbols-rounded">
                                    menu
                                </span>
                            </button>
                            <p class="text-4xl text-neutral font-bold">
                                <slot name="header"></slot>
                            </p>
                        </div>

                        <div class="dropdown dropdown-end">
                            <label tabindex="0" class="btn btn-ghost m-1">
                                <img src="/img/account.png" style="width: 32px;" alt="profile">
                            </label>
                            <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-white rounded-box w-52">
                                <li><a>ตั้งค่า</a></li>
                                <li>
                                    <form @submit.prevent="logout">
                                        <button type="submit">ออกจากระบบ</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <slot />
                </main>
            </div>


            <!-- Page Heading -->
            <!-- <header v-if="$slots.header" class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header> -->
        </div>
    </div>
</template>
