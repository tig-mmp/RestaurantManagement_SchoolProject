<template>
    <div class="jumbotron">
        <h2>Edit Password</h2>
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
            }
        },
        methods: {
            savePassword() {
                axios.put('/api/users/'+this.user.id+'/password', this.user)
                    .then(response=>{
                        this.$store.commit('setUser',response.data.data);
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
