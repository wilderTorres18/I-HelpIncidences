<template>
  <div>
      <Head :title="__(title)" />
    <div class="bg-white rounded-md shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="update">
          <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
              <text-input v-model="form.name" :error="form.errors.name" class="pr-6 pb-8 w-full lg:w-1/2" :label="__('Name')" />
          </div>
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center">
          <button v-if="!department.deleted_at"  class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">{{ __('Delete Department') }}
            </button>
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">{{ __('Update Department') }}
            </loading-button>
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
    department: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
          name: this.department.name,
      }),
    }
  },
  methods: {
    update() {
      this.form.put(this.route('departments.update', this.department.id))
    },
    destroy() {
      if (confirm('¿Está seguro de que desea eliminar este departamento?')) {
        this.$inertia.delete(this.route('departments.destroy', this.department.id))
      }
    },
    restore() {
      if (confirm('¿Está seguro de que desea restaurar este departamento?')) {
        this.$inertia.put(this.route('departments.restore', this.department.id))
      }
    },
  },
}
</script>
