<template>
  <div>
    <Head :title="__(title)" />
    <div class="flex flex-wrap">
      <div class="max-w-full lg:w-3/5">
        <form class="bg-white rounded-md shadow overflow-hidden mr-2" @submit.prevent="update">
          <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
            <!--Super Admin -->
            <select-edit-input
              v-model="form.user_id" placeholder="Search customer" :on-input="doFilter"
              :items="customers" :error="form.errors.user_id"
              class="pr-6 pb-8 w-full lg:w-1/3" :label="__('Customer')"
              :value="ticket.user"
              :editable="user_access.ticket.update && ticket.status.slug !== 'closed'"
            />

            <select-edit-input
              v-model="form.priority_id" placeholder="Search priority"
              :items="priorities" :error="form.errors.priority_id"
              class="pr-6 pb-8 w-full lg:w-1/3" :label="__('Priority')"
              :value="ticket.priority"
              :editable="user_access.ticket.update && ticket.status.slug !== 'closed'"
            />
            <select-edit-input
              v-if="!(hidden_fields && hidden_fields.includes('department'))"
              v-model="form.department_id" placeholder="Search department"
              :items="departments" :error="form.errors.department_id"
              class="pr-6 pb-8 w-full lg:w-1/3" :label="__('Department')"
              :value="ticket.department"
              :editable="user_access.ticket.update && ticket.status.slug !== 'closed'"
            />

            <select-edit-input
              v-if="!(hidden_fields && hidden_fields.includes('assigned_to'))"
              v-model="form.assigned_to" placeholder="Search user"
              :on-input="doFilterUsersExceptCustomer"
              :items="usersExceptCustomers" :error="form.errors.assigned_to"
              class="pr-6 pb-8 w-full lg:w-1/3" :label="__('Assigned to')"
              :value="ticket.assigned_user??'Not Assigned'"
              :editable="user_access.ticket.update && ticket.status.slug !== 'closed'"
            />

            <select-edit-input
              v-model="form.status_id" placeholder="Select status to change"
              :items="statuses" :error="form.errors.status_id"
              class="pr-6 pb-8 w-full lg:w-1/3" :label="__('Status')"
              :value="ticket.status?ticket.status.name:'N/A'"
              :editable="ticket.status.slug !== 'closed'"
            />

            <select-edit-input
              v-if="!(hidden_fields && hidden_fields.includes('ticket_type'))"
              v-model="form.type_id" placeholder="Search type"
              :items="types" :error="form.errors.type_id"
              class="pr-6 pb-8 w-full lg:w-1/3" :label="__('Ticket type')"
              :value="ticket.type"
              :editable="user_access.ticket.update && ticket.status.slug !== 'closed'"
            />

            <select-edit-input
              v-if="!(hidden_fields && hidden_fields.includes('category'))"
              v-model="form.category_id" placeholder="Search category"
              :items="categories" :error="form.errors.category_id"
              class="pr-6 pb-8 w-full lg:w-1/3" :label="__('Category')"
              :value="ticket.category"
              :editable="user_access.ticket.update && ticket.status.slug !== 'closed'"
            />

            <div class="assigned_user pr-6 pb-8 w-full lg:w-1/3 flex flex-col">
              <div class="font-bold text-sm mb-1">{{ __('Created') }}</div>
              <div class="font-light text-sm">{{ moment(ticket.created_at).fromNow() }}</div>
            </div>

            <text-input
              v-if="user_access.ticket.update" v-model="form.subject" :error="form.errors.subject"
              class="pr-6 pb-8 w-full lg:w-2/3" :label="__('Subject')"
            />
            <div v-else class="assigned_user pr-6 pb-8 w-full lg:w-full flex flex-col">
              <div class="font-bold text-sm mb-1">{{ __('Subject') }}</div>
              <div class="font-light text-sm">{{ ticket.subject }}</div>
            </div>

            <div class="assigned_user pr-6 pb-8 w-full lg:w-full flex flex-col">
              <span class="font-bold text-sm mb-2">{{ __('Request Details') }}: </span>
              <div class="font-light text-sm" v-html="ticket.details" />
            </div>


            <!-- Super Admin Comment -->
            <input
              ref="file" type="file"
              accept=".xlsx,.xls,image/*,.doc, .docx,.ppt, .pptx,.txt,.pdf, .zip" class="hidden"
              multiple="multiple" @change="fileInputChange"
            />
            <div class="pr-6 pb-8 w-full lg:w-full flex-col">
              <button
                type="button" class="btn flex justify-center items-center pb-3 border-0 pl-0"
                @click="fileBrowse"
              >
                <icon name="file" class="flex-shrink-0 h-5 fill-gray-400 pr-1" />
                <strong>{{ __('Attach File') }}</strong>
              </button>
              <div
                v-for="(file, fi) in attachments" v-if="attachments.length"
                :key="fi" class="flex items-center justify-between pr-6 pt-8 w-full"
              >
                <div class="flex-1 pr-1">
                  {{ file.name }} <span class="text-gray-500 text-xs">({{
                    getFileSize(file.size)
                  }})</span> <a
                    v-if="file.user" class="text-sm"
                    :href="route('users.edit', file.user.id)"
                  >{{
                    file.user.first_name
                  }} {{ file.user.last_name }}</a> at <span class="text-sm">{{
                    file.created_at
                  }}</span>
                </div>
                <div class="a__buttons flex justify-end items-center ">
                  <button
                    type="button" class="btn flex items-center "
                    @click="downloadAttachment(file)"
                  >
                    {{ __('Download') }}
                  </button>
                  <button
                    type="button" class="btn flex items-center ml-3"
                    @click="removeAttachment(file, fi)"
                  >
                    {{ __('Remove') }}
                  </button>
                </div>
              </div>
              <div
                v-for="(file, fi) in form.files"
                v-if="form.files.length"
                :key="fi" class="flex items-center justify-between pr-6 pt-8 w-full lg:w-1/2"
              >
                <div class="flex-1 pr-1">
                  {{ file.name }} <span class="text-gray-500 text-xs">({{
                    getFileSize(file.size)
                  }})</span>
                </div>
                <button
                  v-if="ticket.status && ticket.status.slug !== 'closed'" type="button"
                  class="btn flex justify-center items-center" @click="fileRemove(file, fi)"
                >
                  {{ __('Remove') }}
                </button>
              </div>
            </div>

            <div
              v-if="ticket.status && ticket.status.slug === 'closed' && ticket.review"
              class="pr-6 pb-8 w-full lg:w-1/3 flex items-center"
            >
              <strong>{{ __('Rating') }}: </strong> &nbsp; {{ ticket.review.rating }}
              <p>
                {{ ticket.review.review }}
              </p>
            </div>

            <div
              v-if="ticket.status && ticket.status.slug === 'closed' && !ticket.review && auth.user.role.slug === 'customer'"
              class="flex flex-col w-full"
            >
              <div class="assigned_user star__review pb-5 w-full lg:w-1/3 flex flex-col">
                <div class="font-bold">{{ __('How do you rate this support service?') }}</div>
                <div class="star-rating pt-5">
                  <input id="5-stars" v-model="form.rating" type="radio" name="rating" value="5" />
                  <label for="5-stars" class="star">&#9733;</label>
                  <input id="4-stars" v-model="form.rating" type="radio" name="rating" value="4" />
                  <label for="4-stars" class="star">&#9733;</label>
                  <input id="3-stars" v-model="form.rating" type="radio" name="rating" value="3" />
                  <label for="3-stars" class="star">&#9733;</label>
                  <input id="2-stars" v-model="form.rating" type="radio" name="rating" value="2" />
                  <label for="2-stars" class="star">&#9733;</label>
                  <input id="1-star" v-model="form.rating" type="radio" name="rating" value="1" />
                  <label for="1-star" class="star">&#9733;</label>
                </div>
              </div>
              <textarea-input
                v-model="form.review" :error="form.errors.review"
                class="pr-6 pb-4 w-full lg:w-1/3" :label="__('Feedback')"
              />
              <div class="flex lg:w-1/4 mb-4">
                <button class="btn-indigo" type="submit">{{ __('Submit') }}</button>
              </div>
            </div>
          </div>
          <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center">
            <button
              v-if="user_access.ticket.delete" class="text-red-600 hover:underline" tabindex="-1"
              type="button" @click="destroy"
            >
              {{ __('Delete') }}
            </button>
            <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">
              {{ __('Save') }}
            </loading-button>
          </div>
        </form>
      </div>
      <div class="max-w-full lg:w-2/5">
        <div class="bg-white rounded-md shadow overflow-hidden ml-2 chat-area comment-box flex-1 flex flex-col">
          <div class="flex-3">
            <div class="chat-header flex flex-col pb-3">
              <h3 class="text-xl">{{ __('Ticket discussion') }}</h3>
              <p class="text-sm font-light">
                {{ __('Comment histories for this ticket will be available here.') }}
              </p>
            </div>
          </div>
          <div class="messages flex-1 overflow-auto reverse__order">
            <div
              v-for="(comment, index) in comments.slice().reverse()" :key="index"
              class="message mb-4 flex"
            >
              <div v-if="comment.user_id !== user.id" class="flex-2">
                <div class="w-12 h-12 relative">
                  <span
                    v-if="comment.user" class="w-12 h-12 rounded-full mx-auto user_icon"
                    alt="chat-user"
                  >{{ Array.from(comment.user.first_name)[0] }}</span>
                </div>
              </div>
              <div v-if="comment.user_id !== user.id" class="flex-1 px-2">
                <h3 v-if="comment.user" class="font-bold pb-2 text-sm pt-1">
                  {{
                    comment.user.first_name
                  }} {{ comment.user.last_name }}
                </h3>
                <div
                  class="inline-block bg-gray-300 user-comment-round p-2 px-4 text-gray-700 leading-5"
                >
                  <span>{{ comment.details }}</span>
                </div>
                <div class="pl-4">
                  <small
                    class="text-gray-500"
                  >{{ moment(comment.updated_at).fromNow(true) }}</small>
                </div>
              </div>

              <div v-if="comment.user_id === user.id" class="flex-1 px-2 text-right">
                <div class="inline-block bg-blue rounded-full p-2 px-4 text-white leading-5">
                  <span>{{ comment.details }}</span>
                </div>
                <div class="pr-4">
                  <small
                    class="text-gray-500"
                  >{{ moment(comment.updated_at).fromNow(true) }}</small>
                </div>
              </div>
            </div>
          </div>
          <div class="flex-2 pt-4 pb-3">
            <div class="write bg-white shadow flex rounded-lg">
              <div class="flex-3 flex content-center items-center text-center p-4 pr-0">
                <span class="block text-center text-gray-400 hover:text-gray-800">
                  <svg
                    fill="none" stroke-linecap="round" stroke-linejoin="round"
                    stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"
                    class="h-6 w-6"
                  ><path
                    d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                  /></svg>
                </span>
              </div>
              <div class="flex-1">
                <textarea
                  v-model="comment" name="message" class="w-full block outline-none py-4 px-4 text-sm bg-transparent overflow-hidden"
                  rows="1"
                  :placeholder="__('Escribe un comentario y presiona enter para enviar...')" autofocus
                  @keydown.enter.exact.prevent="submitComment"
                />
              </div>
              <div class="flex-2 w-35 p-2 flex content-center items-center">
                <div class="flex-1 text-center">
                  <span class="text-gray-400 hover:text-gray-800">
                    <span class="inline-block align-text-bottom">
                      <svg
                        fill="none" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"
                        class="w-6 h-6"
                      ><path
                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"
                      /></svg>
                    </span>
                  </span>
                </div>
                <div class="flex-1">
                  <button
                    class="bg-blue w-10 h-10 rounded-full flex justify-center items-center"
                    @click="submitComment"
                  >
                    <icon class="w-4 h-4 fill-gray-100" name="send" />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {Link, Head} from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
