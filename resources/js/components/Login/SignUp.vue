<template>
    <section class="login-page">
        <UserButtons></UserButtons>
        <div class="user-options-forms user-options-forms-unregistered">
            <div class="user-forms-signup">
                <h2 class="forms-title">Registrieren</h2>
                <SignUpForm></SignUpForm>
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
