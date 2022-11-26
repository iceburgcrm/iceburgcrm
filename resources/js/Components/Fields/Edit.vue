<template>
    <div class="grid grid-auto grid-flow-row">
        <label for="search" class="text-lg font-medium leading-5 text-base-content">
            <span class="font-semibold" v-if="type==='relationship'">
                {{ current_field.module.name.charAt(0).toUpperCase() + current_field.module.name.slice(1) }}
                {{ current_field.label }}
            </span>
            <span class="font-semibold" v-else>
                {{ current_field.label }}
            </span>
        </label>
        <div v-if="props.field.input_type === 'tel'" class="rounded">
            <input size="40" placeholder="+1 (999) 999-9999" v-maska="['+1 (###) ##-####', '+1 (###) ###-####']" :name="current_field.module_id + '_' + current_field.name" type="text" class="input-secondary rounded sm:text-sm sm:leading-5" v-model="display_value" />
        </div>

        <div v-else-if="props.field.input_type === 'currency'" class="rounded">
            <input type="number" min="0.00" step="0.01" :name="current_field.module_id + '_' + current_field.name"  class="input-secondary rounded sm:text-sm sm:leading-5" v-model="display_value" />
        </div>
        <div v-else-if="props.field.input_type === 'checkbox'" class="rounded">
            <input type="checkbox"  placeholder="" :name="current_field.module_id + '_' + current_field.name"  class="input-secondary checkbox  rounded sm:text-sm sm:leading-5" v-model="display_value" />
        </div>
        <div v-else-if="props.field.input_type === 'password'" class="rounded">
            <input :name="current_field.module_id + '_' + current_field.name" type="password" class="input-secondary rounded sm:text-sm sm:leading-5" v-model="display_value" />
        </div>
        <div v-else-if="props.field.input_type === 'image' || props.field.input_type === 'video' || props.field.input_type === 'file'" class="rounded">
            <input :name="current_field.module_id + '_' + current_field.name" type="file" class="input-secondary rounded sm:text-sm sm:leading-5" @change="onFileChanged($event)" />
        </div>
        <div v-else-if="props.field.input_type === 'number'" class="rounded">
            <input size="3" placeholder="" :name="current_field.module_id + '_' + current_field.name" type="number" class="input-secondary rounded sm:text-sm sm:leading-5" v-model="display_value" />
        </div>
        <div v-else-if="props.field.input_type === 'email'" class="rounded">
            <input size="40" placeholder="email@email.com" :name="current_field.module_id + '_' + current_field.name" type="email" class="input-secondary rounded sm:text-sm sm:leading-5" v-model="display_value" />
        </div>
        <div v-else-if="props.field.input_type === 'url'" class="rounded">
            <input size="40" placeholder="https://" :name="current_field.module_id + '_' + current_field.name" type="text" class="input-secondary rounded sm:text-sm sm:leading-5" v-model="display_value" />
        </div>
        <div v-else-if="props.field.input_type === 'zip'" class="">
            <input size="40" placeholder="" v-maska="" :name="current_field.module_id + '_' + current_field.name" type="text" class="input-secondary rounded sm:text-sm sm:leading-5" v-model="display_value" />
        </div>
        <div v-else-if="props.field.input_type === 'date'" class="input-secondary rounded sm:text-sm sm:leading-5">
            <div class="w-1/2">
                <Datepicker v-model="display_value"></Datepicker>
            </div>
        </div>
        <div v-else-if="current_field.input_type === 'related'">
            <select :name="current_field.module_id + '_' + current_field.name" v-model="display_value"  class="select-secondary rounded-lg sm:text-sm sm:leading-5">
                <option v-for="related_field in related_fields" :value="related_field.id">{{related_field.name}}</option>
            </select>
       </div>
        <div v-else-if="current_field.input_type === 'address'" class="rounded">
           <textarea rows="3" cols="40" v-model="display_value" :name="current_field.module_id + '_' + current_field.name"  class="input-secondary rounded sm:text-sm sm:leading-5">
            </textarea>
        </div>
        <div v-else-if="current_field.input_type === 'textarea'" class="rounded">
           <textarea rows="6" cols="40" v-model="display_value" :name="current_field.module_id + '_' + current_field.name"  class="input-secondary rounded sm:text-sm sm:leading-5">
            </textarea>
        </div>
        <div v-else-if="props.field.input_type === 'color'" class="rounded">
            <input type="color" placeholder="" :name="current_field.module_id + '_' + current_field.name" class="input-secondary rounded sm:text-sm sm:leading-5" v-model="display_value" />
        </div>
        <div v-else-if="current_field.input_type === 'radio'" class="rounded outline-primary"><input :name="current_field.module_id + '_' + current_field.name" type="text" class="form-input sm:text-sm sm:leading-5" v-model="display_value" />
        </div>
        <div v-else>
            <input size="40" class="input-secondary rounded sm:text-sm sm:leading-5" :name="current_field.module_id + '__' + current_field.name" type="text" v-model="display_value" />
        </div>
    </div>
</template>
<script>
import { ref, defineEmits, watch, onMounted, reactive } from 'vue';
import { mask } from 'maska';
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
</script>
<script setup>

import {ref} from "vue";

const props = defineProps({
    default_value: [Object, null],
    field: [Object, null],
    type: [String, null],
    record: [Object, null],
});
let display_value = ref(props.default_value);
const current_field = ref(props.field);
const emit = defineEmits(['newFieldValue']);

const field_type = ref('unknown');
const module_name = ref('unknown');
const data_link_id = ref(null);
const related_id = ref(null);
const related_fields = ref([]);

const file = ref();

const onFileChanged = function($event) {
    const target = $event.target;
    if (target && target.files) {
        file.value = target.files[0];
    }
}


if(props.field !==  null && props.field !== undefined) {
    field_type.value = props.field.input_type;
    module_name.value = props.field.module.name;
    data_link_id.value=display_value.value;
    if(field_type.value === 'related') {
        if(props.field.related_module !== undefined) {
            related_id.value = props.field.related_module.id;
            data_link_id.value = props.data_value;
            module_name.value = props.field.related_module.name;

            axios.get(`/data/related_fields/field_id/${props.field.id}`)
                .then(response => {
                    related_fields.value = response.data;
                })
                .catch(error => {
                });
        }
    }
}

onMounted(() => {
    watch(display_value, (val) => {
        const field = props.field.module_id + "__" + props.field.name;
        emit('newFieldValue', {value: val, field_name: field});
    });

});


</script>
