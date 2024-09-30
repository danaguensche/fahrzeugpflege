<template>
    <section class="login rubik-font">
        <div class="user-options-container">
            <div class="user-options-registered">
                <h2 class="user-registered-title">Haben Sie schon einen Account?</h2>
                <button class="user-registered-login" id="login-button">Anmelden</button>
                <p>Hier kommen Sie zur Anmeldung</p>
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
                        <button type="button" class="forms-buttons-forgot">Passwort vergessen?</button>
                        <input type="submit" value="Anmelden" class="forms-buttons-action">
                    </div>
                </form>
            </div>
        </div>
    </section>
</template>

<style scoped>
@import url(../../../css/login/login.css);
</style>