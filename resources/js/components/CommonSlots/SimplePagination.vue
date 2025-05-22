<template>
    <div class="simple-pagination">
      <v-pagination
        v-model="currentPage"
        :length="totalPages"
        :total-visible="5"
        @input="onPageChange"
        next-icon="mdi-chevron-right"
        prev-icon="mdi-chevron-left"
        color="grey darken-1"
        class="simple-pagination__content py-1"
        :class="{ 'simple-pagination--condensed': condensed }"
        dense
      ></v-pagination>
    </div>
  </template>
  
  <script>
  export default {
    name: "SimplePagination",
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
      condensed: {
        type: Boolean,
        default: false
      }
    },
    data() {
      return {
        currentPage: this.page
      }
    },
    computed: {
      totalPages() {
        return Math.ceil(this.totalItems / this.itemsPerPage)
      }
    },
    watch: {
      page(newValue) {
        this.currentPage = newValue
      }
    },
    methods: {
      onPageChange(value) {
        this.$emit('update:page', value)
      }
    }
  }
  </script>
  
  <style scoped>
  .simple-pagination {
    display: inline-flex;
    justify-content: center;
  }
  
  .simple-pagination__content :deep(.v-pagination__item) {
    height: 28px;
    min-width: 28px;
    width: 28px;
    font-size: 0.8rem;
  }
  
  .simple-pagination__content :deep(.v-pagination__navigation) {
    height: 28px;
    width: 28px;
    margin: 0 4px;
  }
  
  .simple-pagination--condensed :deep(.v-pagination__item) {
    margin: 0 2px;
  }
  
  .simple-pagination--condensed :deep(.v-pagination__navigation) {
    margin: 0 2px;
  }
  
  /* Verstecke Schatten und reduziere Hervorhebung */
  .simple-pagination__content :deep(.v-pagination__item),
  .simple-pagination__content :deep(.v-pagination__navigation) {
    box-shadow: none !important;
    background-color: transparent;
  }
  
  .simple-pagination__content :deep(.v-pagination__item--active) {
    color: white !important;
  }
  </style>