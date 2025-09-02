<template>
  <Head :title="`Connector ${connector.name || 'New'}`" />
  <BreezeAuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-base-content leading-tight">
        {{ connector.name ? `Connector: ${connector.name}` : 'Create New Connector' }}
      </h2>
    </template>

    <BreadCrumbs :levels="$page.props.breadcrumbs" />

    <!-- Tabs -->
    <div class="tabs mt-6 mb-6">
      <a class="tab tab-bordered h-10 leading-10"
         :class="{'tab-active': activeTab==='details'}"
         @click="activeTab='details'">Details</a>
      <a class="tab tab-bordered h-10 leading-10"
         :class="{'tab-active': activeTab==='endpoints'}"
         @click="activeTab='endpoints'">Endpoints</a>
      <a class="tab tab-bordered h-10 leading-10"
         :class="{'tab-active': activeTab==='commands'}"
         @click="activeTab='commands'">Commands</a>
    </div>

    <!-- Details Tab -->
    <div v-show="activeTab==='details'" class="bg-base-100 p-4 rounded shadow">
      <div v-if="!editingConnector && connector.id">
        <!-- Read-only view for existing connector -->
        <h3 class="font-bold text-lg">{{ connector.name }}</h3>
        <p>Class: {{ connector.class }}</p>
        <p>Auth Type: {{ connector.auth_type }}</p>
        <p>Base URL: {{ connector.base_url }}</p>
        <p>Auth Key: {{ connector.auth_key }}</p>
        <p>Status: <span :class="connector.status ? 'text-green-600' : 'text-red-600'">{{ connector.status ? 'Active' : 'Inactive' }}</span></p>
        <button class="btn btn-sm btn-primary mt-2" @click="editingConnector=true">Edit Connector</button>
      </div>

      <div v-else>
        <!-- Form for new or editing connector -->
        <div class="form-control mb-4">
          <label class="label"><span class="label-text">Name</span></label>
          <input type="text" v-model="connector.name" class="input input-bordered w-full" required />
        </div>
        <div class="form-control mb-4">
          <label class="label"><span class="label-text">Class</span></label>
          <input type="text" v-model="connector.class" class="input input-bordered w-full" required />
        </div>
        <div class="form-control mb-4">
          <label class="label"><span class="label-text">Auth Type</span></label>
          <select v-model="connector.auth_type" class="select select-bordered w-full">
            <option value="">Select Auth Type</option>
            <option value="OAuth2">OAuth2</option>
            <option value="API Key">API Key</option>
            <option value="Basic Auth">Basic Auth</option>
            <option value="None">None</option>
          </select>
        </div>

        <div v-if="connector.auth_type==='OAuth2'">
          <div class="form-control mb-4">
            <label class="label"><span class="label-text">Client ID</span></label>
            <input type="text" v-model="connector.client_id" class="input input-bordered w-full" />
          </div>
          <div class="form-control mb-4">
            <label class="label"><span class="label-text">Client Secret</span></label>
            <input type="text" v-model="connector.client_secret" class="input input-bordered w-full" />
          </div>
          <div class="form-control mb-4">
            <label class="label"><span class="label-text">Token URL</span></label>
            <input type="text" v-model="connector.token_url" class="input input-bordered w-full" />
          </div>
          <div class="form-control mb-4">
            <label class="label"><span class="label-text">Refresh Token</span></label>
            <input type="text" v-model="connector.refresh_token" class="input input-bordered w-full" />
          </div>
        </div>

        <div v-if="connector.auth_type==='Basic Auth'">
          <div class="form-control mb-4">
            <label class="label"><span class="label-text">Username</span></label>
            <input type="text" v-model="connector.username" class="input input-bordered w-full" />
          </div>
          <div class="form-control mb-4">
            <label class="label"><span class="label-text">Password</span></label>
            <input type="text" v-model="connector.password" class="input input-bordered w-full" />
          </div>
        </div>

        <div class="form-control mb-4">
          <label class="label"><span class="label-text">Base URL</span></label>
          <input type="text" v-model="connector.base_url" class="input input-bordered w-full" />
        </div>

        <div class="form-control mb-4">
          <label class="label"><span class="label-text">Auth Key</span></label>
          <input type="text" v-model="connector.auth_key" class="input input-bordered w-full" />
        </div>

        <div class="form-control mb-4">
          <label class="label"><span class="label-text">Token Expiry</span></label>
          <input type="text" :value="connector.token_expires_at" disabled class="input input-bordered w-full" />
        </div>

        <div class="form-control mb-4">
          <label class="label"><span class="label-text">Active</span></label>
          <input type="checkbox" v-model="connector.status" class="toggle toggle-success" />
        </div>

        <div class="mt-4">
          <button @click="save_connector" class="btn btn-success">{{ connector.id ? 'Save Connector' : 'Add Connector' }}</button>
          <button v-if="connector.id" @click="delete_connector" class="btn btn-error ml-2">Delete Connector</button>
          <button @click="editingConnector=false" class="btn btn-sm ml-2">Cancel</button>
        </div>
      </div>
    </div>

    <!-- Endpoints Tab -->
    <div v-show="activeTab==='endpoints'" class="bg-base-100 p-4 rounded shadow">
      <div class="flex items-center justify-between mb-4">
        <h4 class="text-lg font-semibold">Endpoints</h4>
        <button @click="add_endpoint()" class="btn btn-primary">Add Endpoint</button>
      </div>
      <div v-for="(endpoint,index) in endpoints" :key="endpoint.id" class="p-4 mb-4 border rounded">
        <div class="mb-2">
          <label v-if="endpoint.editing">Name</label>
          <input v-if="endpoint.editing" v-model="endpoint.name" class="input input-bordered w-full"/>
          <div v-else class="font-bold">Name: {{ endpoint.name }}</div>
        </div>
        <div class="mb-2">
          <label v-if="endpoint.editing">Endpoint</label>
          <input v-if="endpoint.editing" v-model="endpoint.endpoint" class="input input-bordered w-full"/>
          <div v-else>Endpoint: {{ endpoint.endpoint }}</div>
        </div>
        <div class="mb-2">
          <label v-if="endpoint.editing">Request Type</label>
          <select v-if="endpoint.editing" v-model="endpoint.request_type" class="select select-bordered w-full">
            <option value="GET">GET</option>
            <option value="POST">POST</option>
            <option value="PUT">PUT</option>
            <option value="DELETE">DELETE</option>
          </select>
          <div v-else>Request Type: {{ endpoint.request_type }}</div>
        </div>
        <div class="mb-2">
          <label v-if="endpoint.editing">Class Name</label>
          <input v-if="endpoint.editing" v-model="endpoint.class_name" class="input input-bordered w-full"/>
          <div v-else>Class: {{ endpoint.class_name }}</div>
        </div>
        <div class="mb-2">
          <label v-if="endpoint.editing">Params (JSON)</label>
          <textarea v-if="endpoint.editing" v-model="endpoint.params" class="textarea textarea-bordered w-full"></textarea>
          <div v-else>Params: <pre>{{ endpoint.params }}</pre></div>
        </div>
        <div class="mb-2">
          <label v-if="endpoint.editing">Headers (JSON)</label>
          <textarea v-if="endpoint.editing" v-model="endpoint.headers" class="textarea textarea-bordered w-full"></textarea>
          <div v-else>Headers: <pre>{{ endpoint.headers }}</pre></div>
        </div>
        <div class="mb-2">
          <label>Status:</label>
          <input type="checkbox" v-model="endpoint.status" class="toggle toggle-success" />
        </div>
        <div class="mt-2">
          <div v-if="endpoint.editing">
            <button @click="save_endpoint(endpoint)" class="btn btn-success btn-sm">Save</button>
            <button @click="cancel_edit_endpoint(endpoint,index)" class="btn btn-warning btn-sm ml-2">Cancel</button>
          </div>
          <div v-else>
            <button @click="edit_endpoint(endpoint)" class="btn btn-primary btn-sm">Edit</button>
            <button @click="delete_endpoint(endpoint.id,index)" class="btn btn-error btn-sm ml-2">Delete</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Commands Tab -->
    <div v-show="activeTab==='commands'" class="bg-base-100 p-4 rounded shadow">
      <div class="flex items-center justify-between mb-4">
        <h4 class="text-lg font-semibold">Commands</h4>
        <button @click="add_command()" class="btn btn-primary">Add Command</button>
      </div>

      <div v-for="(command,index) in connector.commands" :key="command.id" class="p-4 mb-4 border rounded">
        <!-- Name -->
        <div class="mb-2">
          <label v-if="command.editing">Name</label>
          <input v-if="command.editing" v-model="command.name" class="input input-bordered w-full"/>
          <div v-else class="font-bold">Name: {{ command.name }}</div>
        </div>
        <!-- Method Name -->
        <div class="mb-2" v-if="command.editing">
          <label>Method Name</label>
          <input v-model="command.method_name" class="input input-bordered w-full"/>
        </div>
        <div v-else>
          <div class="font-bold">Method: {{ command.method_name }}</div>
        </div>
        <!-- Endpoint -->
        <div class="mb-2" v-if="command.editing">
          <label>Endpoint</label>
          <select v-model="command.endpoint_id" class="select select-bordered w-full">
            <option value="">-- None --</option>
            <option v-for="ep in endpoints" :key="ep.id" :value="ep.id">{{ ep.endpoint }}</option>
          </select>
        </div>
        <div v-else-if="command.endpoint_id">
          Endpoint: {{ endpoints.find(ep=>ep.id===command.endpoint_id)?.name || 'Unknown' }}
        </div>
        <!-- Description -->
        <div class="mb-2">
          <label v-if="command.editing">Description</label>
          <textarea v-if="command.editing" v-model="command.description" class="textarea textarea-bordered w-full"></textarea>
          <div v-else>{{ command.description }}</div>
        </div>
        <!-- Run & Status (only for saved commands) -->
        <template v-if="!command.editing && command.id">
          <div class="mb-2">
            <button @click="run_command(command.id)" class="btn btn-success btn-sm">Run</button>
            <span class="ml-4">Last Ran: {{ command.last_ran }}</span>
            <span class="ml-4">Last Data: {{ command.last_run_data }}</span>
          </div>
          <div class="mb-2">
            <label>Status:</label>
            <input type="checkbox" v-model="command.status" class="toggle toggle-success" />
          </div>
        </template>
        <!-- Action Buttons -->
        <div class="mt-2">
          <div v-if="command.editing">
            <button @click="save_command(command.id)" class="btn btn-success btn-sm">Save</button>
            <button @click="cancel_edit(command)" class="btn btn-warning btn-sm ml-2">Cancel</button>
          </div>
          <div v-else>
            <button @click="edit_command(command)" class="btn btn-primary btn-sm">Edit</button>
            <button @click="delete_command(command.id,index)" class="btn btn-error btn-sm ml-2">Delete</button>
          </div>
        </div>
      </div>
    </div>

  </BreezeAuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { Head, usePage } from '@inertiajs/inertia-vue3';
