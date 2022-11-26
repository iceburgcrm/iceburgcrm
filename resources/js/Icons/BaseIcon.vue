<template>
    <component :class="class_style" :is="icon"  />
</template>
<script setup>
import {
    BuildingOffice2Icon,
    BuildingOfficeIcon,
    BriefcaseIcon,
    BuildingLibraryIcon,
    BuildingStorefrontIcon,
    HomeIcon,
    HomeModernIcon,
    UserPlusIcon,
    UserMinusIcon,
    UserCircleIcon,
    UserIcon,
    ChatBubbleLeftIcon,
    CircleStackIcon,
    BookOpenIcon,
    Bars4Icon,
    UsersIcon,
    LightBulbIcon,
    MegaphoneIcon,
    InboxStackIcon,
    CurrencyDollarIcon,
    ArrowRightOnRectangleIcon,
    CalculatorIcon,
    QueueListIcon,
    PencilSquareIcon,
    DocumentIcon,
    PencilIcon,
    UserGroupIcon,
    GlobeAmericasIcon,
    RectangleGroupIcon,
    GlobeAltIcon,
    CurrencyPoundIcon,
    SparklesIcon,
    PhoneIcon,
    Cog6ToothIcon

} from '@heroicons/vue/24/solid'
import {ref, reactive, computed, defineAsyncComponent, markRaw} from 'vue'

const props =  defineProps({
        name: [String, null],
        class_style: [String, null]
    });

const class_style = props.class_style || 'h-4 w-4';
const lookup = {
    BuildingOffice2Icon,
    BuildingOfficeIcon,
    BuildingLibraryIcon,
    BuildingStorefrontIcon,
    BriefcaseIcon,
    HomeIcon,
    HomeModernIcon,
    UserPlusIcon,
    UserMinusIcon,
    UserCircleIcon,
    UserIcon,
    ChatBubbleLeftIcon,
    CalculatorIcon,
    CircleStackIcon,
    BookOpenIcon,
    Bars4Icon,
    UsersIcon,
    LightBulbIcon,
    MegaphoneIcon,
    InboxStackIcon,
    CurrencyDollarIcon,
    ArrowRightOnRectangleIcon,
    QueueListIcon,
    PencilSquareIcon,
    DocumentIcon,
    PencilIcon,
    UserGroupIcon,
    GlobeAmericasIcon,
    RectangleGroupIcon,
    GlobeAltIcon,
    CurrencyPoundIcon,
    SparklesIcon,
    PhoneIcon,
    Cog6ToothIcon
}
const icon = markRaw(lookup[props.name]);
const AsyncComp = markRaw(defineAsyncComponent(() =>
      import('@heroicons/vue/24/solid').then(comp => ({ default: comp[props.name]}))
))
</script>
