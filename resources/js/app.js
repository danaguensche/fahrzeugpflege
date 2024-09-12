require('./bootstrap');

window.Vue = require('vue');
Vue.component('mainapp', require('./components/App.vue').default);

const app = new Vue({
    el: '#app'
})

import App from './components/App.vue';

app.component('app', App);

app.mount('#app');