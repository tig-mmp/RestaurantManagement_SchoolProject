<template>
    <div class="jumbotron">
        <h2>Edit User</h2>
        <div class="form-group">
            <label for="inputName">Name</label>
            <input
                    type="text" class="form-control" v-model="user.name"
                    name="name" id="inputName"
                    placeholder="name"/>
        </div>
        <div class="form-group">
            <label for="inputUsername">Username</label>
            <input
                    type="text" class="form-control" v-model="user.username"
                    name="name" id="inputUsername"
                    placeholder="username"/>
        </div>
        <div class="form-group">
            <label for="inputEmail">Email</label>
            <input
                    type="email" class="form-control" v-model="user.email"
                    name="email" id="inputEmail"
                    placeholder="Email address"/>
        </div>
        <div class="form-group">
            <label for="old_password">Email</label>
            <input
                    type="password" class="form-control"
                    name="old_password" id="old_password"
                    placeholder="Old password" required/>
        </div>
        <div class="form-group">
            <label for="password">Email</label>
            <input
                    type="password" class="form-control"
                    name="password" id="password"
                    placeholder="New password" required/>
        </div>
        <div class="form-group">
            <label for="passwordConfirm">Email</label>
            <input
                    type="password" class="form-control"
                    name="password_confirmation" id="passwordConfirm"
                    placeholder="Password Confirmation" required/>
        </div>
        <div class="form-group">
            <a class="btn btn-primary" v-on:click.prevent="saveUser()">Save</a>
            <a class="btn btn-light" v-on:click.prevent="cancelEdit()">Cancel</a>
        </div>
    </div>
</template>

<script type="text/javascript">
    module.exports={
        data: function(){
            return {
                user: {
                    id:"",
                    name:"",
                    username:"",
                    email:"",
                    type:"",
                    old_password:"",
                    password:"",
                    password_confirmation:"",
                },
            }
        },
        methods: {
            saveUser() {
                axios.put('api/users/'+this.user.id, this.user)
                    .then(response=>{
                        Object.assign(this.user, response.data.data);
                        this.$emit('user-saved', this.user)
                    });
            },
            cancelEdit: function(){
                axios.get('api/users/'+this.user.id)
                    .then(response=>{
                        Object.assign(this.user, response.data.data);
                        this.$emit('user-canceled', this.user);
                    });
            },getInformationFromLoggedUser() {
                this.user = this.$store.state.user;
            },
        },
        mounted() {
            this.getInformationFromLoggedUser();
        }
    }
</script>
