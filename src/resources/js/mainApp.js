import './bootstrap';
import './plugins';
import Vue from 'vue';
import './core/coreApp';
import store from './store/Index'
import './common/Translator'
import './common/Helper/helpers'
import './common/commonComponent'
import './tenant/tenantComponent'
import JsonExcel from "vue-json-excel"

Vue.component("downloadExcel", JsonExcel);
Vue.prototype.$filters_front = [];

import * as VueGoogleMaps from 'vue2-google-maps'
Vue.use(VueGoogleMaps, {
    load: {
        key: 'AIzaSyAaMXI-8UBZ3nCI8qHBMa8JI6Mw0TIM8qY',
        libraries: 'places', // This is required if you use the Autocomplete plugin
        // OR: libraries: 'places,drawing'
        // OR: libraries: 'places,drawing,visualization'
        // (as you require)
    }
});
const app = new Vue({
    store,
    el: '#app',
});
