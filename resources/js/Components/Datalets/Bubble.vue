<template>
    <div class="card w-full bg-base-100 shadow-xl">

            <div class="card-title bg-secondary text-secondary-content">
                &nbsp;
            </div>
            <div class="card-body bg-base-100 text-base-content">
                <div class="w-full">
                    <LineChart ref="chartRef" :chartData="chartData" :options="options" />
                </div>
            </div>
    </div>
</template>
<script>

</script>
<script setup>
import { LineChart } from 'vue-chart-3';
import Chart from 'chart.js/auto'
import {computed, ref} from "vue";


const props = defineProps({
    data: [Object, Array, null],
    datalet: [Object, null]
});

const chartRef = ref();
const options = ref({
    responsive: true,
    plugins: {
        legend: {
            display: false,
        },
        title: {
            display: true,
            text: props.datalet.label,
        },
    },
});

const chartData = computed(() => ({
    labels: props.data.labels,
    datasets: [
        {
            data: props.data.data,

        },
    ],
}));


</script>
