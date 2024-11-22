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
  flash: {
    cms: {
      success?: string
    }
  }
}

/**
 * The different types of error codes that the back-end
 * may return.
 */
export enum ErrorCode {
  INVALID_CREDENTIALS = 'INVALID_CREDENTIALS_ERROR',
  TOO_MANY_REQUESTS = 'TOO_MANY_REQUESTS_ERROR',
  EMAIL_ALREADY_VERIFIED = 'EMAIL_ALREADY_VERIFIED_ERROR',
  VALIDATION = 'VALIDATION_ERROR',
  RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND_ERROR',
  UNAUTHORIZED = 'UNAUTHORIZED_ERROR',
  UNKNOWN_ROUTE = 'UNKNOWN_ROUTE_ERROR',
  SERVER = 'SERVER_ERROR',
  PAYLOAD_TOO_LARGE = 'PAYLOAD_TOO_LARGE_ERROR',
  EXTERNAL_ACCOUNT_EMAIL_CONFLICT = 'EXTERNAL_ACCOUNT_EMAIL_CONFLICT_ERROR',
}
