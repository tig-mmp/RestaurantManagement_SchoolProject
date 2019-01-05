<template>
    <div class="jumbotron">
        <form autocomplete="off" @submit.prevent="register" method="post">
            <h2>Create Meal</h2>
            <div class="form-group">
                <label>Table: </label>
                <label>
                    <select v-model="select" id="select">
                        <option v-for="mesa in mesasLivres" :value="mesa">{{mesa}}</option>
                    </select>
                </label>
            </div>
            <button id="submit" type="submit" class="btn btn-default" v-on:click.prevent="createMeal">Submit</button>
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
            createMeal: function () {
                if (document.getElementById("select").selectedIndex !== -1) {//se o campo estiver vazio não faz nada
                    axios.post('/api/meals', {table_number: this.select, 'id': this.userId})//cria a meal
                    .then(response => {
                        if(response.data != ''){
                            this.$emit('meal-created', response.data);//envia para o main que envia para o meals que lista as meals do waiter
                            this.$socket.emit('mealCreated', this.select);
                        }
                    }).catch(error => {});
                }
            },
            addTable(table){
                this.mesasLivres.push(table);
                this.mesasLivres.sort((a, b) => a - b);//ordena numericamente
            },
            removeTable(table){
                this.mesasLivres.splice(this.mesasLivres.indexOf(table), 1);
            },
            getMesasLivres(){
                axios.get('api/tables')
                .then(response=> {
                    Object.values(response.data).forEach((table) => {
                        //isto era um objeto de objetos, Object.values transforma num array com os values
                        // e assim ja da para percorrer no foreach
                        this.mesasLivres.push(table.table_number);
                    });
                }).catch(function (error) {
                    this.getMesasLivres();
                });
            }
        },
        mounted() {
            this.getMesasLivres();
        },
        sockets: {
            mealRemoved(table){
                this.addTable(table);
            },
            mealCreated(table){
                this.removeTable(table);
            },
            updateTables(){
                this.mesasLivres = [];
                this.getMesasLivres();
            },
            waiterUpdateOrders(){
                this.mesasLivres = [];
                this.getMesasLivres();
            },
        }
    }
</script>