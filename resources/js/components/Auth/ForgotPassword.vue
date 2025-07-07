<template>
    <v-container fluid fill-height>
        <v-row align="center" justify="center">
            <v-col cols="12" sm="8" md="4">
                <v-card class="elevation-12">
                    <v-toolbar dark color="primary">
                        <v-toolbar-title>Passwort vergessen</v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <v-form ref="form" v-model="valid" lazy-validation>
                            <v-text-field
                                label="E-Mail"
                                name="email"
                                prepend-icon="mdi-email"
                                type="email"
                                v-model="email"
                                :rules="emailRules"
                                required
                            ></v-text-field>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="primary" @click="submit" :disabled="!valid || loading" :loading="loading">
                            Link senden
                        </v-btn>
                    </v-card-actions>
                </v-card>
                <v-alert
                    v-if="message"
                    :type="messageType"
                    class="mt-4"
                    dismissible
                >
                    {{ message }}
                </v-alert>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            valid: true,
            email: '',
            emailRules: [
                v => !!v || 'E-Mail ist erforderlich',
                v => /.+@.+\..+/.test(v) || 'E-Mail muss gültig sein',
            ],
            loading: false,
            message: '',
            messageType: 'success',
        };
    },
    methods: {
        async submit() {
            if (this.$refs.form.validate()) {
                this.loading = true;
                this.message = '';
                try {
                    const response = await axios.post('/api/forgot-password', {
                        email: this.email,
                    });
                    this.message = response.data.message;
                    this.messageType = 'success';
                } catch (error) {
                    this.message = error.response?.data?.message || 'Ein Fehler ist aufgetreten.';
                    this.messageType = 'error';
                } finally {
                    this.loading = false;
                }
            }
        },
    },
};
</script>
