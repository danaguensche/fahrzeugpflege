<template>
    <v-footer class="footer">
        <div class="footer-container">
            <div class="breadcrumbs-container">
                <v-breadcrumbs :items="links" divider="/">
                    <template v-slot:title="{ item }">
                        {{ item.title }}
                    </template>
                    <template v-slot:prepend>
                        <v-icon icon="mdi-view-dashboard" size="small"></v-icon>
                    </template>
                </v-breadcrumbs>
            </div>
        </div>
    </v-footer>
</template>

<script>
import Footer from './Footer.vue';

export default {
    name: "BreadCrumbs",
    components: {
        Footer
    },
    props: {
        links: {
            type: Array,
            default: () => [{}],
            required: true,
        }
    },
    mounted() {
        console.log('Component mounted, links:', this.links);
    }
}
</script>

<style scoped>
.footer {
    left: 0;
    position: fixed;
    bottom: 0;
    width: 100%;
    background-color: #f5f5f5;
    min-height: 80px !important;
}

.footer-container {
    width: 100%;
    display: flex;
    justify-content: flex-end;
    padding: 0 20px;
}

.breadcrumbs-container {
    display: flex;
    align-items: center;
}

.breadcrumbs-right {
    margin-left: auto;
}
</style>