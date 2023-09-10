<template>
    <section class="vh-100">
        <div class="container-fluid h-custom">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                    <form @submit.prevent="userLogin" class="login-form">
                        <!-- Email input -->
                        <div class="form-outline mb-4">
                            <label class="form-label" for="email">Email address</label>
                            <input type="email" id="email" v-model="loginData.email" class="form-control form-control-lg"
                                placeholder="Enter a valid email address" />
                        </div>

                        <!-- Password input -->
                        <div class="form-outline mb-3">
                            <label class="form-label" for="password">Password</label>
                            <input type="password" id="password" v-model="loginData.password"
                                class="form-control form-control-lg" placeholder="Enter password" />
                        </div>

                        <div class="text-center text-lg-start mt-4 pt-2">
                            <button type="submit" class="btn btn-primary btn-lg"
                                style="padding-left: 2.5rem; padding-right: 2.5rem;">Login</button>
                            <p class="small fw-bold mt-2 pt-1 mb-0">Don't have an account?
                                <router-link to="/signup" custom v-slot="{ navigate }">
                                    <a class="link-danger" @click="navigate" role="link">
                                        Register
                                    </a>
                                </router-link>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</template>
 
<script>
import axios from 'axios';

export default {
    data() {
        return {
            loginData: {
                email: "",
                password: "",
            }
        };
    },
    methods: {
        // Log in with the email and password
        userLogin() {
            axios.post("http://127.0.0.1:8000/api/login", this.loginData, {
                headers: {
                    "content-type": "text/json",
                },
            }).then(response => {
                // Check status
                if (response.status === 200) {
                    // Save access token in local storage
                    const token = response.data.access_token;
                    localStorage.setItem('token', token);
                    this.$router.push('todos');
                }
            }).catch(error => {
                console.log(error)
                if (error.response.data.error && error.response.data.error == "Please Verify Email") {
                    var userEmail = this.loginData.email
                    this.$router.push({
                        name: "VerifyOTP",
                        params: { data: userEmail }
                    });
                } else {
                    alert(error);
                }
            });
        },
    },
};
</script>

<style>
.login-form {
    justify-content: center;
    align-items: center;
    position: absolute;
    width: 400px;
    z-index: 1000;
    padding: 40px;
    border-radius: 4px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 9);
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}
</style>