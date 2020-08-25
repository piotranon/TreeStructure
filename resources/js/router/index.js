import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

import Home from '.././views/Home'
import About from '.././views/About'
import Login from '.././views/Login'
import Register from '.././views/Register'
import NotFound from '.././views/NotFound'

const routes = [{
        path: '/',
        name: 'Home',
        component: Home
    },
    {
        path: '/about',
        name: 'About',
        meta: {
            auth: true
        },
        component: About
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
        component: Register
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
