
<template>
    <Head title="Settings" />
    <BreezeAuthenticatedLayout>

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                All Modules
            </h2>

        </template>
        <BreadCrumbs :levels="$page.props.breadcrumbs" />
        <div class="w-full mt-5 bg-base-100">
            <div class="max-w-full p-6 sm:px-3 lg:px-4">
                <input type="text" placeholder="Filter by Module Name" class="input input-secondary rounded" v-model="title" />
                <div class="grid row-auto gap-5">
                    <div v-if="modules.length === 0" class="card w-full bg-base-200 text-base-content border-primary border">
                        <div class="card-body">
                            None Found
                            </div>
                    </div>
                    <div v-for="module in modules" class="card w-full bg-base-200 text-base-content border-primary border">
                        <div class="card-body">
                            <div class="grid grid-flow-col grid-col-3 space-between ">
                                <div>
                                    <BaseIcon class_style="h-16 w-16 " class="backdrop-opacity-30" :name="module.icon" />
                                    <h2 class="card-title">{{ module.label }}</h2>
                                    <p>{{ module.description }}</p>
                                </div>
                                <div>
                                    <div class="card-actions justify-end">
                                        <a class="p-2 w-16 text-xs btn  btn-outline btn-secondary text-secondary-content rounded-box shadow" :href="`/audit_log/${module.id}`" method="get" as="button">
                                            Audit Log
                                        </a>
                                        <a class="p-2 w-16 text-xs btn  btn-primary text-primary-content rounded-box shadow" :href="`/module/${module.name}`" method="get" as="button">
                                            Search
                                        </a>
                                        <a class="p-2 w-16 text-xs btn  btn-secondary text-secondary-content rounded-box shadow" :href="`/module/${module.name}/add`" method="get" as="button">
                                            Add
                                        </a>
                                        <a class="p-2 w-20 text-xs btn  btn-outline  btn-secondary text-secondary-content rounded-box shadow" :href="`/import?from_module_id=${module.id}`" method="get" as="button">
                                            Import
                                        </a>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>

    </BreezeAuthenticatedLayout>
</template>
<script setup>
import {computed, onMounted, ref, watch} from "vue";
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head, usePage } from '@inertiajs/inertia-vue3';
import axios from "axios";
import BreadCrumbs from "@/Components/BreadCrumbs";
import BaseIcon from "@/Icons/BaseIcon";

const props = defineProps({
   modules: [Object, Array, null]
});
const default_modules = ref(usePage().props.value.modules);
const title = ref('');

const modules = computed(() => default_modules.value.filter(function(item){
    if(title.value.length > 0 && item.label.toLowerCase().indexOf(title.value.toLowerCase()) < 0){
        return false;
    }
    return true;
    })
);


</script>
