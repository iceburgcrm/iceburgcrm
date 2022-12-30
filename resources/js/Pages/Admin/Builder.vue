
<template>
    <Head title="Builder" />
    <BreezeAuthenticatedLayout>

        <template #header>
            <h2 class="font-semibold text-xl text-base-content leading-tight">
                Builder
            </h2>

        </template>
        <BreadCrumbs :levels="$page.props.breadcrumbs" />


        <div class="w-full bg-base-100">
            <div class="bg-base-100 mt-10 text-base-content max-w-full sm:px-3 lg:px-4">

                <div class="bg-base-100 text-base-content grid col-span-12 grid-flow-col mt-5">

                    <div class="grid grid-flow-row mb-5 w-full">
                        <label>Select a Module</label>
                        <select v-model="module_id" @change="select_module()" name="module_id" class="input select-secondary text-lg w-full">
                            <option :value="item.id" v-for="item in modules">{{item.label}}</option>
                        </select>
                    </div>

                </div>

                <div class="grid grid-flow-row">
                    <label>Create a Module</label>
                    <div><input placeholder="Enter a name" v-model="created_items['module']" type="text" class="text-xs rounded border-secondary">
                        <button class="btn btn-secondary btn-outline btn-sm ml-1" @click="create_item('module')">Add</button>
                    </div>
                </div>


                <div class="" v-if="edit_module_screen">
                    <div v-if="module" class="card w-full bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h2 class="card-title">{{ module.label }}</h2>
                            <p class="w-full grid-flow-col grid-col-3 mt-5">
                            <div class="mt-5 mb-5"><button @click="regenerate_module(module.id)" class="mb-10 btn btn-error text-error-content btn-outline">Regenerate Module (Existing data will be deleted)</button> Seed: <input v-model="seed" type="text" size="4" placeholder="50" /></div>

                            <div class="grid grid-flow-col col-auto">
                                <div class="grid grid-flow-row justify-start">
                                    <label>Create a Field</label>
                                    <div><input placeholder="Enter a name" v-model="created_items['field']" type="text" class="text-xs ml-2 rounded border-secondary">
                                        <button class="btn btn-secondary ml-1 btn-outline btn-sm" @click="create_item('field')">Add</button>
                                    </div>
                                </div>
                                <div class="grid grid-flow-row justify-end">
                                    <label>Create a Subpanel</label>
                                    <div><input placeholder="Enter a name" v-model="created_items['subpanel']" type="text" class="text-xs ml-2 rounded border-secondary">
                                        <button class="btn btn-secondary btn-outline btn-sm ml-1" @click="create_item('subpanel')">Add</button>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-10">
                                <label class="text-xl">Details</label>
                                        <div class="collapse collapse-plus border border-base-300 bg-base-100 rounded-box">
                                            <input type="checkbox" class="peer" />
                                            <div class="collapse-title bg-neutral text-neutral-content peer-checked:bg-base-200 peer-checked:text-base-content">
                                                <label class="text-lg">Details</label>
                                                </div>
                                            <div class="collapse-content bg-neutral text-neutral-content peer-checked:bg-base-200 peer-checked:text-base-content">
                                                <div class="align-right justify-end"><button class="btn btn-error btn-outline" @click="delete_item('module', module.id)">Delete</button></div>

                                                <p class="grid grid-flow-col col-auto" v-for="(value,key) in module">
                                                <div v-show="!['id', 'updated_at', 'created_at'].includes(key)" class="form-control w-full mt-4">
                                                    <label class="label">
                                                        <span class="label-text text-lg">{{key.toUpperCase()}}</span> </label>
                                                        <p class="font-light">{{help['module'][key]}}</p>
                                                        <input type="text" :id="'data' + key+'_'+'module'+'_'+module.id" @change="save_value(key,'module', module.id)" placeholder="" class="input input-bordered w-full max-w-xs" :value="`${value}`" />
                                                </div>
                                                </p>
                                            </div>
                                        </div>



                            </div>
                                <div class="mt-5">
                                    <label class="text-xl">Fields</label>
                                    <ul>
                                        <li v-for="field in fields" class="w-full">
                                            <div class="collapse collapse-plus border border-base-300 bg-base-100 rounded-box">
                                                <input type="checkbox" class="peer" />
                                                <div class="collapse-title bg-primary text-primary-content peer-checked:bg-base-200 peer-checked:text-base-content">
                                                    <label class="text-lg">{{field.label}} </label>
                                                </div>
                                                <div class="collapse-content bg-primary text-primary-content peer-checked:bg-base-200 peer-checked:text-base-content">
                                                    <div class="align-right justify-end"><button class="btn btn-error btn-outline" @click="delete_item('field', field.id)">Delete</button></div>
                                                    <p class="grid grid-flow-col col-auto" v-for="(value,key) in field">

                                                        <div v-show="!['id', 'updated_at', 'created_at'].includes(key)" class="form-control w-full mt-4">
                                                            <label class="label">
                                                                <span class="label-text">{{key.toUpperCase()}}</span>
                                                            </label>
                                                            <p class="font-light">{{help['field'][key]}}</p>
                                                            <input type="text" placeholder="" :id="'data' + key+'_'+'field'+'_'+field.id" @change="save_value(key, 'field', field.id)" class="input input-bordered w-full max-w-xs" :value="`${value}`" />
                                                        </div>

                                                    </p>
                                                </div>
                                            </div>


                                        </li>
                                    </ul>
                                </div>
                                <div class="mt-5">
                                    <label class="text-xl">Subpanels</label>

                                    <ul>
                                    <li v-for="subpanel in subpanels">
                                        <div class="collapse collapse-plus border border-base-300 bg-base-100 rounded-box">
                                            <input type="checkbox" class="peer" />
                                            <div class="collapse-title bg-secondary text-secondary-content peer-checked:bg-base-200 peer-checked:text-base-content">
                                                <label class="text-lg">{{subpanel.label}}</label>
                                            </div>
                                            <div class="collapse-content bg-secondary text-secondary-content peer-checked:bg-base-200 peer-checked:text-base-content">
                                                <div class="align-right justify-end"><button class="btn btn-error btn-outline" @click="delete_item('subpanel', subpanel.id)">Delete</button></div>

                                                <p class="grid grid-flow-col col-auto" v-for="(value,key) in subpanel">
                                                <div v-show="!['id', 'updated_at', 'created_at'].includes(key)" class="form-control w-full max-w-xs mt-4">
                                                    <label class="label">
                                                        <span class="label-text">{{key.toUpperCase()}}</span>
                                                    </label>
                                                     <p class="font-light">{{help['subpanel'][key]}}</p>
                                                    <div class="grid-flow-row" v-if="key === 'subpanelfields'">

                                                        <div class="" v-for="(subpanel_array_fields in value">
                                                            <ul class="ml-5 list-disc" v-for="(subpanel_field, subpanel_field_key) in subpanel_array_fields">
                                                                <li v-if="subpanel_field_key === 'field'">{{subpanel_field.module.label}} - {{subpanel_field.label}} [<a class="text-error" @click="delete_subpanel_field(subpanel_field.id, value.id)">x</a>]</li>
                                                            </ul>
                                                        </div>
                                                        <div class="mb-5">
                                                            <button class="text-sm input-primary btn btn-primary text-primary-content mt-5" value="Add a field" @click="show_subpanel_field_list(subpanel.id)">Add</button> [requires a selected relationship]
                                                            <span v-if="subpanel_field_values[subpanel.id]">
                                                                <br>
                                                                <select v-if="subpanel_field_values[subpanel.id]" v-model="subpanel_field_list[subpanel.id]" name="relationship_id" class="input select-primary w-full md:w-1/2 lg:w-1/4">
                                                                    <option :value="item.id" v-for="item in subpanel_field_values">{{item.module.label}} - {{item.label}}</option>
                                                                </select>
                                                                <button class="text-lg input-primary text-neutral mt-5 ml-5" value="Add a field" @click="add_subpanel_field(key, value.id, subpanel.id)">Add Field</button>
                                                            </span>
                                                    </div>
                                                    </div>
                                                     <div v-else-if="key === 'relationship'">
                                                     </div>
                                                    <input v-else type="text" placeholder="" :id="'data' + key+'_'+'subpanel'+'_'+subpanel.id" @change="save_value(key, 'subpanel', subpanel.id)" class="input input-bordered w-full max-w-xs" :value="`${value}`" />
                                                </div>
                                                </p>
                                            </div>
                                        </div>


                                    </li>
                                    </ul>
                                </div>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-5">
                <div class="card w-full bg-base-100 shadow-xl">
                    <div class="card-body">
                <label class="text-xl">Datalets</label>
                <div class=" grid grid-flow-row">
                    <label>Create a Datalet</label>
                    <div><input placeholder="Enter a name" v-model="created_items['datalet']" type="text" class="text-xs rounded border-secondary">
                        <button class="btn btn-secondary btn-outline btn-sm ml-1" @click="create_item('datalet')">Add</button>
                    </div>
                </div>
                <ul>
                    <li v-for="datalet in datalets" class="w-full">
                        <div class="collapse collapse-plus border border-base-300 bg-base-100 rounded-box">
                             <input type="checkbox" class="peer" />
                            <div class="collapse-title bg-primary text-primary-content peer-checked:bg-base-200 peer-checked:text-base-content">
                                <label class="text-lg">{{datalet.label}}</label>
                            </div>
                            <div class="collapse-content bg-primary text-primary-content peer-checked:bg-base-200 peer-checked:text-base-content">
                                <div class="align-right justify-end"><button class="btn btn-error btn-outline" @click="delete_item('datalet', datalet.id)">Delete</button></div>

                                <p class="grid grid-flow-col col-auto" v-for="(value,key) in datalet">

                                <div v-show="!['id', 'updated_at', 'created_at'].includes(key)" class="form-control w-full mt-4">
                                    <label class="label">
                                        <span class="label-text">{{key.toUpperCase()}}</span>
                                    </label>
                                    <p class="font-light">{{help['datalet'][key]}}</p>
                                    <input type="text" placeholder="" :id="'data' + key+'_'+'datalet'+'_'+datalet.id" @change="save_value(key, 'datalet', datalet.id)" class="input input-bordered w-full max-w-xs" :value="`${value}`" />
                                </div>

                                </p>
                            </div>
                        </div>


                    </li>
                </ul>
                    </div>
                </div>
            </div>
            <div class="mt-5">
                <div class="card w-full bg-base-100 shadow-xl">
                    <div class="card-body">

                        <label class="text-xl">Relationships</label>
                        <div class=" grid grid-flow-row">
                            <label>Create Relationship</label>
                            <div>
                                Modules: <input placeholder="ID of each module 1,2" v-model="create_relationship_modules" type="text" class="text-xs rounded border-secondary">
                                Name: <input placeholder="modulename1_modulename2_modulename3" v-model="created_items['relationship']" type="text" class="text-xs rounded border-secondary">
                                <button class="btn btn-secondary btn-outline btn-sm ml-1" @click="create_item('relationship')">Add</button>
                            </div>
                        </div>
                        <ul>
                            <li v-for="relationship in relationships" class="w-full">
                                <div class="collapse collapse-plus border border-base-300 bg-base-100 rounded-box">

                                    <input type="checkbox" class="peer" />
                                    <div class="collapse-title bg-secondary text-secondary-content peer-checked:bg-base-200 peer-checked:text-base-content">
                                        <label class="text-lg">{{relationship.name}} </label>
                                    </div>
                                    <div class="collapse-content bg-secondary text-secondary-content peer-checked:bg-base-200 peer-checked:text-base-content">
                                        <div class="align-right justify-end"><button @click="delete_item('relationship', relationship.id)" class="btn btn-error btn-outline">Delete</button></div>

                                        <p class="grid grid-flow-col col-auto" v-for="(value,key) in relationship">

                                        <div v-show="!['id', 'updated_at', 'created_at'].includes(key)" class="form-control w-full mt-4">
                                            <label class="label">
                                                <span class="label-text">{{key.toUpperCase()}}</span>
                                            </label>
                                            <p class="font-light">{{help['relationship'][key]}}</p>
                                            <input type="text" placeholder="" :id="'data' + key+'_'+'field'+'_'+relationship.id" @change="save_value(key, 'field', relationship.id)" class="input input-bordered w-full max-w-xs" :value="`${value}`" />
                                        </div>

                                        </p>
                                    </div>
                                </div>


                            </li>
                        </ul>
                    </div>
                </div>
                <Alert :message="alert.alert_text" :is_successful="alert.success_alert" :is_error="alert.error_alert" />

            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
