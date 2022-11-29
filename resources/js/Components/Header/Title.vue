<template>
    <h1 class="font-semibold text-xl text-base-content pt-4 pr-5">
        <BaseIcon class_style="h-8 w-8 " :name="props.module.icon" />
        {{props.module.label}} {{ props.page_description }}
    </h1>

</template>
<script setup>
import BaseIcon from "@/Icons/BaseIcon";

const props = defineProps({
    module: [Object, null],
    page_description: [String, null]
});
</script>
