<template>
  <section class="login-page">
    <div :class="['user-options-forms', 'user-options-forms-registered', { 'active': isActive }]">
      <div class="user-forms-login">
        <h2 class="forms-title">Anmelden</h2>
        <LoginForm></LoginForm>
      </div>
    </div>
  </section>
</template>

<script>
import LoginForm from './LoginForm.vue';
import { mapState } from 'vuex';

export default {
  name: "Login",
  components: {
    LoginForm,
  },
  computed: {
    ...mapState(['activeForm']),
    isActive() {
      return this.activeForm === 'login';
    }
  }
}
</script>
