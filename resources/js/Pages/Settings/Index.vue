<template>
  <div>
    <Head :title="title" />
    <div class="bg-white rounded-md shadow overflow-hidden mr-2">
      <form @submit.prevent="update">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.app_name" :error="form.errors.app_name" class="pr-6 pb-8 w-full lg:w-1/3" :label="__('App Name')" />
          <select-input v-model="form.default_language" :error="form.errors.default_language" class="pr-6 pb-8 w-full lg:w-1/3" :label="__('Default Language')">
            <option v-for="l in languages" :key="l.id" :value="l.code">{{ l.name }}</option>
          </select-input>
          <div class="pb-8 pr-6 w-full flex lg:w-1/3">
            <file-input v-model="form.logo" :error="form.errors.logo" class="pr-2 w-full lg:w-4/5" type="file" accept="image/png" label="Logo" />
            <div class="w-full lg:w-1/5 flex items-end justify-center">
              <img v-if="form.main_logo" class="block mb-2 " :src="form.main_logo" />
            </div>
          </div>
          <div class="pb-8 pr-6 w-full flex lg:w-1/3">
            <file-input v-model="form.logo_white" :error="form.errors.logo_white" class="pr-2 w-full lg:w-4/5" type="file" accept="image/png" label="Logo White" />
            <div class="w-full lg:w-1/5 flex items-end justify-center">
              <img v-if="form.main_logo_white" class="block mb-2 rounded bg-dark" :src="form.main_logo_white" />
            </div>
          </div>
          <div class="pb-8 pr-6 w-full flex lg:w-1/3">
            <file-input v-model="form.favicon" :error="form.errors.favicon" class="pr-2 w-full lg:w-4/5" type="file" accept="image/png" label="Favicon" />
            <div class="w-full lg:w-1/5 flex items-end justify-center">
              <img v-if="form.main_favicon" class="block mb-2 " :src="form.main_favicon" />
            </div>
          </div>
          <div class="assigned_user pr-6 pb-8 w-full lg:w-full flex flex-col">
            <div class="font-bold text-sm mb-1">{{ __('Enable Options') }} </div>
          </div>
          <div v-for="(option, ni) in form.enable_options" :key="ni" class="flex items-center justify-start pr-6 pb-8 w-full lg:w-1/3">
            <label :for="option.slug" class="flex toggle_swtich items-center cursor-pointer">
              <div class="relative">
                <input :id="option.slug" v-model="option.value" type="checkbox" class="sr-only" />
                <div class="w-10 h-4 bg-gray-400 rounded-full shadow-inner" />
                <div class="dot absolute w-6 h-6 bg-white rounded-full shadow -left-1 -top-1 transition" />
              </div>
              <div class="ml-3 text-sm">
                {{ option.name }}
              </div>
            </label>
          </div>
          <div class="assigned_user pr-6 pb-8 w-full lg:w-full flex flex-col">
            <div class="font-bold text-sm mb-1">{{ __('Email Notifications') }} </div>
          </div>
          <div v-for="(notification, ni) in form.email_notifications" :key="ni" class="flex items-center justify-start pr-6 pb-8 w-full lg:w-1/3">
            <label :for="notification.slug" class="flex toggle_swtich items-center cursor-pointer">
              <div class="relative">
                <input :id="notification.slug" v-model="notification.value" type="checkbox" class="sr-only" />
                <div class="w-10 h-4 bg-gray-400 rounded-full shadow-inner" />
                <div class="dot absolute w-6 h-6 bg-white rounded-full shadow -left-1 -top-1 transition" />
              </div>
              <div class="ml-3 text-sm">
                {{ notification.name }}
              </div>
            </label>
          </div>
          <div class="assigned_user pr-6 pb-8 w-full lg:w-full flex flex-col">
            <div class="font-bold text-sm mb-1">{{ __('Cron Job Instruction') }} </div>
            <p class="mt-2 font-light">If you would like to send mail without delaying you need to set a cron job for that with once every 3 minute.</p>
            <code class="mt-5 mb-5 prose block whitespace-pre text-sm">*/2 * * * * /usr/bin/php artisan queue:work --queue=high,default --stop-when-empty</code>
            <p>After adding the above cron job you need to add <code>QUEUE_ENABLE=true</code> on the .env file </p>
          </div>
          <div class=" pr-6 pb-1 w-full lg:w-full flex flex-col">
            <div class="font-bold text-sm mb-1">{{ __('Hide ticket fields') }} </div>
          </div>


          <div class="flex flex-wrap w-full pb-8">
            <label v-for="htf in hide_ticket_fields" class=" select-none flex items-center pr-8 capitalize" :for="htf">
              <input :id="htf" v-model="form.hide_ticket_fields" class="mr-1" :value="htf" type="checkbox" />
              <span class="text-sm">{{ htf.replace(/_/g, ' ') }}</span>
            </label>
          </div>

          <textarea-input v-model="form.custom_css" :error="form.errors.custom_css" :rows="15" class=" pb-6 w-full" placeholder="your custom css here.." :label="__('Custom CSS')" />
        </div>
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center">
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">{{ __('Save') }}</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { Link, Head } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import Pagination from '@/Shared/Pagination'
import TextInput from '@/Shared/TextInput'
import TextareaInput from '@/Shared/TextareaInput'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import FileInput from '@/Shared/FileInput'

export default {
  metaInfo: { title: 'Priorities' },
  components: {
    Icon,
    Link,
      Head,
      FileInput,
    Pagination,
      TextInput,
      TextareaInput,
      SelectInput,
      LoadingButton,
  },
  layout: Layout,
  props: {
      title: String,
      settings: Object,
      languages: Array,
      pusher: Boolean,
  },
    remember: 'form',
  data() {
    return {
        form: this.$inertia.form({
            app_name: this.settings.app_name.value,
            logo: null,
            logo_white: null,
            favicon: null,
            main_logo: '/images/logo.png',
            main_logo_white: '/images/logo_white.png',
            main_favicon: '/favicon.png',
            default_language: this.settings.default_language.value,
            footer_text: this.settings.footer_text.value,
            custom_css: typeof this.settings.custom_css !== 'undefined' && this.settings.custom_css ? this.settings.custom_css.value : null,
            email_notifications: this.settings.email_notifications.value.map(en=>{return {'name': en.name,'slug': en.slug,'value': !!en.value}}),
            enable_options: this.settings.enable_options.value.map(eo=>{return {'name': eo.name,'slug': eo.slug,'value': !!eo.value}}),
            hide_ticket_fields: this.settings.hide_ticket_fields && this.settings.hide_ticket_fields.value ? this.settings.hide_ticket_fields.value : [],
        }),
        hide_ticket_fields: [ 'department', 'assigned_to', 'ticket_type', 'category'],
    }
  },
    created() {
      console.log(this.settings.hide_ticket_fields)
      console.log(this.form)
        console.log(this.form.enable_options)
    },
    methods: {
      update() {
          const vm = this
          const enableChat = this.form.enable_options.find(o=>o.slug='chat')
          if(!!enableChat.value && !this.pusher){
              alert('Configure el empujador para habilitar el chat.')
              return
          }
          this.form.post(this.route('global.update'), {
              onSuccess: () => {
                  const successMessage = vm.$page.props.flash.success
                  this.form.logo = null
                  this.form.logo_white = null
                  if(successMessage){
                      window.location.reload()
                  }
              },
          })
      },
  },
}
</script>
