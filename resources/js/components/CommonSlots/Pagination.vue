<template>
    <div class="pagination-container">
        <n-pagination
            :page="currentPage"
            :page-count="totalPages"
            @update:page="changePage"
        />
    </div>
</template>

<script>
import { NPagination } from 'naive-ui'

export default {
    name: "Pagination",
    components: {
        NPagination
    },
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