<template>
    <v-btn class="bg-blue-grey-darken-3 border-0"  @click="handleClick" variant="outlined" color="white">
        <slot></slot>
        </v-btn>
</template>

<script>
export default {
    name: "DefaultButton",
    emits: ['click'],
    methods: {
        handleClick(event) {
            this.$emit('click', event)
        }
    }
}
</script>

<style scoped>
.button {
    z-index: 10;
}

</style>
