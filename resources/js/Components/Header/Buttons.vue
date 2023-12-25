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

        <div class="btn-group" v-if="$page.props.auth.openai === true && props.fields">
            <a v-if="props.permissions.import && props.allowed.includes('import')" class="p-2 w-20 text-xs btn   btn-outline btn-secondary text-secondary-content rounded-box shadow"  as="button"  @click="toggle_modal" @keydown.escape="toggle_modal">
                AI Assist
            </a>
            <dialog id="my_modal_1" class="modal" :class="{ 'modal-open': showModal }"  @submit.prevent>
                <div class="modal-box m-5 w-screen w-full">
                    <div class="modal-action">
                        <div class="dialog grid grid-flow-col auto-cols-max">
                            <button class="btn btn-primary btn-sm" @click="ai_field_assist" :disabled="isProcessingAI">Process</button>
                            <button class="btn btn-error btn-sm" @click="toggle_modal">Close</button>
                        </div>
                    </div>
                    <h3 class="font-bold text-lg">Select Fields</h3>
                    <p class="py-4 w-full">
                        <table class="w-full">
                            <tr v-for="(option, index) in props.fields" :key="index">
                                <td>
                                    <div class="btn-group">
                                        <input
                                            :id="`checkbox_${option.name}`"
                                            type="checkbox"
                                            v-model="ai_fields[option.name]"
                                            ref="checkboxRef"
                                        />
                                    </div>
                                </td>
                                <td>
                                    <label :for="`checkbox_${index}`">{{ option.name }}</label>

                                </td>
                                <td>
                                    <input
                                        type="text"
                                        :value="props.field_data['1__' + option.name]"
                                        class="w-full px-3 py-2 border rounded-md"
                                        readonly
                                    />
                                </td>
                            </tr>
                        </table>
                    </p>
                    <div class="modal-action">
                        <div class="dialog grid grid-flow-col auto-cols-max">
                            <button class="btn btn-primary btn-sm" @click="ai_field_assist" :disabled="isProcessingAI">Process</button>
                            <button class="btn btn-error btn-sm" @click="toggle_modal">Close</button>
                        </div>
                    </div>
                </div>
            </dialog>

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
import {watch, onMounted, ref, reactive, nextTick} from 'vue';
import Alert from "@/Components/Alert";




const props = defineProps({
    permissions: [Object, null],
    module: [Object, null],
    allowed: [Array, null],
    record: [Object, null],
    fields: [Object, null],
    field_data: [Object, null]
});

const emit = defineEmits(['newFieldValues']);


const ai_fields = ref({});


const types = ['xlsx', 'xls', 'csv', 'tsv', 'ods', 'html'];
const download_menu = ref('');
const isProcessingAI = ref(false);

const convert_module_id = ref('');
const delete_id = ref('');

const alert_data = reactive({
    success_alert: ref(null),
    error_alert: ref(null),
    alert_text: ref('')
});

const showModal = ref(false);

const toggle_modal = function() {

    ai_fields.value = {};

    nextTick(() => {
        props.fields.forEach((option) => {
            const optionName = option.name;
            const fieldDataKey = `${props.module.id}__${optionName}`;
            const hasNonEmptyData =
                props.field_data &&
                typeof props.field_data[fieldDataKey] === 'string' &&
                props.field_data[fieldDataKey].trim().length > 0;
            ai_fields.value[optionName] = false;
        });



    });
    showModal.value = !showModal.value;



}

const ai_field_assist = function () {
    isProcessingAI.value = true;
    axios.post('/data/ai_assist/fields/' + usePage().props.value.module.id, {'ai_fields': ai_fields.value, 'field_data': props.field_data},
        { responseType: 'json' })
        .then(response => {
            emit('updateInputValues', response.data);
            showModal.value = !showModal.value;
            isProcessingAI.value = false;
        })
        .catch(error => {
            console.error('Error:', error);
        });
}



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

function isObject(value) {
    return Object.prototype.toString.call(value) === '[object Object]';
}
</script>
