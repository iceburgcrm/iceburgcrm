<template>
    <Head :title="`Connector ${connector.name || 'New'}`" />
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-base-content leading-tight">
                {{ connector.name ? `Edit Connector: ${connector.name}` : 'Create New Connector' }}
            </h2>
        </template>

        <BreadCrumbs :levels="$page.props.breadcrumbs" />
        <div class="w-full bg-base-100">
            <div class="bg-base-100 mt-10 text-base-content max-w-full sm:px-3 lg:px-4">

                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">Name</span>
                    </label>
                    <input type="text" v-model="connector.name" class="input input-bordered w-full" required />
                </div>
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">Class</span>
                    </label>
                    <input type="text" v-model="connector.class" class="input input-bordered w-full" required />
                </div>
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">Auth Type</span>
                    </label>
                    <select v-model="connector.auth_type" class="select select-bordered w-full">
                        <option value="">Select Auth Type</option>
                        <option value="OAuth2">OAuth2</option>
                        <option value="API Key">API Key</option>
                        <option value="Basic Auth">Basic Auth</option>
                        <option value="None">None</option>
                    </select>
                </div>
                <div v-if="connector.auth_type === 'OAuth2'">
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Client ID</span>
                        </label>
                        <input type="text" v-model="connector.client_id" class="input input-bordered w-full" />
                    </div>
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Client Secret</span>
                        </label>
                        <input type="text" v-model="connector.client_secret" class="input input-bordered w-full" />
                    </div>
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Token URL</span>
                        </label>
                        <input type="text" v-model="connector.token_url" class="input input-bordered w-full" />
                    </div>
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Refresh Token</span>
                        </label>
                        <input type="text" v-model="connector.refresh_token" class="input input-bordered w-full" />
                    </div>
                </div>
                <div v-if="connector.auth_type === 'Basic Auth'">
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Username</span>
                        </label>
                        <input type="text" v-model="connector.username" class="input input-bordered w-full" />
                    </div>
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Password</span>
                        </label>
                        <input type="text" v-model="connector.password" class="input input-bordered w-full" />
                    </div>
                </div>
                <!-- Existing fields -->
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">Base URL</span>
                    </label>
                    <input type="text" v-model="connector.base_url" class="input input-bordered w-full" />
                </div>
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">Auth Key</span>
                    </label>
                    <input type="text" v-model="connector.auth_key" class="input input-bordered w-full" />
                </div>
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">Token Expiry</span>
                    </label>
                    <input type="text" :value="connector.token_expires_at" disabled class="input input-bordered w-full" />
                </div>
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">Active</span>
                    </label>
                    <input type="checkbox" v-model="connector.status" class="toggle toggle-success" />
                </div>
                <a @click="save_connector" class="btn btn-sm btn-success">{{ connector.id ? 'Save Connector' : 'Add Connector' }}</a>
                <a v-if="connector.id" @click="delete_connector" class="btn btn-sm btn-error ml-4">Delete Connector</a>
            </div>
        </div>

        <!-- Commands Section -->
        <div class="w-full bg-base-100">
            <div class="bg-base-100 mt-10 text-base-content max-w-full sm:px-3 lg:px-4">
                <a @click="add_command()" class="btn float-right w-100 btn-sm btn-primary mb-4">
                    Add
                </a>
                <h4 class="text-lg text-center mb-4">Commands</h4>
                <div v-for="(command, index) in connector.commands" :key="command.id">
                    <div class="w-full stats w-400 stats-vertical mb-10 lg:stats-horizontal shadow">
                        <div class="stat">
                            <label v-if="command.editing" class="label">Name</label>
                            <input v-if="command.editing" v-model="command.name" class="input input-bordered w-full" />
                            <div v-else class="stat-value">{{ command.name }}</div>

                            <label v-if="command.editing" class="label mt-2">Method Name</label>
                            <input v-if="command.editing" v-model="command.method_name" class="input input-bordered w-full" />
                            <div v-else class="stat-title mt-5 mb-5"><b>Method Name: {{ command.method_name }}</b></div>

                            <label v-if="command.editing" class="label mt-2">Description</label>
                            <textarea v-if="command.editing" v-model="command.description" class="textarea textarea-bordered w-full"></textarea>
                            <div v-else class="stat-desc">{{ command.description }}</div>
                        </div>

                        <div class="stat">
                            <a @click="run_command(command.id)" class="btn w-20 btn-sm btn-success">Run</a>

                            <div class="stat-title"><b>Last Ran: {{ command.last_ran }}</b></div>
                            <div class="stat-desc"><b>Last Run Data: {{ command.last_ran_data }}</b></div>
                        </div>

                        <div class="stat w-100">
                            <div class="stat-title">Status:
                                <input type="checkbox" v-model="command.status" class="toggle toggle-success" />
                            </div>

                            <div class="stat">
                                <!-- Save and Cancel Buttons -->
                                <div v-if="command.editing">
                                    <a @click="save_command(command.id)" class="btn btn-sm btn-success">Save</a>
                                    <a @click="cancel_edit(command)" class="btn btn-sm btn-warning ml-2">Cancel</a>
                                </div>

                                <!-- Edit and Delete Buttons -->
                                <div v-else>
                                    <a @click="edit_command(command)" class="btn btn-sm btn-primary ml-2">Edit</a>
                                    <a @click="delete_command(command.id, index)" class="btn btn-sm btn-error ml-2">Delete</a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>



