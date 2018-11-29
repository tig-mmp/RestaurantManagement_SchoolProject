<template>
    <div class="container col-md-8">
        <div class="text-center text-warning">
            MENU CARD
        </div>
        <div class="row justify-content-center mt-5" v-for="item in items">

                <div class="col-sm-2">
                    <img :src="'/imgItems/' + item.photo_url" class="rounded-circle border border-warning" width="100" height="100" >
                </div>
                <div class="col-sm-8">
                    <div class="col-sm font-weight-bold">
                        {{item.name}}
                    </div>
                    <div class="col-sm text-secondary">
                        {{item.description}}
                    </div>
                </div>
                <div class="col-sm-2 text-warning">
                  {{item.price}}€
                </div>
        </div>
    </div>
</template>

<script type="text/javascript">
    // Component code (not registered)
    export default {
        data: function(){
            return { 
                items: []
            }
        },
        methods: {
          
        },
        mounted() {
            axios.get('api/items')
                    .then(response=>{this.items = response.data; });

        }      
    }
</script>