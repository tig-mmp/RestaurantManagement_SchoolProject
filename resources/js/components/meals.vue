<template>
    <div>
        <div>
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
                            <a class="btn btn-sm btn-success" v-on:click.prevent="startOrder(meal.id)">add order</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="create">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">image</th>
                    <th scope="col">name</th>
                    <th scope="col">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr class="color-box" v-for="dish in dishes" :key="dish.id">
                    <td>{{dish.name}}</td>
                    <img :src="'/imgItems/' + dish.photo_url" class="rounded-circle border border-warning" width="25" height="25" >
                    <td>
                        <a class="btn btn-sm btn-success" v-on:click.prevent="createOrder(dish.id)">create order</a>
                    </td>
                </tr>
                </tbody>
            </table>
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">image</th>
                    <th scope="col">name</th>
                    <th scope="col">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr class="color-box" v-for="drink in drinks" :key="drink.id">
                    <td>{{drink.name}}</td>
                    <img :src="'/imgItems/' + drink.photo_url" class="rounded-circle border border-warning" width="25" height="25" >
                    <td>
                        <a class="btn btn-sm btn-success" v-on:click.prevent="createOrder(drink.id)">create order</a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
<script>
    module.exports= {
        data: function () {
            return {
                meals: [],
                create: false,
                mealId: '',
                dishes: [],
                drinks: [],
            }
        },
        methods: {
            startOrder(id){
                this.create = true;
                this.mealId = id;
            },
            getMeals(){
                axios.get( 'api/users/'+this.$store.state.user.id+'/meals').
                then(response=>{
                    this.meals = response.data.data;
                })
            },
            createOrder(id){
                axios.post('/api/orders/create', {'item_id': id, 'meal_id': this.mealId, 'responsible_cook_id': this.$store.state.user.id})
                    .then(response=>{
                        console.log(response.data.data);
                    });
            }
        },
        mounted() {
            this.getMeals();
            axios.get( 'api/dishes').then(response=>{this.dishes = response.data.data;});
            axios.get( 'api/drinks').then(response=>{this.drinks = response.data.data;});
        },
        sockets: {
            updateMeals(){
                console.log("updating");
                this.getMeals();
            },
        },
    }
</script>
