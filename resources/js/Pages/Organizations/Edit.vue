<template>
  <div>
    <Head :title="title" />
    <div class="max-w-full bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="update">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.name" :error="form.errors.name" class="pb-8 pr-6 w-full lg:w-1/2" :label="__('Name')" />
          <text-input v-model="form.email" :error="form.errors.email" class="pb-8 pr-6 w-full lg:w-1/2" :label="__('Organization Email')" />
          <text-input v-model="form.phone" :error="form.errors.phone" class="pb-8 pr-6 w-full lg:w-1/2" :label="__('Phone')" />
          <text-input v-model="form.address" :error="form.errors.address" class="pb-8 pr-6 w-full lg:w-1/2" :label="__('Address')" />
          <text-input v-model="form.city" :error="form.errors.city" class="pb-8 pr-6 w-full lg:w-1/2" :label="__('City')" />
          <!--          <text-input v-model="form.region" :error="form.errors.region" class="pb-8 pr-6 w-full lg:w-1/2" label="Province/State" />
          <select-input v-model="form.country" :error="form.errors.country" class="pb-8 pr-6 w-full lg:w-1/2" :label="__('Country')">
            <option :value="null" />
            <option v-for="c in countries" :key="c.id" :value="c.id">{{ __(c.name) }}</option>
          </select-input>
          <text-input v-model="form.postal_code" :error="form.errors.postal_code" class="pb-8 pr-6 w-full lg:w-1/2" label="Postal code" />-->
        </div>
        <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
          <button v-if="!organization.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">
            {{ __('Delete Organization') }}
          </button>
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">{{ __('Update Organization') }}</loading-button>
        </div>
      </form>
    </div>

    <h2 class="mt-12 text-2xl font-bold">{{ __('Associated Users') }}</h2>
    <div class="mt-6 bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-nowrap">
        <tr class="text-left font-bold">
          <th class="pb-4 pt-6 px-6">{{ __('Name') }}</th>
          <th class="pb-4 pt-6 px-6">{{ __('User email') }}</th>
          <th class="pb-4 pt-6 px-6" colspan="2">{{ __('Phone') }}</th>
        </tr>
        <tr v-for="user in organization.users" :key="user.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="route('customers.edit', user.id)">
              {{ user.first_name }} {{ user.last_name }}
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="route('customers.edit', user.id)" tabindex="-1">
              {{ user.email }}
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="route('customers.edit', user.id)" tabindex="-1">
              {{ user.phone }}
            </Link>
          </td>
          <td class="w-px border-t">
            <Link class="flex items-center px-4" :href="route('customers.edit', user.id)" tabindex="-1">
              <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
            </Link>
          </td>
        </tr>
        <tr v-if="organization.users.length === 0">
          <td class="px-6 py-4 border-t" colspan="4">No se encontraron usuarios.</td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'

export default {
  components: {
    Head,
    Icon,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
  },
  layout: Layout,
  props: {
    organization: Object,
    countries: Array,
      title: String,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        name: this.organization.name,
        email: this.organization.email,
        phone: this.organization.phone,
        address: this.organization.address,
        city: this.organization.city,
        region: this.organization.region,
        country: this.organization.country,
        postal_code: this.organization.postal_code,
      }),
    }
  },
  methods: {
    update() {
      this.form.put(route('organizations.update', this.organization.id))
    },
    destroy() {
      if (confirm('¿Estás seguro de que deseas eliminar esta empresa?')) {
        this.$inertia.delete(route('organizations.destroy', this.organization.id))
      }
    },
    restore() {
      if (confirm('¿Estás seguro de que deseas restaurar esta empresa?')) {
        this.$inertia.put(route('organizations.restore', this.organization.id))
      }
    },
  },
}
</script>
