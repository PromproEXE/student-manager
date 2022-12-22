<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { isStudent, isTeacher, isAdmin } from '@/role';
import { cloneDeep } from 'lodash'
import { Inertia } from '@inertiajs/inertia';
</script>
<script>
export default {
    data() {
        return {
            items: [],
            searchText: '',
            selectedItem: {},
            rootUrl: route('missing_items_list_view') + '#'
        }
    },
    methods: {
        async getMissingItem() {
            try {
                let res = await axios('/api/missing-items/')
                if (res.status == 200) {
                    this.items = res.data
                }
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างดึงประกาศของหาย')
                console.log(err)
            }
        },
        async UpdateItem() {
            try {
                this.selectedItem.found = true
                this.selectedItem.found_by = Inertia.page.props.user.name
                let res = await axios.put('/api/missing-items/update/' + this.selectedItem.id, this.selectedItem)
                if (res.status == 200) {
                    alert('เลื่อนสถานะเป็นพบเจ้าของแล้ว')
                    this.clearSelectItem()
                    this.getMissingItem()
                }
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างแก้ไขประกาศ')
                console.log(err)
            }
        },
        async deleteItem() {
            try {
                let res = await axios.delete('/api/missing-items/delete/' + this.selectedItem.id)
                if (res.status == 200) {
                    alert('ลบประกาศเรียบร้อยแล้ว')
                    this.clearSelectItem()

                    this.getMissingItem()
                }
            }
            catch (err) {
                alert('เกิดปัญหาระหว่างลบประกาศ')
                console.log(err)
            }
        },
        selectItem(data) {
            this.selectedItem = cloneDeep(data)
        },
        clearSelectItem() {
            window.location.href = this.rootUrl
            setTimeout(() => this.selectedItem = {}, 200)
        },
        filteredMissingItem() {
            let data = this.items

            if (this.searchText != '') {
                data = data.filter((item) => item.title.indexOf(this.searchText) > -1)
            }

            return data
        }
    },
    mounted() {
        this.getMissingItem()
    }
}
</script>
<template>

    <Head>
        <title>{{ !isAdmin($page.props.user.role) ? 'แจ้งของหาย' : 'จัดการของหาย' }}</title>
    </Head>
    <AppLayout>
        <template #header>{{ !isAdmin($page.props.user.role) ? 'แจ้งของหาย' : 'จัดการของหาย' }}</template>
        <div class="grid grid-cols-6 gap-5 mb-5">
            <div class="form-control"
                :class="{ 'col-span-6': !isAdmin($page.props.user.role), 'col-span-5': isAdmin($page.props.user.role) }">
                <label class="input-group">
                    <span class="material-symbols-rounded">
                        search
                    </span>
                    <input type="text" placeholder="ค้นหาของหายได้ที่นี่" class="input input-bordered w-full"
                        v-model="searchText" />
                </label>
            </div>
            <div class="text-right" v-if="isAdmin($page.props.user.role)">
                <a :href="route('missing_items_edit_view') + '?mode=add'" role="button"
                    class="btn btn-warning text-lg"><span class="material-symbols-rounded mr-2">
                        add
                    </span>เพิ่มประกาศ</a>
            </div>
        </div>
        <div class="grid grid-cols-4 gap-5 overflow-auto rounded-xl" style="max-height: calc(100vh - 205px)">
            <div class="rounded-xl bg-white p-5 h-fit" v-for="(item, i) in filteredMissingItem()" :key="'items-' + i">
                <div class="flex justify-between">
                    <p class="text-xl font-bold">{{ item.title }}</p>
                    <div class="dropdown" v-if="isAdmin($page.props.user.role)">
                        <label tabindex="0" class="btn btn-ghost btn-circle">
                            <span class="material-symbols-rounded text-2xl font-bold">
                                menu
                            </span>
                        </label>
                        <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52">
                            <li v-if="!item.found"><a href="#found-modal" @click="selectItem(item)">พบเจ้าของแล้ว</a>
                            </li>
                            <li v-if="!item.found"><a
                                    :href="route('missing_items_edit_view') + '?mode=edit&id=' + item.id">แก้ไข</a></li>
                            <li><a href="#delete-modal" class="text-error" @click="selectItem(item)"><span
                                        class="material-symbols-rounded">
                                        delete
                                    </span>ลบ</a></li>
                        </ul>
                    </div>
                </div>
                <p class="text-gray-500 mb-3">{{ item.details }}</p>
                <div class="carousel w-full mb-3" v-if="item.img.length > 1">
                    <div :id="'img-' + item.id + '-' + i" class="carousel-item w-full" v-for="(img, i) in item.img"
                        :key="'img-' + item.id + '-' + i">
                        <img :src="img" class="w-full" />
                    </div>
                </div>
                <img :src="item.img[0]" class="w-full mb-3" v-if="item.img.length == 1">
                <div class="flex justify-center w-full gap-2 mb-3" v-if="item.img.length > 1">
                    <a :href="'#img-' + item.id + '-' + i" class="btn btn-xs btn-circle btn-secondary"
                        v-for="(img, i) in item.img" :key="'img-' + item.id + '-' + i + '-control'">{{ i + 1 }}</a>
                </div>
                <a href="#found-modal" role="button" v-if="isAdmin($page.props.user.role)" :disabled="item.found"
                    @click="selectItem(item)">
                    <button class="btn btn-sm btn-success w-full text-lg" :disabled="item.found">พบเจ้าของแล้ว</button>
                </a>
            </div>
        </div>

        <!-- FOUND MODAL -->
        <div class="modal" id="found-modal" v-if="Object.keys(selectedItem).length > 0">
            <div class="modal-box">
                <div class="flex justify-end items-center">
                    <a href="#" class="btn btn-circle btn-ghost" role="button" @click="clearSelectItem()">
                        <span class="material-symbols-rounded">
                            close
                        </span>
                    </a>
                </div>
                <div class="text-center">
                    <p class="text-xl">คุณต้องการเลื่อนสถานะเป็นพบเจ้าของแล้วหรือไม่</p>
                    <p class="text-xl font-bold">{{ selectedItem.title }}</p>
                </div>
                <div class="modal-action grid grid-cols-2 gap-5">
                    <button type="button" class="btn btn-gray text-lg" @click="clearSelectItem()">ยกเลิก</button>
                    <button type="button" class="btn btn-success text-lg" @click="UpdateItem()">พบเจ้าของแล้ว</button>
                </div>
            </div>
        </div>

        <!-- DELETE MODAL -->
        <div class="modal" id="delete-modal" v-if="Object.keys(selectedItem).length > 0">
            <div class="modal-box">
                <div class="flex justify-end items-center">
                    <a href="#" class="btn btn-circle btn-ghost" role="button" @click="clearSelectItem()">
                        <span class="material-symbols-rounded">
                            close
                        </span>
                    </a>
                </div>
                <div class="text-center">
                    <p class="text-xl">คุณต้องการลบประกาศนี้หรือไม่</p>
                    <p class="text-xl font-bold">{{ selectedItem.title }}</p>
                </div>
                <div class="modal-action grid grid-cols-2 gap-5">
                    <button type="button" class="btn btn-gray text-lg" @click="clearSelectItem()">ยกเลิก</button>
                    <button type="button" class="btn btn-error text-lg" @click="deleteItem()">ลบประกาศ</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
