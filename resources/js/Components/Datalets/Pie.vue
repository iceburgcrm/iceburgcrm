<template>

    <div class="card w-full bg-base-100 shadow-xl">
        <div class="card-title bg-accent text-primary-content">
            &nbsp;
        </div>
        <div class="card-body bg-base-100 text-base-content">
            <div class="w-full">
                <PieChart ref="chartRef" :chartData="chartData" :options="options" />
            </div>
        </div>


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
            backgroundColor: ['#583122', '#895637', '#aec2cb', '#571719'],
        },
    ],
}));


</script>
