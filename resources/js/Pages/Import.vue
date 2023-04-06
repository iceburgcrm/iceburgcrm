
<template>
    <Head title="Settings" />
    <BreezeAuthenticatedLayout>

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Import
            </h2>

        </template>
        <BreadCrumbs :levels="$page.props.breadcrumbs" />
        <div class="w-full mt-10 bg-base-200">
            <div class="max-w-full sm:px-3 lg:px-4 p-10">
                <Alert
                    :message="data.alert_text"
                    :is_successful="data.success_alert"
                    :is_error="data.error_alert"
                />

                <div class="grid row-auto">
                    <div class="p-5  grid grid-row-2 bg-base-100 text-base-content">

                            <div class="grid h-20 w-full card bg-base-300 rounded-box place-items-center">
                                <label for="search" class="block text-sm font-medium leading-5 select-secondary input-secondary text-secondary-content">
                                    Select a File
                                </label>
                                <input class="file-input file-input-bordered file-input-secondary w-full max-w-xs" type="file" id="file-input" @change="onFileChanged($event)" name="file" />
                            </div>
                   </div>

                    <div class="p-5  grid grid-row-2 bg-base-100">
                        <label for="search" class="block text-sm font-medium leading-5 text-gray-700">
                            Select Module
                        </label>
                        <select required v-model="data.module_id" name="module_id" class="input select-secondary rounded">
                            <option :value="item.id" v-for="item in $page.props.modules">{{item.label}}</option>
                        </select>
                    </div>
                    <div class="p-5  grid grid-row-2 bg-base-100">
                        <label for="search" class="block text-sm font-medium leading-5 text-base-content">
                            Use First Row as Column Names
                        </label>
                        <input v-model="data.first_row_header" type="checkbox" class="checkbox checkbox-secondary rounded">
                    </div>

                    <Preview v-if="preview_data.show" :fields="preview_data.fields" :row="preview_data.row" />

                    <div class="p-5 bg-base-100">
                        <input v-if="preview_data.show === false && data.module_id !== null" @click.prevent="upload()" type="submit" class="btn btn-secondary" name="import" value="Preview" />
                        <input v-if="preview_data.show" @click.prevent="process()" type="submit" class="btn btn-secondary" name="import" value="Import" />
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
import Preview from "@/Components/Import/Preview";
import Alert from "@/Components/Alert";

const file = ref();
const form = ref();

const data = reactive({
    module_name: usePage().props.value.data.module_name,
    module_id: usePage().props.value.data.module_id,
    success_alert: ref(''),
    error_alert: ref(''),
    alert_text: ref(''),
    first_row_header: usePage().props.value.data.first_row_header,
    preview: ref(true)
});

const preview_data = reactive({
    fields: {},
    row: {},
    show: ref(false)
})


const onFileChanged = function($event) {
    preview_data.show=false;
    const target = $event.target;
    if (target && target.files) {
        file.value = target.files[0];
    }
}

const process = function(){
    data.preview=false;
    upload();
}

async function upload() {

    if (file.value) {
            data.alert_text= '';
            data.success_alert=false;
            data.error_alert=false;
            const formData = new FormData();

            formData.append('input_file', file.value);
            formData.append('module_id', data.module_id);
            formData.append('module_name', data.module_name);
            formData.append('preview', data.preview);
            formData.append('first_row_header', data.first_row_header);
            const headers = { 'Content-Type': 'multipart/form-data'};
            axios.post('/data/import', formData, {headers}).then((res) =>
            {

                if(data.preview === false)
                {
                    data.alert_text="Records have been imported";
                    window.scrollTo(0, 'top');
                    data.success_alert=true;
                    data.preview=false;
                    setTimeout(() => {
                        window.location='/import?success=1&records=';
                    }, 2000);

                }
                else {

                    preview_data.fields=res.data.fields;
                    preview_data.row=res.data.row;
                    preview_data.show=true;
                    data.preview=true;
                }

            }).catch(function (error) {

                data.alert_text="There was an error with your import.  Please try again.";
                data.error_alert=true;
                    setTimeout(() => {
                        data.error_alert=null;
                        data.alert_text='';
                    }, 5000)});

    }
};
</script>
