/**
 * @description Get the 2-letter initials from the full name
 * @example
 * import { getAvatarDisplayNamePlaceholder } from '@/Utils/avatar-helpers'
 * const placeholder = getAvatarDisplayNamePlaceholder('Juan Luna') // returns 'JL'
 */
export const getAvatarDisplayNamePlaceholder = function (fullName?: string) {
  if (!fullName) return ''

  // we'll display the initials for the fake avatar
  const names = fullName.split(' ')
  let initials = names[0].substring(0, 1).toUpperCase()

  if (names.length > 1) {
    initials += names[names.length - 1].substring(0, 1).toUpperCase()
  }

  return initials
}
