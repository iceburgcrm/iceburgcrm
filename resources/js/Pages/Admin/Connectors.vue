<template>
    <Head title="Connectors" />
    <BreezeAuthenticatedLayout>

        <template #header>
            <h2 class="font-semibold text-xl text-base-content leading-tight">
                Connectors
            </h2>
        </template>

        <BreadCrumbs :levels="$page.props.breadcrumbs" />

        <div class="w-full bg-base-100">
            <div class="bg-base-100 mt-10 text-base-content max-w-full sm:px-3 lg:px-4">
                <!-- Add a link to create a new connector -->
                <div class="mb-4">
                    <a class="btn btn-primary" :href="`/admin/connector`">New Connector</a>
                </div>

                <table class="bg-base-200 text-base-content table table-zebra table-compact lg:table-normal w-full border-secondary border-solid">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Auth Type</th>
                        <th>Base URL</th>
                        <th>Client ID</th>
                        <th>Actions</th>
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
        formData.append('status', event.target.value); // Assuming you are getting the status from the event
        formData.append('id', id);

        await axios.post('/data/connector/set_connector', formData);

        const response = await axios.get('/data/connectors');
        connectors.value = response.data.connectors;

        // Handle success alert
        // For example:
        // data.success_alert = true;
        // setTimeout(() => data.success_alert = false, 2000);
    } catch (error) {
        console.error("Error setting connector status", error);

        // Handle error alert
        // For example:
        // data.error_alert = true;
        // data.alert_text = "There was an error saving your auth key";
        // setTimeout(() => {
        //     data.error_alert = false;
        //     data.alert_text = '';
        // }, 5000);
    }
};
</script>
