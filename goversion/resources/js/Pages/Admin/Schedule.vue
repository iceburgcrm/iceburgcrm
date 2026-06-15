<template>
    <Head title="Scheduler" />
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">Scheduler</h2>
        </template>

        <BreadCrumbs :levels="$page.props.breadcrumbs" />

        <div class="max-w-full sm:px-3 lg:px-4 mt-10">
            <div class="bg-base-200 p-6">
                <div class="overflow-x-auto bg-base-100">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in schedule" :key="item.id">
                                <td>{{ item.name }}</td>
                                <td>{{ item.status }}</td>
                                <td>{{ item.updated_at || item.created_at }}</td>
                            </tr>
                            <tr v-if="!schedule.length">
                                <td colspan="3">No scheduled jobs</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import BreadCrumbs from '@/Components/BreadCrumbs';
import { Head, usePage } from '@inertiajs/inertia-vue3';
import { computed } from 'vue';

const schedule = computed(() => usePage().props.value.schedule || []);
</script>
