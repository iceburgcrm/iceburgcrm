<template>
    <Head title="Module" />

    <BreezeAuthenticatedLayout>
            <template #header>
                <div class="grid grid-flow-col">
                    <div class="col-span-8">
                       <Title :module="$page.props.module" page_description="Details" />
                    </div>
                    <div class="col-span-4">
                        <HeadButtons
                            class="col-span-2"
                            :module="$page.props.module"
                            :permissions="$page.props.permissions"
                            :allowed="['import', 'add', 'export', 'edit', 'delete', 'convert_to', 'audit_log']"
                            :record="$page.props.record"
                        />
                    </div>
                </div>
            </template>



    <BreadCrumbs :levels="$page.props.breadcrumbs" />



        <div class="max-w-full grid col-end-auto col-auto place-items-end">
            <div>
                <label class="input-group">
                    <a  class="btn btn-ghost" v-if="$page.props.previous" :href="`/module/${$page.props.module.name}/view/${$page.props.previous}`" method="get" as="button">
                        Previous
                    </a>
                    <a  class="btn btn-ghost" v-if="$page.props.next" :href="`/module/${$page.props.module.name}/view/${$page.props.next}`" method="get" as="button">
                        Next
                    </a>
                </label>
            </div>
        </div>

        <div class="max-w-full sm:px-3 lg:px-4">
            <div class="max-w-full bg-primary shadow-sm sm:rounded-lg">
                <div class="p-6 bg-base-200 text-base-content border-b border-gray-200">
                    <div class="">
                        <div class="grid grid-col-1 md:grid-cols-3 gap-4">
                            <div class="card shadow-xl bg-base-100"  v-for="field in $page.props.fields">
                                <div class="card-body h-100 w-200">
                                    <h2 class="card-title font-light text-xs">{{field.label}}</h2>

                                    <Display class="font-medium"
                                        v-bind:data_value="$page.props.record[field.name]"
                                        v-bind:record="$page.props.record"
                                        v-bind:field="field"
                                        v-bind:remove_links="1"
                                        v-bind:field_data="$page.props.field_data[field.name]"
                                    />
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
        <div class="max-w-full grid col-end-auto col-auto place-items-end">
            <div>
                <label class="input-group">
                    <a  class="btn btn-ghost" v-if="$page.props.previous" :href="`/module/${$page.props.module.name}/view/${$page.props.previous}`" method="get" as="button">
                        Previous
                    </a>
                    <a  class="btn btn-ghost" v-if="$page.props.next" :href="`/module/${$page.props.module.name}/view/${$page.props.next}`" method="get" as="button">
                        Next
                    </a>
                </label>
            </div>
        </div>

        <div v-if="$page.props.workflow.length > 0" class="pt-5 pb-5 max-w-full sm:px-3 lg:px-4 bg-base-200 text-base-content rounded  shadow-sm m-5" >
            <div class="card shadow-xl bg-base-100 text-base-content  border-gray-200">
                <div class="card-body">
                    <h2 class="card-title mb-5"><a :id="label">Workflow</a></h2>
                    <div class="grid grid-flow-row">
                        <div class="align-center">
                            <ul class="steps steps-vertical w-full">
                                <li :class="item.className" v-for="item in $page.props.workflow">
                                <span v-if="item.from_data">
                                    <a v-if="!item.current" class="underline" :href="`/module/${item.from_data.module_name}/view/${item.from_data.link_id}`">{{item.from_data.module_label}}+</a>
                                    <span class="font-bold" v-else>{{item.from_data.module_label}}</span>
                                </span>
                                <span v-else>
                                    {{item.primary_module.label}}
                                </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="max-w-full grid col-auto">
            <div class="p-5" v-for="subpanel_id in $page.props.subpanel_ids">
                <Subpanel
                    :record_id="$page.props.record.id"
                    :module_id="$page.props.module.id"
                    v-bind:id="subpanel_id"
                    :permissions="$page.props.permissions"
                />
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Display from '@/Components/Fields/Display';
import Subpanel from "@/Pages/Subpanel";
import BreadCrumbs from "@/Components/BreadCrumbs";
import HeadButtons from "@/Components/Header/Buttons";
import Title from "@/Components/Header/Title";
</script>
