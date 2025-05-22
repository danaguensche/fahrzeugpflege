<template>
    <v-snackbar v-model="snackbar.show" :color="snackbar.color" timeout="3000">
        {{ snackbar.text }}

        <template v-slot:actions>
            <v-btn variant="text" @click="snackbar.show = false">
                Schließen
            </v-btn>
        </template>
    </v-snackbar>
</template>

<script>
export default {
    name: 'SnackBar',
    props: {
        text: {
            type: String,
            default: '',
        },
        color: {
            type: String,
            default: 'success',
        },
        show: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            snackbar: {
                show: false,
                text: '',
                color: 'success',
            },
        };
    },
    methods: {
        showSnackbar(text, color = 'success') {
            this.snackbar.text = text;
            this.snackbar.color = color;
            this.snackbar.show = true;
        },
    },
};
</script>