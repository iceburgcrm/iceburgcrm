<template>
    <Alert :message="alert_data.alert_text" :is_successful="alert_data.success_alert" :is_error="alert_data.error_alert" />
    <div class="w-full pb-5 sm:px-3 lg:px-4 bg-base-200 text-base-content rounded  shadow-sm pt-5" >
        <div class="card shadow-xl bg-base-100 text-base-content  border-gray-200">
            <div class="card-body">
                <h2 class="card-title"><a :id="label">{{label}}</a></h2>

                <div>
                    <h2 class="text-primary-content"></h2>
                </div>
                <div class="grid grid-flow-col grid-col-2 justify-between">
                    <div>
                        <a class="btn btn-outline btn-primary btn-sm text-sm" :href="`/subpanel/add/${props.id}?from_module=${props.module_id}&amp;from_id=${props.record_id}`" method="get" as="button">
                            {{ $t('page.add') }}
                        </a>
                    </div>
                    <div>
                        <div class="container flex mx-auto">
                            <div class="flex border-2 rounded">
                                <button @click="searchSubPanel()"  class="flex items-center justify-center px-4 border-r  text-sm btn-sm">
                                    <svg class="w-6 h-6 text-gray-600" fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                                         viewBox="0 0 24 24">
                                        <path
                                            d="M16.32 14.9l5.39 5.4a1 1 0 0 1-1.42 1.4l-5.38-5.38a8 8 0 1 1 1.41-1.41zM10 16a6 6 0 1 0 0-12 6 6 0 0 0 0 12z">
                                        </path>
                                    </svg>
                                </button>
                                <input type="text" class=" text-sm px-4 py-2 w-40" v-model="search_text" :placeholder="$t('page.searchtext')">
                                <select class=" text-sm" v-model="search_field">
                                    <option value="">{{ $t('page.selectcolumn') }}</option>
                                    <option  v-for="(field, index) in fields" :value="field.field.module_id + '__' + field.field.name">{{field.field.name}}</option>
                                </select>

                            </div>
                        </div>

                    </div>


                </div>

                <div class="overflow-scroll">
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full border-separate">
                            <thead>
                            <tr>
                                <th class="w-50"> </th>
                                <th v-for="field in fields">
                                    {{field.field.module.name}}.{{field.label ? field.label : field.field.label}}
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(value, index2) in data">

                                <td class="w-20">
                                    <a class="text-xs btn-xs underline btn-link btn text-primary pb-2" v-if="props.permissions.write" :href="`/subpanel/${props.id}/edit/${value.relationship_id}?from_module=${props.module_id}&amp;from_id=${props.record_id}`" method="get" as="button">
                                        {{ $t('page.edit') }}
                                    </a><br>
                                    <a class="text-xs btn-xs underline btn-link btn text-error" v-if="props.permissions.write" @click.prevent="deleteRecord(value.relationship_id)" href="" method="get" role="button">
                                        {{ $t('page.delete') }}
                                    </a>
                                </td>
                                <td class="hover" v-for="(field, index) in fields">
                                    <Display
                                        v-bind:data_value="value[field.field.module.name + '__' + field.field.name]"
                                        v-bind:record="value"
                                        v-bind:field="field.field"
                                    />
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="w-50">
                    <label class="text-sm">{{ $t('page.perpage') }}: </label>
                    <select class=" text-sm" v-model="per_page">
                        <option value="2">2</option>
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {ref, onMounted, reactive} from 'vue';
import axios from "axios";
import Display from '@/Components/Fields/Display';
import { usePage } from '@inertiajs/inertia-vue3';

const props = defineProps({
    id: [String, null],
    record_id: [String, null],
    module_id: [String, null],
    permissions: [Object, null]
});

const fields = ref([]);
const data = ref([]);
const label = ref('');
const search_text = ref('');
const search_field = ref('');
const per_page = ref(usePage().props.value.auth.system_settings.submodule_search_per_page);
const relationship_id = ref(0);

const alert_data = reactive({
    success_alert: ref(null),
    error_alert: ref(null),
    alert_text: ref('')
});

const subpanel = ref({});
let options = [];
const get_subpanel_data = function (){
    options['search_field']=search_field.value;
    options['search_text']=search_text.value;
    options['per_page']=per_page.value;
    axios.get(`/data/subpanel/${props.id}?`+ Object.keys(options).map(key => key + '=' + options[key])
        .join('&'))
        .then(response => {
            fields.value=response.data.fields;
            data.value=response.data.data.data;
            label.value=response.data.label;
            relationship_id.value=response.data.relationship_id;

        })
        .catch(error => {
            console.log(error);
        });
};

const searchSubPanel = function () {
    get_subpanel_data();
};

const deleteRecord = async function (record_id) {

    let ok = await confirm('Are you sure you want to delete this?');
    if(ok)
    {
        axios.post('/data/delete/' + relationship_id.value + '/type/relationship', [record_id],
            {responseType: 'arraybuffer'})
            .then(response => {
                get_subpanel_data();
            }).catch(function (error) {
            alert_data.error_alert=1;
            alert_data.alert_text='There was an error';
            setTimeout(() => {
                alert_data.error_alert=null;
                alert_data.alert_text='';
            }, 5000);
        });
    }
};

onMounted(() => {
    get_subpanel_data();
});

</script>
