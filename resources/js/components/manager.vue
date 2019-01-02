<template>
    <div class="wrapper" v-if="this.$store.state.user.shift_active === 1">
        <nav id="sidebar" :class="{active:showSideBar}" class="managerLeftMenu">
            <div class="sidebar-header">
                <h3>Manager</h3>
            </div>

            <ul class="list-unstyled components">
                <li>
                    <router-link to="/manager/dashboard">Dashboard</router-link>
                </li>
                <li>
                    <router-link to="/manager/managerItemList">Items</router-link>
                </li>
                <li>
                    <router-link to="/manager/managerTableList">Tables</router-link>
                </li>
                <li>
                    <router-link to="/manager/managerUserList">Users</router-link>
                </li>

            </ul>
        </nav>

            <div class="col-sm">
                <button type="button" id="sidebarCollapse" class="navbar-btn" :class="{active:showSideBar}" @click="sideBarAction()">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                <div class="container-fluid mt-5">
                    <router-view></router-view>
                </div>
            </div>
    </div>
    <div class="jumbotron" v-else>
        <h1>Shift not started</h1>
    </div>
</template>
<script>
    export default {
        data: function(){
            return { 
                editedItem: null,
                editedUser: null,
                showSideBar: false,
            }
        },
        methods:{
            sideBarAction(){
                this.showSideBar = this.showSideBar === true ? false : true;
            }
        },
        sockets: {
            msg_from_server_managers(dataFromServer){
                //this.msgManagersTextArea = dataFromServer + '\n' + this.msgManagersTextArea;
                let toast = this.$toasted.show(dataFromServer[0], {
                    theme: "outline",
                    position: "top-center",
                    duration : 1000
                });
            },
        },
    }
</script>

