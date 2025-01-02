<template>
    <Head :title="$t('page.module')" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <div class="grid grid-flow-col">
                <div class="col-span-8">
                    <Title :module="$page.props.module" :page_description="$page.props.type === 'add' ? 'Add' : 'Edit'" />
                </div>
                <div class="col-span-4">

                    <HeadButtons
                        ref="header_buttons"
                        class="col-span-2"
                        @updateInputValues="updateFieldValues"
                        :field_data="fieldValueData"
                        :fields="$page.props.fields"
                        :module="$page.props.module"
                        :permissions="$page.props.permissions"
                        :allowed="$page.props.type === 'edit' ? ['import', 'add', 'export', 'audit_log', 'delete'] :
 ['import', 'add', 'export', 'audit_log']"
                    />
                </div>
            </div>
        </template>

        <BreadCrumbs :levels="$page.props.breadcrumbs" />
        <Alert :message="alert.alert_text" :is_successful="alert.success_alert" :is_error="alert.error_alert" />

        <div class="max-w-full sm:px-3 lg:px-4">
            <!-- @submit.prevent="getFormValues" -->
            <form name="search" method="GET">
                <div class="bg-base-200 mt-10 overflow-hidden shadow-sm">
                    <div class="p-10 border-b border-gray-200">
                        <div class="bg-base-100 grid grid-cols-1 row-gap-5 p-10 lg:grid-cols-2 md:grid-cols-2 lg:row-gap-6 rounded-lg">
                            <div class="col-span-1 col-gap-5 lg:col-span-1" v-for="(field, key) in search_fields">
                                <div class="flex items-center pt-5 pb-5">

                                    <Edit @newFieldValue="fieldValue"
                                          :key="record[field.name]"
                                          :field="field"
                                          :type="search_type"
                                          :default_value="convert_from_record ? convert_from_record[field.name]  : record ? record[field.name] : ''"
                                    />


                                </div>
                            </div><div class="text-right mt-10">
                            <input @click.prevent="save_record()" type="submit" class="btn btn-secondary" name="save" :value="$t('page.save')" /> </div>
                        </div>
                        </div>

                </div>
            </form>
        </div>
    </BreezeAuthenticatedLayout>
</template>
<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head, usePage } from '@inertiajs/inertia-vue3';
import axios from "axios";
import BreadCrumbs from "@/Components/BreadCrumbs";
import HeadButtons from "@/Components/Header/Buttons";
import Title from "@/Components/Header/Title";
import Alert from "@/Components/Alert";

const alert = reactive({
    success_alert : ref(0),
    error_alert : ref(0),
    alert_text : ref('')
});

import Edit from '@/Components/Fields/Edit'
import { ref, toRef, toRefs, computed, toRaw, watch, onMounted, reactive, onUpdated } from 'vue';

const module_id = ref(usePage().props.value.module.id);
const relationship_id = ref('');
const search_type = ref('module');
const record = ref(usePage().props.value.record || {});
const convert_from_record = ref(usePage().props.value.convert_from_record);

const from_id = ref(usePage().props.value.from_id);
const from_module = ref(usePage().props.value.from_module);

const default_values = ref('');
let fieldValueData = {};
const search_fields = ref(reactive(usePage().props.value.fields));

let params = [];

const header_buttons = ref(null);

onMounted(() => {
        watch([relationship_id, module_id], (val) => {
        axios.get('/data/search_fields/' + relationship_id.value + '/search_type/' + search_type.value).then(response => {
            search_fields.value = response.data;
        }, { deep: true });
    });
});

const save_record = function () {
    fieldValueData['from_id'] = from_id.value ? from_id.value : 0;
    fieldValueData['from_module'] = from_module.value ? from_module.value.id : 0;
    fieldValueData['search_type'] = search_type.value;
    fieldValueData['module_id'] = module_id.value;
    fieldValueData['relationship_id'] = relationship_id.value;

    if(record.value !== null)
    {
        fieldValueData['record_id']=record.value.id;
    }
    axios.post('/data/save', fieldValueData).then(response => {
            if(response.data)
            {
                window.location = '/module/' + usePage().props.value.module.name + '/view/' + response.data;
            }
        })
        .catch(error => {
            alert.error_alert=1;
            alert.alert_text=error.response.data.errors;
            window.scrollTo(0, top);
            setTimeout(() => {
                alert.error_alert=null;
                alert.alert_text='';
            }, 5000);
        });
}

const fieldValue = ((evt) => {
    fieldValueData[evt.field_name] = evt.value;
    header_buttons.value.field_data=fieldValueData
});

const updateFieldValues = ((evt) => {
    evt.forEach((data) => {
        record.value[data.name]=data.value;
    });
    search_fields.value = [];
    search_fields.value = reactive(usePage().props.value.fields);

});

onUpdated(() => {
    console.log('Component updated');
    // You can inspect the updated state or perform actions after the update
});




</script>
