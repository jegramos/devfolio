<script setup lang="ts">
import Card from 'primevue/card'
</script>

<template>
  <Card>
    <template #title>
      <span class="text-sm font-bold">CHANGE PROFILE PICTURE</span>
    </template>
    <template #content>
      <div class="flex justify-between gap-x-6">
        <p class="text-wrap">
          Let’s put a face to the name! Upload a photo of yourself to personalize your profile. Please to keep the file size under
          2MB.
        </p>
        <div class="flex h-32 w-32 flex-shrink-0 items-center justify-center rounded-lg bg-primary-900">
          <span class="text-6xl text-surface-0">JR</span>
        </div>
      </div>
    </template>
  </Card>
</template>

<style scoped></style>
