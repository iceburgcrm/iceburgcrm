<template>
    <Head title="Settings" />
    <BreezeAuthenticatedLayout>

        <template #header>
            <h2 class="font-semibold text-xl leading-tight">
                {{ $t('page.settings') }}
                <HelpTooltip slug="settings" position="bottom-right"/>
            </h2>
        </template>

        <BreadCrumbs :levels="$page.props.breadcrumbs" />

        <div class="w-full mt-10 bg-base-200">
            <div class="max-w-full sm:px-3 lg:px-4 p-10">
                <div class="grid row-auto text-base-content">
                    <Alert 
                        :message="alert.alert_text" 
                        :is_successful="alert.success_alert" 
                        :is_error="alert.error_alert" 
                    />

                    <!-- Title -->
                    <div class="p-5 grid grid-row-2 bg-base-100 text-base-content">
                        <label for="title" class="block text-sm font-medium leading-5">
                            {{ $t('page.title') }}
                        </label>
                        <input 
                            class="input input-secondary rounded w-full " 
                            name="title" 
                            v-model="data.title" 
                        />
                    </div>

                    <!-- Description -->
                    <div class="p-5 grid grid-row-2 bg-base-100 text-base-content">
                        <label for="description" class="block text-sm font-medium leading-5">
                            {{ $t('page.description') }}
                        </label>
                        <textarea 
                            cols="40" rows="6" 
                            class="input textarea input-secondary rounded w-full " 
                            name="description" 
                            v-model="data.description"
                        ></textarea>
                    </div>

                    <div class="p-5 grid grid-row-2 bg-base-100 text-base-content">
                      <!-- Theme Selector -->
                      <div class="mb-3">
                        <label class="block text-sm font-medium mb-2">Theme</label>
                        <select v-model="data.theme" class="select select-secondary w-full">
                          <option v-for="item in $page.props.themes" :key="item.name">{{ item.name }}</option>
                        </select>
                      </div>

                    <div class="flex flex-wrap gap-2 items-center mt-2 p-2 rounded shadow-sm" :data-theme="data.theme">
                      <template v-for="color in ['primary','secondary','accent','neutral','base-100','base-200','base-300','info','success','warning','error']" :key="color">
                        <div class="flex flex-col items-center text-xs text-center w-12">
                          <div 
                            class="w-10 h-10 rounded shadow cursor-pointer" 
                            :class="'bg-' + color"
                            :title="color"
                          ></div>
                          <span class="truncate w-10">{{ color }}</span>
                        </div>
                      </template>
                    </div>

</div>





                    <!-- Language -->
                    <div class="p-5 grid grid-row-2 bg-base-100 text-base-content">
                        <label for="language" class="block text-sm font-medium leading-5">
                            {{ $t('page.language') }}
                        </label>
                        <select 
                            class="select select-secondary rounded w-full " 
                            name="language" 
                            v-model="data.language"
                        >
                            <option value="en">English</option>
                            <option value="fr">French</option>
                            <option value="de">German</option>
                            <option value="es">Spanish</option>
                            <option value="hi">Hindu</option>
                            <option value="ja">Japanese</option>
                            <option value="pt">Portuguese</option>
                            <option value="ru">Russian</option>
                            <option value="zh">Chinese</option>
                            <option value="ar">Arabic</option>
                        </select>
                    </div>

                    <!-- Default Search Records -->
                    <div class="p-5 grid grid-row-2 bg-base-100 text-base-content">
                        <label for="search_per_page" class="block text-sm font-medium leading-5">
                            {{ $t('page.defaultsearchrecords') }}
                        </label>
                        <select 
                            class="select select-secondary rounded w-full " 
                            name="search_per_page" 
                            v-model="data.search_per_page"
                        >
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>

                    <!-- Default Subpanels -->
                    <div class="p-5 grid grid-row-2 bg-base-100 text-base-content">
                        <label for="submodule_search_per_page" class="block text-sm font-medium leading-5">
                            {{ $t('page.defaultsubpanels') }}
                        </label>
                        <select 
                            class="select select-secondary rounded w-full " 
                            name="submodule_search_per_page" 
                            v-model="data.submodule_search_per_page"
                        >
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>

                    <!-- Max Export Records -->
                    <div class="p-5 grid grid-row-2 bg-base-100 text-base-content">
                        <label for="max_export_records" class="block text-sm rounded font-medium leading-5">
                            {{ $t('page.maxexportrecords') }}
                        </label>
                        <input 
                            class="input input-secondary rounded w-full " 
                            name="max_export_records" 
                            v-model="data.max_export_records" 
                        />
                    </div>

                    <!-- Help -->
                    <div class="p-5 grid grid-row-2 bg-base-100 text-base-content">
                      <label for="help" class="block text-sm font-medium leading-5">
                        Help
                      </label>
                      <input
                        type="checkbox"
                        id="help"
                        class="toggle toggle-secondary mt-2"
                        v-model="data.help"
                      />
                    </div>



                    <!-- Save Button -->
                    <div class="p-5 bg-base-100 text-base-content">
                        <input 
                            type="button" 
                            class="btn btn-primary rounded"  
                            @click="save()" 
                            value="Save" 
                        />
                    </div>

                </div>
            </div>
        </div>

    </BreezeAuthenticatedLayout>
