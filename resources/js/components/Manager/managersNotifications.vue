<template>
	<div class="container-fluid">
		<div class="form-group">
		    <h3>Manual Notifications</h3>
		    <textarea class="form-control" id="exampleFormControlTextarea1" rows="11"
		    v-model="notificationsTextArea"></textarea>
		</div>
	</div>
</template>
<script>
	export default{
		data(){
			return{
				notificationsTextArea: '',
			}
		},
		sockets: {
            msg_from_server_managers(dataFromServer){
                this.notificationsTextArea = dataFromServer[0] + '\n' + this.notificationsTextArea;
            },
        },
	};
	
</script>