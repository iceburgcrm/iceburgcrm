<template>
   <Head :title="`Search ${$page.props.module.label}`" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <div class="grid grid-flow-col">
                <div class="col-span-8">
                    <Title :module="$page.props.module" page_description="Search" />
                </div>
                <div class="col-span-4">
                    <HeadButtons
                        class="col-span-2"
                        :module="$page.props.module"
                        :permissions="$page.props.permissions"
                        :allowed="['import', 'add', 'export', 'audit_log']"
                    />
                </div>
            </div>

        </template>
        <BreadCrumbs :levels="$page.props.breadcrumbs" />
            <div class="max-w-full sm:px-3 lg:px-4 bg-base-200 mt-10 text-base-content">


                <div tabindex='0' class="collapse">
                    <input type="checkbox" v-model="show_search" id="show-search" />
                    <label class="collapse-title text-xsm select-none" for="show-search">

                        <span v-if="!show_search">Show</span><span v-if="show_search">Hide</span> Search Fields
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3" />
                    </svg>

                    </label>

                <div class="collapse-content">
                    <form name="search" method="get">
                        <div class="overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-base-100 text-base-content border-b border-gray-200">
                                <div class=" grid grid-cols-1 row-gap-5 mt-6 lg:grid-cols-2 md:grid-cols-2 lg:row-gap-6">
                                    <div class="col-span-1 col-gap-2 lg:col-span-1" v-for="(field, key) in $page.props.search_fields">
                                        <div class="flex mt-5 items-center">
                                            <Edit @newFieldValue="fieldValue"
                                                  :field="field"
                                                  :type="search_type"
                                                  :default_value="params[field.module_id + '__' + field.name]"
                                            />

                                        </div>
                                    </div>
                                </div>
                                <div class="mt-6">

                                </div>
                                <div class="grid col-span-3 grid-flow-col">
                                    <div class="text-left">
                                        <!--
                                        <select v-model="text_search_type" class="input-secondary rounded select-base ">
                                            <option value="fuzzy">Fuzzy</option>
                                            <option value="fuzzy_right">Fuzzy Right</option>
                                            <option value="exact">Exact</option>
                                        </select>
                                        -->

                                        <select v-model="order_by" class="input-secondary rounded select-base ">
                                            <option value="">Order By</option>

                                            <option v-for="field in $page.props.order_by_fields" :value="field.module_id + '__' + field.name">
                                                {{field.name.toUpperCase()}}
                                            </option>
                                        </select>

                                        <select v-model="search_order" class="input-secondary rounded select-base text-base-content">
                                            <option value="">Order Direction</option>
                                            <option value="asc">Ascending</option>
                                            <option value="desc">Descending</option>
                                        </select>
                                    </div>

                                    <div class="text-right">
                                        <input @click="reset_fields()" type="reset" class="btn ml-5 btn-outline btn-accent" name="reset" value="Reset" />
                                        <input @click.prevent="search_button()" type="submit" class="btn btn-primary" name="search" value="Search" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
                <!-- @submit.prevent="getFormValues" -->

            </div>
        <Alert :message="alert_data.alert_text" :is_successful="alert_data.success_alert" :is_error="alert_data.error_alert" />






        <div class="max-w-full sm:px-3 lg:px-4  bg-base-200">
                <form name="search_actions"><div class="grid grid-auto grid-flow-col justify-between">
                    <div class="mb-3">
                        <pagination v-model:page="page" @pagevalue="updatepage" class="mt-6 text-right" :links="pagination_links" />

                    </div>

                    <select class="text-left align-baseline input mt-4 bg-base-200 dropdown-content shadow rounded-box w-24" v-model="per_page">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>

                </div>
                    <div class="bg-base-200 text-base-content overflow-x-scroll shadow-sm sm:rounded-lg">
                        <div class="overflow-x-auto">
                            <table ref="tabs" class="table table-primary w-full border-primary border-solid">
                                <thead>
                                <tr>
                                    <th>
                                        <input type="button" class="btn-primary outline-primary">
                                        <select class="dropdown-content menu p-2 shadow bg-base-100 rounded-box text-sm w-24" v-model="action_menu">
                                            <option value="" class="text-sm">Action</option>
                                            <option v-if="$page.props.permissions.export === 1" value="xlsx">Export Excel(XLSX)</option>
                                            <option v-if="$page.props.permissions.export === 1" value="xls">Export Excel Compatible (XLS)</option>
                                            <option v-if="$page.props.permissions.export === 1" value="csv">Export CSV</option>
                                            <option v-if="$page.props.permissions.export === 1" value="tsv">Export TSV</option>
                                            <option v-if="$page.props.permissions.export === 1" value="ods">Export ODS</option>
                                            <option v-if="$page.props.permissions.export === 1" value="html">Export HTML</option>
                                            <option v-if="$page.props.permissions.write === 1" value="delete">Delete</option>
                                        </select>
                                    </th>
                                    <th v-for="field in display_fields">
                                        <a class="underline decoration-gray-900" @click.prevent="sort_by_field(field)">{{ field.label }}</a>

                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-if="show_loading" ><td class="w-full" colspan="500">Loading...</td></tr>
                                <tr v-for="(row, index) in search_data">
                                    <td class="w-50">
                                        <input type="checkbox" v-bind:id="'id-' + row.id"  v-model="selected_records" v-bind:value="row[$page.props.module.name + '_row_id']">
                                        <a class="pl-3" :href="`/module/${$page.props.module.name}/edit/${row[$page.props.module.name + '_row_id']}`">Edit</a>
                                    </td>
                                    <td class="text-lg h-10" v-for="(item, key) in $page.props.display_fields">
                                        <Display
                                            v-bind:data_value="row[item.module.name + '__' + item.name]"
                                            v-bind:record="row"
                                            v-bind:field="item"
                                            :field_data="$page.props.field_data[item.name]"
                                        />

                                    </td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>Actions</th>
                                    <th  v-for="(field, key) in $page.props.display_fields" >
                                        <a v-if="active_fields[key]"  class="underline decoration-gray-900" @click.prevent="sort_by_field(field)">{{ field.label }}</a>
                                    </th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>

                        <pagination v-model:page="page" @pagevalue="updatepage" class="mt-6" :links="pagination_links" />

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

