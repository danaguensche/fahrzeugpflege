<template>
    <v-card class="pa-4 d-flex flex-column align-center justify-center text-center" :color="color" :outlined="outlined"
        elevation="2">
        <slot name="icon">
            <v-icon large>{{ icon }}</v-icon>
        </slot>

        <div class="text-h5 mt-2">{{ value }}</div>
        <div class="text-subtitle-2">{{ title }}</div>

        <slot />
    </v-card>
</template>

<script>
export default {
    name: "WidgetLayout",
    props: {
        title: {
            type: String,
            required: true
        },
        value: {
            type: [String, Number],
            required: true
        },
        icon: {
            type: String,
            default: "mdi-information"
        },
        color: {
            type: String,
            default: "white"
        },
        outlined: {
            type: Boolean,
            default: false
        }
    }
};
</script>

<style scoped>
.v-card {
    min-height: 120px;
}
</style>