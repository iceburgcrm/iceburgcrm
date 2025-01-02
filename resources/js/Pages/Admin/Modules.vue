
<template>
    <Head :title="$t('page.modules')" />
    <BreezeAuthenticatedLayout>

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $t('page.modules') }}
            </h2>

        </template>
        <BreadCrumbs :levels="$page.props.breadcrumbs" />
        <div class="w-full">
            <div class="max-w-full sm:px-3 lg:px-4">

                <div class="grid row-auto">
                    <div class="p-5  grid grid-row-2 bg-white">
                        <label for="search" class="block text-sm font-medium leading-5 text-gray-700">
                            {{ $t('page.selectmodule') }}
                        </label>
                        <select v-model="module_id" name="module_id" class="select select-primary w-full md:w-1/2 lg:w-1/4">
                            <option :value="item.id" v-for="item in $page.props.modules">{{item.label}}</option>
                        </select>
                    </div>


                    <div class="p-5 bg-white">
                        <input @click.prevent="save()" type="submit" class="btn btn-secondary" :value="$t('page.save')" />
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
<script setup>
import { ref, reactive } from "vue";
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head, usePage } from '@inertiajs/inertia-vue3';
import axios from "axios";
import BreadCrumbs from "@/Components/BreadCrumbs";

const module_id = ref(0);
const modules = ref(usePage().props.value.data.modules);

const save = function(){

}
</script>
