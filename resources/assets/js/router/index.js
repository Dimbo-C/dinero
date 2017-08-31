import Vue from 'vue';
import Router from 'vue-router';

import NotFound from './../views/NotFound.vue'
import Dashboard from './../views/Dashboard.vue'
import AdminsOwn from './../views/admins/own/Own.vue'
import AdminsOwnMetrics from './../views/admins/own/Metrics.vue'
import AdminsRent from './../views/admins/rent/Rent.vue'
import Proxies from './../views/proxies/Proxies.vue'
import ProxiesSystem from './../views/proxies/ProxiesSystem.vue'
import ProxiesAdmin from './../views/proxies/ProxiesAdmin.vue'
import Finance from './../views/finance/Finance.vue'
import FinanceRent from './../views/finance/FinanceRent.vue'
import FinanceBitcoin from './../views/finance/FinanceBitcoin.vue'
import FinanceBitcoinHistory from './../views/finance/FinanceBitcoinHistory.vue'
import FinanceQiwiWallet from './../views/finance/FinanceQiwiWallet.vue'
import AddQiwiWallet from './../views/finance/qiwi/AddQiwiWallet.vue'
import QiwiWalletHistory from './../views/finance/qiwi/QiwiWalletHistory.vue'
import QiwiWalletSettings from './../views/finance/qiwi/QiwiWalletSettings.vue'
import FinanceQiwiDashboard from './../views/finance/FinanceQiwiDashboard.vue'

Vue.use(Router);

const router = new Router({
  mode: 'history',
  linkActiveClass: 'active',
  routes: [
    { path: '*', component: NotFound },
    { path: '/dashboard', component: Dashboard },
    { path: '/admins/own', component: AdminsOwn },
    { path: '/admins/own/metrics', component: AdminsOwnMetrics },
    { path: '/admins/rent', component: AdminsRent },
    { path: '/proxies', component: Proxies },
    { path: '/proxies/system', component: ProxiesSystem },
    { path: '/proxies/admin', component: ProxiesAdmin },
    { path: '/finance', component: Finance },
    { path: '/finance/rent', component: FinanceRent },
    { path: '/finance/bitcoin', component: FinanceBitcoin },
    { path: '/finance/qiwi', component: FinanceQiwiWallet },
    { path: '/finance/qiwi/add-wallet', component: AddQiwiWallet },
    { path: '/finance/qiwi/:wallet/history', component: QiwiWalletHistory },
    { path: '/finance/qiwi/:wallet/settings', component: QiwiWalletSettings },
    { path: '/finance/qiwi/dashboard', component: FinanceQiwiDashboard },
    { path: '/finance/bitcoin/history', component: FinanceBitcoinHistory },
  ]
});

export default router;