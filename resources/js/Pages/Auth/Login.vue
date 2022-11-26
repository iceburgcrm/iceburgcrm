<script setup>
import BreezeButton from '@/Components/Button.vue';
import BreezeCheckbox from '@/Components/Checkbox.vue';
import BreezeGuestLayout from '@/Layouts/Guest.vue';
import BreezeInput from '@/Components/Input.vue';
import BreezeLabel from '@/Components/Label.vue';
import BreezeValidationErrors from '@/Components/ValidationErrors.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <BreezeGuestLayout>
        <Head title="Log in" />

        <BreezeValidationErrors class="mb-4" />

        <div v-if="status" class="mb-4 font-medium text-sm text-success-content">
            {{ status }}
        </div>

        {{$page.props.auth.system_settings.title ? $page.props.auth.system_settings.title : ''}}
        <form @submit.prevent="submit" class=" bg-base-200 text-base-content">
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Email</span>
                </label>
                <input type="text" placeholder="email" class="input input-bordered" v-model="form.email" required autofocus autocomplete="username" />
            </div>
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Password</span>
                </label>

                <input type="password" placeholder="password" class="input input-bordered" v-model="form.password" required autocomplete="current-password" />
                    <label class="label">
                    <a v-if="canResetPassword" :href="route('password.request')" class="label-text-alt link link-hover">Forgot password?</a>
                </label>
            </div>

            <div class="form-control mt-6">
                <button class="btn btn-primary" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Login</button>

            </div>
            <!--
            <Link v-if="canRegister" :href="route('register')" class="ml-4 text-sm text-base-content underline">
                    Register
                </Link>
                -->



        </form>
    </BreezeGuestLayout>
</template>
