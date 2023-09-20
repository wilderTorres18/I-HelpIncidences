import { createApp, h } from 'vue'
import { InertiaProgress } from '@inertiajs/progress'
import { createInertiaApp } from '@inertiajs/inertia-vue3'
import Echo from 'laravel-echo'

import axios from 'axios'
import VueAxios from 'vue-axios'

import Donut from 'vue-css-donut-chart';
import 'vue-css-donut-chart/dist/vcdonut.css';

import CKEditor from '@ckeditor/ckeditor5-vue';

window.Pusher = require('pusher-js');
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true
})


InertiaProgress.init()

//     .filter('dayMonth', function(value) {
//         if (value) {
//             return moment(String(value)).format('DD MMM')
//         }
//     })
//     .filter('timeOnly', function(value) {
//         if (value) { return moment(String(value)).format('h:mma')}
//     })
//     .filter('timeAgo', function(value) {
//         if (value) { return moment(String(value)).fromNow()}
//     })
//     .filter('reverseData', function(value) {
//         return value.slice().reverse()
//     })

// .config.globalProperties.$filters = {
//                 timeAgo(value) {
//                     if (value) { return moment(String(value)).fromNow()}
//                 },
//                 dayMonth(value){
//                     return moment(String(value)).format('DD MMM')
//                 },
//                 timeOnly(value){
//                     if (value) { return moment(String(value)).format('h:mma')}
//                 },
//                 reverseData(value){
//                     return value.slice().reverse()
//                 },
//             }

createInertiaApp({
    resolve: name => require(`./Pages/${name}`),
    title: title => title ? `${title}`:``,
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .mixin({ methods: { route : route } })
            .mixin(require('./base'))
            .use(plugin)
            .use(VueAxios, axios)
            .use(Donut)
            .use(CKEditor)
            .mount(el)
    },
})
