<script setup lang="ts">
/**
 * Extended version of 2 Input Components
 * @see https://primevue.org/password/#api
 * @see https://primevue.org/inputgroup/
 *
 *
 * Guide for Vue3's attribute inheritance feature
 * @see https://vuejs.org/guide/components/attrs
 */

import Password from 'primevue/password'
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
  enableFeedback: {
    type: Boolean,
    default: false,
  },
  feedbackHeader: {
    type: String,
    default: '',
  },
  feedbackHelperList: {
    type: Array<string>,
    default: [],
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
        <Password v-bind="$attrs">
          <template v-if="props.enableFeedback || $attrs.feedback" #header>
            <div class="text-xm mb-4 font-semibold text-xs md:text-sm">{{ props.feedbackHeader }}</div>
          </template>
          <template v-if="props.enableFeedback || $attrs.feedback" #footer>
            <Divider />
            <ul class="my-0 mt-2 ml-2 pl-2 leading-normal text-xs md:text-sm">
              <li v-for="item in props.feedbackHelperList" :key="item">
                {{ item }}
              </li>
            </ul>
          </template>
        </Password>
      </InputGroup>
      <small v-if="$attrs.invalid" class="mt-1 animate-shake text-xs text-red-500 dark:text-red-300">
        {{ props.invalidMessage }}
      </small>
    </div>
  </template>

  <template v-else>
    <div class="flex w-full flex-col">
      <Password v-bind="$attrs">
        <template v-if="props.enableFeedback || $attrs.feedback" #header>
          <div class="text-xm mb-4 font-semibold text-xs md:text-sm">{{ props.feedbackHeader }}</div>
        </template>
        <template v-if="props.enableFeedback || $attrs.feedback" #footer>
          <Divider />
          <ul class="my-0 mt-2 ml-2 pl-2 leading-normal text-xs md:text-sm">
            <li v-for="item in props.feedbackHelperList" :key="item">
              {{ item }}
            </li>
          </ul>
        </template>
      </Password>
      <small v-if="$attrs.invalid" class="mt-1 animate-shake text-xs text-red-500 dark:text-red-300">
        {{ props.invalidMessage }}
      </small>
    </div>
  </template>
</template>

<style scoped></style>
