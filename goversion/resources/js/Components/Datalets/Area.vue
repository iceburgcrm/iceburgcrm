<template>
    <div class="card w-full bg-base-100 mb-10 shadow-xl">
        <div class="card-title bg-secondary text-secondary-content">
            &nbsp;
        </div>
        <div class="card-body bg-base-100 text-base-content">
            <div class="w-full">
                <PolarAreaChartChart ref="chartRef" :chartData="chartData" :options="options" />
            </div>
        </div>
    </div>
</template>
<script setup>
import { PolarAreaChart } from 'vue-chart-3';
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
            backgroundColor: ['#77CEFF', '#0079AF', '#123E6B', '#97B0C4'],
        },
    ],
}));

</script>
