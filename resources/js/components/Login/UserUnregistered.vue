<template>
    <div class="user user-options-container">
        <div class="user-options-unregistered">
            <h2 class="user-unregistered-title">Haben Sie noch keinen Account?</h2>
            <p>Hier können Sie sich einen Account erstellen</p>
            <UnregisteredButton></UnregisteredButton>
        </div>
    </div>

</template>

<script>
import UnregisteredButton from './Slots/UnregisteredButton.vue';

export default {
    name: "UserUnregistered",
    components: {
        UnregisteredButton
    },
    data() {
        return {
            signup_isActive: false
        };
    }
}
</script>