<template>
    <v-btn variant="elevated" color="primary" prepend-icon="mdi-printer" class="ml-2" @click="print">
        Drucken
    </v-btn>
</template>

<script>
export default {
    name: 'PrintButton',
    methods: {
        print() {
            window.print();
        },
    },
}
</script>