<template>
    <Head :title="$t('page.data')" />
    <BreezeAuthenticatedLayout>

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $t('page.data') }}
                <HelpTooltip slug="admin_data" position="bottom-right"/>
            </h2>

        </template>
        <BreadCrumbs :levels="$page.props.breadcrumbs" />
        <div class="w-full mt-10 bg-base-200">
            <div class="max-w-full sm:px-3 lg:px-4 p-10">
                <a class="font-medium ml-2 mt-10 hover:text-accent text-base-content font-bold grid-row-1" href="/module/ice_users" method="get" as="button">
                    {{ $t('page.users') }}
                </a>

                <!-- Reset CRM button as a separate element -->
                <a class="ml-5 mt-5 btn btn-error text-white btn-xs" @click="reset_crm">
                    {{ $t('page.resetcrm') }}
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
import HelpTooltip from '@/Components/HelpTooltip.vue';

const reset_crm = async function (){
    let ok = await confirm($t('page.areyousurereset'));
    if(ok)
    {
        axios.post('/data/resetcrm', null,
            {responseType: 'arraybuffer'})
            .then(response => {
                alert_data.success_alert=1;
                alert_data.alert_text=$t('page.crmhasbeenreset');
                setTimeout(() => {
                    alert_data.success_alert=null;
                    alert_data.alert_text='';
                }, 5000);
            }).catch(function (error) {
            alert_data.error_alert=1;
            alert_data.alert_text=$t('page.therewasanerror');
            setTimeout(() => {
                alert_data.error_alert=null;
                alert_data.alert_text='';
            }, 5000);
        });
    }
}
</script>
