import 'bootstrap'
import 'bootstrap/dist/css/bootstrap.min.css'

import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'

Vue.config.productionTip = false

//api requests
// import API from "./api";
// Vue.prototype.$http = API;

import axios from 'axios';

Vue.prototype.$http = axios;

// eslint-disable-next-line
const app = new Vue({
    components: {
        App
    },
    router,
    store,
    created() {
        const userInfo = sessionStorage.getItem('user')
        if (userInfo) {
            const userData = JSON.parse(userInfo)
            this.$store.commit('setUserData', userData)
        }
        axios.interceptors.response.use(
            response => response,
            error => {
                if (error.response.status === 401) {
                    console.log(error.response)
                    this.$store.dispatch('logout')
                }
                return Promise.reject(error)
            }
        )
    },
    render: h => h(App)
}).$mount('#app')
