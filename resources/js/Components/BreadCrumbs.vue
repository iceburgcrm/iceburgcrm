<template>
    <div class=" sm:px-3 lg:px-4 ml-2 text-sm breadcrumbs">
        <ul>
            <li v-for="level in props.levels">
                <a v-if="level.url" valign="baseline" :href="level.url">
                    <span v-html="getSVG(level.svg)"></span>
                    {{level.name}}
                </a>
                <a class="font-weight-bold hover:no-underline" v-else>
                    <span v-html="getSVG(level.svg)"></span>
                    {{level.name}}
                </a>
            </li>
        </ul>
    </div>
</template>
<script setup>
import { computed } from 'vue';

const parser = new DOMParser();

const props  = defineProps({
            'levels': [Object, null]
            });


const getSVG = function(name) {
    return eval(name);
}

const home = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-4 h-4 mr-2 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>';
const settings = '<svg class="mr-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>';
</script>
