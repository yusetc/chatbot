import Vue from 'vue'
import App from './App.vue'
import axios from 'axios'
import vmodal from 'vue-js-modal'
import {BootstrapVue, BootstrapVueIcons} from 'bootstrap-vue'
import "bootstrap/dist/css/bootstrap.min.css"
import "bootstrap-vue/dist/bootstrap-vue.css"

Vue.config.productionTip = false
Vue.use(BootstrapVue)
Vue.use(BootstrapVueIcons)
Vue.use(vmodal)
Vue.prototype.$axios = axios

new Vue({
  render: h => h(App),
}).$mount('#app')

