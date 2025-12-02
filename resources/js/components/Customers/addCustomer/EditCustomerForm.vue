<template>
    <v-dialog v-model="showDialogLocal" persistent max-width="700px">
        <v-card class="pa-2">
            <v-card-title class="headline pa-6 pb-4">
                <v-icon class="mr-3" color="primary">mdi-account-edit</v-icon>
                Kunde bearbeiten
            </v-card-title>
            
            <v-divider></v-divider>
            
            <v-card-text class="pa-6">
                <v-form ref="form" v-model="valid" lazy-validation>
                    <v-row>
                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="customer.id"
                                label="ID"
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-identifier"
                                class="mb-3"
                                disabled
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="customer.firstname"
                                label="Vorname *"
                                :rules="[v => !!v || 'Vorname ist erforderlich']"
                                required
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-account"
                                class="mb-3"
                                :maxlength="50"
                                :counter="50"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="customer.lastname"
                                label="Nachname *"
                                :rules="[v => !!v || 'Nachname ist erforderlich']"
                                required
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-account"
                                class="mb-3"
                                :maxlength="50"
                                :counter="50"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="customer.email"
                                label="E-Mail *"
                                :rules="emailRules"
                                required
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-email"
                                class="mb-3"
                                type="email"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="customer.phonenumber"
                                label="Telefonnummer *"
                                :rules="phoneRules"
                                required
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-phone"
                                class="mb-3"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="customer.addressline"
                                label="Straße und Hausnummer"
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-home"
                                class="mb-3"
                                :maxlength="100"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="customer.postalcode"
                                label="PLZ"
                                :rules="postalCodeRules"
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-mailbox"
                                class="mb-3"
                                :maxlength="5"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="customer.city"
                                label="Stadt"
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-city"
                                class="mb-3"
                                :maxlength="50"
                            ></v-text-field>
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
                    @click="saveCustomer"
                    :loading="loading">
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
    name: 'EditCustomerForm',
    
    components: {
        SnackBar,
    },
    
    props: {
        modelValue: Boolean,
        customerData: {
            type: Object,
            default: null,
        },
    },

    emits: ['update:modelValue', 'customer-edited'],

    data() {
        return {
            valid: true,
            loading: false,
            customer: {
                id: null,
                firstname: '',
                lastname: '',
                email: '',
                phonenumber: '',
                addressline: '',
                postalcode: '',
                city: '',
            },
            originalId: null,
            emailRules: [
                v => !!v || 'E-Mail ist erforderlich',
                v => /.+@.+\..+/.test(v) || 'Ungültige E-Mail-Adresse'
            ],
            phoneRules: [
                v => !!v || 'Telefonnummer ist erforderlich',
                v => /^[0-9+\-\s()]{6,}$/.test(v) || 'Ungültige Telefonnummer'
            ],
            postalCodeRules: [
                v => !v || /^[0-9]{5}$/.test(v) || 'PLZ muss 5 Ziffern haben'
            ],
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
                this.populateFormData();
            }
        },
        customerData: {
            handler(newData) {
                if (newData && this.modelValue) {
                    this.populateFormData();
                }
            },
            deep: true,
            immediate: true,
        },
    },

    methods: {
        populateFormData() {
            if (this.customerData) {
                this.customer = {
                    id: this.customerData.id || null,
                    firstname: this.customerData.firstname || '',
                    lastname: this.customerData.lastname || '',
                    email: this.customerData.email || '',
                    phonenumber: this.customerData.phonenumber || '',
                    addressline: this.customerData.addressline || '',
                    postalcode: this.customerData.postalcode || '',
                    city: this.customerData.city || '',
                };
                this.originalId = this.customerData.id;
            }
        },

        closeDialog() {
            this.$emit('update:modelValue', false);
            this.resetForm();
        },

        async saveCustomer() {
            const { valid } = await this.$refs.form.validate();
            if (valid) {
                this.loading = true;
                try {
                    const customerData = {
                        firstname: this.customer.firstname,
                        lastname: this.customer.lastname,
                        email: this.customer.email,
                        phonenumber: this.customer.phonenumber,
                        addressline: this.customer.addressline,
                        postalcode: this.customer.postalcode,
                        city: this.customer.city,
                    };

                    await axios.put(`/api/customers/${this.originalId}`, customerData);
                    this.$emit('customer-edited');
                    this.showSnackbar('Kunde erfolgreich aktualisiert', 'success');
                    this.closeDialog();
                } catch (error) {
                    console.error('Error updating customer:', error);
                    this.showSnackbar(
                        error.response?.data?.message || 'Fehler beim Aktualisieren des Kunden', 
                        'error'
                    );
                } finally {
                    this.loading = false;
                }
            }
        },
        
        resetForm() {
            if (this.$refs.form) {
                this.$refs.form.reset();
                this.$refs.form.resetValidation();
            }
            this.customer = {
                id: null,
                firstname: '',
                lastname: '',
                email: '',
                phonenumber: '',
                addressline: '',
                postalcode: '',
                city: '',
            };
            this.originalId = null;
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
