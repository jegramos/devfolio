<script setup lang="ts">
import { computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import Button from 'primevue/button'
import AppAnimatedFloaters from '@/Components/AppAnimatedFloaters.vue'

const props = defineProps({
  status: {
    type: Number,
    default: 500,
  },
})

const title = computed(() => {
  return {
    503: 'Service Unavailable',
    500: 'Server Error',
    404: 'Page Not Found',
    403: 'Forbidden',
  }[props.status]
})

const description = computed(() => {
  return {
    503: 'Sorry, we are doing some maintenance. Please check back soon.',
    500: 'Whoops, something went wrong on our servers.',
    404: 'Sorry, the page you are looking for could not be found.',
    403: 'Sorry, you are forbidden from accessing this page.',
  }[props.status]
})

const navigateBack = function () {
  window.history.back()
}
</script>

<template>
  <Head :title="title"></Head>
  <div
    class="flex h-[100vh] w-[100vw] flex-col items-center justify-start bg-gradient-to-b from-primary/90 to-primary pt-16 md:justify-center md:pt-0"
  >
    <AppAnimatedFloaters />
    <h1 class="font-stylish text-[7rem] font-black text-surface-200">{{ status }}</h1>
    <p class="text-surface-200">{{ description }}</p>
    <Button icon="pi pi-caret-left" label="GO BACK" class="mt-6" @click="navigateBack"></Button>
  </div>
</template>
