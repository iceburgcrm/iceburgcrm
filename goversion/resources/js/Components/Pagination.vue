<template>
<div v-if="links.length > 3">
<div class="flex flex-wrap -mb-1">
    <template v-for="(link, p) in props.links" :key="p">
        <div v-if="p == props.page" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-primary-content border rounded"
             v-html="link.label" :class="{ 'bg-primary text-base-100': link.active }"  />

        <a v-else @click="click_link" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-base=100 focus:border-accent focus:text-accent-content"
           :class="{ 'bg-primary text-base-100': link.active }" :href="link.url"  v-html="link.label"></a>
    </template>
</div>
</div>
</template>
<script setup>
import { defineEmits, ref, onMounted, watch } from 'vue';

const props = defineProps({
    links: [Object, null],
    page: [Number, null],
});

const emit = defineEmits(['pagevalue']);

const click_link = (e) => {
    e.preventDefault();
    emit('pagevalue', e.target.getAttribute('href').split('=')[1]);
}
</script>
