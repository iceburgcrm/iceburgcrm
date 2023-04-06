
<template>
    <Head :title="`Connector ${connector.name}`" />
    <BreezeAuthenticatedLayout>

        <template #header>
            <h2 class="font-semibold text-xl text-base-content leading-tight">
                <div class="collapse">
                    <input type="checkbox" class="peer" />
                    <div class="collapse-title peer-checked:bg-secondary peer-checked:text-secondary-content">
                        Connector - {{connector.name}}
                    </div>
                    <div class="collapse-content bg-primary text-primary-content peer-checked:bg-secondary peer-checked:text-secondary-content">
                        <p class="grid">
                            <div>
                                <div class="form-control w-full max-w-xs">
                                    <label class="label">
                                        <span class="label-text">API Key</span>
                                    </label>
                                    <input type="text" @change="change_connector" placeholder="Auth Key" v-model="connector.auth_key" />
                                </div>
                            </div>
                            <div>
                                <div class="form-control w-full max-w-xs">
                                    <label class="label">
                                        <span class="label-text">Name</span>
                                    </label><input type="text" @change="change_connector" v-model="connector.name" />
                                </div>
                            </div>
                            <div>
                                <div class="form-control w-full max-w-xs">
                                    <label class="label">
                                        <span class="label-text">Active</span>
                                    </label>{{connector.status}}
                                    <input type="checkbox" @change="change_connector" class="toggle toggle-success" v-model="connector.status" checked />
                                </div>
                            </div>
                            <div>
                                <a @click="delete_connector" class="btn btn-sm btn-error">Delete Connector</a>
                            </div>
                        </p>
                    </div>
                </div>
                <br />

            </h2>

        </template>
        <BreadCrumbs :levels="$page.props.breadcrumbs" />



        <div class="w-full bg-base-100">
            <h4 class="text-lg text-center">Endpoints</h4>
            <div class="bg-base-100 mt-10 text-base-content max-w-full sm:px-3 lg:px-4">
                <table class="bg-base-200 text-base-content table table-zebra table-compact lg:table-normal w-full border-secondary border-solid">
                    <thead>
                    <th>Endpoint</th>
                    <th>Mapping Class</th>
                    <th>Type</th>
                    <th>Params</th>
                    <th>Header</th>
                    <th>Last</th>
                    <th>Status</th>
                    <th>Actions</th>
                    </thead>
                    <tr v-for="endpoint in $page.props.connector.endpoints">
                        <td>
                            <input type="text" size="10" :value="endpoint.endpoint" />
                        </td>
                        <td>
                            <input type="text" size="10"  :value="endpoint.class_name" />
                        </td>
                        <td>
                            <div class="form-control w-full max-w-xs">
                                <select @change="change_endpoint" v-model="endpoint.request_type" class="select select-bordered">
                                    <option value="GET">GET</option>
                                    <option value="POST">POST</option>
                                    <option value="PUT">PUT</option>
                                </select>
                            </div>
                        </td>
                        <td>
                           <textarea @change="change_endpoint" class="textarea " :value="endpoint.params" placeholder=""></textarea>
                        </td>
                        <td>
                            <textarea @change="change_endpoint" class="textarea " :value="endpoint.headers" placeholder=""></textarea>

                        </td>

                        <td>
                            <div class="grid grid-flow-row">
                                <b>Last Status: {{endpoint.last_status}}</b>
                                <b>Last Message: {{endpoint.last_message}}</b>
                                <b>Last Ran: {{endpoint.last_ran}}</b>

                            </div>
                        </td>
                        <td>
                            <input @change="change_endpoint" type="checkbox" class="toggle toggle-success" v-model="endpoint.status" checked />
                        </td>
                        <td class="grid-flow-row">
                            <div>
                                <a @click="run_endpoint(endpoint.id)" class="btn btn-sm btn-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                                    </svg>
                                </a>
                            </div>
                            <div>
                                <a :href="`/admin/schedule/endpoint/${endpoint.id}`" class="btn btn-sm btn-neutral">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </a>
                            </div>
                            <div>
                                <a @click="delete_endpoint(endpoint.id)" class="btn btn-sm btn-error">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
<script setup>
import {ref, computed, reactive} from "vue";
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head, usePage } from '@inertiajs/inertia-vue3';
import axios from "axios";
import BreadCrumbs from "@/Components/BreadCrumbs";
import Alert from "@/Components/Alert";


const connector = ref(usePage().props.value.connector);
const endpoint = ref([]);
const method_types = ref(usePage().props.value.method_types);
const form = ref();

const data = reactive({
    success_alert: ref(''),
    error_alert: ref(''),
    alert_text: ref(''),
});

const headers = { 'Content-Type': 'multipart/form-data'};

const delete_endpoint = function(endpoint_id){
    formData.append('status', connector.status.value);
    axios.post('/data/connector/delete_endpoint/' + endpoint_id, {}, {headers}).then((res) => {
        data.alert_text="Deleted";
        data.success_alert=true;
        setTimeout(() => {
            axios.get('/data/connectors').then((res) => {
               connector.value = res.data;
           });
        }, 2000);
    }).catch(function (error){
        data.alert_text="There was an error saving your auth key";
        data.error_alert=true;
        setTimeout(() => {
            data.error_alert=null;
            data.alert_text='';
        }, 5000);
    });
}
const delete_connector = function(){
    formData.append('status', connector.status.value);
    axios.post('/data/connector/delete_connector/' + connector.id.value, {}, {headers}).then((res) => {
        data.alert_text="Deleted";
        data.success_alert=true;
        setTimeout(() => {
            window.location='/admin/connectors';
        }, 2000);
    }).catch(function (error){
        data.alert_text="There was an error saving your auth key";
        data.error_alert=true;
        setTimeout(() => {
            data.error_alert=null;
            data.alert_text='';
        }, 5000);
    });
}

const run_endpoint = function(endpoint_id)
{

}
const change_connector = function()
{
    const formData = new FormData();
    formData.append('auth_key', connector.auth_key.value);
    formData.append('name', connector.name.value);
    formData.append('status', connector.status.value);
    axios.post('/data/connector/set_connector', formData, {headers}).then((res) => {
        data.alert_text="Saved";
        data.success_alert=true;
        setTimeout(() => {
            data.success_alert=false;
        }, 2000);
    }).catch(function (error){
        data.alert_text="There was an error saving your auth key";
        data.error_alert=true;
        setTimeout(() => {
            data.error_alert=null;
            data.alert_text='';
        }, 5000);
    });

}

const change_endpoint = function()
{
    const formData = new FormData();
    formData.append('auth_key', connector.auth_key.value);
    formData.append('name', connector.name.value);
    formData.append('status', connector.status.value);
    axios.post('/data/connector/set_endpoint', formData, {headers}).then((res) => {
        data.alert_text="Saved";
        data.success_alert=true;
        setTimeout(() => {
            data.success_alert=false;
        }, 2000);
    }).catch(function (error){
        data.alert_text="There was an error saving your auth key";
        data.error_alert=true;
        setTimeout(() => {
            data.error_alert=null;
            data.alert_text='';
        }, 5000);
    });

}

const set_permission = function (permission_id, permission_can, type)
{
    data.alert_text= '';
    data.success_alert=false;
    data.error_alert=false;
    const formData = new FormData();

    formData.append('input_file', file.value);
    formData.append('module_id', data.module_id);
    formData.append('module_name', data.module_name);
    formData.append('preview', data.preview);
    formData.append('first_row_header', data.first_row_header);
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
</script>
