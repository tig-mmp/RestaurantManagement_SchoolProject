<template>
    <div class="jumbotron">
        <form autocomplete="off" @submit.prevent="register" method="post">
            <h2>Create User</h2>
            <div class="form-group">
                <label for="inputName">Name</label>
                <input
                        type="text" class="form-control" v-model="user.name"
                        name="name" id="inputName" required
                        placeholder="name"/>
            </div>
            <div class="form-group">
                <label for="inputUsername">Username</label>
                <input
                        type="text" class="form-control" v-model="user.username"
                        name="name" id="inputUsername" required
                        placeholder="username"/>
            </div>
            <div class="form-group">
                <label for="inputEmail">Email</label>
                <input
                        type="email" class="form-control" v-model="user.email"
                        name="email" id="inputEmail" required
                        placeholder="Email address"/>
            </div>
            <div class="form-group">
                <label>Type</label>
                <select v-model="select">
                    <option v-for="type in types" :value="type">{{type}}</option>
                </select>
            </div>
            <button id="submit" type="submit" class="btn btn-default" v-on:click.prevent="register">Submit</button>
        </form>
    </div>
</template>

<script type="text/javascript">
    export default {
        data: function(){
            return {
                user: {
                    name:"",
                    username:"",
                    password:"",
                    email:"",
                    types:"",
                },
                select: "manager",//para não começar vazio
                types: [
                    "manager",
                    "waiter",
                    "cook",
                    "cashier"
                ]
            };
        },
        methods: {
            register:function(){
                this.user.type = this.select;
                axios.post('/api/users/create', this.user)
                .then(response=>{
                    Object.assign(this.user, response.data.data);
                    this.$emit('user-saved', this.user);
                });
            }
        }
    }
</script>