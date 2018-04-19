require('../../../../../../resources/assets/js/bootstrap');
require('../../../../../../vendor/acciocms/core/src/resources/assets/js/base-components');

import Vue from 'vue'
import VueRouter from 'vue-router';
import { store } from '../../../../../../vendor/acciocms/core/src/resources/assets/js/store'
import Base from '../../views/backend/Base.vue';

Vue.use(VueRouter);

const routes = [
    { path: '/cms/:adminPrefix/:lang/plugins/accio/instant-articles', component: Base },
];

const router = new VueRouter({
    mode: 'history',
    routes: routes
});

const app = new Vue({
    el: '#app',
    router,
    store,
});