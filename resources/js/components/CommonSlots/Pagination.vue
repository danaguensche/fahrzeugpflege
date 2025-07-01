<template>
    <div class="pagination-controls">
        <div class="pagination-info">
            {{ (page - 1) * itemsPerPage + 1 }} -
            {{ Math.min(page * itemsPerPage, totalItems) }}
            von {{ totalItems }} Einträgen
        </div>
        <div class="pagination-buttons">
            <v-btn 
                icon 
                variant="plain" 
                :disabled="page <= 1" 
                @click="$emit('update:page', 1)" 
                class="pagination-btn"
            >
                <v-icon>mdi-page-first</v-icon>
            </v-btn>
            <v-btn 
                icon 
                variant="plain" 
                :disabled="page <= 1" 
                @click="$emit('update:page', page - 1)"
                class="pagination-btn"
            >
                <v-icon>mdi-chevron-left</v-icon>
            </v-btn>
            <div class="page-selector">
                Seite
                <v-select 
                    :model-value="page" 
                    :items="pageItems" 
                    variant="outlined" 
                    density="compact"
                    class="page-select" 
                    hide-details 
                    @update:model-value="handlePageSelect"
                ></v-select>
                von {{ totalPages }}
            </div>
            <v-btn 
                icon 
                variant="plain" 
                :disabled="page >= totalPages" 
                @click="$emit('update:page', page + 1)"
                class="pagination-btn"
            >
                <v-icon>mdi-chevron-right</v-icon>
            </v-btn>
            <v-btn 
                icon 
                variant="plain" 
                :disabled="page >= totalPages" 
                @click="$emit('update:page', totalPages)"
                class="pagination-btn"
            >
                <v-icon>mdi-page-last</v-icon>
            </v-btn>
        </div>
        <div class="items-per-page">
            <span>Einträge pro Seite:</span>
            <v-select 
                :model-value="itemsPerPage" 
                :items="itemsPerPageOptions" 
                variant="outlined" 
                density="compact"
                class="items-select" 
                hide-details 
                @update:model-value="handleItemsPerPageSelect"
            ></v-select>
        </div>
    </div>
</template>

<script>
export default {
    name: "Pagination",
    props: {
        page: {
            type: Number,
            required: true
        },
        itemsPerPage: {
            type: Number,
            required: true
        },
        totalItems: {
            type: Number,
            required: true
        },
        itemsPerPageOptions: {
            type: Array,
            default: () => [10, 20, 50, 100]
        }
    },
    
    computed: {
        totalPages() {
            return Math.ceil(this.totalItems / this.itemsPerPage) || 1;
        },
        
        pageItems() {
            const pages = [];
            for (let i = 1; i <= this.totalPages; i++) {
                pages.push(i);
            }
            return pages;
        }
    },
    
    methods: {
        handlePageSelect(selectedPage) {
            const pageNumber = parseInt(selectedPage);
            if (pageNumber && pageNumber !== this.page) {
                this.$emit('update:page', pageNumber);
            }
        },
        
        handleItemsPerPageSelect(selectedItemsPerPage) {
            const itemsPerPage = parseInt(selectedItemsPerPage);
            if (itemsPerPage && itemsPerPage !== this.itemsPerPage) {
                this.$emit('update:itemsPerPage', itemsPerPage);
            }
        }
    },
    
    emits: ['update:page', 'update:itemsPerPage']
}
</script>

<style scoped>
.pagination-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background-color: #f5f7fa;
    border-top: 1px solid #edf2f7;
}

.pagination-buttons {
    display: flex;
    align-items: center;
    gap: 8px;
}

.pagination-btn {
    min-width: 36px;
    width: 36px;
    height: 36px;
}

.pagination-info {
    font-size: 0.9rem;
    color: #666;
}

.page-selector {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
}

.page-select {
    width: 80px;
}

.items-per-page {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
}

.items-select {
    width: 80px;
}

@media (max-width: 960px) {
    .pagination-controls {
        flex-direction: column;
        gap: 12px;
    }
}
</style>