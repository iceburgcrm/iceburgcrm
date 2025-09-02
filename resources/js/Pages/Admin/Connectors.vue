<template>
  <Head :title="$t('page.connectors')" />
  <BreezeAuthenticatedLayout>

    <template #header>
      <h2 class="font-semibold text-xl text-base-content leading-tight">
        {{ $t('page.connectors') }}
      </h2>
    </template>

    <BreadCrumbs :levels="$page.props.breadcrumbs" />

    <div class="w-full bg-base-100 mt-10 text-base-content max-w-full sm:px-3 lg:px-4 pb-10">
      <div class="mb-4">
        <a class="btn btn-primary" :href="`/admin/connector`">New Connector</a>
      </div>

     
     
      <!-- Tab Panels -->
      <div class="mt-4 border border-base-300 rounded-lg p-4 bg-base-200">
        <table class="table table-zebra table-compact w-full">
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
            <tr
              v-for="connector in connectors"
              :key="connector.id"
            >
              <td><span class="font-semibold">{{ connector.name }}</span></td>
              <td>{{ connector.auth_type }}</td>
              <td>{{ connector.base_url }}</td>
              <td>{{ connector.client_id || 'N/A' }}</td>
              <td>
                <a class="btn btn-xs btn-primary" :href="`/admin/connector/${connector.id}`">Details</a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </BreezeAuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head, usePage } from '@inertiajs/inertia-vue3';
import BreadCrumbs from "@/Components/BreadCrumbs";

const connectors = ref(usePage().props.value.connectors);



</script>
