<script setup>
import BreezeApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/inertia-vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-base-100 text-base-content">

        <div class="hero min-h-screen bg-base-200"  style="background-image: url(/images/iceburg.png);">
            <div class="hero-content flex-col lg:flex-row-reverse">
                <div class="text-center lg:text-left">
                    <div class="opacity-70 bg-white rounded-2xl p-10">
                    <h1 class="text-5xl font-bold text-neutral border-accent">{{$page.props.auth.system_settings.title ? $page.props.auth.system_settings.title : ''}}</h1>
                    <p class="py-6">
                        {{$page.props.auth.system_settings.description ? $page.props.auth.system_settings.description : ''}}
                    </p>
                    <p class="py-4">
                            <a class="text-xl" href="https://www.iceburg.ca">Iceburg CRM</a> 
                            <a class="text-xl font=semibold" href="https://www.github.com/iceburgcrm">Download</a>
                        </p>
                </div></div>
                <div class="card flex-shrink-0 w-full max-w-sm shadow-2xl bg-base-100">
                    <div class="card-body">
                        <slot />
                    </div>
                </div>
            </div>
        </div>


    </div>

</template>
