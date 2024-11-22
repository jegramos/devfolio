<script setup lang="ts">
import Toolbar from 'primevue/toolbar'
import CmsMobileSidebar from '@/Layouts/Navigation/CmsMobileSidebar.vue'
import AppLogo from '@/Components/AppLogo.vue'
</script>

<template>
  <nav class="flex w-full">
    <Toolbar class="w-full !rounded-3xl !bg-primary !px-4">
      <template #start>
        <AppLogo color="light" text-size-class="text-xl" icon-size-class="text-md" />
      </template>

      <template #end>
        <div class="flex justify-end">
          <CmsMobileSidebar />
        </div>
      </template>
    </Toolbar>
  </nav>
</template>

<style scoped></style>
