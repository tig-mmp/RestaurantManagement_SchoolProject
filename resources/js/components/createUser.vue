<template>
    <div>
        <form autocomplete="off" @submit.prevent="register" method="post">


            <v-text-field v-model="user.name" label="Name" required></v-text-field>
            <v-text-field v-model="user.username" label="Username" required></v-text-field>
            <v-text-field v-model="user.email" label="Email" required></v-text-field>

            <v-select v-model="select" :items="items" required></v-select>

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
                    items:"",
                },
                select: null,
                items: [
                    "manager",
                    "waiter",
                    "cook",
                    "cashier"
                ]
            };
        },
        methods: {
            register:function(){
                axios.post('/api/users/create', this.user)
                    .then(response=>{
                        console.log(response.data.data);
                        Object.assign(this.user, response.data.data);
                        this.$emit('user-saved', this.user);
                    });
            }
        }
    }
</script>