
<template>
    <Head title="Settings" />
    <BreezeAuthenticatedLayout>

        <template #header>
            <h2 class="font-semibold text-xl leading-tight">
                {{ $t('page.settings') }}
            </h2>

        </template>
        <BreadCrumbs :levels="$page.props.breadcrumbs" />
                <div class="w-full mt-10 bg-base-200">
                    <div class="max-w-full sm:px-3 lg:px-4 p-10">
                    <div class="grid row-auto text-base-content">
                    <Alert :message="alert.alert_text" :is_successful="alert.success_alert" :is_error="alert.error_alert" />

                    <div class="p-5  grid grid-row-2 bg-base-100 text-base-content">
                        <label for="search_per_page" class="block text-sm font-medium leading-5">
                            {{ $t('page.title') }}
                        </label>
                        <input class="input input-secondary rounded w-full md:w-1/2 lg:w-1/4" name="title" v-model="data.title" />
                    </div>

                    <div class="p-5  grid grid-row-2 bg-base-100 text-base-content">
                        <label for="search_per_page" class="block text-sm font-medium leading-5">
                            {{ $t('page.description') }}
                        </label>
                        <textarea cols="40" rows="6" class="input textarea input-secondary rounded w-full md:w-1/2 lg:w-1/4" name="description" v-model="data.description"></textarea>
                    </div>

                    <div class="p-5  grid grid-row-2 bg-base-100 text-base-content">
                        <label for="search" class="block text-sm font-medium leading-5">
                            {{ $t('page.theme') }}
                        </label>
                        <select name="theme" v-model="data.theme"  class="select select-secondary rounded w-full md:w-1/2 lg:w-1/4">
                            <option v-for="item in $page.props.themes">{{item.name}}</option>
                        </select>
                    </div>

                    <div class="p-5  grid grid-row-2 bg-base-100 text-base-content">
                        <label for="search_per_page" class="block text-sm font-medium leading-5">
                            {{ $t('page.language') }}
                        </label>
                        <select class="select select-secondary rounded w-full md:w-1/2 lg:w-1/4" name="language" v-model="data.language">
                            <option value="en">English</option>
                            <option value="fr">French</option>
                            <option value="de">German</option>
                            <option value="es">Spanish</option>
                            <option value="hi">Hindu</option>
                            <option value="ja">Japanese</option>
                            <option value="pt">Portuguese</option>
                            <option value="en">Russian</option>
                            <option value="zh">Chinese</option>
                            <option value="ar">Arabic</option>
                        </select>
                    </div>



                    <div class="p-5  grid grid-row-2 bg-base-100 text-base-content">
                        <label for="search_per_page" class="block text-sm font-medium leading-5">
                            {{ $t('page.defaultsearchrecords') }}
                        </label>
                        <select class="select select-secondary rounded w-full md:w-1/2 lg:w-1/4" name="search_per_page" v-model="data.search_per_page">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>


                    <div class="p-5  grid grid-row-2 bg-base-100 text-base-content">
                        <label for="search_per_page" class="block text-sm font-medium leading-5">
                            {{ $t('page.defaultsubpanels') }}
                        </label>
                        <select class="select select-secondary rounded w-full md:w-1/2 lg:w-1/4" name="submodule_search_per_page" v-model="data.submodule_search_per_page">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>



                    <div class="p-5  grid grid-row-2 bg-base-100 text-base-content">
                        <label for="search_per_page" class="block text-sm rounded font-medium leading-5">
                            {{ $t('page.maxexportrecords') }}
                        </label>
                        <input class="input input-secondary rounded w-full md:w-1/2 lg:w-1/4" name="max_export_records" v-model="data.max_export_records" />
                    </div>


                    <div class="p-5  bg-base-100 text-base-content">
                        <input type="button" class="btn btn-primary rounded"  @click="save()" value="Save" />
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
import Alert from "@/Components/Alert";

const data = reactive({
    title : ref(usePage().props.value.auth.system_settings.title),
    description : ref(usePage().props.value.auth.system_settings.description),
    theme : ref(usePage().props.value.auth.system_settings.theme),
    language : ref(usePage().props.value.auth.system_settings.language),
    search_per_page : ref(usePage().props.value.auth.system_settings.search_per_page),
    submodule_search_per_page : ref(usePage().props.value.auth.system_settings.submodule_search_per_page),
    max_export_records : ref(usePage().props.value.auth.system_settings.max_export_records)
});


const alert = reactive({
    success_alert : ref(0),
    error_alert : ref(0),
    alert_text : ref('')
});


const reload = function ()
{
    window.location.reload();
}
const save = function ()
{
    axios.post('/data/settings', data).then(response => {
        let timer;

        if(response.data === 1)
        {
            window.location.reload();
        }
        else {
            alert.error_alert=1;
            clearTimeout(timer)
            timer = setTimeout(() => {
                alert.error_alert=0;
                alert.alert_text="error";
            }, 5000);
        }
    })
    .catch(error => {
        let timer;
        alert.error_alert=1;
        clearTimeout(timer)
        timer = setTimeout(() => {
            alert.error_alert=0;
            alert.alert_text="error";
            }, 5000);
    });
}
</script>
