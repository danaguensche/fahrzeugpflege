<template>
    <div class="login-page">
        <UserButtons></UserButtons>
        <Login></Login>
        <SignUp></SignUp>
    </div>
</template>

<script>

import Login from '../Login/Login.vue';
import SignUp from '../Login/SignUp.vue';
import UserButtons from '../Login/UserButtons.vue';

export default {
    name: "LoginPage",
    components: {
        Login,
        SignUp,
        UserButtons
    }
}

</script>

<style>
@import url(../../../css/login/login.css);
</style>