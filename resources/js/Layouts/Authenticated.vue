<template>
  <div>
    <!-- NAVBAR -->
    <div class="navbar bg-primary text-primary-content">
      <!-- LEFT: Mobile menu + Brand -->
      <div class="navbar-start">
        <!-- Mobile dropdown -->
        <div class="dropdown">
          <label tabindex="0" class="btn btn-ghost lg:hidden" aria-label="Open menu">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </label>

          <ul tabindex="0" class="dropdown-content menu p-2 shadow rounded-box w-60 bg-base-100 text-base-content">
            <li>
              <a href="/dashboard" class="flex items-center gap-2">
                <!-- home -->
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 9.75L12 3l9 6.75M4.5 10.5V21h15V10.5"/>
                </svg>
                {{ $t('page.dashboard') }}
              </a>
            </li>
            <li>
              <a href="/modules" class="flex items-center gap-2">
                <!-- squares-2x2 -->
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h8v8H3V3zm10 0h8v8h-8V3zM3 13h8v8H3v-8zm10 0h8v8h-8v-8z"/>
                </svg>
                {{ $t('page.allmodules') }}
              </a>
            </li>

            <!-- Mobile: dynamic module categories -->
            <template v-for="mc in $page.props.auth.modules" :key="`mbl-${mc.id ?? mc.name}`">
              <li v-if="mc.id < 5" class="menu-title mt-1">
                <span class="flex items-center gap-2">
                  <!-- chevron-down -->
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M7.41 8.58 12 13.17 16.59 8.58 18 10l-6 6-6-6 1.41-1.42z"/>
                  </svg>
                  {{ mc.label }}
                </span>
              </li>
              <li v-if="mc.id < 5" v-for="m in mc.modules" :key="`mbl-${m.name}`">
                <a :href="`/module/${m.name}`" class="flex items-center gap-2 hover:bg-base-200 rounded">
                  <BaseIcon :name="m.icon" />
                  <span class="font-medium">{{ m.label }}</span>
                </a>
              </li>
            </template>
          </ul>
        </div>

        <!-- Brand -->
        <a href="/" class="btn btn-ghost normal-case text-xl">
          {{ $page.props.auth.system_settings.title ? $page.props.auth.system_settings.title : '' }}
        </a>
      </div>

      <!-- CENTER: Desktop categories -->
      <div class="navbar-center hidden lg:flex z-40">
        <ul class="menu menu-horizontal p-0 bg-primary text-primary-content z-40">
          <li>
            <a href="/dashboard" class="flex items-center gap-2">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9.75L12 3l9 6.75M4.5 10.5V21h15V10.5"/>
              </svg>
              <span class="font-semibold">{{ $t('page.dashboard') }}</span>
            </a>
          </li>
          <li>
            <a href="/modules" class="flex items-center gap-2">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h8v8H3V3zm10 0h8v8h-8V3zM3 13h8v8H3v-8zm10 0h8v8h-8v-8z"/>
              </svg>
              <span class="font-semibold">{{ $t('page.allmodules') }}</span>
            </a>
          </li>

          <li tabindex="0" v-for="mc in $page.props.auth.modules" :key="`desk-${mc.name}`">
            <a v-if="mc.name !== 'admin'" class="flex items-center gap-1">
              <span>{{ mc.label }}</span>
              <!-- chevron-down -->
              <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                <path d="M7.41 8.58 12 13.17 16.59 8.58 18 10l-6 6-6-6 1.41-1.42z"/>
              </svg>
            </a>
            <ul v-if="mc.name !== 'admin'" class="p-2 bg-base-100 text-base-content shadow rounded-box">
              <li v-for="m in mc.modules" :key="`desk-${m.name}`">
                <a :href="`/module/${m.name}`" class="flex items-center gap-2 hover:bg-base-200 rounded px-2 py-1">
                  <BaseIcon :name="m.icon" />
                  <span class="font-medium">{{ m.label }}</span>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </div>

      <!-- RIGHT: Calendar + User dropdown -->
      <div class="navbar-end">
        <a class="mr-2" href="/calendar" aria-label="Calendar">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
               stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5A2.25 2.25 0 0 1 5.25 5.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25M7.5 12.75h9"/>
          </svg>
        </a>

        <BreezeDropdown align="right" width="64">
          <template #trigger>
            <span class="inline-flex rounded-md">
              <button
                type="button"
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md bg-base-100 text-base-content hover:bg-base-200 focus:outline-none transition ease-in-out duration-150"
              >
                {{ $page.props.auth.user.name }}
                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                     fill="currentColor">
                  <path fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 0 1 1.414 0L10 10.586l3.293-3.293a1 1 0 1 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 0-1.414z"
                        clip-rule="evenodd"/>
                </svg>
              </button>
            </span>
          </template>

          <template #content>
            <!-- Use menu styles for consistent spacing -->
            <ul class="menu p-2 bg-base-100 text-base-content rounded-box w-72">
              <!-- General -->
              <li class="menu-title">
                <span>{{ $t('general') || 'General' }}</span>
              </li>
              <li>
                <a :href="route('import')" method="post" as="button" class="flex items-center gap-2">
                  <!-- arrow-down-tray -->
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4-4m-4 4-4-4M4 21h16"/>
                  </svg>
                  {{ $t('page.import') }}
                </a>
              </li>
              <li>
                <a :href="route('logout')" method="post" as="button" class="flex items-center gap-2">
                  <!-- arrow-right-on-rectangle -->
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 12H9m0 0 3-3m-3 3 3 3"/>
                  </svg>
                  {{ $t('page.logout') }}
                </a>
              </li>

              <!-- Admin -->
              <template v-if="$page.props.auth.user.role === 'Admin'">
                <li class="menu-title mt-2">
                  <span>{{ $t('admin') || 'Admin' }}</span>
                </li>

                <li>
                  <a :href="route('settings')" class="flex items-center gap-2">
                    <!-- cog-6-tooth -->
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.6 3.75h4.8l.6 2.4 2.1.9 1.95-1.2 2.4 4.155-1.95 1.2.15 2.325 2.25 1.2-2.4 4.155-2.25-1.2-1.95 1.2-.6 2.4H9.6l-.6-2.4-1.95-1.2-2.25 1.2-2.4-4.155 2.25-1.2-.15-2.325-1.95-1.2L4.8 5.85l1.95 1.2 2.1-.9.75-2.4z"/>
                      <circle cx="12" cy="12" r="3.25"/>
                    </svg>
                    {{ $t('page.settings') }}
                  </a>
                </li>

                <li>
                  <a href="/admin/permissions" class="flex items-center gap-2">
                    <!-- key -->
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a4 4 0 1 0-3.6 5.96L6 18.36V21h2.64l1.8-1.8H12v-1.56l1.8-1.8A4 4 0 0 0 15 7z"/>
                    </svg>
                    {{ $t('page.permissions') }}
                  </a>
                </li>

                <li>
                  <a href="/module/ice_roles" class="flex items-center gap-2">
                    <!-- shield-check -->
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v5c0 5-3.5 7.5-7 9-3.5-1.5-7-4-7-9V7l7-4z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9.5 12l2 2 3.5-3.5"/>
                    </svg>
                    {{ $t('page.roles') }}
                  </a>
                </li>

                <li>
                  <a :href="route('data')" class="flex items-center gap-2">
                    <!-- database -->
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                      <ellipse cx="12" cy="5" rx="7" ry="3"/>
                      <path d="M5 5v6c0 1.66 3.13 3 7 3s7-1.34 7-3V5"/>
                      <path d="M5 11v6c0 1.66 3.13 3 7 3s7-1.34 7-3v-6"/>
                    </svg>
                    {{ $t('page.data') }}
                  </a>
                </li>

                <li>
                  <a :href="route('connectors')" class="flex items-center gap-2">
                    <!-- link -->
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M10 13a5 5 0 0 1 0-7l1-1a5 5 0 0 1 7 7l-1 1M14 11a5 5 0 0 1 0 7l-1 1a5 5 0 0 1-7-7l1-1"/>
                    </svg>
                    {{ $t('page.connectors') }}
                  </a>
                </li>

                <li>
                  <a :href="route('scheduler')" class="flex items-center gap-2">
                    <!-- calendar/clock -->
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M7 3v3M17 3v3M4 8h16M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8"/>
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 12v3m0 0h2"/>
                    </svg>
                    {{ $t('page.scheduler') }}
                  </a>
                </li>

                <li class="mt-1">
                  <a :href="route('builder')" class="flex items-center gap-2 font-semibold">
                    <!-- wrench-screwdriver -->
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M14.5 4.5l5 5M16 3l5 5-2.5 2.5-5-5L16 3zM3 21l6-6"/>
                      <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4 4"/>
                    </svg>
                    {{ $t('page.builder') }}
                  </a>
                </li>

                <li>
                  <a href="/module/ice_modules" class="flex items-center gap-2">
                    <!-- squares -->
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                      <path d="M3 3h8v8H3zM13 3h8v8h-8zM3 13h8v8H3zM13 13h8v8h-8z"/>
                    </svg>
                    {{ $t('page.modules') }}
                  </a>
                </li>

                <li>
                  <a href="/module/ice_fields" class="flex items-center gap-2">
                    <!-- tag -->
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M20 13l-7 7-8-8 7-7h6l2 2v6z"/>
                      <circle cx="15" cy="9" r="1.2"/>
                    </svg>
                    {{ $t('page.fields') }}
                  </a>
                </li>

                <li>
                  <a href="/module/ice_module_subpanels" class="flex items-center gap-2">
                    <!-- rectangle-group -->
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                      <rect x="3" y="4" width="13" height="8" rx="1"/>
                      <rect x="3" y="14" width="8" height="6" rx="1"/>
                      <rect x="13" y="14" width="8" height="6" rx="1"/>
                    </svg>
                    {{ $t('page.subpanels') }}
                  </a>
                </li>

                <li>
                  <a href="/module/ice_relationships" class="flex items-center gap-2">
                    <!-- git-branch-ish -->
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                      <circle cx="6" cy="6" r="2"/>
                      <circle cx="18" cy="6" r="2"/>
                      <circle cx="18" cy="18" r="2"/>
                      <path d="M8 6h8M6 8v6a4 4 0 0 0 4 4h6"/>
                    </svg>
                    {{ $t('page.relationships') }}
                  </a>
                </li>

                <li>
                  <a href="/module/ice_datalets" class="flex items-center gap-2">
                    <!-- table -->
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                      <rect x="3" y="5" width="18" height="14" rx="1"/>
                      <path d="M3 9h18M9 9v10M15 9v10"/>
                    </svg>
                    {{ $t('page.datalets') }}
                  </a>
                </li>

                <li>
                  <a href="/module/ice_users" class="flex items-center gap-2 font-semibold">
                    <!-- users -->
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M16 14a4 4 0 1 0-8 0M3 20a7 7 0 0 1 14 0M17 7a3 3 0 1 1 0 6"/>
                    </svg>
                    {{ $t('page.users') }}
                  </a>
                </li>
              </template>
            </ul>
          </template>
        </BreezeDropdown>
      </div>
    </div>

    <!-- LAYOUT -->
    <div class="min-h-screen bg-base-100">
      <header class="bg-base-200 text-base-content shadow" v-if="$slots.header">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
          <slot name="header"/>
        </div>
      </header>

      <main class="bg-base-100 text-base-content">
        <slot/>
      </main>
    </div>

    <!-- FOOTER -->
    <footer class="footer p-10 bg-neutral text-primary-content">
      <div v-for="mc in $page.props.auth.modules" :key="`ft-${mc.name}`" class="grid grid-flow-col">
        <div v-if="mc.name !== 'admin'" class="p-2">
          <div class="flex items-center gap-1 font-semibold">
            {{ mc.label }}
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
              <path d="M7.41 8.58 12 13.17 16.59 8.58 18 10l-6 6-6-6 1.41-1.42z"/>
            </svg>
          </div>
          <div class="mt-2 space-y-1">
            <div v-for="m in mc.modules" :key="`ft-${m.name}`">
              <a :href="`/module/${m.name}`" class="flex items-center gap-2 hover:underline">
                <BaseIcon :name="m.icon" />
                <span class="font-medium">{{ m.label }}</span>
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
        <div class="grid grid-flow-col gap-4 items-center">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="fill-current">
            <path
              d="M22.672 15.226l-2.432.811.841 2.515c.33 1.019-.209 2.127-1.23 2.456-1.15.325-2.148-.321-2.463-1.226l-.84-2.518-5.013 1.677.84 2.517c.391 1.203-.434 2.542-1.831 2.542-.88 0-1.601-.564-1.86-1.314l-.842-2.516-2.431.809c-1.135.328-2.145-.317-2.463-1.229-.329-1.018.211-2.127 1.231-2.456l2.432-.809-1.621-4.823-2.432.808c-1.355.384-2.558-.59-2.558-1.839 0-.817.509-1.582 1.327-1.846l2.433-.809-.842-2.515c-.33-1.02.211-2.129 1.232-2.458 1.02-.329 2.13.209 2.461 1.229l.842 2.515 5.011-1.677-.839-2.517c-.403-1.238.484-2.553 1.843-2.553.819 0 1.585.509 1.85 1.326l.841 2.517 2.431-.81c1.02-.33 2.131.211 2.461 1.229.332 1.018-.21 2.126-1.23 2.456l-2.433.809 1.622 4.823 2.433-.809c1.242-.401 2.557.484 2.557 1.838"/>
          </svg>
          <a class="underline" href="https://www.iceburg.ca">Iceburg CRM</a>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import BreezeDropdown from '@/Components/Dropdown.vue'
import BaseIcon from '@/Icons/BaseIcon'

const showingNavigationDropdown = ref(false)
</script>
