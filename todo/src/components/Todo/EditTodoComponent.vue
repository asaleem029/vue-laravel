<template>
    <!-- Include Header Component -->
    <HeaderComponent />
    <div>
        <form class="w-50 ml-3" style="margin-left: 25%;" @submit.prevent="updateTodo(todoData.id)">
            <h3>Edit New Todo</h3>
            <input type="hidden" id="id" v-model="todoData.id" />

            <!-- Title input -->
            <div class="form-outline mb-4">
                <label class="form-label" for="title">Title</label>
                <input type="text" id="title" v-model="todoData.title" class="form-control form-control-lg"
                    placeholder="Enter Title" />
            </div>

            <!-- Description input -->
            <div class="form-outline mb-3">
                <label class="form-label" for="description">Description</label>
                <textarea type="text" id="description" v-model="todoData.description" class="form-control form-control-lg"
                    placeholder="Enter Description">
                </textarea>
            </div>

            <div class="row text-lg-start mt-4 pt-2 ">
                <div class="col-sm">
                    <button type="submit" :disabled="isSubmitting"
                        class="btn btn-primary btn-md float-right">Update</button>
                </div>

                <div class="col-sm">
                    <router-link to="/todos" custom v-slot="{ navigate }">
                        <button class="btn btn-primary btn-md" @click="navigate" role="link">
                            Cancel
                        </button>
                    </router-link>
                </div>
            </div>
        </form>
    </div>
</template>
 
<script>
import axios from 'axios';
import HeaderComponent from '../HeaderComponent.vue';
export default {
    components: {
        HeaderComponent,
    },
    data() {
        return {
            todoData: {
                id: "",
                title: "",
                description: "",
            },
            isSubmitting: false
        };
    },
    methods: {
        getTodo() {
            this.isSubmitting = true;
            const id = this.$route.params.id;
            // Call get api to get todo 
            const auth_token = localStorage.getItem('token')
            axios.get("http://127.0.0.1:8000/api/todo/" + id, {
                headers: {
                    "content-type": "text/json",
                    'Authorization': `Bearer ${auth_token}`
                },
            }).then(response => {
                // Check status code
                if (response.status === 200) {
                    // Set form data
                    this.todoData.id = response.data.todo.id;
                    this.todoData.title = response.data.todo.title;
                    this.todoData.description = response.data.todo.description;
                    this.isSubmitting = false;
                }
            }).catch(error => {
                console.log(error);
                this.isSubmitting = true;
            });
        },

        // Update Todo data
        updateTodo() {
            this.isSubmitting = true;
            // Call put api to update todo data
            const auth_token = localStorage.getItem('token')
            axios.put("http://127.0.0.1:8000/api/todo/" + this.todoData.id, this.todoData, {
                headers: {
                    "content-type": "text/json",
                    'Authorization': `Bearer ${auth_token}`
                },
            }).then(response => {
                // Check status code
                if (response.status === 200) {
                    // Redirect to Todo List 
                    alert(response.data.message)
                    this.$router.push("/todos");
                    this.isSubmitting = false;
                }
            }).catch(error => {
                console.log(error);
                this.isSubmitting = false;
            });
        },
    },
    beforeMount() {
        this.getTodo();
    },
};
</script>