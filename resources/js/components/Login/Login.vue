<template>
    <section class="login rubik-font">
        <div class="user-options-container">
            <div class="user-options-unregistered">
                    <h2 class="user-unregistered-title">Haben Sie noch keinen Account?</h2>
                    <p>Hier können Sie sich einen Account erstellen</p>
                    <button class="user-unregistered-signup" id="signup-button">Registrieren</button>
            </div>
        </div>
        <div class="user-options-forms" id="user-options-forms">
            <div class="user-forms-login">
                <h2 class="forms-title">Anmelden</h2>
                <form class="forms-form">
                    <fieldset class="forms-fieldset">
                        <div class="forms-field">
                            <input type="email" placeholder="Email" class="forms-field-input" required autofocus />
                        </div>
                        <div class="forms-field">
                            <input type="password" placeholder="Passwort" class="forms-field-input" required />
                        </div>
                    </fieldset>
                    <div class="forms-buttons">
                        <input type="submit" value="Anmelden" class="forms-buttons-action">
                        <a href="#" class="forms-buttons-forgot">Passwort vergessen?</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</template>

<style scoped>
@import url(../../../css/login/login.css);
</style>