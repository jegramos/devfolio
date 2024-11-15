/**
 * Utility for creating custom Vuelidate validators.
 * For more details, see:
 * @see https://vuelidate-next.netlify.app/custom_validators.html
 */

import { helpers } from '@vuelidate/validators'

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
