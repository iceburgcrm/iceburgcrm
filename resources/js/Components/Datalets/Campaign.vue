<template>

    <div class="card w-full bg-base-100 sshadow-xl">


        <div class="card-title bg-accent text-primary-content">
            {{ props.data.name }}
        </div>
        <div class="card-body bg-base-100 text-base-content">
            dfds d sd ffsdsdf fsd fsdf sdf ds f dsfsdfsd
        </div>
        <figure><video class="h-100" controls>
            <source :src="`${props.data.creative}`">
            Your browser does not support the video tag.
        </video><div class="ml-5">
            <span>Campaign name: {props.data.creative}</span>
        </div></figure>

    </div>
</template>
<script>

</script>
<script setup>
import { PieChart } from 'vue-chart-3';
import Chart from 'chart.js/auto'
import {computed, ref} from "vue";

const props = defineProps({
    data: [Object, Array, null],
    datalet: [Object, null]
});


</script>
