<template>
  <v-list class="bg-transparent">
    <v-list-item>
      <template v-slot:prepend>
        <v-icon icon="mdi-account" color="primary" class="mr-2"></v-icon>
      </template>
      <v-list-item-title class="font-weight-medium">{{ labels['customer'] }}</v-list-item-title>
      <v-list-item-subtitle class="mt-1 text-body-1">
        <template v-if="customer">
          <v-btn variant="text" color="primary" prepend-icon="mdi-account-details"
            :to="`/kunden/kundendetails/${customer.id}`" class="pa-0">
            {{ customer.firstname }} {{ customer.lastname }}
          </v-btn>
        </template>
        <template v-else>
          <span class="text-grey">Kein Kunde zugeordnet</span>
        </template>
      </v-list-item-subtitle>
    </v-list-item>
    <v-divider></v-divider>
    <v-list-item>
      <template v-slot:prepend>
        <v-icon icon="mdi-identifier" color="primary" class="mr-2"></v-icon>
      </template>
      <v-list-item-title class="font-weight-medium">{{ labels['customer_id'] }}</v-list-item-title>
      <v-list-item-subtitle class="mt-1 text-body-1">
        {{ customerId || 'Nicht zugeordnet' }}
      </v-list-item-subtitle>
    </v-list-item>
  </v-list>
</template>

<script>
export default {
  name: 'CustomerInfoList',
  props: {
    customer: {
      type: Object,
      default: null,
    },
    customerId: {
      type: [Number, String],
      default: null,
    },
    labels: {
      type: Object,
      default: () => ({
        customer: "Kunde",
        customer_id: "Kunden-ID"
      }),
    }
  }
}
</script>