<template>
    <Head title="Settings" />
    <BreezeAuthenticatedLayout>

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Import
            </h2>

        </template>
        <BreadCrumbs :levels="$page.props.breadcrumbs" />
        <div class="w-full mt-10 bg-base-200">
            <div class="max-w-full sm:px-3 lg:px-4 p-10">
            <Qalendar class=" bg-base-100 text-base-content" :events="events"
            locale="en-US"
            firstDayOfWeek="sunday"
            :disable-dates="disableDates"
            :default-date="new Date(2022, 5, 1)"
            />
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
<script setup>
import { ref, reactive } from "vue";
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import BreadCrumbs from "@/Components/BreadCrumbs";
</script>
<script>
import { Qalendar } from "qalendar";
import { Head, usePage } from '@inertiajs/inertia-vue3';
import { ref, reactive } from "vue";
export default {
    components: {
        Qalendar,
    },

    data() {
        return {
            config: {

                month: {
                    // Hide leading and trailing dates in the month view (defaults to true when not set)
                    showTrailingAndLeadingDates: false
                },
                color: "yellow",
                colorScheme: "blue",
                // if not set, the mode defaults to 'week'. The three available options are 'month', 'week' and 'day'
                // Please note, that only day and month modes are available for the calendar in mobile-sized wrappers (~700px wide or less, depending on your root font-size)
                defaultMode: 'month',
                // The silent flag can be added, to disable the development warnings. This will also bring a slight performance boost
                isSilent: true,
                showCurrentTime: true
                },
            events: usePage().props.value.events
        }
    },
}

console.log(
    [
    {
        title: "Advanced algebra",
        with: "Chandler Bing",
        time: { start: "2022-05-16 12:05", end: "2022-05-16 13:35" },
        color: "yellow",
        isEditable: true,
        id: "753944708f0f",
        description: "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Asperiores assumenda corporis doloremque et expedita molestias necessitatibus quam quas temporibus veritatis. Deserunt excepturi illum nobis perferendis praesentium repudiandae saepe sapiente voluptatem!"
    },
        {
            title: "Ralph on holiday",
            with: "Rachel Greene",
            time: { start: "2022-05-10", end: "2022-05-22" },
            color: "green",
            isEditable: true,
            id: "5602b6f589fc"
        }
    ]);
</script>

<style>
@import "/css/qcalendar_style.css";
</style>
