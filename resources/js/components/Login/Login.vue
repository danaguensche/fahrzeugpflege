<template>
  <section class="login-page">
    <UserButtons></UserButtons>
    <div class="user-options-forms user-options-forms-registered">
      <div class="user-forms-login">
        <h2 class="forms-title">Anmelden</h2>
        <LoginForm></LoginForm>
      </div>
    </div>
  </section>
</template>

<script>
import LoginForm from './LoginForm.vue';
import UserButtons from './UserButtons.vue';

export default {
  name: "Login",
  components: {
    LoginForm,
    UserButtons
  },
  methods: {
    redirectToSignup() {
      window.location.href = "/signup"
    }
  }
}
</script>