<template>
    <v-container>
      <v-dialog v-model="dialogVisible" :max-width="maxWidth" transition="dialog-bottom-transition">
        <v-card :title="alertHeading">
          <v-card-text>{{ alertParagraph }}</v-card-text>
          <v-card-actions>
            <v-btn
              prepend-icon="mdi-check-circle"
              v-if="alertTypeClass === 'alertTypeConfirmation'"
              @click="confirmationClicked"
              :text="alertOkayButton"
            ></v-btn>
            <v-spacer></v-spacer>
            <v-btn v-if="alertTypeClass === 'alertTypeConfirmation'"
              prepend-icon="mdi-cancel"
              :text="alertCloseButton"
              @click="closeDialog"
            ></v-btn>

            <v-btn v-else="alertTypeClass === 'alertTypeDefault'"
              prepend-icon="mdi-check-circle"
              :text="alertCloseButton"
              @click="closeDialog"
            ></v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-container>
  </template>
  
  <script>
    export default {
      name: 'VuetifyAlert',
      props: {
        modelValue: Boolean,
        alertHeading: String,
        alertParagraph: String,
        alertCloseButton: String,
        alertOkayButton: String,
        maxWidth: {
          type: String,
          default: '500',
        },
        alertTypeClass: {
          type: String,
          default: 'alertTypeDefault',
        },
      },
      computed: {
        dialogVisible: {
          get() {
            return this.modelValue
          },
          set(value) {
            this.$emit('update:modelValue', value)
          },
        },
      },
      methods: {
        closeDialog() {
          this.dialogVisible = false
        },
        confirmationClicked() {
          this.$emit('confirmation')
        },
      },
    }
  </script>
  