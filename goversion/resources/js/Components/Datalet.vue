<template>
    <div v-if="props.datalet.type.id === 1" class="col-span-12">
        <Doughnut :data="props.data" :datalet="props.datalet"  />
    </div>
    <div v-if="props.datalet.type.id === 2" class="col-span-12">
        <Bubble :data="props.data" :datalet="props.datalet"  />
    </div>
    <div v-if="props.datalet.type.id === 3" class="col-span-12">
        <Bar :data="props.data" :datalet="props.datalet"  />
    </div>
    <div v-if="props.datalet.type.id === 4" class="col-span-12">
        <Pie :data="props.data" :datalet="props.datalet"  />
    </div>
    <div v-if="props.datalet.type.id === 5" class="col-span-12">
        <Area :data="props.data" :datalet="props.datalet"  />
    </div>
    <div v-if="props.datalet.type.id === 6" class="col-span-12">
        <Meetings :data="props.data" :datalet="props.datalet"  />
    </div>
    <div v-if="props.datalet.type.id === 7" class="col-span-12">
        <CrmStats :data="props.data" :datalet="props.datalet"  />
    </div>
    <div v-if="props.datalet.type.id === 8" class="col-span-12">
        <Summary :data="props.data" :datalet="props.datalet"  />
    </div>

</template>
<script setup>

import {ref, onMounted} from 'vue';
import axios from "axios";

import Doughnut from '@/Components/Datalets/Doughnut'
import Bar from '@/Components/Datalets/Bar'
import Bubble from '@/Components/Datalets/Bubble'
import Pie from '@/Components/Datalets/Pie'
import Area from '@/Components/Datalets/Area'
import Meetings from '@/Components/Datalets/Meetings'
import Summary from '@/Components/Datalets/Summary.vue'
import CrmStats from '@/Components/Datalets/CrmStats.vue'

const props = defineProps({
   datalet: [Object, null],
    data: [Object, null]
});
/*
const data = ref([]);
const id = ref(Math.floor(Math.random() * 100000))

onMounted(() => {
    axios.get('/data/datalet?id=' + props.datalet.id)
        .then(response => {
            console.log(response.data);
            data.value=[];
            data.value=response.data;
            id.value = Math.random() * 100000;
        });
});
*/
</script>
