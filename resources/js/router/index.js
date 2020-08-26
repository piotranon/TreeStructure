import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

// import Home from '.././views/Home'
import Admin from '.././views/Admin'
import Login from '.././views/Login'
import Register from '.././views/Register'
import NotFound from '.././views/NotFound'
import Logout from '.././views/Logout'

const routes = [{
        path: '/',
        name: 'Home',
        redirect: 'Login'
    },
    {
        path: '/admin',
        name: 'Admin',
        meta: {
            auth: true
        },
        component: Admin
    },
    {
        path: '/login',
        name: 'Login',
        component: Login
    },
    {
        path: '/register',
        name: 'Register',
        component: Register
    },
    {
        path: '/logout',
        name: 'Logout',
        meta: {
            auth: true
        },
        component: Logout
    },
    {
        path: '/404',
        name: 'NotFound',
        component: NotFound
    },
    {
        path: '*',
        redirect: '/404'
    }
]

const router = new VueRouter({
    mode: 'history',
    base: '',
    routes
})

//check is user is logged in
router.beforeEach((to, from, next) => {
    const loggedIn = sessionStorage.getItem('user')
    if (to.matched.some(record => record.meta.auth) && !loggedIn) {
        next('/login')
        return
    }
    next()
})


export default router
