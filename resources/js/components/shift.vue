<template>
    <div class="jumbotron">
        <div v-if="user.shift_active === 0">
            You're not working
            <a class="btn btn-primary" v-on:click.prevent="setShift()">Start shift</a>
            <div>Last shift ended: {{user.last_shift_start}}</div>
            <div>Time past: {{this.differenceEnd}}</div>
        </div>
        <div v-else>
            You're working
            <a class="btn btn-primary" v-on:click.prevent="setShift()">End shift</a>
            <div>Last shift started: {{user.last_shift_end}}</div>
            <div>Time past: {{this.differenceStart}}</div>

            <manager-chat></manager-chat>

        </div>
    </div>
</template>
<script type="text/javascript">
    import managerChat from './managerChat.vue';

    export default {
        components: {
            'manager-chat': managerChat,
        },
        data: function(){
            return {
                user: [],
                differenceStart: "",
                differenceEnd: ""
            }
        },
        methods: {
            setShift() {
                if (this.user.shift_active === 1){
                    this.user.shift_active = 0;
                    this.user.last_shift_end = this.changeDateFormat(new Date());
                    this.$socket.emit('user_exit', this.user);
                } else {
                    this.user.shift_active = 1;
                    this.user.last_shift_start = this.changeDateFormat(new Date());
                    this.$socket.emit('user_enter', this.user);
                }
                axios.put('/api/users/'+this.user.id, this.user)
                    .then(response=>{
                        this.$store.commit('setUser',response.data.data);
                    });
            },
            getInformationFromLoggedUser() {
                this.user = this.$store.state.user;
            },
            changeDateFormat(d){
                return d.getFullYear()+"-"+(d.getMonth()+1)+"-"+(d.getDay()+2)+" "+d.getHours()+":"+d.getMinutes()+":"+d.getSeconds();
            },
            differenceDate(date1, date2){
                let timeDiff = Math.abs(date1.getTime() - date2.getTime());
                let date = new Date(timeDiff);
                let d = Math.floor(date/(1000*60*60*24)); //o getDay() devolve um valor errado
                let h = date.getHours() - 1;    //o getMonth() devolve de 0 a 11
                let m = date.getMinutes();
                let s = date.getSeconds();
                return d + " dias e " + h + ":" + m + ":" + s;
            },
            updateDate(){
                let atual = new Date();
                if (this.user.shift_active === 0){
                    let end = new Date(this.user.last_shift_end);
                    this.differenceEnd = this.differenceDate(atual, end);
                } else{
                    let start = new Date(this.user.last_shift_start);
                    this.differenceStart = this.differenceDate(atual, start);
                }
            },
        },
        mounted() {
            this.getInformationFromLoggedUser();
            this.$nextTick(function () {
                window.setInterval(() => {
                    this.updateDate()
                },1000);
            });
        },
    }
</script>

