<template>
    <div class="card lg:card-side bg-base-100 shadow-xl">
        <div class="card-body">
            <p>
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th v-for="field in fields">{{field}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td v-for="item in row">{{item}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </p>
        </div>
    </div>
</template>
<script setup>
import axios from "axios";
import { ref, toRef, toRefs, computed, toRaw, watch, onMounted, reactive } from 'vue';
const props = defineProps({
    fields: [Object, Array, null],
    row: [Object, Array, null]
});
</script>
