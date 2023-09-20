import Vue from 'vue'
import VueMeta from 'vue-meta'
import PortalVue from 'portal-vue'
import { InertiaProgress } from '@inertiajs/progress'
import { createInertiaApp } from '@inertiajs/inertia-vue'
import moment from 'moment'
import Echo from 'laravel-echo'
import axios from 'axios'
import VueAxios from 'vue-axios'
import VueSweetalert2 from 'vue-sweetalert2'

window.Pusher = require('pusher-js')

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true
})


Vue.config.productionTip = false
Vue.mixin({ methods: { route: window.route } })
Vue.mixin(require('./base'))
Vue.use(PortalVue)
Vue.use(VueMeta)
Vue.use(VueAxios, axios)
Vue.use(VueSweetalert2)
Vue.filter('dayMonth', function(value) {
    if (value) {
        return moment(String(value)).format('DD MMM')
    }
})
Vue.filter('timeOnly', function(value) {
    if (value) { return moment(String(value)).format('h:mma')}
})
Vue.filter('timeAgo', function(value) {
    if (value) { return moment(String(value)).fromNow()}
})

Vue.filter('reverseData', function(value) {
    return value.slice().reverse()
})


Vue.prototype.moment = moment

InertiaProgress.init()

createInertiaApp({
  resolve: name => require(`./Pages/${name}`),
  setup({ el, app, props }) {
    new Vue({
      metaInfo: {
        titleTemplate: title => (title ? `${title} - eTicket` : 'eTicket'),
      },
      render: h => h(app, props),
    }).$mount(el)
  },
})


