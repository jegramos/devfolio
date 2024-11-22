/** @see https://tailwind.primevue.org/vite */
import PrimeVue, { type PrimeVueConfiguration } from 'primevue/config'

export const primeVue = {
  options: PrimeVue,
  config: <PrimeVueConfiguration>{
    theme: 'none',
    ripple: true,
  },
}
