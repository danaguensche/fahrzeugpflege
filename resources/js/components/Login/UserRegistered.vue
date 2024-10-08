<template>
    <div class="user user-options-container">
        <div class="user-options-text">
            <div class="user-options-text">
                <div class="user-options-registered">
                    <h2 class="user-registered-title">Haben Sie schon einen Account?</h2>
                    <p>Hier kommen Sie zur Anmeldung</p>
                    <RegisteredButton></RegisteredButton>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import RegisteredButton from './Slots/RegisteredButton.vue';

export default {
    name: "UserRegistered",
    components: {
        RegisteredButton
    },

    data() {
        return {
            login_isActive: false
        };
    }
}
</script>
