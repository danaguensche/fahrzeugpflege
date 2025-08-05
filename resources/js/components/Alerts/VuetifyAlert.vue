<template>
  <v-container>
    <v-dialog v-model="dialogVisible" :max-width="maxWidth" persistent transition="dialog-bottom-transition"
      @keydown.esc="handleEscape">
      <v-card :class="cardClass" elevation="8" rounded="lg">
        <!-- Header mit Icon -->
        <v-card-title class="d-flex align-center pa-6 pb-4">
          <v-icon :icon="alertIcon" :color="alertColor" size="28" class="mr-3"></v-icon>
          <span class="text-h5 font-weight-medium">{{ alertHeading }}</span>
        </v-card-title>

        <!-- Divider für bessere Trennung -->
        <v-divider :color="alertColor" opacity="0.3"></v-divider>

        <!-- Content -->
        <v-card-text class="pa-6 py-8">
          <p class="text-body-1 mb-0" style="line-height: 1.6;">
            {{ alertParagraph }}
          </p>
        </v-card-text>

        <!-- Actions mit verbessertem Layout -->
        <v-card-actions class="pa-6 pt-0 d-flex justify-space-between">
          <!-- Cancel Button für Confirmation Type (links) -->
          <v-btn v-if="isConfirmationType" variant="outlined" :color="alertColor" prepend-icon="mdi-cancel"
            @click="closeDialog" size="default">
            {{ alertCloseButton }}
          </v-btn>
          <div v-else></div>

          <!-- Primary Action Button (rechts) -->
          <v-btn :variant="isConfirmationType ? 'elevated' : 'elevated'" :color="alertColor"
            :prepend-icon="isConfirmationType ? 'mdi-check-circle' : 'mdi-check-circle'"
            @click="isConfirmationType ? confirmationClicked() : closeDialog()" size="default" :loading="loading">
            {{ isConfirmationType ? alertOkayButton : alertCloseButton }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
export default {
  name: 'VuetifyAlert',
  props: {
    modelValue: {
      type: Boolean,
      required: true
    },
    alertHeading: {
      type: String,
      required: true
    },
    alertParagraph: {
      type: String,
      required: true
    },
    alertCloseButton: {
      type: String,
      default: 'Schließen'
    },
    alertOkayButton: {
      type: String,
      default: 'Bestätigen'
    },
    maxWidth: {
      type: [String, Number],
      default: '500'
    },
    alertTypeClass: {
      type: String,
      default: 'alertTypeDefault',
      validator: (value) => ['alertTypeDefault', 'alertTypeConfirmation', 'alertTypeWarning', 'alertTypeError', 'alertTypeSuccess'].includes(value)
    },
    loading: {
      type: Boolean,
      default: false
    },
    allowEscapeClose: {
      type: Boolean,
      default: true
    }
  },

  emits: ['update:modelValue', 'confirmation', 'close'],

  computed: {
    dialogVisible: {
      get() {
        return this.modelValue
      },
      set(value) {
        this.$emit('update:modelValue', value)
      }
    },

    isConfirmationType() {
      return this.alertTypeClass === 'alertTypeConfirmation'
    },

    alertColor() {
      const colorMap = {
        alertTypeDefault: 'primary',
        alertTypeConfirmation: 'error',
        alertTypeWarning: 'warning',
        alertTypeError: 'error',
        alertTypeSuccess: 'success'
      }
      return colorMap[this.alertTypeClass] || 'primary'
    },

    alertIcon() {
      const iconMap = {
        alertTypeDefault: 'mdi-information',
        alertTypeConfirmation: 'mdi-help-circle',
        alertTypeWarning: 'mdi-alert',
        alertTypeError: 'mdi-alert-circle',
        alertTypeSuccess: 'mdi-check-circle'
      }
      return iconMap[this.alertTypeClass] || 'mdi-information'
    },

    cardClass() {
      return `alert-card alert-card--${this.alertTypeClass}`
    }
  },

  methods: {
    closeDialog() {
      this.dialogVisible = false
      this.$emit('close')
    },

    confirmationClicked() {
      this.$emit('confirmation')
    },

    handleEscape() {
      if (this.allowEscapeClose && !this.isConfirmationType) {
        this.closeDialog()
      }
    }
  }
}
</script>

<style scoped>
.alert-card {
  overflow: hidden;
}

.alert-card--alertTypeWarning {
  border-top: 4px solid rgb(var(--v-theme-warning));
}

.alert-card--alertTypeError {
  border-top: 4px solid rgb(var(--v-theme-error));
}

.alert-card--alertTypeSuccess {
  border-top: 4px solid rgb(var(--v-theme-success));
}

.alert-card--alertTypeDefault {

  border-top: 4px solid rgb(var(--v-theme-primary));
}

.alert-card--alertTypeConfirmation {
  border-top: 4px solid rgb(var(--v-theme-error));
}

/* Hover-Effekte für Buttons */
.v-btn:hover {
  transform: translateY(-1px);
  transition: transform 0.2s ease;
}

/* Bessere Typographie */
.v-card-title {
  letter-spacing: 0.025em;
}

/* Responsive Anpassungen */
@media (max-width: 600px) {
  .v-card-actions {
    flex-direction: column-reverse !important;
    gap: 12px;
  }

  .v-card-actions .v-btn {
    width: 100%;
  }
}
</style>