<template>

    <div class="card w-full bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title">{{ props.datalet.label }} {{ props.datalet.type.name }}
                {{props.datalet.module ? props.datalet.module.label : ''}}</h2>

            <div class="w-full">
                <div class="carousel w-full">
                    <div id="item1" class="carousel-item w-full">
                        <img src="https://placeimg.com/800/200/arch" class="w-full" />
                    </div>
                    <div id="item2" class="carousel-item w-full">
                        <img src="https://placeimg.com/800/200/arch" class="w-full" />
                    </div>
                    <div id="item3" class="carousel-item w-full">
                        <img src="https://placeimg.com/800/200/arch" class="w-full" />
                    </div>
                    <div id="item4" class="carousel-item w-full">
                        <img src="https://placeimg.com/800/200/arch" class="w-full" />
                    </div>
                </div>
                <div class="flex justify-center w-full py-2 gap-2">
                    <a href="#item1" class="btn btn-xs">1</a>
                    <a href="#item2" class="btn btn-xs">2</a>
                    <a href="#item3" class="btn btn-xs">3</a>
                    <a href="#item4" class="btn btn-xs">4</a>
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
    data: [Object, null],
    datalet: [Object, null]
});


</script>
