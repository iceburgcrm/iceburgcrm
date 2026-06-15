<template>
    <div z-index="500" v-if="is_successful" class="btm-nav rounded-none alert alert-success shadow-lg">
        <div>
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>{{ message }}</span>
        </div>
    </div>
    <div z-index="500" v-if="is_error" class="btm-nav rounded-none alert alert-error shadow-lg">
        <div>
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>Error! {{ message }}</span>
        </div>
    </div>
</template>
<script setup>
const props = defineProps({
    message: [String, Number, null],
    is_successful: [String, Number, Boolean, Object, null],
    is_error: [String, Number, Boolean, null]
});
</script>
