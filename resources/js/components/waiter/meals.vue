<template>
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
                        <a v-show="summary !== meal.id" class="btn btn-sm btn-success" v-on:click.prevent="showSummary(meal.id)">summary</a>
                        <a v-show="summary === meal.id" class="btn btn-sm btn-danger" v-on:click.prevent="closeSummary(meal.id)">close summary</a>
                        <a v-show="mealId !== meal.id" class="btn btn-sm btn-success" v-on:click.prevent="startOrder(meal.id)">add order</a>
                        <a v-show="mealId === meal.id" class="btn btn-sm btn-danger" v-on:click.prevent="closeOrder()">close order</a>
                        <a class="btn btn-sm btn-warning" v-on:click.prevent="endMeal(meal.id)">end meal</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
    module.exports= {
        props: ['newMeal', 'userId', 'mealIdToRemove'],
        data: function () {
            return {
                meals: [],
                mealId: '',
                summary: ''
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
            showSummary(id){
                this.summary = id;
                this.$emit('show-summary', id);
            },
            closeSummary(id){
                this.summary = null;
                this.$emit('show-summary', null);
            },
            endMeal(id){
                this.$emit('end-meal', id);
            }
        },
        mounted() {
            axios.get('api/users/'+this.userId+'/meals').then(response=>{
                this.meals = response.data.data;
            });
        },
        watch: {
            newMeal: function (meal) {
                this.meals.push(meal);
            },
            mealIdToRemove: function (mealId) {
                this.meals.splice(this.meals.findIndex(meal => meal.id === mealId), 1);
            }
        }
    }
</script>
