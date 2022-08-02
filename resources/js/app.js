/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
import {library} from '@fortawesome/fontawesome-svg-core';
import {fas} from '@fortawesome/free-solid-svg-icons';
import axios from 'axios';
import './bootstrap.bundle.min.js';

window.Vue = require('vue').default;

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('employee-component', require('./components/EmployeeComponent.vue').default);
library.add(fas);
Vue.component('history-component', require('./components/HistoryComponent.vue').default);
Vue.component('edit-component', require('./components/EditComponent.vue').default);
Vue.prototype.$userName = document.querySelector("meta[name='user-name']").getAttribute('content');

import VueSweetalert2 from 'vue-sweetalert2';
Vue.use(VueSweetalert2);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// const router = new VueRouter({ mode: 'history', routes: routes});
// const app = new Vue(Vue.util.extend({ router }, App)).$mount('#app');
const app = new Vue({
    el: '#app',
});
