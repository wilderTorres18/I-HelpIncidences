<template>
  <div>
      <Head :title="__(title)" />
    <div class="bg-white rounded-md shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="update">
          <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
              <text-input v-model="form.name" :error="form.errors.name" class="pr-6 pb-8 w-full " label="Name" />
<!--              <text-input v-model="form.color" :error="form.errors.color" class="pr-6 pb-8 w-full lg:w-1/2" label="Color" />-->
          </div>
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center">
          <button v-if="!category.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Eliminar categoria</button>
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Actualizar categoria</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import LoadingButton from '@/Shared/LoadingButton'
import { Link, Head } from '@inertiajs/inertia-vue3'

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
    category: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
          name: this.category.name,
          color: this.category.color,
      }),
    }
  },
  methods: {
    update() {
      this.form.put(this.route('categories.update', this.category.id))
    },
    destroy() {
      if (confirm('¿Estás seguro de que deseas eliminar esta categoria?')) {
        this.$inertia.delete(this.route('categories.destroy', this.category.id))
      }
    },
    restore() {
      if (confirm('¿Estás seguro de que deseas restaurar esta categoria?')) {
        this.$inertia.put(this.route('categories.restore', this.category.id))
      }
    },
  },
}
</script>
