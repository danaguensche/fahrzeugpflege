<template>
    <v-dialog v-model="showDialogLocal" persistent max-width="700px">
        <v-card class="pa-2">
            <v-card-title class="headline pa-6 pb-4">
                <v-icon class="mr-3" color="primary">mdi-account-plus</v-icon>
                Neuen Benutzer hinzufügen
            </v-card-title>

            <v-card-subtitle class="pa-6 pt-0 pb-4">
                Erstellen Sie einen einzigartigen Benutzernamen, mit dem sich der Benutzer anmelden kann.
            </v-card-subtitle>
            
            <v-divider></v-divider>
            
            <v-card-text class="pa-6">
                <v-form ref="form" v-model="valid" lazy-validation>
                    <v-row>
                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="user.username"
                                label="Benutzername"
                                :rules="[v => !!v || 'Benutzername ist erforderlich']"
                                required
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-account"
                                class="mb-3"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-select
                                v-model="user.role"
                                label="Rolle"
                                :items="roleOptions"
                                item-title="text"
                                item-value="value"
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-account-cog"
                                class="mb-3"
                                :rules="[v => !!v || 'Rolle ist erforderlich']"
                                required
                            ></v-select>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card-text>
            
            <v-divider></v-divider>
            

            <!-- Buttons -->
            <v-card-actions class="pa-6 pt-4">

                <v-spacer></v-spacer>
                <v-btn 
                    variant="outlined" 
                    color="grey" 
                    @click="closeDialog"
                    class="mr-3">
                 <v-icon start>mdi-close</v-icon>
                    Abbrechen
                </v-btn>

                <v-btn 
                    variant="elevated" 
                    color="primary" 
                    @click="saveUser"
                    :loading="usersLoading">
                    <v-icon start>mdi-content-save</v-icon>
                    Speichern
                </v-btn>

            </v-card-actions>
        </v-card>
        
        <SnackBar 
            v-if="snackbar.show" 
            :text="snackbar.text" 
            :color="snackbar.color" 
            @close="snackbar.show = false"/>
    </v-dialog>
</template>

<script>
import axios from 'axios';
import SnackBar from '../../Details/SnackBar.vue';

export default {
    name: 'AddUserForm',
    components: {
        SnackBar,
    },
    props: {
        modelValue: Boolean,
    },
    data() {
        return {
            valid: true,
            user: {
                username: '',
                role: 'trainee',
            },
            roleOptions: [
                { text: 'Trainee', value: 'trainee' },
                { text: 'Trainer', value: 'trainer' },
                { text: 'Admin', value: 'admin' },
            ],
            usersLoading: false,
            snackbar: {
                show: false,
                text: '',
                color: 'success',
            },
        };
    },
    computed: {
        showDialogLocal: {
            get() {
                return this.modelValue;
            },
            set(value) {
                this.$emit('update:modelValue', value);
            },
        },
    },
    watch: {
        modelValue(val) {
            if (val) {
                this.resetForm();
            }
        },
    },
    methods: {

        closeDialog() {
            this.$emit('update:modelValue', false);
            this.resetForm();
        },

        async saveUser() {
            const { valid } = await this.$refs.form.validate();
            if (valid) {
                this.usersLoading = true;
                try {
                    const userData = {
                        username: this.user.username,
                        role: this.user.role,
                    };

                    await axios.post('/api/users', userData);
                    this.$emit('user-added');
                    this.showSnackbar('Benutzer erfolgreich hinzugefügt', 'success');
                    this.closeDialog();
                } catch (error) {
                    console.error('Error saving user:', error);
                    this.showSnackbar(error.response?.data?.message || 'Fehler beim Speichern des Benutzers', 'error');
                } finally {
                    this.usersLoading = false;
                }
            }
        },
        
        resetForm() {
            if (this.$refs.form) {
                this.$refs.form.reset();
                this.$refs.form.resetValidation();
            }
            this.user = {
                username: '',
                role: 'trainee',
            };
        },
        
        showSnackbar(text, color = 'success') {
            this.snackbar = {
                show: true,
                text,
                color,
            };
        },
    },
};
</script>

<style scoped>

.v-dialog {
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
}

.v-card {
    border-radius: 12px;
}

.v-card-title {
    font-size: 1.25rem;
    font-weight: 600;
}
</style>