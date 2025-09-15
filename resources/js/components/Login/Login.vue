<template>
    <v-container fluid class="fill-height">
        <v-row class="justify-center align-center fill-height">
            <v-col cols="12" sm="8">
                <Form></Form>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import Form from './Form.vue';
export default {
    name: "Login",
    components: {
        Form,
    }
}
</script>

<style scoped>
.fill-height {
    min-height: 100vh;
}
</style>