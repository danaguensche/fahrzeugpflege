<template>
    <div>
    <v-btn prepend-icon="mdi-check-circle" variant="text" class="fixed-width">
        Bestätigen
    </v-btn>
    <v-btn prepend-icon="mdi-cancel" variant="text" class="fixed-width">
        Abbrechen
    </v-btn>
    <v-btn prepend-icon="mdi-delete" variant="text" class="fixed-width">
        löschen
    </v-btn>
    </div>
</template>

<script>
import SvgIcon from '@jamescoyle/vue-icon'
import { mdiCancel } from '@mdi/js'
export default {
    name: "ButtonGroup",
    components: {
        SvgIcon
    }
}
</script>

<style scoped>

.fixed-width {
    width: 150px;
}
</style>