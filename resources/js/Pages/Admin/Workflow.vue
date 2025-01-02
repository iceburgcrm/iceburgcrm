
<template>
    <Head :title="$t('page.workflow')" />
    <BreezeAuthenticatedLayout>

        <template #header>
            <h2 class="font-semibold text-xl text-base-content leading-tight">
                {{ $t('page.workflow') }}
            </h2>

        </template>
        <BreadCrumbs :levels="$page.props.breadcrumbs" />


        <div class="w-full bg-base-100">
            <div class="bg-base-100 mt-10 text-base-content max-w-full sm:px-3 lg:px-4">
                <Alert :message="alert.alert_text" :is_successful="alert.success_alert" :is_error="alert.error_alert" />


                <div class="bg-base-100 text-base-content grid grid-auto grid-flow-col">

                    <div>
                        <ul class="steps steps-vertical">
                            <li class="step step-primary">{{ $t('page.lead') }}</li>
                            <li class="step step-primary">{{ $t('page.contact') }}</li>
                            <li class="step step-primary">{{ $t('page.account') }}</li>
                            <li class="step step-primary">{{ $t('page.opportunity') }}</li>
                            <li class="step">{{ $t('page.contract') }}</li>
                        </ul>
                        </div>
                    </div>

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

const role_id = ref('');
const module_id = ref('');
const default_permissions = ref(usePage().props.value.permissions);

const alert = reactive({
    success_alert : ref(0),
    error_alert : ref(0),
    alert_text : ref('')
});

const permissions = computed(() => default_permissions.value.filter(function(item){
        if(
            (role_id.value !== '' && role_id.value !== item.role_id)
            ||
            (module_id.value !== '' && module_id.value !== item.module_id)
        )
        {
            return false;
        }
        return true;
    })
);


const get_permissions = function(){
    axios.get('/data/permissions').then(response => {
        default_permissions.value = response.data;
    }, { deep: true });
}

const set_permission = function (permission_id, permission_can, type)
{
    default_permissions.value[
        default_permissions.value.map(function(e) { return e.id; }).indexOf(permission_id)
        ]['can_' + type]=0;

    axios.post('/data/permissions/save', {
        id: permission_id,
        current_state: permission_can,
        type: type
    }).then(response => {
        get_permissions();
    }).catch(function (error) {
        alert.error_alert=1;
        alert.alert_text=error.text;
        setTimeout(() => {
            alert.error_alert=null;
            alert.alert_text='';
        }, 5000);
    });

}
</script>
