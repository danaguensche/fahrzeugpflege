<template>
    <v-btn prepend-icon="mdi-cancel" variant="text" class="fixed-button-width bg-blue-grey-lighten-5">
        <slot></slot>
    </v-btn>
</template>

<script>

import SvgIcon from '@jamescoyle/vue-icon'
import { mdiCancel } from '@mdi/js'

export default {
    name: "CancelButton",
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