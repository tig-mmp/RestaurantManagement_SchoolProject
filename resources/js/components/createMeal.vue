<template>
    <div class="jumbotron">
        <form autocomplete="off" @submit.prevent="register" method="post">
            <h2>Create Meal</h2>
            <div class="form-group">
                <label>Type</label>
                <select v-model="select">
                    <option v-for="mesa in mesasLivres" :value="mesa.table_number">{{mesa.table_number}}</option>
                </select>
            </div>
            <button id="submit" type="submit" class="btn btn-default" v-on:click.prevent="register">Submit</button>
        </form>

    </div>
</template>

<script type="text/javascript">
    export default {
        data: function () {
            return {
                select: '',
                mesasLivres: []
            };
        },
        methods: {
            register: function () {
                axios.post('/api/meals/create', {table_number: this.select, 'id': this.$store.state.user.id})
                .then(response => {
                });
            },
            getMesasLivres() {
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