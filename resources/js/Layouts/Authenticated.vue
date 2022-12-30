
<template>
    <div>

        <div class="navbar bg-primary text-primary-content">
            <div class="navbar-start">
                <div class="dropdown">
                    <label tabindex="0" class="btn btn-ghost lg:hidden">
                       <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" /></svg>
                    </label>
                    <ul tabindex="0" class=" text-base-content bg-base-100 dropdown-content menu p-2 shadow rounded-box w-52">
                        <li style="font-weight: bolder;"><a href="/dashboard">Dashboard</a></li>
                        <li style="font-weight: bolder;"><a href="/modules">All Modules</a></li>
                        <ul class="p-2  bg-base-100" v-for="module_category in $page.props.auth.modules">
                            <li v-if="module_category.id < 5" class="semi-bold">{{module_category.label}} <svg class="fill-current" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z"/></svg>
                            </li>
                            <li v-if="module_category.id < 5"  v-for="single_module in module_category.modules">
                                <a class="text-base-content font-semibold border-solid border-secondary link link-secondary hover:text-secondary" :href="`/module/${single_module.name}`">
                                    <BaseIcon :name="single_module.icon" /> {{single_module.label}}
                                </a>

                            </li>
                        </ul>
                    </ul>
                </div>
                <a class="btn btn-ghost normal-case text-xl">{{$page.props.auth.system_settings.title ? $page.props.auth.system_settings.title : ''}}</a>
            </div>
            <div class="navbar-center hidden lg:flex z-40">
                <ul class="menu menu-horizontal p-0 text-primary-content bg-primary z-40">
                    <li style="font-weight: bolder;"><a href="/dashboard">Dashboard</a></li>
                    <li style="font-weight: bolder;"><a href="/modules">All Module</a></li>
                    <li tabindex="0" v-for="module_category in $page.props.auth.modules">
                         <a v-if="module_category.name != 'admin'"> {{module_category.label}}

                             <svg class="fill-current" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z"/></svg>
                        </a>
                        <ul v-if="module_category.name != 'admin'" class="p-2 bg-base-100">
                            <li v-for="single_module in module_category.modules">

                                <a class="text-base-content font-semibold border-solid border-secondary link link-secondary hover:text-secondary" :href="`/module/${single_module.name}`">
                                    <BaseIcon :name="single_module.icon" /> {{single_module.label}}
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="navbar-end">

                <BreezeDropdown align="right" width="48">
                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md bg-base-100 text-base-content hover:text-primary focus:outline-none transition ease-in-out duration-150">
                                                {{ $page.props.auth.user.name }}

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                    </template>

                    <template #content>
                        <div class="grid grid-flow-row p-5 bg-base-100 gp-5">
                            <ul>
                                <li>
                                    <a class="font-medium  hover:text-accent text-base-content grid-row-1" :href="route('logout')" method="post" as="button">
                                        Log Out
                                    </a>
                                </li>
                                <li>
                                    <a class="font-medium  hover:text-accent text-base-content grid-row-1" :href="route('import')" method="post" as="button">
                                        Import
                                    </a>
                                </li>
                                <li v-if="$page.props.auth.user.role === 'Admin'"><hr class="mt-3 mb-3" /></li>

                                <li v-if="$page.props.auth.user.role === 'Admin'">
                                    <a class="font-medium  hover:text-accent text-base-content grid-row-1" :href="route('settings')" method="get" as="button">
                                        Settings
                                    </a>
                                </li>
                                <li v-if="$page.props.auth.user.role === 'Admin'">
                                    <a class="font-medium  hover:text-accent text-base-content grid-row-1" href="/admin/permissions" method="get" as="button">
                                        Permissions
                                    </a>
                                </li>
                                <li v-if="$page.props.auth.user.role === 'Admin'">
                                    <a class="font-medium hover:text-accent text-base-content grid-row-1" href="/admin/roles" method="get" as="button">
                                        Roles
                                    </a>
                                </li>
                                <li v-if="$page.props.auth.user.role === 'Admin'">
                                    <a class="font-bold mt-3   hover:text-accent text-base-content grid-row-1" :href="route('builder')" method="get" as="button">
                                        Builder
                                    </a>
                                </li>
                                <li v-if="$page.props.auth.user.role === 'Admin'">
                                    <a class="font-mediumhover:text-accent ml-2 text-base-content grid-row-1"  href="/module/modules" method="get" as="button">
                                        Modules
                                    </a>
                                </li>
                                <li v-if="$page.props.auth.user.role === 'Admin'">
                                    <a class="font-medium ml-2  hover:text-accent text-base-content grid-row-1"  href="/module/fields" method="get" as="button">
                                        Fields
                                    </a>
                                </li>
                                <li v-if="$page.props.auth.user.role === 'Admin'">
                                    <a class="font-medium ml-2  hover:text-accent text-base-content grid-row-1" href="module_subpanels" method="get" as="button">
                                        Subpanels
                                    </a>
                                </li>
                                <li v-if="$page.props.auth.user.role === 'Admin'">
                                    <a class="font-medium ml-2   hover:text-accent text-base-content grid-row-1" href="/module/datalets" method="get" as="button">
                                        Datalets
                                    </a>
                                </li>
                                <li v-if="$page.props.auth.user.role === 'Admin'">
                                    <a class="font-medium ml-2  hover:text-accent text-base-content font-bold grid-row-1" href="/module/users" method="get" as="button">
                                        Users
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </template>
                </BreezeDropdown>
            </div>
        </div>
        <div class="min-h-screen bg-base-100">



            <!-- Page Heading -->
            <header class="bg-base-200 text-base-content shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>
            <!-- Page Content -->
            <main class="bg-base-100 text-base-content">
                <slot />
            </main>
        </div>
    </div>

    <footer class="footer p-10 bg-neutral text-primary-content">
        <div class="text-primary-content grid grid-flow-col grid-auto" tabindex="0" v-for="module_category in $page.props.auth.modules">
            <div  class="grid grid-flow-col grid-auto">

                <div v-if="module_category.name != 'admin'" class="p-2 grid grid-row"><a v-if="module_category.name != 'admin'"> {{module_category.label}}
                    <svg class="fill-current" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z"/></svg>
                </a><br>
                    <div v-for="single_module in module_category.modules">

                        <a class="font-semibold text-primary-content  border-solid border-secondary link link-secondary hover:text-secondary" :href="`/module/${single_module.name}`">
                            <BaseIcon :name="single_module.icon" /> {{single_module.label}}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <footer class="footer px-10 py-4 border-t bg-base-200 text-base-content border-base-300">
        <div class="items-center grid-flow-col">
            <p></p>
        </div>
        <div class="md:place-self-center md:justify-self-end">
            <div class="grid grid-flow-col gap-4">
                <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" class="fill-current"><path d="M22.672 15.226l-2.432.811.841 2.515c.33 1.019-.209 2.127-1.23 2.456-1.15.325-2.148-.321-2.463-1.226l-.84-2.518-5.013 1.677.84 2.517c.391 1.203-.434 2.542-1.831 2.542-.88 0-1.601-.564-1.86-1.314l-.842-2.516-2.431.809c-1.135.328-2.145-.317-2.463-1.229-.329-1.018.211-2.127 1.231-2.456l2.432-.809-1.621-4.823-2.432.808c-1.355.384-2.558-.59-2.558-1.839 0-.817.509-1.582 1.327-1.846l2.433-.809-.842-2.515c-.33-1.02.211-2.129 1.232-2.458 1.02-.329 2.13.209 2.461 1.229l.842 2.515 5.011-1.677-.839-2.517c-.403-1.238.484-2.553 1.843-2.553.819 0 1.585.509 1.85 1.326l.841 2.517 2.431-.81c1.02-.33 2.131.211 2.461 1.229.332 1.018-.21 2.126-1.23 2.456l-2.433.809 1.622 4.823 2.433-.809c1.242-.401 2.557.484 2.557 1.838 0 .819-.51 1.583-1.328 1.847m-8.992-6.428l-5.01 1.675 1.619 4.828 5.011-1.674-1.62-4.829z"></path></svg>

                <a class="underline" href="https://www.iceburg.ca">Iceburg CRM</a></div>
        </div>
    </footer>
</template>
<script setup>
import { ref } from 'vue';
import BreezeApplicationLogo from '@/Components/ApplicationLogo.vue';
import BreezeDropdown from '@/Components/Dropdown.vue';
import BreezeDropdownLink from '@/Components/DropdownLink.vue';
import BreezeNavLink from '@/Components/NavLink.vue';
import BreezeResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/inertia-vue3';

import BaseIcon from "@/Icons/BaseIcon";

const showingNavigationDropdown = ref(false);

</script>