<script setup>
import {ref, computed, reactive, onMounted} from "vue";
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head, usePage } from '@inertiajs/inertia-vue3';
import axios from "axios";
import BreadCrumbs from "@/Components/BreadCrumbs";
import Alert from "@/Components/Alert";
import Buttons from "@/Components/Header/Buttons";

const alert = reactive({
    success_alert : ref(0),
    error_alert : ref(0),
    alert_text : ref('')
});

const help = {
    'module': {
        'name': 'Database field name of the module.  Requires module regeneration.',
        'create_table': 'This flag when active will create the table.  Disable for existing tables or tables generated through standard migrations',
        'faker_seed': 'This flag when active will create faker records',
        'admin': 'This is an admin module.  Only admins can view these types of modules',
        'status': 'Is the module currently active?',
        'primary': 'Primary modules are displayed in the menu',
        'icon': 'Name of the icon.  Must match the list of available icons in the base icon vue file',
        'module_group_id': 'ID of the group the module is part of'
    },
    'field': {
        'name': 'Database field name of the module.  Requires module regeneration.',
        'module_id': 'The module this field is part of',
        'validation': 'Validation rules for field.  Uses the same validation rules are laravel.  Please visit the laravel documentation for more details',
        'input_type': 'This defines the field type and how the data will be displayed.  [color, checkbox, text, email, currency, url, tel, date, password, textarea]',
        'data_type': 'Database data type [string, integer, date, float, ...]',
        'field_length': 'Database length for field.  Import for string but not for integer',
        'related_module_id': 'ID of the module the field is related to',
        'related_field_id': 'ID of the field in the related module',
        'related_value_id': 'ID of the value in the related module',
        'decimal_places': 'Used for numeric fields like currency to determine the amount of decimal places for the database field',
        'status': 'Is the field active'
    },
    'subpanel': {
        'name': 'Name of the subpanel'
    },
    'datalet': {
        'name': 'Not used',
        'label': 'Display label of datalet',
        'type': 'Type of datalet.  By default 5 exist, add your own in the datalet module.'
    },
    'relationship': {
        'name': 'Name of the relationship.  Each module name separated by _ order not important',
        'modules': 'Modules part of the relationship.  Comma separated list.',
        'related_field_types': 'Type of data for relationship join.  Comma separated list. Default is integer.',
        'status': 'Is the relationship active?'
    }
};

