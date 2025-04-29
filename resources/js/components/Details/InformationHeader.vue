<template>

            <div class="pa-4
                          bg-primary-lighten-5
                          rounded-t-lg
                          d-flex align-center
                          justify-space-between">
                <span class="text-h6 font-weight-medium">{{ title }}</span>
                <div v-if="editMode">
                    <v-chip color="warning" variant="flat">Bearbeitungsmodus</v-chip>
                </div>
                <div v-else>
                    <v-chip color="success" variant="flat">Ansichtsmodus</v-chip>
                </div>
            </div>

</template>

<script>
export default {
    name: 'InformationHeader',

    props: {
        editMode: {
            type: Boolean,
            default: false
        },
        title: {
            type: String,
            default: ''
        },
    }
}
</script>