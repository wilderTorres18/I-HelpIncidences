<template>
  <div>
    <Head :title="__(title)" />
    <div class="bg-white rounded-md shadow overflow-hidden w-full">
      <form @submit.prevent="update">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.name" :error="form.errors.name" class="pr-6 pb-8 w-full lg:w-1/2" :label="__('Name')" />
          <text-input v-model="form.slug" :error="form.errors.slug" class="pr-6 pb-8 w-full lg:w-1/2" :label="__('Slug')" />
        </div>
        <div class="py-8 -mr-6 -mb-8 flex flex-wrap overflow-x-auto">
          <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
            <div class="overflow-hidden">
              <table class="min-w-full">
                <thead class="border-b">
                  <tr>
                    <th scope="col" class="text-sm font-bold text-gray-900 px-6 py-4 text-left">
                      Funciones
                    </th>
                    <th scope="col" class="text-sm font-bold text-gray-900 px-6 py-4 text-left">
                      Leer
                    </th>
                    <th scope="col" class="text-sm font-bold text-gray-900 px-6 py-4 text-left">
                      Editar
                    </th>
                    <th scope="col" class="text-sm font-bold text-gray-900 px-6 py-4 text-left">
                      Crear
                    </th>
                    <th scope="col" class="text-sm font-bold text-gray-900 px-6 py-4 text-left">
                      Eliminar
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(value, name, index) in form.access" :key="index" class="border-b">
                    <td class="px-6 py-4 whitespace-nowrap text-sm capitalize text-gray-900"> {{ name }} </td>
                    <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                      <label class="flex toggle_swtich items-center cursor-pointer">
                        <div class="relative">
                          <input v-model="value.read" type="checkbox" class="sr-only" />
                          <div class="w-10 h-4 bg-gray-400 rounded-full shadow-inner" />
                          <div class="dot absolute w-6 h-6 bg-white rounded-full shadow -left-1 -top-1 transition" />
                        </div>
                      </label>
                    </td>
                    <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                      <label class="flex toggle_swtich items-center cursor-pointer">
                        <div class="relative">
                          <input v-model="value.update" type="checkbox" class="sr-only" />
                          <div class="w-10 h-4 bg-gray-400 rounded-full shadow-inner" />
                          <div class="dot absolute w-6 h-6 bg-white rounded-full shadow -left-1 -top-1 transition" />
                        </div>
                      </label>
                    </td>
                    <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                      <label class="flex toggle_swtich items-center cursor-pointer">
                        <div class="relative">
                          <input v-model="value.create" type="checkbox" class="sr-only" />
                          <div class="w-10 h-4 bg-gray-400 rounded-full shadow-inner" />
                          <div class="dot absolute w-6 h-6 bg-white rounded-full shadow -left-1 -top-1 transition" />
                        </div>
                      </label>
                    </td>
                    <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                      <label class="flex toggle_swtich items-center cursor-pointer">
                        <div class="relative">
                          <input v-model="value.delete" type="checkbox" class="sr-only" />
                          <div class="w-10 h-4 bg-gray-400 rounded-full shadow-inner" />
                          <div class="dot absolute w-6 h-6 bg-white rounded-full shadow -left-1 -top-1 transition" />
                        </div>
                      </label>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center">
          <button v-if="!['admin','manager','customer'].includes(role.slug)" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">{{ __('Eliminar') }}</button>
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">{{ __('Actualizar') }}</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import { Link, Head } from '@inertiajs/inertia-vue3'
import TextInput from '@/Shared/TextInput'
import LoadingButton from '@/Shared/LoadingButton'

export default {
    metaInfo() {
        return { title: this.form.name }
    },
    components: {
        LoadingButton,
        TextInput,
        Link,
        Head,
    },
    layout: Layout,
    props: {
        title: String,
        role: Object,
    },
    remember: 'form',
    data() {
        return {
            form: this.$inertia.form({
                name: this.role.name,
                slug: this.role.slug,
                access: JSON.parse(this.role.access),
            }),
        }
    },
    created() {
        // console.log(this.form.access)
    },
    methods: {
        update() {
            this.form.put(this.route('roles.update', this.role.id))
        },
        destroy() {
            if (confirm('¿Estás seguro de que deseas eliminar este rol?')) {
                this.$inertia.delete(this.route('roles.destroy', this.role.id))
            }
        },
        restore() {
            if (confirm('¿Estás seguro de que deseas restaurar este rol?')) {
                this.$inertia.put(this.route('roles.restore', this.role.id))
            }
        },
    },
}
</script>
