<!-- <template>
  <div class="d-flex">
    <Sidebar />
    <div class="flex-grow-1 p-3">
      <router-view />
    </div>
  </div>
</template> -->

<!-- <script>
import Sidebar from '../components/Sidebar.vue';

export default {
  components: { Sidebar }
};
</script> -->

<template>
    <div id="app">
    <div class="container-fluid">
      <div class="row">
        <!-- Sidebar -->
<<<<<<< Updated upstream
        <nav class="col-md-2 d-none d-md-block sidebar">
          <div class="logo">Erisn Accounting</div>
          <a href="#/dashboard"><i class="fas fa-home"></i> Dashboard</a>
          <a href="#/accounts"><i class="fas fa-book"></i> Chart of Accounts</a>
          <a href="#/customers"><i class="fas fa-users"></i> Customers</a>
          <a href="#/vendors"><i class="fas fa-store"></i> Vendors</a>
          <a href="#/invoices"><i class="fas fa-file-invoice"></i> Invoices</a>
          <a href="#/payments"><i class="fas fa-file-invoice"></i> Payments</a>
          <a href="#/expenses"><i class="fas fa-money-bill"></i> Expenses</a>
          <a href="#/stipends"><i class="fas fa-hand-holding-usd"></i> Stipends</a>
          <a href="#/banking"><i class="fas fa-bank"></i> Banking</a>
          <a href="#/reports"><i class="fas fa-chart-bar"></i> Reports</a>
          <a href="#/settings"><i class="fas fa-cog"></i> Settings</a>
        </nav>
=======
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
>>>>>>> Stashed changes
        <!-- Main Content -->
        <main class="col-md-10 main-content">
          <router-view></router-view>
        </main>
      </div>
    </div>
  </div>
</template>