const module = ref({});
const subpanels = ref(null);
const fields = ref(null);
const module_relationships = ref(null);
const datalets = ref(usePage().props.value.datalets);
const relationships = ref(usePage().props.value.relationships);

const seed = ref(50);
const create_relationship_modules = ref('');
const default_permissions = ref(usePage().props.value.permissions);
const created_items = ref([]);

const modules = ref(usePage().props.value.modules);
const module_id = ref(0);
const edit_module_screen = ref(false);
const data = ref({});
const subpanel_list = ref([]);
const relationship_list = ref([]);

const subpanel_field_list = ref([]);
const subpanel_field_values = ref([]);

const show_subpanel_field_list = function(subpanel_id){
    axios.get('/data/builder/' + subpanel_id + '/type/select_subpanel_fields').then(response => {
        subpanel_field_values.value=response.data;
        subpanel_field_list.value[subpanel_id]=true;
    });
}

const new_module_selected = function(){
    edit_module_screen.value=false;
}
const create_item = function(name)
{
    if(created_items.value[name].length > 0)
    {
        axios.post('/data/builder/' + module_id.value + '/type/add_' + name, {
            'name': created_items.value[name],
            'relationship_modules': create_relationship_modules.value
            }).then(response => {

            created_items.value[name]='';
            create_relationship_modules.value='';

            alert.success_alert=1;
            alert.alert_text=name + " was successfully added.  Select the item from the list to edit additional fields and change the field to active";
            setTimeout(() => {
                alert.success_alert=null;
                alert.alert_text='';
            }, 2000);
            select_module();
            get_datalets();
            get_relationships();
        }).catch(function (error) {
            alert.error_alert=1;
            alert.alert_text=error.text;
            setTimeout(() => {
                alert.error_alert=null;
                alert.alert_text='';
            }, 2000);
        });
    }
}

