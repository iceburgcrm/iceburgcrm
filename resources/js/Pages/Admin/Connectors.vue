
<template>
    <Head title="Settings" />
    <BreezeAuthenticatedLayout>

        <template #header>
            <h2 class="font-semibold text-xl text-base-content leading-tight">
                Connectors
            </h2>

        </template>
        <BreadCrumbs :levels="$page.props.breadcrumbs" />


        <div class="w-full bg-base-100">
            <div class="bg-base-100 mt-10 text-base-content max-w-full sm:px-3 lg:px-4">
                <table class="bg-base-200 text-base-content table table-zebra table-compact lg:table-normal w-full border-secondary border-solid">
                    <thead>
                    <th>Name</th>
                    <th>Endpoints</th>
                    <th>Base URL</th>
                    <th>Actions</th>
                    </thead>
                    <tr v-for="connector in connectors">
                        <td>
                            <span class="text-lg">{{connector.name}}</span>
                        </td>
                        <td>
                            <div class="grid grid-flow-row" v-for="endpoint in connector.endpoints">
                                <div>
                                    {{endpoint.endpoint}}
                                </div>

                            </div>
                        </td>
                        <td>
                            {{connector.base_url}}
                        </td>
                        <td>
                            <a class="btn btn-sm btn-primary" :href="`/admin/connector/${connector.id}`">Edit</a>
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


const connectors = ref(usePage().props.value.connectors);
console.log(connectors.value);

const set_connector_status= function(id, event) {

    event.preventDefault();
}
/*
const set_connector_status= function(connector_id, connector_status)
{
    const formData = new FormData();
    formData.append('status', connector_status);
    formData.append('id', connector_id);
    axios.post('/data/connector/set_connector', formData, {headers}).then((res) => {
       connectors =
           axios.get('/data/connectors',
               {responseType: 'arraybuffer'})
               .then(response => {
                   connectors.value=response.data.connectors;
               }).catch(function (error) {

               }, 5000);
           });

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
*/

</script>
