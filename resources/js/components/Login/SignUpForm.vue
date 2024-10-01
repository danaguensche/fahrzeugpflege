<template>
    <fieldset class="forms-fieldset">
        <div class="forms-field">
            <input type="text" placeholder="Vorname" id="firstname" class="forms-field-input" required />
        </div>
        <div class="forms-field">
            <input type="text" placeholder="Nachname" id="lastname" class="forms-field-input" required />
        </div>
        <div class="forms-field">
            <input type="email" placeholder="Email" id="email" class="forms-field-input" required />
        </div>
        <div class="forms-field">
            <input type="password" placeholder="Passwort" id="password" class="forms-field-input" required />
        </div>
        <div class="forms-field">
            <input type="password" placeholder="Passwort wiederholen" id="password-repeat" class="forms-field-input" required />
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