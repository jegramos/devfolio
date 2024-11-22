<script setup lang="ts">
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faTerminal } from '@fortawesome/free-solid-svg-icons'
import { computed, withDefaults, defineProps } from 'vue'

type AppLogoProps = {
  iconOnly?: boolean
  color?: 'primary' | 'light'
  textSizeClass?: string
  iconSizeClass?: string
}

const props = withDefaults(defineProps<AppLogoProps>(), {
  iconOnly: false,
  color: 'primary',
  textSizeClass: 'text-2xl',
  iconSizeClass: 'text-xl',
})

const colorClasses = computed(() => {
  switch (props.color) {
    case 'primary':
      return {
        text: 'text-primary',
        logoContainer: 'bg-primary ring-primary',
        logoIcon: 'text-primary-contrast',
      }
    case 'light':
      return { text: 'text-surface-0', logoContainer: 'bg-surface-0 ring-surface-0', logoIcon: 'text-primary' }
    default:
      throw new Error('The AppLogo "color" prop must be either "primary" or "light".')
  }
})
</script>

<template>
  <div class="flex items-center justify-center font-brand">
    <div :class="`mr-3.5 rounded-md px-1.5 py-0.5 ring ${colorClasses.logoContainer}`">
      <FontAwesomeIcon :icon="faTerminal" :class="`${colorClasses.logoIcon} ${props.iconSizeClass}`" />
    </div>
    <h1 :class="`font-menu mt-2 font-bold ${colorClasses.text} ${props.textSizeClass}`">
      {{ $page.props.appName }}
    </h1>
  </div>
</template>

<style scoped></style>
