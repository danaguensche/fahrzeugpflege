<template>
    <form class="forms-form">
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
                <input type="password" placeholder="Passwort wiederholen" id="password-repeat" class="forms-field-input"
                    required />
            </div>
        </fieldset>
        <div class="forms-buttons">
            <button @click="submitForm" type="submit" class="forms-buttons-action">Registrieren</button>
        </div>
    </form>
</template>

<script>

export default {
    name: "SignUpForm",
    methods: {
        submitForm() {

        },

        validateFirstname() {

        },

        validateLastname() {

        },

        validateEmail() {

        },

        validatePassword() {

        },

        validatePasswordRepeat() {

        }
    }
}

</script>