const add_subpanel_field = function(key, subpanel_id, sub_id){
    axios.post('/data/builder/' + module_id.value + '/type/add_subpanel_field', {
        subpanel_field_id: subpanel_field_list.value[sub_id],
        subpanel_id: sub_id
    }).then(response => {
        select_module();
    }).catch(function (error) {
        alert.error_alert=1;
        alert.alert_text=error.text;
        setTimeout(() => {
            alert.error_alert=null;
            alert.alert_text='';
        }, 2000);
    });
}

const delete_subpanel_field = function(subpanel_field_id, subpanel_id) {
    axios.post('/data/builder/' + module_id.value + '/type/delete_subpanel_field', {
        subpanel_field_id: subpanel_field_id,
        subpanel_id: subpanel_id
    }).then(response => {
        select_module();
    }).catch(function (error) {
        alert.error_alert=1;
        alert.alert_text=error.text;
        setTimeout(() => {
            alert.error_alert=null;
            alert.alert_text='';
        }, 2000);
    });
}

const select_module = function () {
    edit_module_screen.value=true;
    axios.get('/data/builder/' + module_id.value + '/type/select_module').then(response => {
        module.value=response.data.module;
        fields.value=response.data.fields;
        subpanels.value=response.data.subpanels;
        module_relationships.value=response.data.relationships;
       get_modules();
    });
}
const regenerate_module = function(m_id)
{
    axios.post('/data/builder/' + m_id + '/type/regenerate', {
        module_id: module_id.value,
        seed: seed.value
    }).then(response => {
        alert.success_alert=1;
        alert.alert_text="Successfully generated";
        setTimeout(() => {
            alert.success_alert=null;
            alert.alert_text='';
        }, 2000);
        select_module();
    }).catch(function (error) {
        alert.error_alert=1;
        alert.alert_text=error.text;
        setTimeout(() => {
            alert.error_alert=null;
            alert.alert_text='';
        }, 2000);
    });
}

