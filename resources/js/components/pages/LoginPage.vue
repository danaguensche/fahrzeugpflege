<template>
    <div class="login-page">
        <Login></Login>
        <SignUp></SignUp>
    </div>
</template>

<script>

import Login from '../Login/Login.vue';
import SignUp from '../Login/SignUp.vue'

export default {
    name: "LoginPage",
    components: {
        Login,
        SignUp
    }
}

</script>

<style>
@import url(../../../css/login/login.css);
</style>