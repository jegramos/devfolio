import { ref } from 'vue'

const isMinimized = ref(false)
const isMaximized = ref(true)

export function useCmsDesktopSidebar() {
  const toggle = function () {
    isMinimized.value = !isMinimized.value
    isMaximized.value = !isMaximized.value
    console.log('isMinimized: ' + isMinimized.value)
    console.log('isMaximized: ' + isMaximized.value)
  }

  const minimize = function () {
    isMinimized.value = true
    isMaximized.value = false
  }

  const maximize = function () {
    isMinimized.value = false
    isMaximized.value = true
  }

  return {
    toggle,
    minimize,
    maximize,
    isMinimized,
    isMaximized,
  }
}
