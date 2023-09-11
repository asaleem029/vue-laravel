<template>
    <section class="vh-100">
        <div class="container-fluid h-custom">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                    <form @submit.prevent="verifyOtp" class="login-form">

                        <!-- ReadOnly Email input -->
                        <div class="form-outline mb-4">
                            <label class="form-label" for="email">User Email</label>
                            <input type="email" v-model="otpData.email" id="email" class="form-control form-control-lg"
                                readonly />
                        </div>

                        <!-- OTP input -->
                        <div class="form-outline mb-4">
                            <label class="form-label" for="otp">Enter OTP</label>
                            <input type="number" v-model="otpData.otp" id="otp" class="form-control form-control-lg" />
                        </div>

                        <div class="text-center text-lg-start mt-4 pt-2">
                            <button type="submit" class="btn btn-primary btn-lg" :disabled="isSubmitting"
                                style="padding-left: 2.5rem; padding-right: 2.5rem;">Verify</button>
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
            otpData: {
                email: this.$route.params.data ? this.$route.params.data : "",
                otp: "",
            },
            isSubmitting: false
        };
    },
    methods: {
        verifyOtp() {
            this.isSubmitting = true;
            // Call API to verify OTP 
            axios.post("http://127.0.0.1:8000/api/verifyOtp", this.otpData, {
                headers: {
                    "content-type": "text/json",
                },
            }).then(response => {
                // Check status
                if (response.status === 200) {
                    // Save access token in local storage
                    const token = response.data.access_token;
                    localStorage.setItem('token', token);
                    // Redirect to todos list page
                    this.$router.push('todos');
                    this.isSubmitting = false;
                }
            }).catch(error => {
                alert(error);
                this.isSubmitting = false;
            });
        },
    },
};
</script>