</template>

<script setup>
import { reactive, ref, computed, onMounted, watch } from "vue";
import { Head, usePage } from '@inertiajs/inertia-vue3';
import axios from "axios";
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import BreadCrumbs from "@/Components/BreadCrumbs";
import Alert from "@/Components/Alert";
import HelpTooltip from '@/Components/HelpTooltip.vue';

// --- Reactive form data ---
const pageProps = usePage().props.value.auth.system_settings;

const data = reactive({
    title: pageProps.title,
    description: pageProps.description,
    theme: pageProps.theme,
    language: pageProps.language,
    search_per_page: pageProps.search_per_page,
    submodule_search_per_page: pageProps.submodule_search_per_page,
    max_export_records: pageProps.max_export_records,
    help: !!pageProps.help,
});

// --- Alerts ---
const alert = reactive({
    success_alert: 0,
    error_alert: 0,
    alert_text: ''
});

// --- Dynamic DaisyUI theme colors ---
const themeColors = ref({});

const updateThemeColors = () => {
    const root = document.documentElement;

    // Grab all DaisyUI theme CSS variables safely
    themeColors.value = {
        primary: getComputedStyle(root).getPropertyValue('--p')?.trim() || '#FFFFFF',
        secondary: getComputedStyle(root).getPropertyValue('--s')?.trim() || '#F472B6',
        accent: getComputedStyle(root).getPropertyValue('--a')?.trim() || '#34D399',
        neutral: getComputedStyle(root).getPropertyValue('--n')?.trim() || '#111827',
        'base-100': getComputedStyle(root).getPropertyValue('--b1')?.trim() || '#FDFDFE',
        'base-200': getComputedStyle(root).getPropertyValue('--b2')?.trim() || '#F4F4F5',
        'base-300': getComputedStyle(root).getPropertyValue('--b3')?.trim() || '#E4E4E7',
        info: getComputedStyle(root).getPropertyValue('--info')?.trim() || '#3B82F6',
        success: getComputedStyle(root).getPropertyValue('--success')?.trim() || '#10B981',
        warning: getComputedStyle(root).getPropertyValue('--warning')?.trim() || '#FACC15',
        error: getComputedStyle(root).getPropertyValue('--error')?.trim() || '#EF4444',
    };
};


// Initial load
onMounted(() => updateThemeColors());

// Watch theme changes
watch(() => data.theme, () => {
    setTimeout(updateThemeColors, 100); // increased from 50ms
});

// --- Save Settings ---
const save = () => {
    axios.post('/data/settings', data)
        .then(response => {
            if (response.data === 1) {
                window.location.reload();
            } else {
                showAlert('error', 'Error saving settings');
            }
        })
        .catch(() => {
            showAlert('error', 'Error saving settings');
        });
};

const showAlert = (type, message) => {
    alert.error_alert = type === 'error' ? 1 : 0;
    alert.success_alert = type === 'success' ? 1 : 0;
    alert.alert_text = message;
    setTimeout(() => {
        alert.error_alert = 0;
        alert.success_alert = 0;
        alert.alert_text = '';
    }, 5000);
};
</script>
