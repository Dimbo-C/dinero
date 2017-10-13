import Vue from "vue";
import axios from "axios";
import lodash from "lodash";
import moment from "moment";
import VueLocalStorage from "vue-ls";
import VTooltip from "v-tooltip";
import BootstrapVue from "bootstrap-vue"

import {Pagination} from "vue-pagination-2";

window.VueLocalStorage = VueLocalStorage;
Vue.use(BootstrapVue);
Vue.use(VueLocalStorage);
Vue.use(VTooltip);

window.Bus = new Vue();

window._ = lodash;
window.moment = moment;

window.moment.locale('ru');

Vue.component('pagination', Pagination);

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

/*
 * Load jQuery and Bootstrap jQuery, used for front-end interaction.
 */
try {
    window.$ = window.jQuery = require('jquery');

    require('bootstrap-sass');
} catch (e) {
}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

/**
 * Define the Vue filters.
 */
require('./filters');

/**
 * Load the App form utilities.
 */
require('./forms/bootstrap');

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: 'your-pusher-key'
// });
