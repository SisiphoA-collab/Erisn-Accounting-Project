<template>
  <div id="app">
    <div class="container-fluid p-0 user-select-none">
      <div class="row">
        <!-- Sidebar -->
          <nav class="col-md-2 font-monospace position-fixed sidebar">
            <div class="fs-4 py-2 px-1">Erisn Accounting
              <hr/>
            </div>
            <a href="#/dashboard" :class="[isActive('/dashboard')]" ><i class="fas fa-home"></i> Dashboard</a>
            <a href="#/accounts" :class="[isActive('/accounts')]"><i class="fas fa-book"></i> Chart of Accounts</a>
            <a href="#/customers" :class="[isActive('/customers')]"><i class="fas fa-users"></i> Customers</a>
            <a href="#/vendors" :class="[isActive('/vendors')]"><i class="fas fa-store"></i> Vendors</a>
            <a href="#/invoices" :class="[isActive('/invoices')]"><i class="fas fa-file-invoice"></i> Invoices</a>
            <a href="#/payments" :class="[isActive('/payments')]"><i class="fas fa-file-invoice"></i> Payments</a>
            <a href="#/expenses" :class="[isActive('/expenses')]"><i class="fas fa-money-bill"></i> Expenses</a>
            <a href="#/stipends" :class="[isActive('/stipends')]"><i class="fas fa-hand-holding-usd"></i> Stipends</a>
            <a href="#/banking" :class="[isActive('/banking')]"><i class="fas fa-bank"></i> Banking</a>
            <a href="#/reports" :class="[isActive('/reports')]"><i class="fas fa-chart-bar"></i> Reports</a>
            <a href="#/settings" :class="[isActive('/settings')]"><i class="fas fa-cog"></i> Settings</a>
          </nav>
        <!-- Main Content -->
        <main class="col-md-10 p-5 offset-2 main-content">
          <div>
            <router-view></router-view>
          </div>
        </main>
      </div>
    </div>
  </div>
</template>
<script setup>
import { useRoute } from 'vue-router';

const route = useRoute()
const isActive = (path) => route.path.startsWith(path)? 'bg-custom text-dark rounded' : '';
</script>
