<template>

    <div class="card bg-base-100 shadow-xl align-center text-center">

        <div class="card-title bg-accent text-primary-content">
            &nbsp
        </div>
        <div class="card-body bg-base-100 text-base-content">
            <div class="w-full">
                <div class="card-body bg-base-100 text-base-content">
                    <div class="stats stats-vertical lg:stats-horizontal shadow">

                        <div class="stat">
                            <div class="stat-title">Latest Meeting</div>
                            <div class="stat-value">{{ props.data[0].name }}</div>
                            <div class="stat-desc">{{ props.data[0].description }}</div>
                        </div>

                        <div class="stat">
                            <div class="stat-title">{{ props.data[0].location }}</div>
                            <div class="stat-desc">Start Time: {{ moment(props.data[0].start_time).format('MMMM Do YYYY, h:mm:ss a') }}</div>
                            <div class="stat-desc">End Time: {{ moment(props.data[0].end_time).format('MMMM Do YYYY, h:mm:ss a') }}</div>
                        </div>


                        <div class="stat">
                            <div class="stat-desc text-center ml-10">
                                <audio controls width="100%">
                                    <source :src="props.data[0].audio_recording" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                            </div>
                        </div>

                    </div>
                </div>

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
import moment from 'moment';

const props = defineProps({
    data: [Object, Array, null],
    datalet: [Object, null]
});

console.log(props.data);
</script>
