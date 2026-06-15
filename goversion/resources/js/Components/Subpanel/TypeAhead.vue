<template>
    <SimpleTypeAhead
        id="typeahead_id"
        placeholder="Search Module..."
        :items="items"
        :minInputLength="3"
        :itemProjection="itemProjectionFunction"
        @selectItem="selectItemEventHandler"
        @onInput="onInputEventHandler"
        @onFocus="onFocusEventHandler"
        @onBlur="onBlurEventHandler"
        ref="inputRef"
    >
    </SimpleTypeAhead>
    <a href="" @click.prevent="clearInputRep">Clear</a>
</template>
<script setup>
import SimpleTypeAhead from 'vue3-simple-typeahead';
import axios from "axios";
import {ref, onMounted, watch, defineEmits, defineExpose} from "vue";

const props = defineProps({
    module: [Object, null]
});

const items = ref([]);
let fieldValueData = {};
let suggestions='';
const display_value = ref('');
const emit = defineEmits(['newFieldValue']);
const inputRef = ref(null);


onMounted(() => {
    watch(display_value, (val) => {
        emit('newFieldValue', {value: val, field_name: "module_id_" + props.module.id});
    });


});

const onInputEventHandler = function (term) {
    fieldValueData['per_page'] = 80;
    fieldValueData['module_id'] = props.module.id;
    fieldValueData['typeahead'] = 1;

    axios.get('/data/search_data?' + Object.keys(fieldValueData).map(key => key + '=' + fieldValueData[key])
        .join('&'))
        .then(response => {
            items.value=[];
            Object.keys(response.data.data).forEach(function(key,index) {
                suggestions='';
                Object.keys(response.data.data[key]).forEach(function(key2,index2) {
                    if(index2 < 5) {
                        suggestions += response.data.data[key][key2] + ', ';
                    }
                });
                items.value.push(suggestions);
            });
        });
}

const selectItemEventHandler = function(val)
{
    display_value.value=val.split(',')[0];
    return val;
}

const clearInputRep = function()
{
    inputRef.value.clearInput();
}

defineExpose({
    clearInputRep
})

</script>
