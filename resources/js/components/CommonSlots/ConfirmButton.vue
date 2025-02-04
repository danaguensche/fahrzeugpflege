<template>
    <v-btn prepend-icon="mdi-check-circle" variant="text" class="fixed-button-width">
        <slot></slot>
    </v-btn>
</template>

<script>

import SvgIcon from '@jamescoyle/vue-icon'
import { mdiCancel } from '@mdi/js'

export default {
    name: "ConfirmButton",
    components: {
        SvgIcon
    }
}
</script>

<style scoped>
.fixed-button-width {
    width: 150px;
}
</style>