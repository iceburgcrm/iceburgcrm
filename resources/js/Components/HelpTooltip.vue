<template>
  <!-- Only render if help is enabled -->
  <span v-if="isHelpEnabled" class="help-tooltip" :class="position">
    <button class="tooltip-btn" @click.stop="toggleTooltip">?</button>

    <div class="tooltip-content" v-if="visible">
      <button class="tooltip-close" @click.stop="closeTooltip">×</button>

      <div class="tooltip-inner" v-if="!loading" v-html="content"></div>
      <div class="tooltip-inner" v-else>Loading...</div>
    </div>
  </span>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'
import { useHelpTooltip } from '@/Compositions/useHelpTooltip'

// Props
const props = defineProps({
  slug: { type: String, required: true },
  position: { type: String, default: 'bottom-right' }
})

// Access system settings
const systemSettings = usePage().props.value.auth.system_settings

// Computed to check if help is enabled
const isHelpEnabled = computed(() => !!systemSettings.help)


// Tooltip state
const { content, loading } = useHelpTooltip(props.slug)
const visible = ref(false)
const toggleTooltip = () => (visible.value = !visible.value)
const closeTooltip = () => (visible.value = false)

function handleClickOutside(e) {
  if (!e.target.closest('.help-tooltip')) visible.value = false
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onBeforeUnmount(() =>
  document.removeEventListener('click', handleClickOutside)
)
</script>

<style scoped>
.help-tooltip {
  position: relative;
  display: inline-block;
  margin-left: 4px;
}

.tooltip-btn {
  border-radius: 50%;
  width: 16px;
  height: 16px;
  font-weight: bold;
  line-height: 14px;
  text-align: center;
  cursor: pointer;
  font-size: 0.75rem;
  background-color: hsl(var(--b2));
  color: hsl(var(--p));
  border: 2px solid hsl(var(--p));
  position: relative;
  top: -5px;
  left: -5px;
}

.tooltip-content {
  position: absolute;
  background-color: hsl(var(--b1));
  color: hsl(var(--c));
  padding: 0.75em 1em;
  border-radius: 0.5rem;
  border: 1px solid hsl(var(--a));
  font-size: 0.8em;
  line-height: 1.4;
  white-space: normal;
  z-index: 1000;
  min-width: 200px;
  max-width: 95vw;
  max-height: 300px;
  overflow-y: auto;
  box-sizing: border-box;
}

.tooltip-inner {
  padding-right: 0.25em;
}

.tooltip-close {
  position: absolute;
  top: 4px;
  right: 6px;
  background: none;
  border: none;
  font-size: 1rem;
  cursor: pointer;
  color: hsl(var(--p));
}

.tooltip-content a {
  color: hsl(var(--p));
  font-weight: 500;
  text-decoration: underline;
  word-break: break-word;
}

.tooltip-content a:hover {
  color: hsl(var(--pc));
  text-decoration: none;
}

/* Positioning variants */
.help-tooltip.right .tooltip-content {
  top: 50%;
  left: 100%;
  transform: translateY(-50%);
  margin-left: 0.25rem;
}
.help-tooltip.left .tooltip-content {
  top: 50%;
  right: 100%;
  transform: translateY(-50%);
  margin-right: 0.25rem;
}
.help-tooltip.top .tooltip-content {
  bottom: 100%;
  left: 50%;
  transform: translateX(-50%);
  margin-bottom: 0.25rem;
}
.help-tooltip.bottom .tooltip-content {
  top: 100%;
  left: 50%;
  transform: translateX(-50%);
  margin-top: 0.25rem;
}
.help-tooltip.bottom-right .tooltip-content {
  top: 100%;
  left: 100%;
  transform: translateX(0);
  margin-top: 0.25rem;
}

.help-tooltip,
.help-tooltip * {
  font-family: 'Ubuntu', sans-serif; /* or whatever font you want */
  font-size: 0.8rem; /* enforce the size */
  line-height: 1.4; /* optional, ensures consistent spacing */
  font-weight: normal; 
}

@media (max-width: 768px) {
  .tooltip-content {
    left: 0 !important;
    right: 0;
    transform: none;
    min-width: 150px;
    max-width: 95vw;
  }
}
</style>
