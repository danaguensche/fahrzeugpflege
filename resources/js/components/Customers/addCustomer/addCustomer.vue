<template>
    <div class="customer-page" :class="{ 'customer-page-sidebar-opened': isSidebarOpen }">
        <div class="content-container">
            <div class="form-container">
                <CustomerForm></CustomerForm>
            </div>
        </div>
    </div>
</template>

<script>
import { mapState } from 'vuex';
import CustomerForm from './CustomerForm.vue';
export default {
    name: 'Customer',
    components: {
        CustomerForm
    },

    computed: {
        ...mapState(['isSidebarOpen'])
    },

    data() {
        return {
        
        }
    }
}

</script>

<style scoped>
.customer-page {
    display: flex;
    flex-direction: column;
    margin-left: 150px;
    margin-right: 30px;
    transition: margin-left 0.3s ease;
    font-family: var(--font-family);
}

.customer-page-sidebar-opened {
    margin-left: 350px;
}

.content-container {
    margin-top: 10px;
    display: flex;
    justify-content: flex-end;
}

.form-container {
    width: 50%;
}

@media only screen and (max-width: 650px) {
    .customer-page {
        margin-left: 160px;
        font-size: 12px;
    }

    .customer-page-sidebar-opened {
        margin-left: 260px;
    }

    .content-container {
        flex-direction: column;
    }

    .form-container {
        width: 100%;
    }

    .searchbar {
        width: 300px;
    }
}
</style>