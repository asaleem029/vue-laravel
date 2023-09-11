<template>
    <section class="vh-100">
        <div class="container-fluid h-custom">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                    <form @submit.prevent="userSignup" class="login-form">

                        <!-- Name input -->
                        <div class="form-outline mb-4">
                            <label class="form-label" for="name">Name</label>
                            <input type="text" id="name" v-model="userData.name" class="form-control form-control-lg"
                                placeholder="Enter Name" />
                        </div>

                        <!-- Email input -->
                        <div class="form-outline mb-4">
                            <label class="form-label" for="email">Email address</label>
                            <input type="email" id="email" v-model="userData.email" class="form-control form-control-lg"
                                placeholder="Enter a valid email address" />
                        </div>

                        <!-- Password input -->
                        <div class="form-outline mb-3">
                            <label class="form-label" for="password">Password</label>
                            <input type="password" id="password" v-model="userData.password"
                                class="form-control form-control-lg" placeholder="Enter password" />
                        </div>

                        <!-- Confirm Password input -->
                        <div class="form-outline mb-3">
                            <label class="form-label" for="c_password">Password</label>
                            <input type="password" id="c_password" v-model="userData.c_password"
                                class="form-control form-control-lg" placeholder="Enter Confirm password" />
                        </div>

                        <div class="text-center text-lg-start mt-4 pt-2">
                            <button type="submit" class="btn btn-primary btn-lg" :disabled="isSubmitting"
                                style="padding-left: 2.5rem; padding-right: 2.5rem;">Register</button>
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
            userData: {
                name: "",
                email: "",
                password: "",
                c_password: "",
            },
            isSubmitting: false
        };
    },

    methods: {
        // Sign Up new user
        userSignup() {
            this.isSubmitting = true;
            // Check password and confirm password are same
            if (this.userData.password != this.userData.c_password) {
                alert("Password and Confirm Password sould be same");
            } else {
                // Call post api to create new user
                axios.post("http://127.0.0.1:8000/api/register", this.userData, {
                    headers: {
                        "content-type": "text/json",
                    },
                }).then(response => {
                    // Check status code
                    if (response.status === 200 && response.data.otp == true) {
                        // Redirect to Verify OTP page
                        var userEmail = response.data.user.email
                        this.$router.push({
                            name: "VerifyOTP",
                            params: { data: userEmail }
                        });

                        this.isSubmitting = false;
                    }
                }).catch(error => {
                    console.log(error);
                    this.isSubmitting = false;
                });
            }
        },
    },
};
</script>