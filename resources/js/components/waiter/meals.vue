<template>
    <div>
        <div>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">start</th>
                        <th scope="col">state</th>
                        <th scope="col">table</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="color-box" v-for="meal in meals" :key="meal.id">
                        <td>{{meal.start}}</td>
                        <td>{{meal.state}}</td>
                        <td>{{meal.table_number}}</td>
                        <td>
                            <a v-show="mealId !== meal.id" class="btn btn-sm btn-success" v-on:click.prevent="startOrder(meal.id)">add order</a>
                            <a v-show="mealId === meal.id" class="btn btn-sm btn-success" v-on:click.prevent="closeOrder()">close order</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
<script>
    module.exports= {
        props: ['newMeal', 'userId'],
        data: function () {
            return {
                meals: [],
                mealId: ''
            }
        },
        methods: {
            startOrder(id){
                this.mealId = id;
                this.$emit('start-order', id);
            },
            closeOrder(){
                this.mealId = null;
                this.$emit('start-order', null);
            },
        },
        mounted() {
            axios.get('api/users/'+this.userId+'/meals').then(response=>{
                this.meals = response.data.data;
            });
        },
        watch: {
            newMeal: function (meal) {
                this.meals.push(meal);
            }
        }
    }
</script>
