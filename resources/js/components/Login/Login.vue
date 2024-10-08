<template>
    <section class="login rubik-font">
        <UserUnregistered></UserUnregistered>
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
import UserUnregistered from './UserUnregistered.vue';
export default {
    name: "Login",
    components: {
        LoginForm,
        UserUnregistered
    },

    methods: {
        redirectToSignup() {
            window.location.href = "/signup"
        }
    }
}

</script>

