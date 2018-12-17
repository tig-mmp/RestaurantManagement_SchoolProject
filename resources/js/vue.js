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

import Toasted from 'vue-toasted';
Vue.use(Toasted);

import VueSocketio from 'vue-socket.io';
Vue.use(new VueSocketio({
    debug: true,
    connection: 'http://198.196.10.10:8080' //TODO
}));

import store from './stores/global-store';

const menu = Vue.component('menu-restaurant', require('./components/menuRestaurant.vue'));
const login = Vue.component('login', require('./components/login.vue'));
const logout = Vue.component('logout', require('./components/logout.vue'));
const manager = Vue.component('manager', require('./components/manager.vue'));
const profile = Vue.component('profile', require('./components/profile.vue'));
const editProfile = Vue.component('editProfile', require('./components/editProfile.vue'));
const editPassword = Vue.component('editPassword', require('./components/editPassword.vue'));
const shift = Vue.component('shift', require('./components/shift.vue'));
const navbar = Vue.component('navbar', require('./components/navbar.vue'));
const createMeal = Vue.component('createMeal', require('./components/createMeal.vue'));
const createUser = Vue.component('createUser', require('./components/createUser.vue'));
const cashier = Vue.component('cashier', require('./components/cashier.vue'));


const routes = [
    { path: '/', redirect: '/menu' },
    { path: '/menu', component: menu, name: 'menu'},
    { path: '/login', component: login, name: 'login'},
    { path: '/logout', component: logout, name: 'logout'},
    { path: '/manager', component: manager, name: 'manager',
        children:[
            { path:'createUser', component: createUser, name: 'createUserChildren'},
        ]
    },
	{ path: '/profile', component: profile, name: 'profile'},
    { path: '/editProfile', component: editProfile, name: 'editProfile'},
    { path: '/editPassword', component: editPassword, name: 'editPassword'},
    { path: '/createUser', component: createUser, name: 'createUser'},
    { path: '/shift', component: shift, name: 'shift'},
    { path: '/createMeal', component: createMeal, name: 'createMeal'},
    { path: '/cashier', component: cashier, name: 'cashier'},
];


const router = new VueRouter({
    //mode: 'history',
    routes:routes
});

router.beforeEach((to, from, next) => {
    if ((to.name == 'profile') || (to.name == 'logout') || (to.name == 'manager') || (to.name == 'shift')) {
        if (!store.state.user) {
            next("/menu");
            return; 
        }       
    }
    
    if (store.state.user) {
        if (to.name == 'login') {
            next("/menu");
            return;
        }
    }
    next();
});

const app = new Vue({
	router,
    store,
    data:{
    },
    created() {
        this.$store.commit('loadTokenAndUserFromSession');
    },
    methods:{

    },
    sockets: {
        msg_from_server_managers(dataFromServer){
            this.msgManagersTextArea = dataFromServer + '\n' + this.msgManagersTextArea;
        },
    },
    mounted() {
        
    }
}).$mount('#app');