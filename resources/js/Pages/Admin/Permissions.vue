
<template>
    <Head title="Settings" />
    <BreezeAuthenticatedLayout>

        <template #header>
            <h2 class="font-semibold text-xl text-base-content leading-tight">
                Permissions
            </h2>

        </template>
        <BreadCrumbs :levels="$page.props.breadcrumbs" />


        <div class="w-full bg-base-100">
            <div class="bg-base-100 mt-10 text-base-content max-w-full sm:px-3 lg:px-4">
                <Alert :message="alert.alert_text" :is_successful="alert.success_alert" :is_error="alert.error_alert" />


                <div class="bg-base-100 text-base-content grid grid-auto grid-flow-col">

                        <div>

                            <select v-model="role_id" name="module_id" class="input select-secondary w-full md:w-1/2 lg:w-1/4">
                                <option value="">Select a Role</option>
                                <option :value="item.id" v-for="item in $page.props.roles">{{item.name}}</option>
                            </select>


                            <select v-model="module_id" name="module_id" class="input select-secondary w-full md:w-1/2 lg:w-1/4">
                                <option value="">Select a Module</option>
                                <option :value="item.id" v-for="item in $page.props.modules">{{item.label}}</option>
                            </select>
                        </div>


                    </div>

                <table class="bg-base-200 text-base-content table table-zebra table-compact lg:table-normal w-full border-secondary border-solid">
                    <thead>
                    <th>Role</th>
                    <th>Module</th>
                    <th>Can Read</th>
                    <th>Can Write</th>
                    <th>Can Import</th>
                    <th>Can Export</th>
                    </thead>
                    <tr v-for="permission in permissions">
                        <td>
                            <a :href="`/modules/roles/${permission.role_id}`">{{permission.roles.name}}</a>
                        </td>
                        <td>
                            <a :href="`/modules/${permission.module_name}`">{{permission.modules.label}}</a>
                        </td>
                        <td>
                            <input type="checkbox" @change="set_permission(permission.id, permission.can_read, 'read')" :checked="permission.can_read" class="checkbox checkbox-secondary">
                        </td>
                        <td>
                            <input type="checkbox" @change="set_permission(permission.id, permission.can_write, 'write')" :checked="permission.can_write" class="checkbox checkbox-secondary">
                        </td>
                        <td>
                            <input type="checkbox" @change="set_permission(permission.id, permission.can_import, 'import')" :checked="permission.can_write" class="checkbox checkbox-secondary">
                        </td>
                        <td>
                            <input type="checkbox" @change="set_permission(permission.id, permission.can_export, 'export')" :checked="permission.can_write" class="checkbox checkbox-secondary">
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
