<template>
    <Head title="Data" />
    <BreezeAuthenticatedLayout>

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Data
            </h2>

        </template>
        <BreadCrumbs :levels="$page.props.breadcrumbs" />
        <div class="w-full mt-10 bg-base-200">
            <div class="max-w-full sm:px-3 lg:px-4 p-10">
                <a class="font-medium ml-2  mt-10 hover:text-accent text-base-content font-bold grid-row-1" href="/module/ice_users" method="get" as="button">
                    <a class="ml-5 mt-5 btn btn-error text-white btn-xs" @click="reset_crm">Reset CRM</a>
                </a>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
<script setup>
import axios from "axios";
import { ref, reactive } from "vue";
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import BreadCrumbs from "@/Components/BreadCrumbs";

const reset_crm = async function (){
    let ok = await confirm('Are you sure you want to delete all non-core CRM records?');
    if(ok)
    {
        axios.post('/data/resetcrm', null,
            {responseType: 'arraybuffer'})
            .then(response => {
                alert_data.success_alert=1;
                alert_data.alert_text='CRM has been reset';
                setTimeout(() => {
                    alert_data.success_alert=null;
                    alert_data.alert_text='';
                }, 5000);
            }).catch(function (error) {
            alert_data.error_alert=1;
            alert_data.alert_text='There was an error';
            setTimeout(() => {
                alert_data.error_alert=null;
                alert_data.alert_text='';
            }, 5000);
        });
    }
}
</script>
