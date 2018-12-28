<template>
    <div class="jumbotron">
        <form autocomplete="off" @submit.prevent="register" method="post">
            <h2>Create Meal</h2>
            <div class="form-group">
                <label>Table: </label>
                <label>
                    <select v-model="select">
                        <option v-for="mesa in mesasLivres" :value="mesa.table_number">{{mesa.table_number}}</option>
                    </select>
                </label>
            </div>
            <button id="submit" type="submit" class="btn btn-default" v-on:click.prevent="register">Submit</button>
        </form>
    </div>
</template>

<script type="text/javascript">
    export default {
        props: ['userId'],
        data: function () {
            return {
                select: '',
                mesasLivres: []
            };
        },
        methods: {
            register: function () {
                axios.post('/api/meals', {table_number: this.select, 'id': this.userId})
                .then(response => {
                    this.$emit('meal-created', response.data);
                    this.$socket.emit('mealCreated', this.select);
                    this.getMesasLivres();
                });

            },
            getMesasLivres(){
                axios.get('api/tables')
                .then(response=>{
                    this.mesasLivres = response.data;
                });
            }
        },
        mounted() {
            this.getMesasLivres();
        },
    }
</script>