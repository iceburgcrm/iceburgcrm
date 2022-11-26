<template>
    <Head title="Module" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 v-if="record" class="font-semibold text-xl text-gray-800 leading-tight">
                Edit {{$page.props.subpanel.label}} Subpanel
            </h2>
            <h2 v-else class="font-semibold text-xl text-gray-800 leading-tight">
                Add {{$page.props.subpanel.label}} Subpanel
            </h2>
        </template>
        <BreadCrumbs :levels="$page.props.breadcrumbs" />
        <Alert :message="alert.alert_text" :is_successful="alert.success_alert" :is_error="alert.error_alert" />

        <div class="max-w-full sm:px-3 lg:px-4">

                <div class="bg-base-200 mt-10 overflow-hidden shadow-sm">
                    <div class="p-10">
        <div class="bg-base-100" v-for="relationship_module in $page.props.subpanel.relationship.relationshipmodule">
            <div class="p-5 border-solid border border-neutral">
                <h1 class="text-xl font-bold">{{relationship_module.module.label}}</h1>
                <div class="grid grid-flow-col grid-col-auto">
                    <div class="col-span-6 pt-10" v-if="!module_records[relationship_module.module.id]">
                        <h4>Search</h4>
                        <div class="form-control">
                            <TypeAhead ref="typeaheadRef[relationship_module.module.id]" @newFieldValue="fieldValue" :fields="fields" :module="relationship_module.module" />
                        </div>
                    </div>
                    <div class="grid-col-6" v-if="module_records[relationship_module.module.id]">
                        <RecordPreview :record_id="module_records[relationship_module.module.id]" :module_id="relationship_module.module.id" />
                        [<a href="" @click.prevent="unselect(relationship_module.module.id)">Clear</a>]
                    </div>
                </div>
                <div v-if="!module_records[relationship_module.module.id]" class="divider">OR</div>
                <div v-if="!module_records[relationship_module.module.id]" class="grid grid-col-12 mt-8">
                    <h4>Add</h4>

                    <div class="grid-col-6" v-for="(field, key) in  fields[relationship_module.module.id]">

                        <Edit class="pt-5"
                              @newFieldValue="fieldValue"
                              :field="field"
                              :type="search_type"
                        />

                    </div>
                </div>
            </div>


        </div>
        <div class="max-w-full sm:px-3 lg:px-4 p-10 bg-base-100  border-solid border border-neutral overflow-hidden shadow-sm ">
            <!-- @submit.prevent="getFormValues" -->
            <form name="search" method="GET">
                <div class="bg-base-100 text-base-content overflow-hidden shadow-sm sm:rounded-lg">
                    <div>
                        <div class="bg-base-100 grid grid-cols-1 row-gap-5 mt-6 lg:grid-cols-3 md:grid-cols-2 lg:row-gap-6">
                            <div class="col-span-1 col-gap-2 lg:col-span-1" v-for="(field, key) in []">
                                <div class="flex items-center -mx-3">
                                    <Edit @newFieldValue="fieldValue"
                                          :field="field"
                                          :type="search_type"
                                          :default_value="record ? record[field.name] : ''"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <input @click.prevent="save_record()" type="submit" class="btn btn-secondary" name="save" value="Save" /> </div>
                    </div>
                </div>
            </form>
            </div>
        </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head, usePage } from '@inertiajs/inertia-vue3';
import axios from "axios";
import BreadCrumbs from "@/Components/BreadCrumbs";
import Edit from '@/Components/Fields/Edit';
import TypeAhead from '@/Components/Subpanel/TypeAhead';
import RecordPreview from '@/Components/Subpanel/RecordPreview';
import { ref, reactive, watch, onMounted} from 'vue';

import Alert from "@/Components/Alert";

const alert = reactive({
    success_alert : ref(0),
    error_alert : ref(0),
    alert_text : ref('')
});

const module_id = ref(0);
const fields = ref(usePage().props.value.fields);
const relationship_id = ref('');

const record = ref(usePage().props.value.record);

const module_fields = ref([]);
const from_module = ref(usePage().props.value.from_module);


const default_values = ref('');
let fieldValueData = {};
const search_fields = ref(reactive(usePage().props.value.fields));
let params = [];
let search_field_types = [];
let search_field_values = [];
const search_type = ref('module');
const module_records = ref({});
const typeaheadRef = ref([]);

let selected_records = usePage().props.value.selected_records;

onMounted(() => {
    Object.keys(fields.value).forEach((key, index) => {
        module_fields[key]=fields.value[key];
    });

    if(selected_records)
    {
        Object.keys(selected_records).forEach(key => {
            module_records.value[key]=selected_records[key];
        });
    }

    watch([relationship_id, module_id], (val) => {
        axios.get('/data/search_fields/' + relationship_id.value + '/search_type/module').then(response => {
            search_fields.value = response.data;
        }, { deep: true });
    });
});
let forward_to='';

const subpanel_name = usePage().props.value.subpanel.name;

const save_record = function () {
    fieldValueData['save_type'] = 'subpanel';
    fieldValueData['subpanel_id'] = usePage().props.value.subpanel.id;
    fieldValueData['module_records'] = module_records.value;

    if(record.value !== null && record.value !== undefined)
    {
        fieldValueData['record_id']=record.value.id;
    }
    axios.post('/data/subpanel/save', fieldValueData).then(response => {
        if(response.data)
        {
            forward_to='/module/' + usePage().props.value.from_module.name + '/view/' + response.data;
            if(usePage().props.value.from_id > 0)
            {
                forward_to='/module/' + usePage().props.value.from_module.name + '/view/' + usePage().props.value.from_id + "#" + subpanel_name;
            }
            window.location = forward_to;
        }
    })
        .catch(error => {
            alert.error_alert=1;
            alert.alert_text=error.response.data.errors;
            window.scrollTo(0, top);
            setTimeout(() => {
                alert.error_alert=null;
                alert.alert_text='';
            }, 50000);
        });
}

const getFields = function (module_id) {
        axios.get('/data/search_fields/' + module_id + '/search_type/module').then(response => {
            module_fields[module_id]=response.data;
        });
}

const fieldValue = ((evt) => {

    if(evt.field_name.startsWith("module_id_"))
    {
        module_records.value[evt.field_name.replace('module_id_', '')]=evt.value;
    }
    fieldValueData[evt.field_name] = evt.value;
});

const unselect = function (module_id){
    delete module_records.value[module_id];
 }
</script>



