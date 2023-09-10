<template>
    <header>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white">
            <div class="container-fluid">
                <div class="collapse navbar-collapse">
                    <h3>TODO APP</h3>
                    <button class="btn btn-danger logout-button" @click="userLogout">
                        Log-Out
                    </button>
                </div>
            </div>
        </nav>
        <!-- Navbar -->
    </header>
</template>
 
<script>
import axios from 'axios';

export default {
    methods: {
        userLogout() {
            const auth_token = localStorage.getItem('token')
            axios.post("http://127.0.0.1:8000/api/logout", {
                headers: {
                    'Authorization': `Bearer ${auth_token}`
                },
            }).then(response => {
                // Check status
                if (response.status === 200) {
                    // Remove access token from local storage
                    localStorage.removeItem('token');
                    this.$router.push('/login');
                }
            }).catch(error => {
                console.log(error)
                alert(error);
            });
        },
    },
};
</script>

<style>
.logout-button {
    margin-right: 0;
    margin-left: auto;
    display: block;
}
</style>