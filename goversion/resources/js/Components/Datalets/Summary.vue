<template>

    <div class="card bg-base-100 shadow-xl align-center text-center">

        <div class="card-title bg-accent text-primary-content">
            &nbsp Latest
        </div>
        <div class="card-body bg-base-100 text-base-content">
            <div class="w-full">
                <div class="col-span-3 row-span-3 mx-2 flex w-72 flex-shrink-0 flex-col xl:mx-0 xl:w-full svelte-1n6ue57">
                    <div class="bg-base-100 grid w-full flex-grow gap-3 rounded-xl rounded-tl-none p-6 shadow-xl">

                        <div class="text-lg font-extrabold">Summary Report (Total Records)</div>
                        <div class="grid gap-3">
                            <div class="dropdown dropdown-top">
                                <div tabindex="0">
                                    <div v-for="data in props.data" class="flex items-center p-1"><span class="text-base-content/70 w-48 text-xs">{{data.name}}</span> <progress max="100" :value="data.value" :class="`progress progress-${data.class}`"></progress></div>
                                 </div>
                                <div tabindex="0" class="dropdown-content py-2">
                                    <div class="card compact bg-neutral-focus text-neutral-content rounded-box w-72 shadow-xl">
                                        <div class="card-body">
                                            <h2 class="card-title font-extrabold capitalize">progress component</h2>
                                            <p class="text-neutral-content text-sm text-opacity-80">Show progressbar, loadings or simple bar charts using progress component</p>
                                            <div class="mt-4 flex justify-end"><a href="/components/progress" class="btn btn-primary btn-sm xl:btn-md">See component</a></div>
                                        </div>
                                    </div>
                                </div>
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

const props = defineProps({
    data: [Object, Array, null],
    datalet: [Object, null]
});

</script>
