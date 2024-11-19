<template>
    <div class="general">
        <div class="login-page">
            <UserButtons></UserButtons>
            <Login :action="loginAction"></Login>
            <SignUp :action="signupAction"></SignUp>
        </div>
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
    },
    props: {
        loginAction: String,
        signupAction: String
    }
}
</script>

