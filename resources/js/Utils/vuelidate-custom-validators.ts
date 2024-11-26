/**
 * Utility for creating custom Vuelidate validators.
 * For more details, see:
 * @see https://vuelidate-next.netlify.app/custom_validators.html
 */

import { helpers } from '@vuelidate/validators'
import { useApiCall } from '@/Composables/useApiCall.ts'

export const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\p{Z}\p{S}\p{P}]).{8,}$/u

/**
 * @description Custom validator for password strength.
 * Ensures that the password:
 * - Contains at least one lowercase letter
 * - Contains at least one uppercase letter
 * - Contains at least one digit
 * - Contains at least one special character
 * - Is a minimum of 8 characters long
 */
export const passwordRule = () => helpers.regex(passwordRegex)

/**
 * @description Custom validator to check the uniqueness of a user identifier.
 * It makes an API call to verify if a given value (username or email) is unique.
 */
export const uniqueUserIdentifierRule = (
  baseUrl: string,
  type: 'username' | 'email' | 'mobile_number',
  excludedId: string | number | null = null
) =>
  async function (value: string) {
    value = encodeURIComponent(value.trim())

    // Remove the last char of the url if it ends with a '/'
    if (baseUrl.charAt(baseUrl.length - 1) === '/') baseUrl = baseUrl.slice(0, -1)

    /** @note We still need to check as the library can still take in non-string types at run time */
    if (value === null || value === '' || value === undefined) return true

    const url = excludedId !== null ? `${baseUrl}/${type}/${value}/${excludedId}` : `${baseUrl}/${type}/${value}`
    const { data } = await useApiCall(url).get().json()
    return data.value.available
  }
