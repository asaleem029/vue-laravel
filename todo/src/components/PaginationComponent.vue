<template>
    <nav aria-label="Page navigation example">
        <ul class="pagination">
            <li class="page-item" v-for="page in totalPages" :key="page">
                <a class="page-link bg-secondary" @click="changePage(page)">{{ page }}</a>
            </li>
        </ul>
    </nav>
</template>
  
<script>
export default {
    props: {
        currentPage: Number, // Current page number
        totalItems: Number,  // Total number of items
        itemsPerPage: Number // Number of items per page
    },
    computed: {
        totalPages() {
            return Math.ceil(this.totalItems / this.itemsPerPage);
        }
    },
    methods: {
        changePage(page) {
            // Emit an event to notify the parent component of the page change
            this.$emit('page-change', page);
        }
    }
}
</script>
  
<style>
.pagination {
    display: inline-block;
}

.pagination a {
    color: black;
    float: left;
    padding: 8px 16px;
    text-decoration: none;
}
</style>