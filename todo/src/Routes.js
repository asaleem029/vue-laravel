import { createWebHistory, createRouter } from "vue-router";
import LoginComponent from './components/LoginComponent.vue';
import SignupComponent from './components/SignupComponent.vue';
import VerifyOTPComponent from './components/VerifyOTPComponent.vue';
import TodoComponent from './components/Todo/TodoComponent.vue';
import NewTodoComponent from './components/Todo/NewTodoComponent.vue';
import EditTodoComponent from './components/Todo/EditTodoComponent.vue';

const routes = [
    {
        path: "/",
        redirect: { path: "/login" },
    },
    {
        name: 'Login',
        path: "/login",
        component: LoginComponent,
        meta: {
            requiresAuth: false,
        },
    },
    {
        name: 'SignUp',
        path: "/signup",
        component: SignupComponent,
        meta: {
            requiresAuth: false,
        },
    },
    {
        name: 'TodoList',
        path: "/todos",
        component: TodoComponent,
        meta: {
            requiresAuth: true,
        },
    },
    {
        name: 'New Todo',
        path: "/todo",
        component: NewTodoComponent,
        meta: {
            requiresAuth: true,
        },
    },
    {
        name: 'Edit Todo',
        path: "/todo/:id",
        component: EditTodoComponent,
        meta: {
            requiresAuth: true,
        },
    },
    {
        name: 'VerifyOTP',
        path: "/verify-otp",
        component: VerifyOTPComponent,
        meta: {
            requiresAuth: false,
        },
        props: true
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');

    if (to.meta.requiresAuth) {
        if (!token || token == null || token == 'undefined' || token == undefined) {
            // User is not authenticated, redirect to login
            next('/login');
        } else {
            // User is authenticated, proceed to the route
            next();
        }
    } else {
        // Non-protected route, allow access
        if (token) {
            // User is authenticated, proceed to the route
            next('todo');
        } else {
            // User is not authenticated, redirect to login
            next();
        }
    }
});

export default router;