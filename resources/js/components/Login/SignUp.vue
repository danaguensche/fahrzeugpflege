<template>
    <section class="registration rubik-font">
        <div class="user user-options-container">
            <div class="user-options-text">
                <div class="user-options-text">
                    <div class="user-options-registered">
                        <h2 class="user-registered-title">Haben Sie schon einen Account?</h2>
                        <p>Hier kommen Sie zur Anmeldung</p>
                        <button @click="redirectToLogin" class="user-registered-login"
                            id="login-button">Anmelden</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="user-options-forms">
            <div class="user-forms-signup">
                <h2 class="forms-title">Registrieren</h2>
                <form method="POST" class="forms-form">
                    <SignUpForm></SignUpForm>
                </form>
            </div>
        </div>


    </section>
</template>

<script>

import SignUpForm from './SignUpForm.vue';

export default {
    name: "SignUp",
    components: {
        SignUpForm
    },
    methods: {
        redirectToLogin() {
            window.location.href = "/login"
        }
    }
}

</script>

<style>
@import url(../../../css/login/login.css);
</style>