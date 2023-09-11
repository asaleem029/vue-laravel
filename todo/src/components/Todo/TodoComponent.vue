<template>
    <!-- Include Header Component -->
    <HeaderComponent />
    <div class="m-3">
        <h3>Todo List</h3>

        <div class="row m-3">
            <div class="col-sm">
                <!-- ADD NEW TODO BUTTON -->
                <router-link to="/todo" custom v-slot="{ navigate }">
                    <button class="btn btn-primary" @click="navigate" role="link">
                        Add New Todo
                    </button>
                </router-link>
            </div>

            <div class="col-sm">
                <!-- SEARCH FILTER -->
                <input v-model="searchQuery" placeholder="Search..."
                    class="form-control form-control-sm w-50  search-field" />

            </div>
        </div>

        <!-- TODO TABLE -->
        <table id="tableComponent" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <!-- loop through each value of the fields to get the table header -->
                    <th> Id </th>
                    <th> Title </th>
                    <th> Description </th>
                    <th> Edit </th>
                    <th> Delete </th>
                </tr>
            </thead>
            <tbody>
                <!-- Loop through the list get the each todo data -->
                <tr v-for="todo in filteredTodosList" :key="todo.id">
                    <td>
                        {{ todo.id }}
                    </td>
                    <td>
                        {{ todo.title }}
                    </td>
                    <td>
                        {{ todo.description }}
                    </td>
                    <td>
                        <router-link :to="{ name: 'Edit Todo', params: { id: todo.id } }" class="btn btn-sm btn-primary"
                            variant="primary">Edit</router-link>
                    </td>
                    <td>
                        <button class="btn btn-danger" :disabled="deleted == true" @click="deleteTodo(todo.id)">
                            Delete
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <PaginationComponent :currentPage="currentPage" :totalItems="totalItems" :itemsPerPage="itemsPerPage"
        @page-change="handlePageChange"></PaginationComponent>
</template>
 
<script>
import axios from 'axios';
import HeaderComponent from '../HeaderComponent.vue';
import PaginationComponent from '../PaginationComponent.vue';

export default {
    components: {
        HeaderComponent,
        PaginationComponent
    },
    data() {
        return {
            searchQuery: "",
            currentPage: 1,
            itemsPerPage: 10,
            todosList: [],
            deleted: false
        };
    },
    computed: {
        totalItems() {
            return this.todosList.length;
        },
        filteredTodosList() {
            if (this.searchQuery) {
                return this.todosList.filter(todo => {
                    return todo.title.toLowerCase().includes(this.searchQuery.toLowerCase())
                })
            } else {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                const end = start + this.itemsPerPage;
                return this.todosList.slice(start, end);
            }
        }
    },
    methods: {
        handlePageChange(page) {
            this.currentPage = page;
        },
        getTodos() {
            // Call API to get todo list
            const auth_token = localStorage.getItem('token')
            axios.get("http://127.0.0.1:8000/api/todos", {
                headers: {
                    "content-type": "text/json",
                    'Authorization': `Bearer ${auth_token}`
                },
            }).then(response => {
                // Check status code
                if (response.status === 200) {
                    this.todosList = response.data.todos;
                }
            }).catch(error => {
                console.log(error);
            });
        },
        deleteTodo(id) {
            this.deleted = true;
            // Call api to delete todo
            const auth_token = localStorage.getItem('token')
            axios.delete("http://127.0.0.1:8000/api/todo/" + id, {
                headers: {
                    "content-type": "text/json",
                    'Authorization': `Bearer ${auth_token}`
                },
            }).then(response => {
                // Check status code
                if (response.status === 200) {
                    alert(response.data.message)
                    this.deleted = false
                    this.getTodos();
                }
            }).catch(error => {
                console.log(error);
            });
        }
    },
    beforeMount() {
        this.getTodos();
    },
};
</script>

<style>
.search-field {
    margin-right: 0;
    margin-left: auto;
    display: block;
}
</style>