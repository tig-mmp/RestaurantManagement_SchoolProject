<template>
    <div class="jumbotron">
        <div class="alert" :class="typeofmsg" v-if="showMessage">
            <button type="button" class="close-btn" v-on:click="showMessage=false">&times;</button>
            <strong>{{ message }}</strong>
        </div>
        <h1>Edit User {{user.email}}</h1>
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
                <input type="file" id="file" ref="file" accept="image/*" v-on:change="handleFileUpload()"/>
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
                file: '',
                typeofmsg: "alert-success",
                showMessage: false,
                message: "",
            }
        },
        methods: {
            save() {
                this.showMessage = false;
                this.message = '';
                if (this.file !== ''){
                    if(document.getElementById('file').files[0].size > 5242880) {
                        this.typeofmsg = "alert-danger";
                        this.message = "max size of image is 5MB";
                        this.showMessage = true;
                    }
                    else {
                        let formData = new FormData();
                        formData.append('file', this.file);
                        axios.post('api/users/' + this.user.id + '/uploadFile',
                            formData,
                            {
                                headers: {
                                    'Content-Type': 'multipart/form-data'
                                }
                            }
                        ).then(response => {
                            this.$store.commit('setUser', response.data.data);
                        })
                        .catch(error => {
                            this.typeofmsg = "alert-danger";
                            this.message = error.response.data;
                            this.showMessage = true;
                        });
                    }
                }
                axios.put('/api/users/'+this.user.id, this.user)
                .then(response=>{
                    this.$store.commit('setUser',response.data.data);
                    if (this.message === ''){
                        this.typeofmsg = "alert-success";
                        this.message = "User changed with success";
                        this.showMessage = true;
                    }
                })
                .catch(error => {
                    this.typeofmsg = "alert-danger";
                    this.message = error.response.data.errors;
                    this.showMessage = true;
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
