<template>
    <form method="POST" class="forms-form">
        <fieldset class="forms-fieldset">
            <div class="forms-field">
                <input type="email" id="email" placeholder="Email" class="forms-field-input" required autofocus />
            </div>
            <div class="forms-field">
                <input type="password" id="password" placeholder="Passwort" class="forms-field-input" required />
            </div>
        </fieldset>
        <div class="forms-buttons">
            <input type="submit" value="Anmelden" class="forms-buttons-action">
            <a href="#" class="forms-buttons-forgot">Passwort vergessen?</a>
        </div>
    </form>

</template>

<script>

export default {
    name: "LoginForm"
}

</script>

<style scoped>
@import url(../../../css/login/loginform.css);
</style>