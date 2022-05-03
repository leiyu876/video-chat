require('./bootstrap');

import Vue from 'vue'
window.Vue = Vue;

Vue.component('users-component', require('./components/UsersComponent').default);

const app = new Vue({
    el: '#app'
});
