<template>
    <Head title="Dashboard" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-base-content leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="bg-base-200">

                    <div class="bg-base-200 border-b border-neutral">
                        <div class="grid grid-col-flow gap-5">
                            <div v-for="datalet in $page.props.datalets">
                                <Datalet v-if="datalet.active == 1" :datalet="datalet" />
                            </div>
                        </div>

                    </div>

        </div>
    </BreezeAuthenticatedLayout>
</template>
<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head, usePage } from '@inertiajs/inertia-vue3';
import Datalet from '@/Components/Datalet.vue';
</script>
