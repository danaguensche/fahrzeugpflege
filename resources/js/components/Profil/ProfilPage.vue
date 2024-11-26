<template>
    <div class="profil-page">
         <Form></Form>
    </div>
</template>

<script>

import Form from './Form.vue';

export default {
    name: "ProfilPage",

    components: {
        Form
    }
}

</script>

<style scoped>

.hr {
    border: none;
    height: 1px;
    background-color: black;
    margin: 20px 0; 
}
</style>