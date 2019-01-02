<template>
    <div>
        <h4>Fill invoice</h4>
        <a class="btn btn-light" v-show="filled" v-on:click.prevent="cancel()">Go back</a>
        <div class="alert" :class="typeofmsg" v-if="showMessage">
            <button type="button" class="close-btn" v-on:click="showMessage=false">&times;</button>
            <strong>{{ message }}</strong>
        </div>
        <div class="form-group">
            <label for="name">Name</label>
            <input
                    type="text" class="form-control" v-model="invoice.name"
                    name="name" id="name" required pattern="^[A-Za-z- ]+$"
                    placeholder="name"
                        title="Name must have only letters and spaces"/>
        </div>
        <div class="form-group">
            <label for="nif">Nif</label>
            <input
                    type="number" class="form-control" v-model="invoice.nif"
                    name="nif" id="nif" required pattern="[0-9]{9}"
                    placeholder="123456789"
                    title="NIF must have 9 digits"/>
        </div>
        <div class="form-group">
            <a class="btn btn-primary" v-on:click.prevent="validateClientSide()">Save</a>
            <a class="btn btn-light" v-on:click.prevent="cancel()">Cancel</a>
        </div>
    </div>
</template>

<script>
    module.exports= {
        props: ['invoiceRecived'],
        data: function () {
            return {
                typeofmsg: "alert-success",
                showMessage: false,
                message: "",
                invoice: {},
                filled: false
            }
        },
        methods: {
            validateClientSide(){
                var nameRGEX = /^[A-Za-z- ]+$/;
                var nifRGEX = /[0-9]{9}/;
                var name = nameRGEX.test(document.getElementById('name').value);
                var nif = nifRGEX.test(document.getElementById('nif').value);
                if(nif === false || name === false){
                    this.typeofmsg = "alert-danger";
                    this.showMessage = true;
                    if(nif === false){
                        this.message = "NIF must have 9 digits";
                    }
                    if(name === false){
                        this.message = "Name must have only letters and spaces";
                    }
                }else {
                    this.update();
                }

            },
            update(){
                this.showMessage = false;
                axios.put('/api/invoices/'+this.invoice.id+'/client', this.invoice)
                    .then(response=>{
                        this.$emit('invoice-filled', this.invoice);
                        this.typeofmsg = "alert-success";
                        this.message = "Invoice filled with success";
                        this.showMessage = true;
                        this.filled = true;
                    })
                    .catch(error => {
                        this.typeofmsg = "alert-danger";
                        this.message = error.response.data;
                        this.showMessage = true;
                    });
            },
            cancel(){
                this.$emit('cancel', null);
            }
        },
        mounted() {
            this.invoice = this.invoiceRecived;
        },
    }
</script>

