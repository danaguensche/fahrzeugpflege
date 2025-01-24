<template>
    <div class="pagination-container">
        <v-container>
            <v-row justify="center">
                <v-col cols="8">
                    <v-pagination
                        v-model="page"
                        :length="totalPages"
                        class="my-4"
                        @input="changePage"
                    ></v-pagination>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
export default {
    name: "Pagination",
    props: {
        currentPage: {
            type: Number,
            required: true
        },
        totalPages: {
            type: Number,
            required: true
        }
    },
    data() {
        return {
            page: this.currentPage
        };
    },
    watch: {
        currentPage(newPage) {
            this.page = newPage;
        }
    },
    methods: {
        changePage(page) {
            this.$emit('page-changed', page);
        }
    }
}
</script>

<style scoped>
.pagination-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 20px auto;
}
</style>