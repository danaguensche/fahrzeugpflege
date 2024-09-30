<template>
    <section class="registration rubik-font">
        <div class="user-options-container">
            <div class="user-options-text">
                <div class="user-options-text">
                    <div class="user-options-registered">
                        <h2 class="user-registered-title">Haben Sie schon einen Account?</h2>
                        <p>Hier kommen Sie zur Anmeldung</p>
                        <button class="user-registered-login" id="login-button">Anmelden</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="user-forms-signup">
            <h2 class="forms-title">Registrieren</h2>
            <form method="POST" class="forms-form">
                <SignUpForm></SignUpForm>
            </form>
        </div>

    </section>
</template>

<script>

import SignUpForm from './SignUpForm.vue';

export default {
    name: "SignUp",
    components: {
        SignUpForm
    }
}

</script>

<style scoped>
@import url(../../../css/login/signup.css);
</style>