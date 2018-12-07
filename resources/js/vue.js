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
const manager = Vue.component('manager', require('./components/manager.vue'));
const profile = Vue.component('profile', require('./components/profile.vue'));
const editProfile = Vue.component('editProfile', require('./components/editProfile.vue'));
const editPassword = Vue.component('editPassword', require('./components/editPassword.vue'));
const shift = Vue.component('shift', require('./components/shift.vue'));

import createUser from './components/createUser.vue';

const routes = [
    { path: '/', redirect: '/menu' },
    { path: '/menu', component: menu, name: 'menu'},
    { path: '/login', component: login, name: 'login'},
    { path: '/logout', component: logout, name: 'logout'},
    { path: '/manager', component: manager, name: 'manager'},
	{ path: '/profile', component: profile, name: 'profile'},
    { path: '/editProfile', component: editProfile, name: 'editProfile'},
    { path: '/editPassword', component: editPassword, name: 'editPassword'},
    { path: '/createUser', component: createUser, name: 'createUser'},
    { path: '/shift', component: shift, name: 'shift'},
];

const router = new VueRouter({
    mode: 'history',
    routes:routes
});

router.beforeEach((to, from, next) => {
    if (!store.state.user) {
        if ((to.name == 'profile') || (to.name == 'logout') || (to.name == 'manager') || (to.name == 'shift')) {
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

/*
    no webSsocketServer:
    socket.on('msg_from_worker_to_managers', function (msg, userInfo) {
		if (userInfo !== undefined) {
			io.sockets.to('manager').emit('msg_from_server_managers', userInfo.name +': "' + msg + '"');
		}
	});
*/

const app = new Vue({
	router,
    store,
    data:{
        msgManagersText: "",
        msgManagersTextArea: "",
    },
    created() {
        this.$store.commit('loadTokenAndUserFromSession');
    },
    methods:{
        sendManagersMsg: function(){
            if (this.$store.state.user === null) {
                this.$toasted.error('User is not logged in!');
            } else {
                this.$socket.emit('msg_from_worker_to_managers', this.msgManagersText, this.$store.state.user);
            }
            this.msgManagersText = "";
        },
    },
    sockets: {
        msg_from_server_managers(dataFromServer){
            this.msgManagersTextArea = dataFromServer + '\n' + this.msgManagersTextArea ;
        },
    },
    mounted() {
        
    }
}).$mount('#app');