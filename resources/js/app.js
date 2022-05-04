require('./bootstrap');

import Vue from 'vue'
window.Vue = Vue;

Vue.component('users-component', require('./components/UsersComponent').default);
Vue.component('video-chat', require('./components/VideoChat.vue').default);
Vue.component('livestream', require('./components/Livestream.vue').default);

const app = new Vue({
    el: '#app'
});