import Display from '@/Components/Fields/Display'
import Edit from '@/Components/Fields/Edit'
import { ref, computed, toRaw, watch, onMounted, reactive } from 'vue';

import Pagination from "@/Components/Pagination";

const per_page = ref(usePage().props.value.auth.system_settings.search_per_page);

const page = ref(1);
const search_order = ref('asc');
const order_by = ref('');
const module_id = ref(usePage().props.value.module.id);
const search_type = ref('module');
const pagination_links = ref(reactive(usePage().props.value.records_object.links));

let search_data = ref(usePage().props.value.records.data);
let fieldValueData = {};
let params = [];

let current_width = ref(0);

const unactive_fields = ref([]);
let active_fields = ref([]);
const show_field_list = ref(false);

const types = ['xlsx', 'xls', 'csv', 'tsv', 'ods', 'html'];

const selected_records = ref([]);
const prop_fields = computed(() => usePage().props.value.fields);
const request_values = computed(() => usePage().props.value.request);

const action_menu = ref('');
const download_menu = ref('');

const show_search = ref(true);
const show_loading = ref(false);

const text_search_type = ref('fuzzy');

const alert_data = reactive({
    success_alert: ref(0),
    error_alert: ref(0),
    alert_text: ref('')
});

const arrayFailed = Object.entries(usePage().props.value.display_fields).map((arr) => {
    return arr[1]
}
);

const default_display_fields = ref(usePage().props.value.display_fields);
const display_fields = ref(usePage().props.value.display_fields);

for(let key in usePage().props.value.display_fields){
    active_fields.value[key]=true;
}

let tabs = ref();

onMounted(() => {
    watch([action_menu], async (val) => {
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
                        action_menu.value='';
                        selected_records.value=[];
                        getSearchData();
                    }).catch(function (error) {
                        alert_data.error_alert=1;
                        alert_data.alert_text='There was an error';
                        setTimeout(() => {
                            alert_data.error_alert=null;
                            alert_data.alert_text='';
                        }, 5000);
                    }) };


        }
        else if(types.indexOf(val+'') >= 0)
        {
            axios.post('/data/download/' + usePage().props.value.module.id + '/' + val, selected_records.value,
                {responseType: 'arraybuffer'})
                .then(response => {
                    var fileURL = window.URL.createObjectURL(new Blob([response.data]));
                    var fileLink = document.createElement('a');
                    fileLink.href = fileURL;
                    fileLink.setAttribute('download', usePage().props.value.module.name + '.' + val);
                    document.body.appendChild(fileLink);
                    fileLink.click();
                    action_menu.value='';
                });
        }
    });
    watch([page, per_page], (val) => {
        getSearchData();
    }, { deep: true });
});

const getSearchData = function () {
    fieldValueData['page'] = page.value;
    fieldValueData['per_page'] = per_page.value;
    fieldValueData['search_order'] = search_order.value;
    fieldValueData['order_by'] = order_by.value;
    fieldValueData['search_type'] = search_type.value;
    fieldValueData['module_id'] = module_id.value;
    fieldValueData['text_search_type'] = text_search_type.value;


    search_data.value = [];
    show_search.value=false;
    show_loading.value=true;
    axios.get('/data/search_data?' + Object.keys(fieldValueData).map(key => key + '=' + fieldValueData[key])
        .join('&'))
        .then(response => {
            show_loading.value=false;
            search_data.value = response.data.data;
            pagination_links.value = reactive(response.data.links);
    });
}

const updatepage = ((evt) => {
    page.value = evt[0];
});

const fieldValue = ((evt) => {
    fieldValueData[evt.field_name] = evt.value;
});

const reset_fields = ((e) => {
    fieldValueData = {};
});

const search_button = ((e) => {
    page.value = 1;
    getSearchData();
});

const sort_by_field = ((field) => {
    order_by.value=field.module_id + '__' + field.name;
    search_order == 'asc' ? search_order.value = 'desc' : search_order.value = 'asc';
    getSearchData();

});
</script>

