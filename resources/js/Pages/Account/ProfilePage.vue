<script lang="ts">
import CmsLayout from '@/Layouts/CmsLayout.vue'

export default {
  layout: CmsLayout,
}
</script>

<script lang="ts" setup>
import Card from 'primevue/card'
import Button from 'primevue/button'
import ProfilePageCard from '@/Pages/Account/ProfilePagePictureCard.vue'
import ProfilePageInfoCard from '@/Pages/Account/ProfilePageInfoCard.vue'
import type { PropType } from 'vue'

export type UserProfile = {
  id: string
  username: string
  email: string
  given_name: string
  family_name: string
  mobile_number?: string
  birthday?: string | Date | null
  gender?: string | null
  country_id?: number | null
  address_line_1?: string | null
  address_line_2?: string | null
  address_line_3?: string | null
  from_external_account: boolean
  recommend_username_change: boolean
}

const props = defineProps({
  profile: {
    type: Object as PropType<UserProfile>,
    required: true,
  },
  countryOptions: {
    type: Array<{ id: number; name: string }>,
    required: true,
  },
  checkAvailabilityBaseUrl: {
    type: String,
    required: true,
  },
  updateProfileUrl: {
    type: String,
    required: true,
  },
})
</script>

<template>
  <section class="mb-4 mt-2 flex flex-col gap-2 lg:mt-0 lg:grid lg:grid-cols-2">
    <!-- Start Profile Information -->
    <div class="flex flex-col">
      <ProfilePageInfoCard
        :profile="props.profile"
        :country-options="countryOptions"
        :check-availability-base-url="props.checkAvailabilityBaseUrl"
        :update-profile-url="props.updateProfileUrl"
        class="w-full"
      />
    </div>
    <!-- End Profile Information -->
    <!-- Start Profile Picture & Change Password -->
    <div class="flex flex-col gap-2">
      <ProfilePageCard />
      <Card>
        <template #title>
          <span class="text-sm font-bold">CHANGE EMAIL</span>
        </template>
        <template #content>
          <div class="flex flex-col">
            <p class="mb-4">You will need to re-verify your email address upong changing.</p>
            <div class="flex h-12 w-full rounded-lg bg-surface-200"></div>
          </div>
        </template>
        <template #footer>
          <div class="mt-2 flex justify-end">
            <Button icon="pi pi-save" label="Save"></Button>
          </div>
        </template>
      </Card>
      <Card>
        <template #title>
          <span class="text-sm font-bold">CHANGE PASSWORD</span>
        </template>
        <template #content>
          <div class="flex flex-col">
            <p class="mb-4">Remember to regularly update your password.</p>
            <div class="flex h-52 w-full rounded-lg bg-surface-200"></div>
          </div>
        </template>
        <template #footer>
          <div class="mt-2 flex justify-end">
            <Button icon="pi pi-save" label="Save"></Button>
          </div>
        </template>
      </Card>
    </div>
    <!-- End Profile Picture & Change Password -->
  </section>
</template>

<style scoped></style>
