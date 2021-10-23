/*jshint esversion: 6 */

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./bootstrap");
window.Vue = require("vue");

import VueRouter from "vue-router";
Vue.use(VueRouter);

import Toasted from "vue-toasted";
Vue.use(Toasted);

import VueSocketio from "vue-socket.io";
Vue.use(
    new VueSocketio({
        debug: true,
        connection: process.env.VUE_APP_WEBSOCKET_URL
    })
);
console.log(process.env);
import store from "./stores/global-store";

const menu = Vue.component(
    "menu-restaurant",
    require("./components/menuRestaurant.vue")
);
const login = Vue.component("login", require("./components/login.vue"));
const logout = Vue.component("logout", require("./components/logout.vue"));
const manager = Vue.component("manager", require("./components/manager.vue"));
const profile = Vue.component("profile", require("./components/profile.vue"));
const editProfile = Vue.component(
    "editProfile",
    require("./components/editProfile.vue")
);
const editPassword = Vue.component(
    "editPassword",
    require("./components/editPassword.vue")
);
const shift = Vue.component("shift", require("./components/shift.vue"));
const navbar = Vue.component("navbar", require("./components/navbar.vue"));
const createUser = Vue.component(
    "createUser",
    require("./components/Manager/createUser.vue")
);
const cashier = Vue.component("cashier", require("./components/cashier.vue"));
const managerItemList = Vue.component(
    "managerItemList",
    require("./components/Manager/managerItemList.vue")
);
const editItem = Vue.component(
    "editItem",
    require("./components/Manager/editItem.vue")
);
const createItem = Vue.component(
    "createItem",
    require("./components/Manager/createItem.vue")
);
const managerTableList = Vue.component(
    "managerTableList",
    require("./components/Manager/managerTableList.vue")
);
const managerUserList = Vue.component(
    "managerUserList",
    require("./components/Manager/managerUserList.vue")
);
const editUser = Vue.component(
    "editUser",
    require("./components/Manager/editUser.vue")
);
const dashboard = Vue.component(
    "dashboard",
    require("./components/Manager/dashboard.vue")
);
const managersNotifications = Vue.component(
    "managersNotifications",
    require("./components/Manager/managersNotifications.vue")
);

const routes = [
    { path: "/", redirect: "/menu" },
    { path: "/menu", component: menu, name: "menu" },
    { path: "/login", component: login, name: "login" },
    { path: "/logout", component: logout, name: "logout" },
    {
        path: "/manager",
        component: manager,
        name: "manager",
        children: [
            {
                path: "managerItemList",
                component: managerItemList,
                name: "managerItemList"
            },
            { path: "editItem", component: editItem, name: "editItem" },
            { path: "createItem", component: createItem, name: "createItem" },
            {
                path: "managerTableList",
                component: managerTableList,
                name: "managerTableList"
            },
            {
                path: "managerUserList",
                component: managerUserList,
                name: "managerUserList"
            },
            { path: "createUser", component: createUser, name: "createUser" },
            { path: "editUser", component: editUser, name: "editUser" },
            { path: "dashboard", component: dashboard, name: "dashboard" }
        ]
    },
    { path: "/profile", component: profile, name: "profile" },
    { path: "/editProfile", component: editProfile, name: "editProfile" },
    { path: "/editPassword", component: editPassword, name: "editPassword" },
    { path: "/shift", component: shift, name: "shift" },
    { path: "/cashier", component: cashier, name: "cashier" }
];

const router = new VueRouter({
    //mode: 'history',
    routes: routes
});

router.beforeEach((to, from, next) => {
    if (
        to.name == "profile" ||
        to.name == "logout" ||
        to.name == "manager" ||
        to.name == "shift"
    ) {
        if (!store.state.user) {
            next("/menu");
            return;
        }
    }
    if (to.name == "manager") {
        if (store.state.user.type != "manager") {
            next("/menu");
            return;
        }
    }
    if (to.name == "login") {
        if (store.state.user) {
            next("/menu");
            return;
        }
    }
    next();
});

const app = new Vue({
    router,
    store,
    data: {},
    created() {
        this.$store.commit("loadTokenAndUserFromSession");
    },
    methods: {},
    sockets: {
        connect() {
            console.log(
                "socket connected (socket ID = " + this.$socket.id + ")"
            );
            if (
                this.$store.state.user !== null &&
                this.$store.state.user.shift_active === 1
            ) {
                this.$socket.emit("user_enter", store.state.user);
            }
        },
        msg_from_server_managers(dataFromServer) {
            this.msgManagersTextArea =
                dataFromServer + "\n" + this.msgManagersTextArea;
        }
    },
    mounted() {}
}).$mount("#app");
