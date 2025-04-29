<template>
    <v-btn variant="outlined" color="primary" prepend-icon="mdi-arrow-left" @click="goBack">
        Zurück
    </v-btn>
</template>

<script>
export default {
    name: 'BackButton',
    methods: {
        goBack() {
            try {
                this.$router.back();
            } catch (e) {
                console.error("Fehler beim Navigieren zurück:", e);
                // Fallback, falls $router.back() nicht funktioniert
                window.history.back();
            }
        },
    }
}
</script>