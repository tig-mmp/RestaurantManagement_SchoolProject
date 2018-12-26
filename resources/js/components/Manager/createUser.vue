<template>
    <div class="container-fluid">
        <h2>Create User</h2>
        <div class="alert alert-danger" v-if="alertFail.show">    
            <button type="button" class="close-btn" v-on:click="alertFail.show=false">&times;</button>
            <ul v-for="error in alertFail.message">
                <li>
                    <strong  alert.message>{{ error }}</strong>
                </li>
            </ul>
        </div>
        <div >
            <form autocomplete="off" @submit.prevent="register" method="post">
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
                <a class="btn btn-light" v-on:click.prevent="cancel">Cancel</a>
            </form>
        </div>
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
                ],
                alertFail:{
                    show:false,
                    isFail:false,
                    message:'',
                },
            };
        },
        methods: {
            register:function(){
                this.user.type = this.select;
                axios.post('/api/users/create', this.user)
                .then(response=>{
                    Object.assign(this.user, response.data.data);
                    this.$emit('user-saved', this.user);
                    this.$router.push('/manager/managerUserList');
                }).catch(error =>{
                        this.buildErrorMessage(error);
                        return;
                });
            },
            cancel:function(){
                this.$router.push('/manager/managerUserList');
            },
            buildErrorMessage(error){
                this.alertFail.show = true;
                this.alertFail.isFail = true;
                this.alertFail.isSuccess = false;
                this.alertFail.message = [];
                for(var prop in error.response.data.errors){
                    this.alertFail.message.push(error.response.data.errors[prop].join('and'));
                }
            },
        }
    }
</script>