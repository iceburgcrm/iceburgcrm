<template>
    <a class="underline decoration-base-content text-sm" v-if="field_type === 'related'"  alt="data_value" :href="`/module/${props.field.related_module.name}/view/${props.data_value}`">
        {{ display_value }}
    </a>
    <span class="grid grid-auto place-items-center" v-else-if="field_type == 'image'">
        <img v-if="display_value != '' && display_value != null" class=" w-1/4" :src="`${display_value}`" />

    </span>
    <span v-else-if="field_type == 'audio'">
        <audio v-if="display_value != '' && display_value != null" controls :src="`${display_value}`" />
    </span>
    <span v-else-if="field_type == 'file'">
        <a v-if="display_value != '' && display_value != null" class="text-sm" :href="`${display_value}`" target="_blank">Download</a>
    </span>
    <span v-else-if="field_type == 'video'">
        <span v-if="display_value != '' && display_value != null">
        <video controls>
          <source :src="`${display_value}`">
          Your browser does not support the video tag.
        </video>
        </span>
    </span>
    <span v-else-if="field_type == 'color'">
        <div class="w-10 h-1" :style="`background-color:${display_value}`"></div>
    </span>
    <span v-else-if="field_type == 'email'">
        <a class="text-sm" :href="`mailto:${display_value}`">{{ display_value }}</a>
    </span>
    <span class="text-sm" v-else-if="field_type == 'currency'">
        ${{ display_value }}
    </span>
    <span class="text-sm" v-else-if="field_type == 'url'">
        <a class="text-xs" :href="`//${display_value}`">{{ display_value }}</a>
    </span>
    <span class="text-sm" v-else-if="field_type == 'tel'">
        <a :href="`tel:${display_value}`">{{ display_value }}</a>
    </span>
    <span class="text-sm" v-else-if="field_type == 'date'">
        {{ new Date(display_value).toLocaleDateString('en-us', { weekday:"short", year:"numeric", month:"short", day:"numeric"})  }}
    </span>
    <span class="text-sm" v-else-if="field_type == 'password'">
        {{ display_value }}
        <input type="checkbox" :checked="display_value" class="checkbox">
    </span>
    <span class="text-sm" v-else-if="field_type == 'textarea'">
        {{ dispay_value ? display_value.length > 30 ? display_value.substring(0,30) + '...' : display_value : '' }}
    </span>
    <span class="text-sm" v-else-if="field_type == 'checkbox'">
        <input type="checkbox" :checked="display_value" class="checkbox checkbox-sm" @click.prevent>
    </span>
    <span class="text-sm" v-else-if="field_type === 'number'">
       {{ display_value }}
    </span>
    <a class="underline decoration-base-content text-sm"  v-else-if="props.remove_links != 1 && data_link_id !== undefined"  alt="data_value" :href="`/module/${module_name}/view/${data_link_id}`">
      {{ display_value }}
    </a>
    <span class="text-sm" v-else>
       {{ display_value }}
    </span>
</template>
<script setup>
import { ref } from 'vue';

const props = defineProps({
    data_value: [String, Number, null],
    record: [Object, null],
    field: [Object, null],
    remove_links: [String, Number, null],
    field_data: [Object, Array, null]
});
const field_type = ref('unknown');
const module_name = ref('unknown');
const data_link_id = ref(null);
const related_id = ref(null);
const display_value = ref(null);

if(props.data_value !==  null) {
    display_value.value = props.data_value;
}

if(props.field !==  null && props.field !== undefined) {
    field_type.value = props.field.input_type;
    module_name.value = props.field.module.name;
    data_link_id.value = props.record[props.field.module.name + '_row_id'];

    if(field_type.value === 'related') {
        if(props.field.related_module !== undefined) {

            const related_field_record=props.field_data.filter((item) => item.id === props.data_value);
            if(related_field_record[0]) display_value.value=related_field_record[0][props.field.related_value_id];
            else display_value.value='Unknown';

        }
    }
}


</script>
