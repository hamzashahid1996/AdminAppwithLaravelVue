import Vue from "vue";
import VueRouter from "vue-router";
import LoginComponent from "./components/LoginComponent";
import AdminComponent from "./components/AdminComponent";
import RolesComponent from "./components/RolesComponent";
import SignUpComponent from "./components/SignUpComponent";
import UserComponent from "./components/UserComponent";

Vue.use(VueRouter);

const routes = [
{
    path:  '/',
    redirect: '/login'
},
{
    path:'/login',
    component: LoginComponent,
    name:'Login'
},
{
    path:'/signup',
    component: SignUpComponent,
    name:'Signup'
},
{
    path:'/admin',
    component: AdminComponent,
    name:'Admin',
    children:
    [
    {
        path:'roles',
        component: RolesComponent,
        name:'Roles'
    },
    {
        path:'users',
        component: UserComponent,
        name:'Users'
    }
    ],
    beforeEnter: (to, from, next) => { //Authorize the user before entering admin page
        axios.get('api/verify')
        .then(res => next())
        .catch(err => next('/login'))
    }

},

];
const router = new VueRouter({routes});
//for global navigation guard
router.beforeEach((to ,from , next) =>{
        const token = localStorage.getItem('token') || null;
        window.axios.defaults.headers['Authorization'] = 'Bearer ' + token;
        next();
})

export default router;




    // for locally verify the token for indiviual route path
    // beforeEnter: (to, from , next) => {
    //     if (localStorage.getItem('token')) {
    //         next();
    //     }
    //     else{
    //         next('/login');
    //     }

    // }
