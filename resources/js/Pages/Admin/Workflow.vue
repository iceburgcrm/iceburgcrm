<template>
  <BreezeAuthenticatedLayout>
    <Head title="Workflow Builder" />

    <template #header>
      <h2 class="font-semibold text-xl text-base-content leading-tight">
        Workflow Builder
        <HelpTooltip slug="workflow" position="bottom-right"/>
      </h2>
    </template>

    <BreadCrumbs :levels="$page.props.breadcrumbs" />

    <div class="w-full bg-base-100">
      <div class="bg-base-100 mt-10 text-base-content max-w-full sm:px-3 lg:px-4">

        <Alert :message="alert.alert_text" :is_successful="alert.success_alert" :is_error="alert.error_alert" />

        <!-- Add new workflow row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-4">
          <select v-model="selectedPrimary" class="input select-secondary w-full">
            <option disabled value="">Select Primary Module</option>
            <option v-for="mod in availablePrimaryModules" :key="mod.id" :value="mod.id">
              {{ mod.name }}
            </option>
          </select>

          <select v-model="selectedModule" class="input select-secondary w-full">
            <option disabled value="">Select Module to Convert To</option>
            <option v-for="mod in availableConvertModules" :key="mod.id" :value="mod.id">
              {{ mod.name }}
            </option>
          </select>

          <button class="btn btn-primary w-full" @click="addRow" :disabled="!selectedPrimary || !selectedModule">
            Add
          </button>
        </div>

        <!-- Workflow table -->
        <table class="table table-zebra table-compact lg:table-normal w-full bg-base-200 border border-base-content">
          <thead>
            <tr>
              <th>Primary Module</th>
              <th>Convert To Module</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(row, index) in workflow" :key="row.id ?? index">
              <td>{{ row.primary_module_name }}</td>
              <td>
                <select
                  class="input select-secondary w-full"
                  v-model="row.module_id"
                  @change="updateModule(row)"
                >
                  <option disabled value="">Select module</option>
                  <option v-for="mod in getAvailableConvertModules(row)" :key="mod.id" :value="mod.id">
                    {{ mod.name }}
                  </option>
                </select>
              </td>
              <td class="space-x-2">
                <button class="btn btn-sm btn-outline" @click="moveUp(index)" :disabled="index === 0">↑</button>
                <button class="btn btn-sm btn-outline" @click="moveDown(index)" :disabled="index === workflow.length - 1">↓</button>
                <button class="btn btn-sm btn-error" @click="removeRow(index)">✕</button>
              </td>
            </tr>
          </tbody>
        </table>

      </div>
    </div>
  </BreezeAuthenticatedLayout>
</template>

<script setup>
import { ref, computed, reactive } from 'vue'
import { Head, usePage } from '@inertiajs/inertia-vue3'
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue'
import axios from 'axios'
import BreadCrumbs from '@/Components/BreadCrumbs'
import Alert from '@/Components/Alert'
import HelpTooltip from '@/Components/HelpTooltip.vue';

const page = usePage()

const workflow = ref([...page.props.value.workflow])
const modulesList = page.props.value.modules

const selectedPrimary = ref('')
const selectedModule = ref('')

const alert = reactive({
  success_alert: 0,
  error_alert: 0,
  alert_text: ''
})

// Available primary modules for adding new workflow row
const availablePrimaryModules = computed(() =>
  modulesList.filter(m => !workflow.value.some(w => w.primary_module_id === m.id))
)

// Available convert-to modules for a specific row
const getAvailableConvertModules = (row) => {
  return modulesList.filter(m => 
    m.id !== row.primary_module_id || m.id === row.module_id
  )
}

// Available convert-to modules for the add-new-row dropdown
const availableConvertModules = computed(() =>
  modulesList.filter(m => m.id !== selectedPrimary.value)
)

// Auto-save workflow
const saveWorkflow = () => {
  const payload = workflow.value.map((w, index) => ({
    id: w.id,
    primary_module_id: w.primary_module_id,
    module_id: w.module_id,
    level: index + 1
  }))

  axios.post('/data/workflow/save', { workflow: payload })
    .then(() => console.log("Workflow saved"))
    .catch(err => {
      console.error("Error saving workflow:", err.response?.data || err)
      alert.error_alert = 1
      alert.alert_text = 'Error saving workflow'
      setTimeout(() => { alert.error_alert = 0; alert.alert_text = '' }, 5000)
    })
}

// Move row up/down
const moveUp = (index) => {
  if (index === 0) return
  [workflow.value[index-1], workflow.value[index]] = [workflow.value[index], workflow.value[index-1]]
  saveWorkflow()
}

const moveDown = (index) => {
  if (index === workflow.value.length-1) return
  [workflow.value[index], workflow.value[index+1]] = [workflow.value[index+1], workflow.value[index]]
  saveWorkflow()
}

// Remove row
const removeRow = (index) => {
  workflow.value.splice(index, 1)
  saveWorkflow()
}

// Add new row
const addRow = () => {
  const primary = modulesList.find(m => m.id === selectedPrimary.value)
  const module = modulesList.find(m => m.id === selectedModule.value)
  if (primary && module) {
    workflow.value.push({
      id: null,
      primary_module_id: primary.id,
      primary_module_name: primary.name,
      module_id: module.id,
      module_name: module.name,
      level: workflow.value.length + 1
    })
    selectedPrimary.value = ''
    selectedModule.value = ''
    saveWorkflow()
  }
}

// Update convert-to module
const updateModule = (row) => {
  const module = modulesList.find(m => m.id === row.module_id)
  if (module) {
    row.module_name = module.name
    saveWorkflow()
  }
}
</script>
