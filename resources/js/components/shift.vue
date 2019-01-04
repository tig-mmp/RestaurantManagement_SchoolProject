<template>
    <div class="jumbotron"  v-if="this.$store.state.user && this.$store.state.user.shift_active === 1">
        <manager-chat></manager-chat>
        <cook v-if="this.user.type === 'cook'"></cook>
        <waiter v-if="this.user.type === 'waiter'" v-bind:userId="user.id"></waiter>
        <cashier v-if="this.user.type === 'cashier'"></cashier>
        <cashier_all v-if="this.user.type === 'cashier'"></cashier_all>
    </div>
</template>
<script type="text/javascript">
    import managerChat from './managerChat.vue';
    import cook from './cook.vue';
    import waiter from './waiter/main.vue';
    import cashier from './cashier.vue';
    import cashier_all from './cashier_all.vue';

    export default {
        components: {
            'manager-chat': managerChat,
            'cook': cook,
            'waiter': waiter,
            'cashier': cashier,
            'cashier_all': cashier_all,
        },
        data: function(){
            return {
                user: ''
            }
        },
        mounted(){
            this.user = this.$store.state.user;
        },
    }
</script>

