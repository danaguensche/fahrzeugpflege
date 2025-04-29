<template>
    <v-card class="pa-6 rounded-xl" elevation="2">
        <v-skeleton-loader
          type="heading,
                list-item-three-line,
                table-heading,
                table-row-divider,
                table-row,
                table-row-divider,
                table-row">
        </v-skeleton-loader>
      </v-card>
</template>

<script>
export default{
    name: 'Loader',

}
</script>