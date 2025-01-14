<template>
    <div class="pagination-container">
        <div class="pagination">
            <p @click.prevent="changePage(currentPage - 1)"> &laquo; </p>
            <p v-for="page in totalPages" :key="page" @click.prevent="changePage(page)"
                :class="{ active: page === currentPage }">{{ page }}</p>
            <p @click.prevent="changePage(currentPage + 1)"> &raquo; </p>
        </div>
    </div>
</template>

<script>
export default {
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
            if (page > 0 && page <= this.totalPages) {
                this.$emit('page-changed', page);
            }
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

.pagination {
    display: inline-block;
}

.pagination p {
    color: black;
    float: left;
    padding: 8px 16px;
    text-decoration: none;
}

.pagination p.active {
    background-color: var(--primary-color);
    color: var(--background-color);
}

.pagination p:hover {
    cursor: pointer;
    background-color: #ddd;
}
</style>