import { useFetch } from '@vueuse/core'

/**
 * @description Make an HTTP request to an endpoint.
 * This is a pre-configured instance of VueUse's useFetch composable.
 *
 * @example
 * const { data, statusCode } = await apiCall('auth/tokens').post(payload).json()
 * if (statusCode.value === 200) {
 *   console.log(data.value)
 * }
 *
 * @see https://vueuse.org/core/useFetch/
 */
export const useApiCall = (url: string, authToken: string | null = null) => {
  return useFetch(url, {
    async beforeFetch({ url, options }) {
      if (!authToken) return { url, options }

      // We add the auth token if the request needs authentication
      options.headers = {
        ...options.headers,
        Authorization: `Bearer ${authToken}`,
      }

      return { options, url }
    },
    updateDataOnError: true,
  })
}
