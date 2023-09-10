<template>
    <HeaderComponent />
    <div>
        <h3>Add New Todo</h3>
        <form @submit.prevent="newTodo">

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

            <div class="text-center text-lg-start mt-4 pt-2">
                <button type="submit" class="btn btn-primary btn-lg"
                    style="padding-left: 2.5rem; padding-right: 2.5rem;">Save</button>
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
                title: "",
                description: "",
            }
        };
    },

    methods: {
        // Save New Todo
        newTodo() {
            // Call post api to create new todo
            const auth_token = localStorage.getItem('token')
            axios.post("http://127.0.0.1:8000/api/todo", this.todoData, {
                headers: {
                    "content-type": "text/json",
                    'Authorization': `Bearer ${auth_token}`
                },
            }).then(response => {
                // Check status code
                if (response.status === 200) {
                    // Redirect to Todo List 
                    alert(response.data.message)
                    this.$router.push("todos");
                }
            }).catch(error => {
                console.log(error);
            });
        },
    },
};
</script>