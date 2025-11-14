<template>
    <v-btn class="mt-10 mb-2 button bg-blue-grey-darken-3"  @click="handleClick" variant="tonal" color="white">
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
