import Vue from 'vue';
import Sidebar from './sidebar/Sidebar.vue'
import PageHeader from './PageHeader.vue'
import Navbar from './Navbar.vue'
import Loading from './Loading.vue'
import Modal from './Modal.vue'
import QiwiWalletGeneralSettings from "./qiwi-wallet-settings/QiwiWalletGeneralSettings.vue"
import QiwiWalletSecuritySettings from "./qiwi-wallet-settings/QiwiWalletSecuritySettings.vue"

// Layout Components...
Vue.component('page-sidebar', Sidebar);
Vue.component('page-header', PageHeader);
Vue.component('navbar', Navbar);
Vue.component('loading', Loading);
Vue.component('modal', Modal);
Vue.component('qiwi-wallet-general-settings', QiwiWalletGeneralSettings);
Vue.component('qiwi-wallet-security-settings', QiwiWalletSecuritySettings);

// Third Party Components...
Vue.component('masked-input', require('./third-party/maskedinput'));
