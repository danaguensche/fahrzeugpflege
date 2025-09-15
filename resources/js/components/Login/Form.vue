<template>
    <v-card class="mx-auto my-12 pt-6 header" outlined>
        <v-container>
            <!-- Titel und Untertitel mit Vuetify Fade-Transition -->
            <v-fade-transition mode="out-in">
                <v-card-title :key="isRegistered ? 'login' : 'signup'"
                    class="text-h5 text-center font-weight-bold pt-6">
                    {{ isRegistered ? 'Willkommen zurück!' : 'Erstellen Sie Ihr Konto' }}
                </v-card-title>
            </v-fade-transition>

            <v-fade-transition mode="out-in">
                <v-card-subtitle :key="isRegistered ? 'login-sub' : 'signup-sub'" class="text-center mb-6">
                    {{ isRegistered ? 'Melden Sie sich an, um fortzufahren' : 'Registrieren Sie sich, um fortzufahren'
                    }}
                </v-card-subtitle>
            </v-fade-transition>
        </v-container>

        <v-card-text class="pa-8 ps-16 pr-16">
            <v-slide-y-transition>
                <v-alert v-if="error" type="error" dismissible @click:close="error = null" class="mb-4">
                    {{ error }}
                </v-alert>
            </v-slide-y-transition>

            <v-form ref="form" v-model="valid" @submit.prevent="submitForm">
                <v-container>
                    <v-row>
                        <v-expand-transition>
                            <div v-if="!isRegistered" class="registration-fields w-100">
                                <v-row>
                                    <v-col cols="12" md="6">
                                        <v-slide-x-transition>
                                            <v-text-field v-model="formData.firstname" label="Vorname"
                                                :rules="nameRules" required variant="outlined" density="comfortable"
                                                prepend-inner-icon="mdi-account" :disabled="loading">
                                            </v-text-field>
                                        </v-slide-x-transition>
                                    </v-col>

                                    <v-col cols="12" md="6">
                                        <v-slide-x-reverse-transition>
                                            <v-text-field v-model="formData.lastname" label="Nachname"
                                                :rules="nameRules" required variant="outlined" density="comfortable"
                                                prepend-inner-icon="mdi-account" :disabled="loading">
                                            </v-text-field>
                                        </v-slide-x-reverse-transition>
                                    </v-col>

                                    <v-col cols="12">
                                        <v-text-field v-model="formData.email" label="E-Mail" :rules="emailRules"
                                            required variant="outlined" density="comfortable"
                                            prepend-inner-icon="mdi-email" class="mb-6" type="email"
                                            autocomplete="email" :disabled="loading">
                                        </v-text-field>
                                    </v-col>

                                </v-row>
                            </div>
                        </v-expand-transition>

                        <v-col cols="12" v-if="isRegistered">
                            <v-text-field v-model="formData.email" label="E-Mail" :rules="emailRules" required
                                variant="outlined" density="comfortable" prepend-inner-icon="mdi-email" class="mb-3"
                                type="email" autocomplete="email" :disabled="loading">
                            </v-text-field>
                        </v-col>

                        <v-slide-y-transition mode="out-in">
                            <v-col v-if="isRegistered" key="login-password" cols="12">
                                <v-text-field v-model="formData.password" label="Passwort" :rules="passwordRules"
                                    required variant="outlined" density="comfortable" prepend-inner-icon="mdi-lock"
                                    class="mb-3" :type="showPassword ? 'text' : 'password'"
                                    :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                                    @click:append-inner="showPassword = !showPassword"
                                    :autocomplete="isRegistered ? 'current-password' : 'new-password'"
                                    :disabled="loading">
                                </v-text-field>
                            </v-col>

                            <div v-else key="signup-passwords" class="password-fields w-100">
                                <v-row>
                                    <v-col cols="6">
                                        <v-slide-x-transition>
                                            <v-text-field v-model="formData.password" label="Passwort"
                                                :rules="passwordRules" required variant="outlined" density="comfortable"
                                                prepend-inner-icon="mdi-lock" class="mb-3"
                                                :type="showPassword ? 'text' : 'password'"
                                                :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                                                @click:append-inner="showPassword = !showPassword"
                                                autocomplete="new-password" :disabled="loading">
                                            </v-text-field>
                                        </v-slide-x-transition>
                                    </v-col>

                                    <v-col cols="6">
                                        <v-slide-x-reverse-transition>
                                            <v-text-field v-model="formData.passwordConfirm"
                                                label="Passwort wiederholen" :rules="passwordConfirmRules" required
                                                variant="outlined" density="comfortable" prepend-inner-icon="mdi-lock"
                                                :type="showPasswordConfirm ? 'text' : 'password'"
                                                :append-inner-icon="showPasswordConfirm ? 'mdi-eye' : 'mdi-eye-off'"
                                                @click:append-inner="showPasswordConfirm = !showPasswordConfirm"
                                                autocomplete="new-password" :disabled="loading">
                                            </v-text-field>
                                        </v-slide-x-reverse-transition>
                                    </v-col>
                                </v-row>
                            </div>
                        </v-slide-y-transition>
                    </v-row>
                </v-container>
            </v-form>
        </v-card-text>

        <v-card-actions>
            <v-container>

                <v-row>
                    <v-col cols="12">
                        <v-scroll-y-transition mode="out-in">
                            <v-btn :key="isRegistered ? 'login-btn' : 'signup-btn'" color="primary" @click="submitForm"
                                :disabled="!valid || loading" :loading="loading" block size="large">
                                {{ isRegistered ? 'Anmelden' : 'Registrieren' }}
                            </v-btn>
                        </v-scroll-y-transition>
                    </v-col>
                </v-row>

                <v-row class="mt-3 mb-12">
                    <v-col cols="12" class="text-center">
                        <v-fade-transition mode="out-in">
                            <span :key="isRegistered ? 'login-text' : 'signup-text'" class="text-body-2">
                                {{ isRegistered ? 'Noch kein Konto?' : 'Bereits registriert?' }}
                            </span>
                        </v-fade-transition>

                        <v-slide-x-transition mode="out-in">
                            <v-btn :key="isRegistered ? 'register-link' : 'login-link'" variant="text" color="primary"
                                @click="toggleMode" :disabled="loading" size="small">
                                {{ isRegistered ? 'Jetzt registrieren' : 'Jetzt anmelden' }}
                            </v-btn>
                        </v-slide-x-transition>
                    </v-col>
                </v-row>
            </v-container>
        </v-card-actions>
    </v-card>