import TextareaInput from '@/Shared/TextareaInput'
import LoadingButton from '@/Shared/LoadingButton'
import SelectInputFilter from '@/Shared/SelectInputFilter'
import SelectEditInput from '@/Shared/SelectEditInput'
import moment from 'moment'


export default {
    components: {
        LoadingButton,
        SelectInput,
        TextInput,
        TextareaInput,
        Link,
        Head,
        Icon,
        SelectInputFilter,
        SelectEditInput,
    },
    layout: Layout,
    props: {
        title: String,
        ticket: Object,
        priorities: Array,
        statuses: Array,
        types: Array,
        departments: Array,
        categories: Array,
        customers: Array,
        usersExceptCustomers: Array,
        attachments: Array,
        comments: Array,
        auth: Object,
        hidden_fields: Object,
    },
    remember: false,
    data() {
        return {
            user: this.$page.props.auth.user,
            type_status: [],
            comment: '',
            editCustomer: false,
            user_access: this.$page.props.auth.user.access,
            form: this.$inertia.form({
                user_id: this.ticket.user_id,
                priority_id: this.ticket.priority_id,
                status_id: this.ticket.status_id,
                department_id: this.ticket.department_id,
                category_id: this.ticket.category_id,
                assigned_to: this.ticket.assigned_to,
                type_id: this.ticket.type_id,
                subject: this.ticket.subject,
                details: this.ticket.details,
                files: this.ticket.files,
                comments: this.ticket.comments,
                created_at: this.ticket.created_at,
                removedFiles: [],
                rating: 0,
                review: '',
            }),
        }
    },
    created() {
        if (this.auth.user.role.slug === 'customer' && this.statuses.length) {
            this.type_status = this.statuses.filter(status => (status.id === this.form.status_id) || status.name.match(/Close.*/))
        } else {
            this.type_status = this.statuses
        }
        this.moment = moment
    },
    methods: {
        doFilter(e) {
            this.axios.get(this.route('filter.customers', {search: e.target.value})).then((res) => {
                this.customers.splice(0, this.customers.length, ...res.data)
            })
        },
        doFilterUsersExceptCustomer(e) {
            this.axios.get(this.route('filter.users_except_customer', {search: e.target.value})).then((res) => {
                this.usersExceptCustomers.splice(0, this.usersExceptCustomers.length, ...res.data)
            })
        },
        fileInputChange(e) {
            let selectedFiles = e.target.files
            for (let i = 0; i < selectedFiles.length; i++) {
                this.form.files.push(selectedFiles[i])
            }
        },
        fileRemove(image, index) {
            this.form.files.splice(index, 1)
        },
        fileBrowse() {
            this.$refs.file.click()
        },
        downloadAttachment(file) {
            const link = document.createElement('a')
            link.href = window.location.origin + '/files/' + file.path
            link.download = file.name
            link.click()
        },
        removeAttachment(file, index) {
            this.attachments.splice(index, 1)
            this.form.removedFiles.push(file.id)
        },
        getFileSize(size) {
            const i = Math.floor(Math.log(size) / Math.log(1024))
            return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'KB', 'MB', 'GB', 'TB'][i]
        },
        uploadFiles() {
            this.form.files = this.$refs.files.files
        },
        update() {
            this.form.post(this.route('tickets.update', this.ticket.id))
            this.form.files = []
            this.form.comment = ''
        },
        destroy() {
            if (confirm('¿Estás seguro que quieres eliminar esta incidenicia?')) {
                this.$inertia.delete(this.route('tickets.destroy', this.ticket.id))
            }
        },
        restore() {
            if (confirm('¿Estás seguro que quieres restaurar esta incidencia?')) {
                this.$inertia.put(this.route('tickets.restore', this.ticket.id))
            }
        },
        submitComment() {
            var that = this
            const messageData = {
                comment: this.comment,
                user_id: this.user.id,
                _token: this.$page.props.csrf_token,
                ticket_id: this.ticket.id,
            }
            this.comment = ''
            this.axios.post(this.route('ticket.comment'), messageData).then((response) => {
                if (response.data) {
                    that.comments.push(response.data)
                }
                this.$emit('comment submitted!')
            }).catch((error) => {
                console.log(error)
            })
        },
    },
}
</script>