import axios from 'axios';
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import BreadCrumbs from '@/Components/BreadCrumbs';

const connector = ref(usePage().props.value.connector || { commands: [] });
const activeTab = ref('details');
const editingConnector = ref(false);
const endpoints = ref([]);
const data = reactive({ success_alert:'', error_alert:'', alert_text:'' });
const headers = { 'Content-Type': 'multipart/form-data' };

onMounted(() => {
  if (connector.value.id) {
    axios.get(`/data/endpoints/${connector.value.id}`)
      .then(res => { endpoints.value = res.data.map(e=>({...e, editing:false})); });
  }
});

// Save connector
const save_connector = () => {
  if (!connector.value.name) {
    alert('The name field is required.');
    return;
  }
  axios.post('/data/connector/set_connector', connector.value)
    .then(res => { 
      data.success_alert = res.data.message || 'Connector saved successfully'; 
      if (!connector.value.id) connector.value.id = res.data.id; // assign new ID if created
      editingConnector.value = false;
    })
    .catch(err => { 
      data.error_alert = err.response?.data?.message || 'Error saving connector'; 
    });
};

// Delete connector
const delete_connector = () => {
  if (!connector.value.id || !confirm('Are you sure you want to delete this connector?')) return;
  axios.delete(`/data/connectors/${connector.value.id}`)
    .then(()=>{ 
      data.success_alert = 'Connector deleted';
      connector.value = { commands: [] };
      endpoints.value = [];
    })
    .catch(()=>{ data.error_alert = 'Error deleting connector'; });
};

