<template>
    <Head :title="$t('page.connectors')" />
    <BreezeAuthenticatedLayout>

        <template #header>
            <h2 class="font-semibold text-xl text-base-content leading-tight">
                {{ $t('page.connectors') }}
            </h2>
        </template>

        <BreadCrumbs :levels="$page.props.breadcrumbs" />

        <div class="w-full bg-base-100">
            <div class="bg-base-100 mt-10 text-base-content max-w-full sm:px-3 lg:px-4">
                <div class="mb-4">
                    <a class="btn btn-primary" :href="`/admin/connector`">{{ $t('page.newconnector') }}</a>
                </div>

                <table class="bg-base-200 text-base-content table table-zebra table-compact lg:table-normal w-full border-secondary border-solid">
                    <thead>
                    <tr>
                        <th>{{ $t('page.name') }}</th>
                        <th>{{ $t('page.authtype') }}</th>
                        <th>{{ $t('page.baseurl') }}</th>
                        <th>{{ $t('page.clientid') }}</th>
                        <th>{{ $t('page.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="connector in connectors" :key="connector.id">
                        <td>
                            <span class="text-lg">{{ connector.name }}</span>
                        </td>
                        <td>
                            {{ connector.auth_type }}
                        </td>
                        <td>
                            {{ connector.base_url }}
                        </td>
                        <td>
                            {{ connector.client_id || 'N/A' }}
                        </td>
                        <td>
                            <a class="btn btn-sm btn-primary" :href="`/admin/connector/${connector.id}`">Details</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script setup>
import { ref } from "vue";
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head, usePage } from '@inertiajs/inertia-vue3';
import BreadCrumbs from "@/Components/BreadCrumbs";

const connectors = ref(usePage().props.value.connectors);

console.log(connectors.value);

const setConnectorStatus = async (id, event) => {
    event.preventDefault();

    try {
        const formData = new FormData();
        formData.append('status', event.target.value);
        formData.append('id', id);

        await axios.post('/data/connector/set_connector', formData);

        const response = await axios.get('/data/connectors');
        connectors.value = response.data.connectors;


    } catch (error) {
        console.error("Error setting connector status", error);
    }
};
</script>