</template>

<script>
export default {
    name: "AuthForm",
    data() {
        return {
            formData: {
                firstname: '',
                lastname: '',
                email: '',
                password: '',
                passwordConfirm: ''
            },
            valid: false,
            isRegistered: true,
            loading: false,
            error: null,
            rememberMe: false,
            showPassword: false,
            showPasswordConfirm: false,

            // Validierungsregeln
            nameRules: [
                v => !!v || 'Dieses Feld ist erforderlich',
                v => (v && v.length >= 2) || 'Mindestens 2 Zeichen erforderlich',
                v => /^[a-zA-ZäöüÄÖÜß\s-]+$/.test(v) || 'Nur Buchstaben, Leerzeichen und Bindestriche erlaubt'
            ],
            emailRules: [
                v => !!v || 'E-Mail ist erforderlich',
                v => /.+@.+\..+/.test(v) || 'E-Mail muss gültig sein'
            ],
            passwordRules: [
                v => !!v || 'Passwort ist erforderlich',
                v => (v && v.length >= 8) || 'Passwort muss mindestens 8 Zeichen lang sein',
                v => /(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(v) || 'Passwort muss mindestens einen Groß-, einen Kleinbuchstaben und eine Zahl enthalten'
            ]
        }
    },
    computed: {
        passwordConfirmRules() {
            return [
                v => !!v || 'Passwort-Bestätigung ist erforderlich',
                v => v === this.formData.password || 'Passwörter stimmen nicht überein'
            ]
        },
        csrf_token() {
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            return metaTag ? metaTag.getAttribute('content') : null;
        }
    },
    methods: {
        toggleMode() {
            this.isRegistered = !this.isRegistered;
            this.error = null;
            this.resetForm();
        },

        resetForm() {
            this.formData = {
                firstname: '',
                lastname: '',
                email: '',
                password: '',
                passwordConfirm: ''
            };
            this.$nextTick(() => {
                this.$refs.form?.resetValidation();
            });
        },

        async submitForm() {
            // Form validieren
            const { valid } = await this.$refs.form.validate();
            if (!valid) return;

            this.loading = true;
            this.error = null;

            try {
                const endpoint = this.isRegistered ? '/login' : '/signup';
                const payload = this.isRegistered
                    ? {
                        email: this.formData.email,
                        password: this.formData.password,
                    }
                    : {
                        firstname: this.formData.firstname,
                        lastname: this.formData.lastname,
                        email: this.formData.email,
                        password: this.formData.password,
                        password_confirmation: this.formData.passwordConfirm,
                        role: 'trainee'
                    };

                if (this.csrf_token) {
                    payload._token = this.csrf_token;
                }

                const response = await this.$http.post(endpoint, payload);

                if (response.data.success) {
                    await this.handleSuccessfulAuth(response.data);
                } else {
                    this.error = response.data.message || 'Ein unerwarteter Fehler ist aufgetreten.';
                }

            } catch (error) {
                this.handleAuthError(error);
            } finally {
                this.loading = false;
            }
        },

        async handleSuccessfulAuth(data) {
            const { token, user, redirect } = data;

            // Standard-Rolle trainee
            if (user && !user.role) {
                user.role = 'trainee';
            }

            try {
                await this.$store.dispatch('auth/login', {
                    token,
                    role: user?.role || 'trainee',
                    id: user?.id || ''
                });
                console.log('Store updated - userRole:', this.$store.state.auth.userRole); // Debug
            } catch (error) {
                console.error('Store update failed:', error);
            }

            // Axios Standard-Header setzen
            if (this.$http?.defaults?.headers?.common) {
                this.$http.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            }
            this.$emit('auth-success', { user, token });
            if (redirect) {
                window.location.href = redirect;
            } else {
                this.$router?.push('/dashboard');
            }
        },

        handleAuthError(error) {
            console.error('Auth-Fehler:', error);

            if (error.response?.status === 422 && error.response.data?.errors) {
                const errors = error.response.data.errors;
                const firstError = Object.values(errors)[0];
                this.error = Array.isArray(firstError) ? firstError[0] : firstError;
            } else if (error.response?.data?.message) {
                this.error = error.response.data.message;
            } else if (error.response?.status >= 500) {
                this.error = 'Serverfehler. Bitte versuchen Sie es später erneut.';
            } else {
                this.error = 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.';
            }
        }
    },

    mounted() {
        // Automatisches Fokussieren des ersten Feldes
        this.$nextTick(() => {
            const firstField = this.$el.querySelector('input');
            if (firstField && !this.$vuetify.display.mobile) {
                firstField.focus();
            }
        });
    }
}
</script>

<style scoped>
.forms-buttons-forgot {
    text-decoration: none;
    color: rgb(var(--v-theme-primary));
}

.forms-buttons-forgot:hover {
    text-decoration: underline;
}

.header {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
    border-radius: 12px;
    height: 700px;
}
</style>