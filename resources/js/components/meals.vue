<template>
<table class="table">
        <thead>
        <tr>
            <th scope="col">start</th>
            <th scope="col">state</th>
            <th scope="col">table_number</th>
            <th scope="col">Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr class="color-box" v-for="meal in meals" :key="meal.id">
            <td>{{meal.start}}</td>
            <td>{{meal.state}}</td>
            <td>{{meal.table_number}}</td>
            <td>
                <a class="btn btn-sm btn-success" v-on:click.prevent="prepare(meal.id)">add order</a>

            </td>
        </tr>
        </tbody>
    </table>
</template>
<script>
    module.exports= {
        data: function () {
            return {
                meals: []
            }
        },
        methods: {
            prepare(id){
                /*axios.put('/api/meals/'+id, {'state':state})
                    .then(response=>{
                        console.log(response);
                        this.getMeals();
                    });*/
            },
            getMeals(){
                axios.get( 'api/users/'+this.$store.state.user.id+'/meals').
                then(response=>{
                    this.meals = response.data.data;
                })
            },
        },
        mounted() {
            this.getMeals();
        },
        sockets: {
            updateMeals(){
                console.log("updating");
                this.getMeals();
            },
        },
    }
</script>
