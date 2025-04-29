<template>

    <div class="pa-4
                  bg-primary-lighten-5
                  rounded-t-lg
                  d-flex align-center
                  justify-space-between">
        <span class="text-h6 font-weight-medium">{{ title }}</span>
    </div>

</template>

<script>
export default {
    name: 'DefaultHeader',

    props: {
        title: {
            type: String,
            default: ''
        },
    }
}
</script>