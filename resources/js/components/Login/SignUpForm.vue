<template>
    <fieldset class="forms-fieldset">
        <div class="forms-field">
            <input type="text" placeholder="Vorname" class="forms-field-input" required />
        </div>
        <div class="forms-field">
            <input type="text" placeholder="Nachname" class="forms-field-input" required />
        </div>
        <div class="forms-field">
            <input type="email" placeholder="Email" class="forms-field-input" required />
        </div>
        <div class="forms-field">
            <input type="password" placeholder="Passwort" class="forms-field-input" required />
        </div>
    </fieldset>
    <div class="forms-buttons">
        <input type="submit" value="Registrieren" class="forms-buttons-action">
    </div>
</template>

<script>

export default {
    name: "SignUpForm",
}

</script>

<style>
@import url(../../../css/login/signupform.css);
</style>