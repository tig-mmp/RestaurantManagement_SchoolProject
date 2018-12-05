<template>
    <div>
        <div class="alert alert-danger" v-if="error && !success">
            <p>There was an error, unable to complete registration.</p>
        </div>
        <div class="alert alert-success" v-if="success">
            <p>Registration completed. You can now <router-link :to="{name:'login'}">sign in.</router-link></p>
        </div>
        <form autocomplete="off" @submit.prevent="register" v-if="!success" method="post">
            <div class="form-group" v-bind:class="{ 'has-error': error && errors.name }">
                <label for="name">Name</label>
                <input type="text" id="name" class="form-control" v-model="user.name" required>
                <span class="help-block" v-if="error && errors.name">{{ errors.name }}</span>
            </div>
            <div class="form-group" v-bind:class="{ 'has-error': error && errors.name }">
                <label for="username">Userame</label>
                <input type="text" id="username" class="form-control" v-model="user.username" required>
                <span class="help-block" v-if="error && errors.username">{{ errors.username }}</span>
            </div>
            <div class="form-group" v-bind:class="{ 'has-error': error && errors.email }">
                <label for="email">E-mail</label>
                <input type="email" id="email" class="form-control" placeholder="user@example.com" v-model="user.email" required>
                <span class="help-block" v-if="error && errors.email">{{ errors.email }}</span>
            </div>

            <div class="form-group">
                <label for="inputEmail">Email</label>
                <input
                        type="email" class="form-control" v-model="user.email"
                        name="email" id="inputEmail"
                        placeholder="Email address"/>
            </div>
            <button id="submit" type="submit" class="btn btn-default" v-on:click.prevent="register">Submit</button>
        </form>
    </div>
</template>

<script type="text/javascript">
    export default {
        props: ['user'],
        data: function(){
            return {
                user: {
                    name:"",
                    username:"",
                    email:"",
                    type:"",
                    password:""
                },
                error: false,
                errors: {},
                success: false
            };
        },
        methods: {
            register:function(){
                console.log(this.user.name);
                console.log(this.user.username);
                console.log(this.user.email);
                axios.post('api/create')
                    .then(response=>{
                        console.log(response.data.data);
                        Object.assign(this.user, response.data.data);
                        this.$emit('user-saved', this.user);
                    });
            }
        }
    }
</script>