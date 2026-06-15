import { ref } from 'vue'

const tooltipCache = {}

export function useHelpTooltip(slug) {
  const content = ref('')
  const loading = ref(true)
  const error = ref(null)

  async function fetchTooltip() {
    if (tooltipCache[slug]) {
      content.value = tooltipCache[slug]
      loading.value = false
      return
    }

    try {
      const res = await fetch(`/data/help?slug=${slug}`)
      if (!res.ok) throw new Error(`Failed to fetch tooltip for "${slug}"`)
      const data = await res.json()
      content.value = data.content
      tooltipCache[slug] = data.content
    } catch (err) {
      console.error(err)
      error.value = 'Help not available'
      content.value = error.value
    } finally {
      loading.value = false
    }
  }

  fetchTooltip()

  return { content, loading, error }
}
