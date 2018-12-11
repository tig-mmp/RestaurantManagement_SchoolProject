<template>
    <div class="jumbotron">
        <h2>Edit User {{user.email}}</h2>
        <div class="form-group">
            <label for="inputName">Name</label>
            <input
                    type="text" class="form-control" v-model="user.name"
                    name="name" id="inputName"
                    placeholder="name"/>
        </div>
        <div class="form-group">
            <label for="inputUsername">Username</label>
            <input
                    type="text" class="form-control" v-model="user.username"
                    name="name" id="inputUsername"
                    placeholder="username"/>
        </div>
        <div class="form-group">
            <label>File
                <input type="file" id="file" ref="file" v-on:change="handleFileUpload()"/>
            </label>
        </div>
        <div class="form-group">
            <a class="btn btn-primary" v-on:click.prevent="save()">Save</a>
            <a class="btn btn-light" v-on:click.prevent="cancelEdit()">Cancel</a>
        </div>
    </div>
</template>

<script type="text/javascript">
    module.exports={
        data: function(){
            return {
                user: [],
                file: ''
            }
        },
        methods: {
            save() {
                if (this.file !== ''){
                    let formData = new FormData();
                    formData.append('file', this.file);
                    axios.post( 'api/users/'+this.user.id+'/uploadFile',
                        formData,
                        {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        }
                    ).then(response=>{
                        this.$store.commit('setUser',response.data.data);
                    })
                        .catch(function(){
                            console.log('FAILURE!!');
                        });
                }
                axios.put('/api/users/'+this.user.id, this.user)
                    .then(response=>{
                        this.$store.commit('setUser',response.data.data);
                    });
            },
            cancelEdit: function(){
                axios.get('api/users/'+this.user.id)
                    .then(response=>{
                        Object.assign(this.user, response.data.data);
                        this.$emit('user-canceled', this.user);
                        this.file = "";
                    });
            },
            getInformationFromLoggedUser() {
                this.user = this.$store.state.user;
            },
            handleFileUpload(){
                this.file = this.$refs.file.files[0];
            }
        },
        mounted() {
            this.getInformationFromLoggedUser();
        }
    }
</script>
