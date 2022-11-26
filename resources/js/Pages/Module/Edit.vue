<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head } from '@inertiajs/inertia-vue3';
</script>

<template>
    <Head title="Module" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit {{$page.props.module.label}}


            </h2>
            <a class="p-2 shadow bg-base-100 rounded-box" :href="`/import?from_module_id=${$page.props.module.id}`" method="get" as="button">
                Import Records
            </a>
            <a :href="`/module/${$page.props.module.name}/add`" method="get" as="button">
                Add Record dd
            </a>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="grid grid-col-2">
                            <div class="col-span-1 col-gap-2 lg:col-span-1" v-for="field in $page.props.module.fields">
                                <div class="flex items-center -mx-3">
                                    <div class="flex-1">
                                        <label for="search" class="block text-sm font-medium leading-5 text-gray-700">
                                            {{ field.label }}
                                        </label>
                                        <div class="mt-1 relative rounded-md shadow-sm">

                                            <input id="search" type="text" :value="$page.props.record[field.name]" class="form-input sm:text-sm sm:leading-5" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
