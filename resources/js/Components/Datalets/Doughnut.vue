<template>
        <div class="card w-full  shadow-xl">
            <div class="card-title bg-primary text-primary-content">
                &nbsp;
            </div>
            <div class="card-body bg-base-100 text-base-content">

                    <div class="w-full">
                        <DoughnutChart ref="chartRef" :chartData="chartData" :options="options" />
                    </div>


            </div>
        </div>
</template>
<script>
const global_window = window;
</script>
<script setup>
import { DoughnutChart } from 'vue-chart-3';
import Chart from 'chart.js/auto'
import {computed, ref, onMounted} from "vue";

const props = defineProps({
    data: [Object, Array, null],
    datalet: [Object, null]
});

const chartRef = ref();
const options = ref({
    responsive: true,
    plugins: {
        legend: {
            position: 'top',
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
