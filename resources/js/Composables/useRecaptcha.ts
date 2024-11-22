export function useRecaptcha(elementId: string) {
  const el = document.getElementById(elementId)
  if (document.contains(el)) el?.remove()

  const recaptchaScript = document.createElement('script')
  recaptchaScript.setAttribute('id', elementId)
  recaptchaScript.setAttribute('src', 'https://www.google.com/recaptcha/api.js')
  document.head.appendChild(recaptchaScript)
}
