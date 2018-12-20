<template>
	<div class="container-fluid">
		<div>
			<h1>Create Item</h1>
			<div class="row">
				<div class="col-sm-8">
			        <div class="form-group">
			            <label for="inputName">Name</label>
			            <input
			                    type="text" class="form-control" v-model="item.name"
			                    name="name" id="inputName"
			                    placeholder="name"/>
			        </div>
			        <div class="form-group">
			            <label for="inputType">Type</label>
			            <select
			                    type="text" class="form-control" v-model="item.type"
			                    name="name" id="inputType"
			                    placeholder="type">
			                    <option>dish</option>
			                    <option>drink</option>
			            </select>
			        </div>
			        <div class="form-group">
			            <label for="inputPrice">Price</label>
			            <input
			                    type="text" class="form-control" v-model="item.price"
			                    name="name" id="inputPrice"
			                    placeholder="price"/>
			        </div>
		        </div>
		        <div class="col-sm">
			        <div class="form-group">
			        	<img :src="avatar" width="200" height="200" >
			            <label>
			                <input type="file" id="file" ref="file" @change="handleFileUpload"/>
			            </label>
			        </div>
		        </div>
	        </div>
	        <div class="form-group">
	            <label for="inputDescription">Description</label>
	            <textarea
	                    type="text" class="form-control" v-model="item.description"
	                    name="name" id="inputDescription"
	                    placeholder="description"/>
	        </div>
	       
	        <div class="form-group">
	            <a class="btn btn-primary" v-on:click.prevent="save()">Save</a>
	            <a class="btn btn-light" v-on:click.prevent="cancelEdit()">Cancel</a>
	        </div>
	    </div>
	</div>
</template>
<script>
    export default {
        data: function(){
            return { 
                item: {
                    name:"",
                    type:"",
                    description:"",
                    price:"",
                },
                file: '',
                avatar: null
            }
        },
        mounted(){
        },
        methods:{
        	save: function(){
        		//Create new Item with default image
        		axios.post('/api/manager/createItem', this.item)
        		.then(response=>{
        			//replace default image by real image
        			console.log(response.data)
	        		let formData = new FormData();
	                formData.append('file', this.file);
					axios.post( 'api/manager/editItem/' + response.data + '/uploadFile',
						formData,
	                    {
	                    	headers: {
	                    		'Content-Type': 'multipart/form-data'
	                    	}
	                    }
					).then(response=>{
	                    	
					})
					.catch(function(){
						console.log('FAILURE!!');
					});
                });
        	},
        	cancelEdit:function(){
        		this.$router.push('/manager/managerItemList');
        	},
        	handleFileUpload(e){
        		this.file = e.target.files[0];
                let image = e.target.files[0];
                let reader = new FileReader();
                reader.readAsDataURL(image);
                reader.onload = e => {
                	this.avatar = e.target.result;
                }
            }
        }
    }
</script>