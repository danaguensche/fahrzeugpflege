<template>
  <section class="login-page">
    <div :class="['user-options-forms-registered', { 'active': activeForm === 'login' }]">
      <h2 class="forms-title">Anmelden</h2>
      <LoginForm></LoginForm>
    </div>
    <div :class="['user-options-forms-unregistered', { 'active': activeForm === 'register' }]">
      <h2 class="forms-title">Registrieren</h2>
      <RegisterForm></RegisterForm>
    </div>
  </section>
</template>

<script>
import LoginForm from './LoginForm.vue';

import { mapState } from 'vuex';
import SignUpForm from './SignUpForm.vue';

export default {
  name: "Login",
  components: {
    LoginForm,
    SignUpForm
    
  },
  computed: {
    ...mapState(['activeForm']),
    isLoginActive() {
      return this.activeForm === 'login';
    },
    isRegisterActive() {
      return this.activeForm === 'register';
    }
  }
}
</script>
