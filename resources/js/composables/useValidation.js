import { ref, reactive } from 'vue';

export function useValidation(initialData) {
    const formData = reactive(initialData);
    const errors = ref({});

    const validators = {
        validateFirstname(value) {
            return value.length > 0 ? true : "Vorname ist erforderlich.";
        },
        validateLastname(value) {
            return value.length > 0 ? true : "Nachname ist erforderlich.";
        },
        validatePhonenumber(value) {
            return value.length > 0 ? true : "Telefonnummer ist erforderlich.";
        },
        validateAddressline(value) {
            return value.length > 0 ? true : "Straße und Hausnummer sind erforderlich.";
        },
        validatePostalcode(value) {
            return value.length > 0 ? true : "Postleitzahl ist erforderlich.";
        },
        validatePostalcode(value) {
            return value.length > 0 ? true : "Stadt ist erforderlich.";
        },
        validateKennzeichen(value) {
            return value.length > 0 ? true : "Bitte geben Sie ein Kennzeichen ein.";
        },

        
        validateEmail(value) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailPattern.test(value) ? true : "Bitte geben Sie eine gültige Email-Adresse ein.";
        },
        validatePassword(value) {
            return value.length >= 8 ? true : "Das Passwort muss mindestens 8 Zeichen lang sein.";
        },
        validatePasswordConfirmation(value, password) {
            return value === password ? true : "Die Passwörter stimmen nicht überein.";
        }
    };

    const validateForm = () => {
        errors.value = {};
        return Object.keys(formData).every(field => {
            //generiert dynamisch den Namen der Validierungsmethode für das entsprechende Feld
            const validatorName = `validate${field.charAt(0).toUpperCase() + field.slice(1)}`;
            if (validators[validatorName]) {
                const result = validators[validatorName](formData[field], formData);
                if (result !== true) {
                    errors.value[field] = result;
                    return false;
                }
            }
            return true;
        });
    };

    return {
        formData,
        errors,
        validateForm
    };
}