// Endpoints Methods
const add_endpoint = () => { 
  endpoints.value.push({
    name: '',
    endpoint: '',
    request_type: 'GET',
    params: '',
    headers: '',
    status: true,
    editing: true,
    id: null,
    connector_id: connector.value.id
  }); 
};
const edit_endpoint = (ep) => { ep.editing = true; };
const cancel_edit_endpoint = (ep,index) => { 
  if (ep.id === null) endpoints.value.splice(index,1); 
  else ep.editing = false; 
};
const save_endpoint = (ep) => {
  if (!ep.endpoint) { alert('Endpoint is required'); return; }
  if (ep.id) {
  axios.post(`/data/endpoints/update/${ep.id}`, ep)
    .then(()=>ep.editing=false)
    .catch(()=>alert('Error saving endpoint'));
} else {
  axios.post(`/data/endpoints/add`, ep)
    .then(res => { ep.id = res.data.id; ep.editing=false; })
    .catch(()=>alert('Error adding endpoint'));
}
};
const delete_endpoint = (id,index) => { 
  if (!id) { endpoints.value.splice(index,1); return; }
  axios.post(`/data/endpoints/delete/${id}`)
    .then(()=>endpoints.value.splice(index,1))
    .catch(()=>alert('Error deleting endpoint'));
};

