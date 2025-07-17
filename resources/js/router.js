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
import InvoiceDetails from './Components/Invoice.vue';

const routes = [
    {
        path: '/',
        redirect: '/dashboard',
        meta: { title: 'Dashboard | Erisn' }

    },
    {
        path: '/dashboard',
        component: Dashboard,
        name: 'dashboard',
        meta: { title: 'Dashboard' }
    },
    {
        path: '/accounts',
        component: ChartOfAccount,
        name: 'accounts',
        meta: { title: 'Chart of Accounts' }
    },
    {
        path: '/customers',
        component: Customers,
        name: 'customers',
        meta: { title: 'Customers' }
    },
    {
        path: '/vendors',
        component: Vendors,
        name: 'vendors',
        meta: { title: 'Vendors' }
    },
    {
        path: '/invoices',
        component: Invoices,
        name: 'invoices',
        meta: { title: 'Invoices' }
    },
    {
        path: '/payments',
        component: Payments,
        name: 'payments',
        meta: { title: 'Payments' }
    },
    {
        path: '/expenses',
        component: Expenses,
        name: 'expenses',
        meta: { title: 'Expenses' }
    },
    {
        path: '/stipends',
        component: Stipends,
        name: 'stipends',
        meta: { title: 'Stipends' }
    },
    {
        path: '/banking',
        component: Banking,
        name: 'banking',
        meta: { title: 'Banking' }
    },
    {
        path: '/reports',
        component: Reports,
        name: 'reports',
        meta: { title: 'Reports' }
    },
    {
        path: '/settings',
        component: Settings,
        name: 'settings',
        meta: { title: 'Settings' }
    },
    {
        path: '/invoices/:id',
        name: 'invoice.show',
        component: InvoiceDetails,
        props: true,
        meta: { title: 'Invoice' }
    },
    {
        path: '/stipends/:id/upload',
        name: 'stipend.upload',
        component: Stipends,
        props: true,
        meta: { title: 'Stipend' }
    },
];

const router = createRouter({
    history: createWebHashHistory(),
    routes,
});

export default router;
