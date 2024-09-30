<template>
    <section class="registration rubik-font">
        <div class="user_options-container">
            <div class="user_options-text">
                <div class="user_options-unregistered">
                    <h2 class="user_unregistered-title">Haben Sie noch keinen Account?</h2>
                    <p>Hier können Sie sich einen Account erstellen</p>
                    <button class="user_unregistered-signup" id="signup-button">Registrieren</button>
                </div>
            </div>
        </div>

        <div class="user_forms-signup">
            <h2 class="forms_title">Registrieren</h2>
            <form class="forms_form">
                <fieldset class="forms_fieldset">
                    <div class="forms_field">
                        <input type="text" placeholder="Vorname" class="forms_field-input" required />
                    </div>
                    <div class="forms_field">
                        <input type="text" placeholder="Nachname" class="forms_field-input" required />
                    </div>
                    <div class="forms_field">
                        <input type="email" placeholder="Email" class="forms_field-input" required />
                    </div>
                    <div class="forms_field">
                        <input type="password" placeholder="Passwort" class="forms_field-input" required />
                    </div>
                </fieldset>
                <div class="forms_buttons">
                    <input type="submit" value="Registrieren" class="forms_buttons-action">
                </div>
            </form>
        </div>

    </section>
</template>

<style scoped>
@import url(../../../css/login/signup.css);
</style>