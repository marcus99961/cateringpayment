import './bootstrap';
// import {createApp} from 'vue'

// import App from './components/app.vue'
// import VueRouter from 'vue-router'



import {createApp} from 'vue'
import * as VueRouter from 'vue-router'

import HomeComponent from './components/app.vue'
import Event from './components/event.vue'
import Payment from './components/payment.vue'
import EventType from './components/event_type.vue'
import Facility from './components/facility.vue'






const routes = [
    {path: '/', component: HomeComponent},
    {path: '/events', component:Event},
    {path: '/payments', component:Payment},
    {path: '/eventtypes', component:EventType},
    {path: '/facilities/:id', component:Facility},
   

]

const router = VueRouter.createRouter({
    history: VueRouter.createWebHistory('/'),
    routes,
    linkActiveClass: "active",
    linkExactActiveClass: "exact-active",
})

const app = createApp({})

window.url = '/'





app.use(router)

app.mount('#app')

