<script setup lang="ts">
import { nextTick } from 'vue'
import { useBroadcastChannel } from '@vueuse/core'
import CmsDesktopSidebar from '@/Layouts/Navigation/CmsDesktopSidebar.vue'
import CmsDesktopToolbar from '@/Layouts/Navigation/CmsDesktopToolbar.vue'
import { useCmsDesktopSidebar } from '@/Composables/useCmsDesktopSidebar'
import CmsMobileToolbar from '@/Layouts/Navigation/CmsMobileToolbar.vue'
import { ChannelName } from '@/Types/broadcast-channel.ts'

const { isMaximized: cmsDesktopSideIsMaximized } = useCmsDesktopSidebar()

// Broadcast to other tabs the user is already logged in if they
// access this layout component (E.g. When users log-in via Google)
const broadcastLogin = function () {
  const { isSupported, post } = useBroadcastChannel({ name: ChannelName.LOGIN_CHANNEL })
  if (isSupported.value) {
    post(true)
  }
}

nextTick(() => {
  broadcastLogin()
})
</script>

<template>
  <section class="flex min-h-screen bg-surface-200 text-surface-700 dark:bg-surface-950 dark:text-surface-0">
    <CmsDesktopSidebar
      :class="`${cmsDesktopSideIsMaximized ? 'w-[20%]' : 'w-0 -translate-x-96 transform'}
       hidden overflow-hidden transition-all duration-200 lg:flex`"
    />
    <section class="mx-2 mt-2 flex flex-1 flex-col md:mx-4 md:mt-4 lg:mt-0">
      <CmsDesktopToolbar class="hidden lg:flex" />
      <CmsMobileToolbar class="lg:hidden" />
      <slot></slot>
    </section>
  </section>
</template>

<style scoped></style>