// Commands Methods
const add_command = () => { 
  connector.value.commands.push({
    id: null,
    connector_id: connector.value.id,
    name: '',
    method_name: '',
    description: '',
    endpoint_id: null,
    status: true,
    editing: true,
    definition_type: 'manual',
    ai_code: ''
  }); 
};

const edit_command = (cmd) => { cmd.editing = true; };
const cancel_edit = (cmd) => { 
  if (cmd.id === null) connector.value.commands.splice(connector.value.commands.indexOf(cmd),1); 
  else cmd.editing = false; 
};

const save_command = (id) => {
  const cmd = connector.value.commands.find(c => c.id === id) || connector.value.commands.find(c => c.id === null);
  if (!cmd.name || !cmd.method_name || !cmd.description) {
    alert("Name, Description and Method Name are required."); 
    return; 
  }
  if (cmd.id) {
    axios.post(`/data/connector/update_command/${cmd.id}`, cmd)
      .then(()=>cmd.editing=false)
      .catch(()=>alert('Error saving command'));
  } else {
    axios.post('/data/connector/add_command', cmd)
      .then(res => { 
        cmd.id = res.data.command.id; 
        cmd.editing = false; 
      })
      .catch(()=>alert('Error adding command'));
  }
};

const delete_command = (id,index) => { 
  if (!id) { connector.value.commands.splice(index,1); return; }
  axios.post(`/data/connector/delete_command/${id}`, {}, { headers })
    .then(()=>{ connector.value.commands.splice(index,1); })
    .catch(()=>alert('Error deleting command'));
};

const run_command = (id) => { 
  axios.get(`/data/commands/run/${id}`)
    .then(res => console.log(res.data))
    .catch(() => alert('Error running command'));
};

</script>
