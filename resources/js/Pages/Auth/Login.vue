<template>
    <Head title="Login" />
  <div class="p-6 min-h-screen flex justify-center items-center light">
      <flash-messages />
    <div class="w-full max-w-md">
        <Link :href="route('home')"><logo class="block mx-auto w-full max-w-xs fill-white" height="50" /></Link>
      <form class="mt-8 bg-white dark:bg-slate-900 border border-gray-100 rounded-lg shadow-xl overflow-hidden" @submit.prevent="login">
        <div class="px-10 py-12">
          <h2 class="text-center font-bold text-xl">{{ __('Login - Support Center!') }}</h2>
          <div class="mx-auto mt-3 w-24 border-b" />
          <text-input v-model="form.email" :error="form.errors.email" class="mt-10" label="Email" type="email" autofocus autocapitalize="off" />
          <text-input v-model="form.password" :error="form.errors.password" class="mt-6" label="Password" type="password" />
          <label class="mt-6 select-none flex items-center" for="remember">
            <input id="remember" v-model="form.remember" class="mr-1" type="checkbox" />
            <span class="text-sm">Remember Me</span>
          </label>
            <loading-button :loading="form.processing" class="ml-auto btn-indigo w-full items-center justify-center mt-8" type="submit">{{ __('Login') }}</loading-button>
            <div class="mt-5 flex justify-center"><Link class="ml-2 " :href="route('password.reset')">{{ __('Forgot your password?') }}</Link></div>
            <div class="mt-5 flex justify-center">Don’t have account? <Link class="ml-2 " :href="route('register')">{{ __('Register') }}</Link></div>
        </div>
          <div class=" py-4 bg-gray-100 dark:bg-slate-900 border-t border-gray-100 dark:border-gray-700 flex flex-col gap-1 login-as items-center" v-if="is_demo">
              <h2 class="text-sm font-bold mb-3">Login As: </h2>
              <div class="action flex flex-col sm:flex-row gap-3">
                  <button class=" btn-indigo" @click="autofillLogin($event, 'admin')">Admin</button>
                  <button class=" btn-indigo" @click="autofillLogin($event,'manager')">Manager</button>
                  <button class=" btn-indigo" @click="autofillLogin($event,'customer')">Customer</button>
              </div>
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
      FlashMessages,
    LoadingButton,
    Logo,
    TextInput,
      Head,
      Link,
  },
    props: {
        is_demo: Number
    },
  data() {
    return {
      form: this.$inertia.form({
        email: '',
        password: '',
        remember: false,
      }),
    }
  },
  methods: {
      login() {
          this.form.post(this.route('login.store'))
      },
      autofillLogin(e, role){
          e.preventDefault()
          const roleEmails = { 'admin': 'john.due.helo@mail.com', 'manager': 'robert.slaughter@mail.com', 'customer' : 'mmarks@example.com'}
          this.form.email = roleEmails[role]
          this.form.password = 'secret'
          this.login();
      }
  }
}
</script>
