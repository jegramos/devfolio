/**
 * The type of Inertia Shared Data used by the app
 * @see https://v2.inertiajs.com/shared-data
 */
export type SharedPage = {
  appName: string
  logoutUrl: string
  pageUris: {
    resume: string
    about: string
  }
  auth: {
    user?: {
      full_name: string
      username: string
      email: string
      roles: string[]
      email_verified: boolean
      profile_picture_url?: string
    }
  }
}

/**
 * The different types of error codes that the back-end
 * may return.
 */
export enum ErrorCode {
  INVALID_CREDENTIALS = 'INVALID_CREDENTIALS',
  TOO_MANY_REQUESTS = 'TOO_MANY_REQUESTS',
  EMAIL_ALREADY_VERIFIED = 'EMAIL_ALREADY_VERIFIED',
}
