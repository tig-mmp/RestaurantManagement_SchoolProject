
<template>
    <div class="container">
        <div class="jumbotron">
            <h2>Profile</h2>
            <div>name: {{user.name}}</div>
            <div>username: {{user.username}}</div>
            <div>email: {{user.email}}</div>
            <div>type: {{user.type}}</div>

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
                    email:"",
                    type:""
                },
            }
        },
        methods: {
            getInformationFromLoggedUser() {
                this.user = this.$store.state.user;
            },
        },
        mounted() {
            this.getInformationFromLoggedUser();
        }
    }
</script>