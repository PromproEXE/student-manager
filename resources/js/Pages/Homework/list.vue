<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
</script>
<script>
export default {
    data() {
        return {
            subjects: [{
                id: 'asdasdasd',
                name: 'ภาษาอังกฤษ',
                teacher: 'สุธาทิพย์ ผลไสว'
            },
            {
                id: 'sdgdgethgseth',
                name: 'ภาษาไทย',
                teacher: 'สุธาทิพย์ ผลไสว'
            },
            {
                id: 'qwiejiqwjiejiqwoer',
                name: 'คณิตศาสตร์',
                teacher: 'สุธาทิพย์ ผลไสว'
            },
            {
                id: 'mnvkmkdfvnfjdnjg',
                name: 'คอมพิวเตอร์',
                teacher: 'สุธาทิพย์ ผลไสว'
            },
            {
                id: 'qiwjeioqjwiej',
                name: 'วิทยาศาสตร์',
                teacher: 'สุธาทิพย์ ผลไสว'
            }]
        }
    }
}
</script>
<template>
    <AppLayout>
        <template #header>ห้องเรียน</template>
        <div class="bg-white rounded-xl grid grid-cols-3 p-5 gap-5">
            <div class="rounded-lg drop-shadow-lg bg-white" v-for="subject in subjects" :key="subject.id">
                <div
                    class="px-5 py-4 flex items-center justify-between border-b-4 rounded-t-lg bg-sky-200 border-sky-400">
                    <div>
                        <a :href="route('homework_classroom_view')" class="text-2xl font-bold underline">{{ subject.name
                        }}</a>
                        <p class="text-md">{{ subject.teacher }}</p>
                    </div>
                    <img src="/img/account.png" style="width: 64px" alt="">
                </div>
                <div class="px-1 py-2 text-right">
                    <button class="btn btn-sm btn-ghost">เข้าห้องเรียน<span class="ml-2 material-symbols-rounded">
                            google_plus_reshare
                        </span></button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
