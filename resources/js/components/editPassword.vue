<template>
    <div class="jumbotron">
        <div class="alert" :class="typeofmsg" v-if="showMessage">
            <button type="button" class="close-btn" v-on:click="showMessage=false">&times;</button>
            <strong>{{ message }}</strong>
        </div>
        <h1>Edit Password</h1>
        <div class="form-group">
            <label for="old_password">Old Password</label>
            <input
                    type="password" class="form-control" v-model="user.old_password"
                    name="old_password" id="old_password"
                    placeholder="Old password"/>
        </div>
        <div class="form-group">
            <label for="password">New password</label>
            <input
                    type="password" class="form-control" v-model="user.password"
                    name="password" id="password"
                    placeholder="New password"/>
        </div>
        <div class="form-group">
            <label for="passwordConfirm">Password Confirmation</label>
            <input
                    type="password" class="form-control" v-model="user.password_confirmation"
                    name="password_confirmation" id="passwordConfirm"
                    placeholder="Password Confirmation"/>
        </div>
        <div class="form-group">
            <a class="btn btn-primary" v-on:click.prevent="savePassword()">Save</a>
            <a class="btn btn-light" v-on:click.prevent="cancelEdit()">Cancel</a>
        </div>
    </div>
</template>

<script type="text/javascript">
    module.exports={
        data: function(){
            return {
                user: [],
                typeofmsg: "alert-success",
                showMessage: false,
                message: "",
            }
        },
        methods: {
            savePassword() {
                this.showMessage = false;
                axios.put('/api/users/'+this.user.id+'/password', this.user)
                    .then(response=>{
                        this.typeofmsg = "alert-success";
                        this.message = "Password changed with success";
                        this.showMessage = true;
                    })
                    .catch(error => {
                        this.typeofmsg = "alert-danger";
                        this.message = error.response.data.errors.password;
                        this.showMessage = true;
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
