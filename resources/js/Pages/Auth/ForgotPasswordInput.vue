<template>
  <Head title="Reset Password" />
  <div class="p-6 min-h-screen flex justify-center items-center light">
    <flash-messages />
    <div class="w-full max-w-md">
      <Link :href="route('home')"><logo class="block mx-auto w-full max-w-xs fill-white" height="50" /></Link>
      <form class="mt-8 bg-white dark:bg-slate-900 border border-gray-100 rounded-lg shadow-xl overflow-hidden" autocomplete="off" @submit.prevent="resetPassword">
        <div class="px-10 py-12">
          <h2 class="text-center font-bold text-xl">{{ __('Restablecer contraseña') }}</h2>
          <div class="mx-auto mt-3 w-24 border-b" />
          <text-input v-model="form.email" :error="form.errors.email" class="mt-10" :label="__('Correo Electrónico')" type="email" autofocus autocomplete="off" aria-autocomplete="none" />
          <text-input v-model="form.password" :error="form.errors.password" class="mt-6" :label="__('Contraseña')" type="password" autocomplete="off" aria-autocomplete="none" />
          <text-input v-model="form.password_confirmation" :error="form.errors.password_confirmation" class="mt-6" :label="__('Confirmar contraseña')" type="password" autocomplete="off" aria-autocomplete="none" />
          <loading-button :loading="form.processing" class="ml-auto btn-indigo w-full items-center justify-center mt-8" type="submit">{{ __('Restablecer contraseña') }}</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Logo from '@/Shared/Logo'
import TextInput from '@/Shared/TextInput'
import LoadingButton from '@/Shared/LoadingButton'
import { Head, Link } from '@inertiajs/inertia-vue3'
import FlashMessages from '@/Shared/FlashMessages'

export default {
  metaInfo: { title: 'Login' },
  components: {
    LoadingButton,
    Logo,
    TextInput,
      Head,
      Link,
      FlashMessages,
  },
    props: {
        is_demo: Number,
        token: String,
    },
  data() {
    return {
      form: this.$inertia.form({
        email: '',
        password: '',
          password_confirmation: '',
          token: this.token,
      }),
    }
  },
  methods: {
      resetPassword() {
          this.form.post(this.route('password.reset.store'))
      },
  },
}
</script>
