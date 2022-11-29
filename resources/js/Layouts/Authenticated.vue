
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
                                    <a class="font-bold hover:text-accent text-base-content grid-row-1" :href="route('logout')" method="post" as="button">
                                        Log Out
                                    </a>
                                </li>
                                <li>
                                    <a class="font-bold hover:text-accent text-base-content grid-row-1" :href="route('import')" method="post" as="button">
                                        Import
                                    </a>
                                </li>
                                <li v-if="$page.props.auth.user.role === 'Admin'">
                                    <a class="font-bold  hover:text-accent text-base-content grid-row-1" :href="route('settings')" method="get" as="button">
                                        Settings
                                    </a>
                                </li>
                                <li v-if="$page.props.auth.user.role === 'Admin'">
                                    <a class="font-bold  hover:text-accent text-base-content grid-row-1" href="/admin/permissions" method="get" as="button">
                                        Permissions
                                    </a>
                                </li>
                                <li v-if="$page.props.auth.user.role === 'Admin'">
                                    <a class="font-bold hover:text-accent text-base-content grid-row-1" href="/admin/roles" method="get" as="button">
                                        Roles
                                    </a>
                                </li>
                                <li v-if="$page.props.auth.user.role === 'Admin'">
                                    <a class="font-bold hover:text-accent text-base-content grid-row-1"  href="/module/modules" method="get" as="button">
                                        Modules
                                    </a>
                                </li>
                                <li v-if="$page.props.auth.user.role === 'Admin'">
                                    <a class="font-bold hover:text-accent text-base-content grid-row-1"  href="/module/fields" method="get" as="button">
                                        Fields
                                    </a>
                                </li>
                                <li v-if="$page.props.auth.user.role === 'Admin'">
                                    <a class="font-bold  hover:text-accent text-base-content grid-row-1" href="module_subpanels" method="get" as="button">
                                        Subpanels
                                    </a>
                                </li>
                                <li v-if="$page.props.auth.user.role === 'Admin'">
                                    <a class="font-bold  hover:text-accent text-base-content grid-row-1" href="/module/datalets" method="get" as="button">
                                        Datalets
                                    </a>
                                </li>
                                <li v-if="$page.props.auth.user.role === 'Admin'">
                                    <a class="hover:text-accent text-base-content font-bold grid-row-1" href="/module/users" method="get" as="button">
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

