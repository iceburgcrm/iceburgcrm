<template>
    <div class="card lg:card-side bg-base-100 shadow-xl">
        <div class="card-body">
            <p>
                <ul>
                    <li class="input-primary" v-for="(item,key) in fields">{{key.replace('_', ' ')}}: {{item}}</li>
                </ul>
            </p>
        </div>
    </div>
</template>
<script setup>
import axios from "axios";
import { ref, onMounted } from 'vue';
const props = defineProps({
    record_id: [String, Number, null],
    module_id: [String, Number, null]
});
axios.get('/data/module/' + props.module_id + '/record/' + props.record_id)
    .then(response => {
        fields.value=response.data;
    },{deep: true});
const fields = ref([]);
onMounted(() => {
        axios.get('/data/module/' + props.module_id + '/record/' + props.record_id)
            .then(response => {
                fields.value=response.data;
            },{deep: true});
});
</script>