<script setup>
import { ref, reactive } from "vue";
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head, usePage } from '@inertiajs/inertia-vue3';
import axios from "axios";
import BreadCrumbs from "@/Components/BreadCrumbs";

const connector = ref(usePage().props.value.connector || { commands: [] }); // Ensure connector is an object
if (connector.value.auth_type) {
    connector.value.auth_type = connector.value.auth_type;
}
const method_types = ref(usePage().props.value.method_types);
const form = ref();
const data = reactive({
    success_alert: '',
    error_alert: '',
    alert_text: '',
});



const headers = { 'Content-Type': 'multipart/form-data' };
const save_connector = () => {

    if (!connector.value.name) {
        console.log(connector);
        alert('The name field is required.');
        return;
    }


    const payload = {
        id: connector.value.id,
        name: connector.value.name,
        class: connector.value.class,
        auth_type: connector.value.auth_type,
        auth_key: connector.value.auth_key,
        base_url: connector.value.base_url,
        token_url: connector.value.token_url || '',
        client_id: connector.value.client_id || '',
        client_secret: connector.value.client_secret || '',
        username: connector.value.username || '',
        password: connector.value.password || '',
        access_token: connector.value.access_token || '',
        refresh_token: connector.value.refresh_token || '',
        token_expires_at: connector.value.token_expires_at || '',
        status: connector.value.status || false
    };

    const url = `/data/connector/set_connector`;
    const method = 'post';

    axios({
        method,
        url,
        data: payload,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
        .then(response => {
            data.success_alert = response.data.message || 'Connector saved successfully';
        })
        .catch(error => {
            data.error_alert = error.response?.data?.message || 'Error saving connector';
        });
};


const add_command = () => {
    const new_command = { name: '', method_name: '', status: true, editing: true, id: null, connector_id: connector.value.id  }; // Initialize without ID
    console.log('in new comnad')
    console.log(new_command)
    connector.value.commands.push(new_command);
};


const delete_connector = () => {
    if (confirm('Are you sure you want to delete this connector?')) {
        axios.delete(`/data/connectors/${connector.value.id}`)
            .then(response => {
                data.success_alert = response.data.message || 'Connector deleted successfully';
                connector.value = { commands: [] }; // Reset the connector after deletion
            })
            .catch(error => {
                data.error_alert = error.response?.data?.message || 'Error deleting connector';
            });
    }
};

const run_command = (command_id) => {
    axios.post('/data/connector/run_command', { command_id }, {
        'Content-Type': 'application/json',
            'Accept': 'application/json'
     })
        .then((res) => {
            data.alert_text = "Command ran successfully";
            data.success_alert = true;
            setTimeout(() => { data.success_alert = false; }, 2000);
        })
        .catch((error) => {
            data.alert_text = "Error running command";
            data.error_alert = true;
            setTimeout(() => { data.error_alert = false; data.alert_text = ''; }, 5000);
        });
}


const edit_command = (command) => {
    command.editing = true;
};



const save_command = (command_id) => {
    const command = connector.value.commands.find(cmd => cmd.id === command_id);

    // If the command is not found by ID, handle it as a new command
    if (!command && command_id === null) {
        const new_command = connector.value.commands.find(cmd => cmd.id === null);
        if (new_command) {
            // If it's a new command with no ID
            save_new_command(new_command);
        }
        return;
    }

    if (!command) {
        console.error("Command not found:", command_id);
        return;
    }

    if (!command.name || !command.method_name || !command.description) {
        alert("Name, Description and Method Name are required.");
        return;
    }

    if (command.id) {
        // Update existing command
        axios.post(`/data/connector/update_command/${command_id}`, command)
            .then((res) => {
                command.editing = false;
            })
            .catch((error) => {
                console.error("Error updating command:", error);
            });
    } else {
        // Add new command
        save_new_command(command);
    }
};


const cancel_edit = (command) => {
    if (command.id === null) {
        // Remove new command if it has no ID
        const index = connector.value.commands.indexOf(command);
        if (index > -1) {
            connector.value.commands.splice(index, 1);
        }
    } else {
        // Simply cancel editing for existing command
        command.editing = false;
    }
};




const save_new_command = (command) => {
    axios.post('/data/connector/add_command', command)
        .then((res) => {
            command.editing = false;
            command.id = res.data.command.id; // Assign the ID returned from the server
            console.log("New command saved with ID:", command.id); // Debug log
        })
        .catch((error) => {
            console.error("Error adding new command:", error);
        });
};


const delete_command = (command_id, index) => {
    axios.post('/data/connector/delete_command/' + command_id, {}, { headers })
        .then((res) => {
            // Remove the command from the array
            connector.value.commands.splice(index, 1);
            data.alert_text = "Command deleted";
            data.success_alert = true;
            setTimeout(() => {
                data.success_alert = false;
            }, 2000);
        })
        .catch((error) => {
            data.alert_text = "Error deleting command";
            data.error_alert = true;
            setTimeout(() => {
                data.error_alert = false;
                data.alert_text = '';
            }, 5000);
        });
};
</script>
