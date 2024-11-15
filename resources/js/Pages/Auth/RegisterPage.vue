<script setup lang="ts">
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { required, helpers, minLength, email } from '@vuelidate/validators'
import Card from 'primevue/card'
import Toast from 'primevue/toast'
import Button from 'primevue/button'
import Divider from 'primevue/divider'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faRocket } from '@fortawesome/free-solid-svg-icons'
import AppLogo from '@/Components/AppLogo.vue'
import AppAnimatedFloaters from '@/Components/AppAnimatedFloaters.vue'
import DfInputText from '@/Components/Inputs/DfInputText.vue'
import DfPassword from '@/Components/Inputs/DfPassword.vue'
import DfSelect from '@/Components/Inputs/DfSelect.vue'
import { useRecaptcha } from '@/Composables/useRecaptcha'
import type { SharedPage } from '@/Types/shared.page'
import { useClientValidatedForm } from '@/Composables/useClientValidatedForm.ts'
import { passwordRegex, passwordRule } from '@/Utils/vuelidate-custom-validators.ts'

const props = defineProps({
  loginUrl: {
    type: String,
    required: true,
  },
  processRegistrationUrl: {
    type: String,
    required: true,
  },
  countryOptions: {
    type: Array<{ id: number; name: string }>,
    required: true,
  },
  recaptchaEnabled: {
    type: String,
    required: true,
  },
  recaptchaSiteKey: {
    type: String || null,
    required: true,
  },
})

const clientValidationRules = {
  $lazy: true,
  username: {
    required: helpers.withMessage('Username is required.', required),
    minLength: helpers.withMessage('Must be 3 or more characters', minLength(3)),
  },
  email: {
    required: helpers.withMessage('Email is required.', required),
    email: helpers.withMessage('Must be a valid email address', email),
  },
  first_name: {
    required: helpers.withMessage('First name is required.', required),
  },
  last_name: {
    required: helpers.withMessage('Last name is required.', required),
  },
  password: {
    required: helpers.withMessage('Password is required.', required),
    password: helpers.withMessage(
      'At least 8 characters, 1 uppercase, 1 lowercase, 1 digit, 1 special character.',
      passwordRule()
    ),
  },
  country_id: {
    required: helpers.withMessage('Select your country.', required),
  },
}

const form = useClientValidatedForm(clientValidationRules, {
  username: '',
  email: '',
  password: '',
  password_confirmation: '',
  first_name: '',
  last_name: '',
  country_id: '',
  recaptcha_response_token: '',
})

const submit = async function (event: Event) {
  const target = event.target as HTMLFormElement
  form.recaptcha_response_token = target['g-recaptcha-response']?.value || ''

  await form.post(props.processRegistrationUrl, {
    onError: () => {
      if (form.errors.password) form.reset('password', 'password_confirmation')
      // This reloads the Recaptcha widget
      useRecaptcha('recaptcha-container')
    },
  })
}

// Load Recaptcha
let recaptchaTheme = 'light'
if (props.recaptchaEnabled) {
  useRecaptcha('recaptcha-container')
  recaptchaTheme = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
}

// Change the BG color if there are errors
const page = usePage<SharedPage>()
const bgColorClass = computed(function () {
  if (page.props.errors.TOO_MANY_REQUESTS) return 'bg-amber-700 dark:bg-amber-900'
  if (form.hasErrors) return 'bg-red-700 dark:bg-red-900'
  else return 'bg-primary/90 dark:bg-primary'
})
</script>

