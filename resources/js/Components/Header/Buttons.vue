<template>
    <div class="grid col-auto gap-4 align-bottom place-content-end">

        <div class="col-span-6">
            <Alert :message="alert_data.alert_text" :is_successful="alert_data.success_alert" :is_error="alert_data.error_alert" />
        </div>



        <div class="btn-group">
        <a v-if="props.permissions.write && props.allowed.includes('add') && props.module.admin != 1" class="p-2 w-20 text-xs btn   btn-primary text-primary-content rounded shadow" :href="`/module/${props.module.name}/add`" method="get" as="button">
            Add
        </a>
        <a v-if="props.permissions.write && props.allowed.includes('edit')" class="p-2 w-20 text-xs btn  btn-warning text-warning-content rounded shadow" :href="`/module/${props.module.name}/edit/${props.record.id}`" method="get" role="button">
            Edit
        </a>
        <a v-if="props.permissions.write && props.allowed.includes('delete')" @click.prevent="deleteRecord(props.record.id,'module')" class="p-2 w-20 text-xs btn   btn-error text-errror-content rounded shadow" href="" method="get" role="button">
            Delete
        </a>
            <a v-if="props.permissions.write && props.allowed.includes('delete_subpanel')" @click.prevent="deleteRecord(props.record.id,'relationship')" class="p-2 w-24 btn  btn-error text-error-content rounded-box shadow" href="" method="get" role="button">
                Delete
            </a>
        </div>
        <div class="btn-group">
            <a v-if="props.permissions.import && props.allowed.includes('import')" class="p-2 w-20 text-xs btn   btn-outline btn-secondary text-secondary-content rounded-box shadow" :href="`/import?from_module_id=${$page.props.module.id}`" method="get" as="button">
                Import
            </a>
            <select v-if="props.permissions.export && props.allowed.includes('export')" class="p-2 w-20 text-xs btn text-left  btn-outline btn-secondary text-secondary-content rounded-box shadow" v-model="download_menu">
                <option value="">Export</option>
                <option value="xlsx">Export Excel(XLSX)</option>
                <option value="xls">Export Excel Compatible (XLS)</option>
                <option value="csv">Export CSV</option>
                <option value="tsv">Export TSV</option>
                <option value="ods">Export ODS</option>
                <option value="html">Export HTML</option>
            </select>
        </div>

        <div class="btn-group">
        <a v-if="props.allowed.includes('convert_to') && props.module.convertedmodules && props.module.convertedmodules.module" class="p-2 w-20 text-xs btn  btn-outline btn-primary text-primary-content rounded-box shadow" :href="`/module/${props.module.convertedmodules.module.name}/add?from_id=${props.record.id}&from_module=${props.module.id}`" method="get" as="button">
            Convert
        </a>

        <a v-if="props.permissions.read && props.allowed.includes('audit_log')" class="p-2 w-20 text-xs btn  btn-outline btn-primary text-primary-content rounded-box
        shadow" :href="`/audit_log/${$page.props.module.id}`" method="get" as="button">
            Audit Log
        </a>
        </div>

        <Alert />
    </div>
</template>
<script setup>

import axios from "axios";
import {usePage} from "@inertiajs/inertia-vue3";
import {watch, onMounted, ref, reactive} from 'vue';
import Alert from "@/Components/Alert";


const props = defineProps({
    permissions: [Object, null],
    module: [Object, null],
    allowed: [Array, null],
    record: [Object, null]
});

const types = ['xlsx', 'xls', 'csv', 'tsv', 'ods', 'html'];
const download_menu = ref('');

const convert_module_id = ref('');
const delete_id = ref('');

const alert_data = reactive({
    success_alert: ref(null),
    error_alert: ref(null),
    alert_text: ref('')
});

const deleteRecord = async function (record_id, type) {
    let ok = await confirm('Are you sure you want to delete this record?');
    if(ok) {
        axios.post('/data/delete/' + props.module.id + '/type/' + type, {record_id},
            {responseType: 'arraybuffer'})
            .then(response => {
                window.location = '/module/' +  props.module.name + '?message="Delete was successful';
            })
            .catch(function (error) {
                alert_data.error_alert.value=1;
                alert_data.alert_text='There was an error';
                setTimeout(() => {
                    alert_data.error_alert.value=null;
                    alert_data.alert_text.value='';
                }, 5000);
            });

    }

};

onMounted(() => {

    watch([download_menu], async (val) => {
        if(val+'' === 'delete'){
            let ok = await confirm('Are you sure you want to delete this?');
            if(ok)
            {
                axios.post('/data/delete/' + usePage().props.value.module.id + '/type/module', selected_records.value,
                    {responseType: 'arraybuffer'})
                    .then(response => {
                        alert_data.success_alert.value=1;
                        alert_data.alert_text.value='Successfully Deleted';
                        setTimeout(() => {
                            alert_data.success_alert.value=null;
                            alert_data.alert_text.value='';
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
        else if(types.indexOf(val+'') >= 0)
        {

                axios.post('/data/download/' + props.module.id + '/' + val, null,
                    {responseType: 'arraybuffer'})
                    .then(response => {
                        var fileURL = window.URL.createObjectURL(new Blob([response.data]));
                        var fileLink = document.createElement('a');
                        fileLink.href = fileURL;
                        fileLink.setAttribute('download', props.module.name + '.' + val);
                        document.body.appendChild(fileLink);
                        fileLink.click();
                        download_menu.value = '';
                    });

        }

    });
    watch([convert_module_id], (val) => {
        window.location.href="/module/" + props.module.name + "/add?convert_from_record=" + props.record['id'] + "&convert_module_id=" + val;
    });
});
</script>
