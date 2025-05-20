import { useAuthStore } from "@/store/stores/authStore";
import { createRouter, createWebHashHistory, RouteRecordRaw } from "vue-router";

const routes: RouteRecordRaw[] = [
    {
        path: '/',
        name: 'root',
        redirect: '/dashboardLayout',
        component: () => import("@/VueApp.vue"),
        children: [
            {
                path: '/dashboardLayout',
                name: 'dashboardLayout',
                component: () => import("@/layouts/DashboardLayout.vue"),
                redirect: '/dashboard',
                meta: {
                    pageTitle: "Admin Dashboard",
                    auth: true,
                    allowedUsers: ['Super-Admin', 'Admin', 'User']
                },
                children: [
                    {
                        path: '/dashboard',
                        name: 'dashboard',
                        component: () => import("@/views/dashboard/DashboardView.vue"),
                        meta: {
                            pageTitle: "Dashboard",
                            auth: true,
                            // allowedUsers: ['Super-Admin', 'Admin', 'User'],
                            permissions: ['access detailed signs']
                        }
                    },
                    {
                        path: '/users',
                        name: 'users',
                        component: () => import("@/views/dashboard/UsersView.vue"),
                        meta: {
                            pageTitle: "Users",
                            auth: true,
                            // allowedUsers: ['Super-Admin', 'User'],
                            permissions: ['access users']
                        }
                    },
                    {
                        path: '/test',
                        name: 'test',
                        component: () => import("@/views/TestView.vue"),
                        meta: {
                            pageTitle: "Test",
                            auth: true
                        }
                    },
                    // {
                    //     path: '/map',
                    //     name: 'map',
                    //     component: () => import("@/views/dashboard/MapView.vue"),
                    //     meta: {
                    //         pageTitle: "Map",
                    //         auth: true,
                    //         allowedUsers: ['Super-Admin', 'Admin', 'User']
                    //     }
                    // }
                ]
            },
            {
                path: '/sign-in',
                name: 'signIn',
                component: () => import("@/views/auth/SignInView.vue"),
                meta: {
                    pageTitle: "Sign-In"
                }
            },
            {
                path: '401',
                name: 'unauth',
                component: () => import('@/views/general/401Unauthorized.vue'),
                meta: {
                    pageTitle: "Unauthorized"
                }
            },
            {
                path: '404',
                name: 'notfound',
                component: () => import("@/views/general/404NotFound.vue"),
                meta: {
                    pageTitle: "Not Found"
                }
            }
        ]
    },
    {
        path: '/:pathMatch(.*)*',
        redirect: '/auth/404'
    }
];

const router = createRouter({
    history: createWebHashHistory(),
    routes: routes
});

router.beforeEach((to, from, next) => {

    // Set page title
    if (to.meta.pageTitle) {
        document.title = `${to.meta.pageTitle} | ${import.meta.env.VITE_APP_NAME}`;
    } else {
        document.title = `${import.meta.env.VITE_APP_NAME}`;
    }

    // Stores
    const authStore = useAuthStore();

    // next()
    if (!to.meta?.auth) {
        if (to.fullPath === '/sign-in') {
            if (authStore.isAuthenticated) {
                if (authStore.canUser('access detailed signs'))
                    next('/dashboardLayout');
                else if (authStore.canUser('access users'))
                    next('/users')
                else{
                    authStore.removeAuth()
                    next('/401');
                }
                    
                // if (authStore.user?.type === 'Admin') {
                //     next('/dashboardLayout');
                // } else {
                //     next('/401');
                // }
            } else {
                next();
            }
        } else {
            next();
        }
    } else {
        if (authStore.isAuthenticated) {
            if (to.meta?.permissions) {
                to.meta.permissions.forEach(permission => {
                    if (!authStore.canUser(permission)) {
                        next('/401');
                    }
                })
            }
            // Check
            if (Array.isArray(to.meta?.allowedUsers)) {
                if (authStore.user) {
                    if (to.meta.allowedUsers.includes(authStore.user.type)) {
                        next();
                    } else {
                        next('/401');
                    }
                } else {
                    next();
                }
            } else {
                next();
            }
        } else {
            next("/sign-in");
        }
    }

    document.documentElement.scroll({
        top: 0,
        left: 0,
        behavior: "smooth"
    });
})

// router.beforeEach((to, from, next) => {
//     const authStore = useAuthStore();
//     if(to.meta.auth) {
//         if(authStore.isAuthenticated) {
//             next();
//         } else {
//             next('/sign-in');
//         }
//     } else {
//         if(to.name === 'signIn') {
//             if(authStore.isAuthenticated) {
//                 next('/dashboard');
//             } else {
//                 next();
//             }
//         } else {
//             next('/sign-in');
//         }
//     }
// });

export default router;