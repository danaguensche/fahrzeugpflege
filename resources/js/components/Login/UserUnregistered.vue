<template>
    <div class="user user-options-container">
        <div class="user-options-unregistered">
            <h2 class="user-unregistered-title">Haben Sie noch keinen Account?</h2>
            <p>Hier können Sie sich einen Account erstellen</p>
            <RegisteredButton :buttonLabel="'Registrieren'"></RegisteredButton>
        </div>
    </div>
</template>

<script>
import RegisteredButton from './Slots/RegisteredButton.vue';

export default {
    name: "UserUnregistered",
    components: {
        RegisteredButton
    },
    data() {
        return {
            isActive: false,
            ButtonLabel: "Registrieren"
        };
    },

    methods: {
        update_ButtonLabel(newName) {
            this.ButtonLabel = newName;
        },
        update_isActive() {
            this.isActive = false;
        }
    }
}
</script>
