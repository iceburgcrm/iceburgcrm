

<template>

    <div class="card bg-base-100 shadow-xl align-center text-center">

        <div class="card-title bg-accent text-primary-content">
        &nbsp;
        </div>
        <div class="card-body bg-base-100 text-base-content">
            <h1 class="text-center font-bold">CRM Summary</h1>
            <div class="stats stats-vertical lg:stats-horizontal shadow border">

                <div class="stat">

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>

                    <div class="stat-title">Modules</div>
                    <div class="stat-value"><a class=" underline-offset-2" href="/module/ice_modules">{{  props.data.modules }}</a></div>

                </div>

                <div class="stat">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>

                    <div class="stat-title">Fields</div>
                    <div class="stat-value"><a href="/module/ice_fields">{{  props.data.fields }}</a></div>
                </div>

                <div class="stat">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                    </svg>

                    <div class="stat-title">Subpanels</div>
                    <div class="stat-value"><a href="/module/ice_module_subpanels">{{  props.data.subpanels }}</a></div>
                </div>

                <div class="stat">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                    </svg>

                    <div class="stat-title">Relationships</div>
                    <div class="stat-value"><a href="/module/ice_relationships">{{  props.data.relationships }}</a></div>
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
