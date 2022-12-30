<template>
    <Head title="Audit Log" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <div class="grid grid-flow-col">
                <div class="col-span-8">
                    <Title :module="$page.props.module" page_description="Audit Log" />
                </div>
                <div class="col-span-4">
                    <HeadButtons
                        class="col-span-2"
                        :module="$page.props.module"
                        :permissions="$page.props.permissions"
                        :allowed="['import', 'add', 'export', 'audit']"
                        :record="$page.props.record"
                    />
                </div>
            </div>
        </template>


        <BreadCrumbs :levels="$page.props.breadcrumbs" />

        <div class="w-full bg-base-100">
            <div class="bg-base-100 mt-10 text-base-content max-w-full sm:px-3 lg:px-4">


                <div class="bg-base-100 text-base-content grid grid-auto grid-flow-col">

                    <div>
                        <select v-model="type"  class="input select-secondary w-full md:w-1/2 lg:w-1/4">
                            <option value="">Select a Type</option>
                            <option value="read">Read</option>
                            <option value="write">Write</option>
                            <option value="import">Import</option>
                            <option value="export">Export</option>
                        </select>

                        <select v-model="user"  class="input select-secondary w-full md:w-1/2 lg:w-1/4">
                            <option value="">Select a User</option>
                            <option :value="item.id" v-for="item in $page.props.users">{{item.name}}</option>
                        </select>
                    </div>


                </div>

                <table class="bg-base-200 text-base-content table table-zebra w-full border-secondary">
                    <thead>
                    <th>ID</th>
                    <th>Type</th>
                    <th>User</th>
                    <th>Date</th>
                    </thead>
                    <tbody>
                    <tr v-for="record in display_logs">
                        <td>
                            {{record.id}}
                        </td>
                        <td>
                            {{record.type}}
                        </td>
                        <td>
                          {{record.user.name}}
                        </td>
                        <td>
                            {{moment(record.created_at).format('MMMM Do YYYY, h:mm:ss a')}}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
<script setup>
import moment from 'moment';
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import {Head, usePage} from '@inertiajs/inertia-vue3';
import BreadCrumbs from "@/Components/BreadCrumbs";
import HeadButtons from "@/Components/Header/Buttons";
import Title from "@/Components/Header/Title";
import {computed, ref} from "vue";

const log_list = ref(usePage().props.value.logs);
const type = ref('');
const user = ref('');

const display_logs = computed(() => log_list.value.filter(function(item){
        if(
            (type.value !== '' && type.value !== item.type)
            ||
            (user.value !== '' && user.value !== item.user.id)
        )
        {
            return false;
        }
        return true;
    })
);

</script>
