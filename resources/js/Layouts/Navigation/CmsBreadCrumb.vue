<script setup lang="ts">
import { ref } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import Breadcrumb from 'primevue/breadcrumb'
import { useCmsNavLinks } from '@/Composables/useCmsNavLinks'

// Get the group name
const page = usePage()
const cmsNavLinks = useCmsNavLinks(page)
let groupName = ref('')
let linkName = ref('')

const items = ref([])

router.on('navigate', () => {
  for (const item of cmsNavLinks.navItems.value) {
    const matchedGroupLink = item.links.find((link) => link.uri === page.url)
    if (matchedGroupLink) {
      groupName.value = item.group
      linkName.value = matchedGroupLink.name
      break
    }
  }
  items.value = [{ label: groupName.value }, { label: linkName.value }]
})
</script>

<template>
  <Breadcrumb :model="items" class="text-sm">
    <template #separator><i class="pi pi-angle-right"></i></template>
  </Breadcrumb>
</template>

<style scoped></style>
