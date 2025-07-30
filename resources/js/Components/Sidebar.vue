<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

defineProps(['collapsed'])

const page = usePage();

</script>

<template>
  <nav
    :class="[
      'col-md-2 font-monospace position-fixed top-0 start-0 sidebar vh-10 p-1 overflow-hidden transition-all',
      collapsed ? 'sidebar-collapsed' : 'sidebar-expanded',
    ]"
  >
    <div class="fs-4 py-3 px-2">
      <span class="">Erisn Accounting</span>
      <hr />
    </div>

    <Link
      :href="route('dashboard')"
      class="d-flex align-items-center gap-2 px-2 py-2 rounded transition bg-opacity-50"
      :class="{
        'bg-secondary text-white': route().current('dashboard'),
        'hover:bg-secondary hover:text-white': true,
      }"
    >
      <i class="fas fa-home" title="Dashboard"></i>
      <span v-show="!collapsed">Dashboard</span>
    </Link>

    <Link :href="route('chartofaccount')" class="nav-item" :class="linkClass('chartofaccount')">
      <i class="fas fa-book" title="ChartOfAccount"></i>
      <span v-show="!collapsed">Chart of Accounts</span>
    </Link>

    <Link :href="route('customers')" class="nav-item" :class="linkClass('customers')">
      <i class="fas fa-user" title="Customers"></i>
      <span v-show="!collapsed">Customers</span>
    </Link>

    <Link :href="route('vendors')" class="nav-item" :class="linkClass('vendors')">
      <i class="fas fa-store" title="Vendors"></i>
      <span v-show="!collapsed">Vendors</span>
    </Link>

    <Link :href="route('invoices')" class="nav-item" :class="linkClass('invoices')">
      <i class="fas fa-file-invoice" title="Invoices"></i>
      <span v-show="!collapsed">Invoices</span>
    </Link>

    <Link :href="route('payments')" class="nav-item" :class="linkClass('payments')">
      <i class="fas fa-dollar-sign" title="Payments"></i>
      <span v-show="!collapsed">Payments</span>
    </Link>

    <Link :href="route('expenses')" class="nav-item" :class="linkClass('expenses')">
      <i class="fas fa-money-bill" title="Expenses"></i>
      <span v-show="!collapsed">Expenses</span>
    </Link>

    <Link :href="route('stipends')" class="nav-item" :class="linkClass('stipends')">
      <i class="fas fa-hand-holding-usd" title="Stipends"></i>
      <span v-show="!collapsed">Stipends</span>
    </Link>

    <Link :href="route('bank')" class="nav-item" :class="linkClass('bank')">
      <i class="fas fa-bank" title="Bank"></i>
      <span v-show="!collapsed">Bank</span>
    </Link>

    <Link :href="route('reports')" class="nav-item" :class="linkClass('reports')">
      <i class="fas fa-chart-bar" title="Reports"></i>
      <span v-show="!collapsed">Reports</span>
    </Link>

    <Link :href="route('settings')" class="nav-item" :class="linkClass('settings')">
      <i class="fas fa-book" title="Settings"></i>
      <span v-show="!collapsed">Settings</span>
    </Link>
  </nav>
</template>

<script>
function linkClass(routeName) {
  return {
    'bg-secondary text-white': route().current(routeName),
    'hover:bg-secondary hover:text-white': true,
    'd-flex align-items-center gap-2 px-2 py-2 rounded transition bg-opacity-50': true,
  };
}
</script>

<style scoped>
.sidebar-expanded {
  width: 250px;
  transition: width 0.3s;
}
.sidebar-collapsed {
  width: 65px;
  transition: width 0.3s;
}
.sidebar-collapsed span {
  display: none;
}
</style>
