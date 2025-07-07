<template>
    <v-container fluid fill-height>
        <v-row align="center" justify="center">
            <v-col cols="12" sm="8" md="4">
                <v-card class="elevation-12">
                    <v-toolbar dark color="primary">
                        <v-toolbar-title>Passwort zurücksetzen</v-toolbar-title>
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
                                :disabled="true"
                            ></v-text-field>

                            <v-text-field
                                label="Passwort"
                                name="password"
                                prepend-icon="mdi-lock"
                                type="password"
                                v-model="password"
                                :rules="passwordRules"
                                required
                            ></v-text-field>

                            <v-text-field
                                label="Passwort bestätigen"
                                name="password_confirmation"
                                prepend-icon="mdi-lock-check"
                                type="password"
                                v-model="password_confirmation"
                                :rules="passwordConfirmationRules"
                                required
                            ></v-text-field>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="primary" @click="submit" :disabled="!valid || loading" :loading="loading">
                            Passwort zurücksetzen
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
            password: '',
            password_confirmation: '',
            token: '',
            emailRules: [
                v => !!v || 'E-Mail ist erforderlich',
                v => /.+@.+\..+/.test(v) || 'E-Mail muss gültig sein',
            ],
            passwordRules: [
                v => !!v || 'Passwort ist erforderlich',
                v => v.length >= 8 || 'Passwort muss mindestens 8 Zeichen lang sein',
            ],
            passwordConfirmationRules: [
                v => !!v || 'Passwortbestätigung ist erforderlich',
                v => v === this.password || 'Passwörter stimmen nicht überein',
            ],
            loading: false,
            message: '',
            messageType: 'success',
        };
    },
    created() {
        this.email = this.$route.query.email || '';
        this.token = this.$route.params.token || '';
    },
    methods: {
        async submit() {
            if (this.$refs.form.validate()) {
                this.loading = true;
                this.message = '';
                try {
                    const response = await axios.post('/api/reset-password', {
                        email: this.email,
                        password: this.password,
                        password_confirmation: this.password_confirmation,
                        token: this.token,
                    });
                    this.message = response.data.message;
                    this.messageType = 'success';
                    // Optionally redirect to login page after successful reset
                    // this.$router.push('/login');
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
