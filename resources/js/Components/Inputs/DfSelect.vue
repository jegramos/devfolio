<script setup lang="ts">
/**
 * Extended version of 2 Input Components
 * @see https://primevue.org/select/#api
 * @see https://primevue.org/inputgroup/
 *
 *
 * Guide for Vue3's attribute inheritance feature
 * @see https://vuejs.org/guide/components/attrs
 */
import Select from 'primevue/select'
import InputGroup from 'primevue/inputgroup'
import InputGroupAddon from 'primevue/inputgroupaddon'
import { computed, useSlots } from 'vue'

defineOptions({
  inheritAttrs: false,
})

const props = defineProps({
  invalidMessage: {
    type: String,
    default: '',
  },
})

const slots = useSlots()
const renderAsInputGroup = computed(() => !!slots.icon)
</script>

<template>
  <template v-if="renderAsInputGroup">
    <div class="flex w-full flex-col">
      <InputGroup class="group">
        <InputGroupAddon>
          <slot name="icon"></slot>
        </InputGroupAddon>
        <Select v-bind="$attrs" />
      </InputGroup>
      <small v-if="$attrs.invalid" class="mt-1 animate-shake text-xs text-red-500 dark:text-red-300">
        {{ props.invalidMessage }}
      </small>
    </div>
  </template>

  <template v-else>
    <div class="flex w-full flex-col">
      <Select v-bind="$attrs" />
      <small v-if="$attrs.invalid" class="mt-1 animate-shake text-xs text-red-500 dark:text-red-300">
        {{ props.invalidMessage }}
      </small>
    </div>
  </template>
</template>

<style scoped></style>