const save_value = function(key, type, type_id)
{
    console.log(document.getElementById('data'+key+'_'+type+'_'+type_id));
    axios.post('/data/builder/' + module_id.value + '/type/save', {
        key: key,
        value: document.getElementById('data'+key+'_'+type+'_'+type_id).value,
        type: type,
        type_id: type_id
    }).then(response => {
        alert.success_alert=1;
        alert.alert_text="Data Saved";
        setTimeout(() => {
            alert.success_alert=null;
            alert.alert_text='';
        }, 2000);
        select_module();
    }).catch(function (error) {
        alert.error_alert=1;
        alert.alert_text=error.text;
        setTimeout(() => {
            alert.error_alert=null;
            alert.alert_text='';
        }, 2000);
    });
}

const delete_item = function (type, delete_id) {
    axios.post('/data/builder/' + module_id.value + '/type/delete', {
        type: type,
        delete_id: delete_id
    }).then(response => {
        //module_id.value=0;
        alert.success_alert=1;
        alert.alert_text="Item was deleted";
        setTimeout(() => {
            alert.success_alert=null;
            alert.alert_text='';
        }, 2000);
        select_module();
        get_datalets();
        get_relationships();
    }).catch(function (error) {
        alert.error_alert=1;
        alert.alert_text=error.text;
        setTimeout(() => {
            alert.error_alert=null;
            alert.alert_text='';
        }, 2000);
    });
}

const get_modules = function () {
    axios.get('/data/builder/' + module_id.value + '/type/get_modules').then(response => {
        modules.value=response.data.modules;
        get_datalets();
        get_relationships();
    });
}

const get_datalets = function () {
    axios.get('/data/builder/' + module_id.value + '/type/get_datalets').then(response => {
        datalets.value=response.data.datalets;
    });
}

const get_relationships = function () {
    axios.get('/data/builder/' + module_id.value + '/type/get_relationships').then(response => {
        relationships.value=response.data.relationships;
    });
}

</script>
