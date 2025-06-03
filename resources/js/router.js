import { createRouter, createWebHashHistory } from 'vue-router';

// Import pages
import Dashboard from './pages/Dashboard.vue';
import ChartOfAccount from './pages/ChartOfAccount.vue';
import Customers from './pages/Customers.vue';
import Vendors from './pages/Vendors.vue';
import Invoices from './pages/Invoices.vue';
import Payments from './pages/Payments.vue';
import Expenses from './pages/Expenses.vue';
import Stipends from './pages/Stipends.vue';
import Banking from './pages/Banking.vue';
import Reports from './pages/Reports.vue';
import Settings from './pages/Settings.vue';

const routes = [
  { path: '/', redirect: '/dashboard' },
  { path: '/dashboard', component: Dashboard },
  { path: '/chartofaccount', component: ChartOfAccount },
  { path: '/customers', component: Customers },
  { path: '/vendors', component: Vendors },
  { path: '/invoices', component: Invoices },
  { path: '/payments', component: Payments },
  { path: '/expenses', component: Expenses },
  { path: '/stipends', component: Stipends },
  { path: '/banking', component: Banking },
  { path: '/reports', component: Reports },
  { path: '/settings', component: Settings },
];

const router = createRouter({
  history: createWebHashHistory(), 
  routes,
});

export default router;
