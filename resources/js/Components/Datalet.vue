<template>
    <div v-if="props.datalet.type.id === 1" class="col-span-12">
        <Doughnut :data="data" :datalet="props.datalet"  />
    </div>
    <div v-if="props.datalet.type.id === 2" class="col-span-12">
        <Bubble :data="data" :datalet="props.datalet"  />
    </div>
    <div v-if="props.datalet.type.id === 3" class="col-span-12">
        <Bar :data="data" :datalet="props.datalet"  />
    </div>
    <div v-if="props.datalet.type.id === 4" class="col-span-12">
        <Pie :data="data" :datalet="props.datalet"  />
    </div>
    <div v-if="props.datalet.type.id === 5" class="col-span-12">
        <Area :data="data" :datalet="props.datalet"  />
    </div>
    <div v-if="props.datalet.type.id === 6" class="col-span-12">
        <Campaign :data="data" :datalet="props.datalet"  />
    </div>
    <div v-if="props.datalet.type.id === 7" class="col-span-12">
        <Meetings :data="data" :datalet="props.datalet"  />
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
import Campaign from '@/Components/Datalets/Campaign'
import Meetings from '@/Components/Datalets/Meetings'

const props = defineProps({
   datalet: [Object, null]
});

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

</script>
