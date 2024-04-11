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
                week: {
                    // Takes the value 'sunday' or 'monday'
                    // However, if startsOn is set to 'sunday' and nDays to 5, the week displayed will be Monday - Friday
                    startsOn: 'monday',
                    // Takes the values 5 or 7.
                    nDays: 7,
                    // Scroll to a certain hour on mounting a week. Takes any value from 0 to 23.
                    // This option is not compatible with the 'dayBoundaries'-option, and will simply be ignored if custom day boundaries are set.
                    scrollToHour: 5,
                },
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
</script>

<style>
@import "/css/qcalendar_style.css";
</style>
