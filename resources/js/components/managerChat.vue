<template>
    <div class="jumbotron">
        Mesage to managers:
        <input type="text" id="inputGlobal" class="inputchat" v-model="msgManagersText" @keypress.enter="this.sendManagersMsg">
        <div v-if="this.user.type == 'manager'">
            chat to managers:
            <input type="text" id="inputDepartment" class="inputchat" v-model="msgManagersTextArea" @keypress.enter="sendDepMsg">
        </div>
    </div>
</template>

<script type="text/javascript">
    module.exports={
        props: ['user', 'msgManagersText', 'msgManagersTextArea'],
        methods: {
            getInformationFromLoggedUser() {
                this.user = this.$store.state.user;
            },
            sendManagersMsg: function(){
                if (this.$store.state.user === null) {
                    this.$toasted.error('User is not logged in!');
                } else {
                    this.$socket.emit('msg_from_worker_to_managers', this.msgManagersText, this.$store.state.user);
                }
                this.msgManagersText = "";
            },

        },
        mounted() {
            this.getInformationFromLoggedUser();
        },
        sockets: {
            msg_from_server_managers(dataFromServer){
                this.msgManagersTextArea = dataFromServer + '\n' + this.msgManagersTextArea;
            },
        },
    }
</script>

