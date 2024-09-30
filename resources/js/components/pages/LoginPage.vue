<template>
    <Login></Login>
    <SignUp></SignUp>
</template>

<script>

import Login from '../Login/Login.vue';
import SignUp from '../Login/SignUp.vue';

export default {
    name: "LoginPage",
    components: {
        Login,
        SignUp
    }
}

</script>
