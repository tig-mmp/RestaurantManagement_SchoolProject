/*jshint esversion: 6 */

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
window.Vue = require('vue');

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import store from './stores/global-store';

const menu = Vue.component('menu-restaurant', require('./components/menuRestaurant.vue'));
const login = Vue.component('login', require('./components/login.vue'));
const logout = Vue.component('logout', require('./components/logout.vue'));

const routes = [
    { path: '/', redirect: '/menu' },
    { path: '/menu', component: menu },
    { path: '/login', component: login, name: 'login'},
    { path: '/logout', component: logout, name: 'logout'},
];

const router = new VueRouter({
  routes:routes
});

router.beforeEach((to, from, next) => {
    if ((to.name == 'profile') || (to.name == 'logout')) {
        if (!store.state.user) {
            next("/login");
            return;
        }
    }
    next();
});

const app = new Vue({
	router,
    store,
    created() {
        this.$store.commit('loadTokenAndUserFromSession');
    },
    mounted() {
        
    }
}).$mount('#app');