<template>
  <section :class="`relative flex h-screen w-full flex-col items-center justify-center px-2 md:px-0 ${bgColorClass}`">
    <Toast />
    <AppAnimatedFloaters />
    <Card class="z-10 w-full md:w-[80%] lg:w-[50%]">
      <template #title>
        <div class="flex flex-col">
          <AppLogo />
          <span class="mt-4 text-2xl font-bold">Create your account</span>
        </div>
      </template>
      <template #subtitle>
        <small>Please enter your details or use your social media account</small>
      </template>
      <template #content>
        <!-- Start Input Form -->
        <form @submit.prevent="submit">
          <!-- Start Username and Email -->
          <section class="mt-4 flex w-full flex-col space-y-4 md:flex-row md:space-x-4 md:space-y-0">
            <DfInputText
              v-model="form.email"
              placeholder="Email *"
              :invalid="!!form.errors.email"
              :invalid-message="form.errors.email"
            >
              <template #icon>
                <i class="pi pi-envelope"></i>
              </template>
            </DfInputText>
            <DfInputText
              v-model="form.username"
              placeholder="Username *"
              :invalid="!!form.errors.username"
              :invalid-message="form.errors.username"
            >
              <template #icon>
                <i class="pi pi-user"></i>
              </template>
            </DfInputText>
          </section>
          <!-- End Username and Email -->
          <!-- Start Firstname and Lastname -->
          <section class="mt-4 flex w-full flex-col space-y-4 md:flex-row md:space-x-4 md:space-y-0">
            <DfInputText
              v-model="form.first_name"
              placeholder="First Name *"
              :invalid="!!form.errors.first_name"
              :invalid-message="form.errors.first_name"
            >
              <template #icon>
                <i class="pi pi-id-card"></i>
              </template>
            </DfInputText>
            <DfInputText
              v-model="form.last_name"
              placeholder="Last Name"
              :invalid="!!form.errors.last_name"
              :invalid-message="form.errors.last_name"
            >
              <template #icon>
                <i class="pi pi-id-card"></i>
              </template>
            </DfInputText>
          </section>
          <!-- End Firstname and Lastname -->
          <!-- Start Password and Confirmation -->
          <section class="mt-4 flex w-full flex-col space-y-4 md:flex-row md:space-x-4 md:space-y-0">
            <DfPassword
              v-model="form.password"
              placeholder="Password *"
              :invalid="!!form.errors.password"
              :invalid-message="form.errors.password"
              toggle-mask
              :enable-feedback="true"
              feedback-header="Create your password"
              :feedback-helper-list="[
                'At least one lowercase letter',
                'At least one uppercase',
                'At least one numeric',
                'At least one symbol from',
                'Minimum 8 characters',
              ]"
              :strong-regex="passwordRegex"
              medium-label="Almost there"
              strong-label="Perfect!"
            >
              <template #icon>
                <i class="pi pi-lock"></i>
              </template>
            </DfPassword>
            <DfPassword
              v-model="form.password_confirmation"
              placeholder="Confirm Password"
              :invalid="!!form.errors.password_confirmation"
              :invalid-message="form.errors.password_confirmation"
              :feedback="false"
              toggle-mask
            >
              <template #icon>
                <i class="pi pi-lock"></i>
              </template>
            </DfPassword>
          </section>
          <!-- End Password and Confirmation -->
          <!-- Start Country -->
          <section class="mt-4 flex flex-col space-y-4 md:w-[49%] md:flex-row md:space-x-4 md:space-y-0">
            <DfSelect
              v-model="form.country_id"
              placeholder="Country *"
              :options="countryOptions"
              editable
              option-label="name"
              option-value="id"
              :invalid="!!form.errors.country_id"
              :invalid-message="form.errors.country_id"
            >
              <template #icon>
                <i class="pi pi-map"></i>
              </template>
            </DfSelect>
          </section>
          <!-- End Country -->
          <!-- Start Recaptcha -->
          <div v-if="props.recaptchaEnabled" class="mt-4 flex flex-col items-start md:w-[49%]">
            <div id="recaptcha-container" class="g-recaptcha" :data-sitekey="recaptchaSiteKey" :data-theme="recaptchaTheme"></div>
            <small
              v-if="!!form.errors.recaptcha_response_token"
              class="mt-1 animate-shake text-xs text-red-500 dark:text-red-300"
            >
              {{ form.errors.recaptcha_response_token }}
            </small>
          </div>
          <!-- End Recaptcha -->
          <Button label="Sign Up" class="mt-4 w-full" :disabled="form.processing" :loading="form.processing" type="submit">
            <template #icon>
              <FontAwesomeIcon :icon="faRocket"></FontAwesomeIcon>
            </template>
          </Button>
          <!-- End Input Form -->
        </form>
      </template>
      <template #footer>
        <div class="flex flex-col space-y-2">
          <Divider class="!my-1 !py-0">
            <small class="font-thin">or</small>
          </Divider>
          <div class="flex flex-col space-y-4 md:flex-row md:space-x-4 md:space-y-0">
            <Button
              :disabled="form.processing"
              icon="pi pi-google"
              label="Sign up with Google"
              class="w-full !border-red-900 !bg-red-900 !text-surface-0"
            />
            <Button
              :disabled="form.processing"
              icon="pi pi-facebook"
              label="Sign up with Facebook"
              class="w-full !border-blue-900 !bg-blue-900 !text-surface-0"
            />
          </div>
        </div>
        <div class="pt-2">
          <small class="tracking-wide">
            Already have an account?
            <Link :href="loginUrl">
              <span class="font-bold text-amber-600 underline-offset-4 hover:underline dark:text-amber-300"> Login </span>
            </Link>
          </small>
        </div>
      </template>
    </Card>
  </section>
</template>

<style scoped></style>
