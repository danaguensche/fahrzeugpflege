<template>
    <section class="login rubik-font">
        <div class="user-options-container">
            <div class="user-options-unregistered">
                <h2 class="user-unregistered-title">Haben Sie noch keinen Account?</h2>
                <p>Hier können Sie sich einen Account erstellen</p>
                <button class="user-unregistered-signup" id="signup-button">Registrieren</button>
            </div>
        </div>
        <div class="user-options-forms" id="user-options-forms">
            <div class="user-forms-login">
                <h2 class="forms-title">Anmelden</h2>
                    <LoginForm></LoginForm>
            </div>
        </div>
    </section>
</template>

<script>

import LoginForm from './LoginForm.vue';
export default {
    name: "Login",
    components: {
        LoginForm
    }
}

</script>
<style scoped>
@import url(../../../css/login/login.css);
</style>