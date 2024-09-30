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
            <form class="forms-form">
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
            </form>
        </div>

    </section>
</template>

<style scoped>
@import url(../../../css/login/signup.css);
</style>