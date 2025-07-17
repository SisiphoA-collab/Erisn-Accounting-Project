<template>
    <div id="app">
        <div class="p-0 user-select-none">
            <div class="">
                <!-- Sidebar -->
                <nav
                    :class="['col-md-2 font-monospace position-fixed top-0 start-0 sidebar vh-100 p-1 overflow-hidden transition-all', collapsed ? 'sidebar-collapsed' : 'sidebar-expanded']">
                    <div class="fs-4 py-2 px-1">
                        <!-- <i class="fas fa-calculator"></i> -->
                        <!-- <span class="d-none d-md-inline">Erisn Accounting</span> -->
                        <span class="">Erisn Accounting</span>
                        <hr />
                    </div>

                    <a href="#/dashboard" :class="[isActive('/dashboard'), 'px-3']">
                        <i class="fas fa-home" data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard"></i>
                        <span class=""> Dashboard </span></a>
                    <a href="#/accounts" :class="[isActive('/accounts'), 'px-3']">
                        <i class="fas fa-book" data-bs-toggle="tooltip" data-bs-placement="right"
                            title="Chart of accounts"></i><span class=""> Chart of
                            Accounts</span> </a>
                    <a href="#/customers" :class="[isActive('/customers'), 'px-3']">
                        <i class="fas fa-users" data-bs-toggle="tooltip" data-bs-placement="right"
                            title="Customers"></i>
                        <span class=""> Customers</span></a>
                    <a href="#/vendors" :class="[isActive('/vendors'), 'px-3']">
                        <i class="fas fa-store" data-bs-toggle="tooltip" data-bs-placement="right"
                            title="Vendors"></i><span class=""> Vendors</span></a>
                    <a href="#/invoices" :class="[isActive('/invoices'), 'px-3']">
                        <i class="fas fa-file-invoice" data-bs-toggle="tooltip" data-bs-placement="right"
                            title="Invoices"></i>
                        <span class=""> Invoices</span></a>
                    <a href="#/payments" :class="[isActive('/payments'), 'px-3']">
                        <i class="fas fa-dollar-sign" data-bs-toggle="tooltip" data-bs-placement="right"
                            title="Payments"></i>
                        <span class=""> Payments</span></a>
                    <a href="#/expenses" :class="[isActive('/expenses'), 'px-3']">
                        <i class="fas fa-money-bill" data-bs-toggle="tooltip" data-bs-placement="right"
                            title="Expenses"></i>
                        <span class=""> Expenses</span></a>
                    <a href="#/stipends" :class="[isActive('/stipends'), 'px-3']">
                        <i class="fas fa-hand-holding-usd" data-bs-toggle="tooltip" data-bs-placement="right"
                            title="Stipends"></i>
                        <span class=""> Stipends</span></a>
                    <a href="#/banking" :class="[isActive('/banking'), 'px-3']">
                        <i class="fas fa-bank" data-bs-toggle="tooltip" data-bs-placement="right"
                            title="Banking"></i><span class=""> Banking</span></a>
                    <a href="#/reports" :class="[isActive('/reports'), 'px-3']">
                        <i class="fas fa-chart-bar" data-bs-toggle="tooltip" data-bs-placement="right"
                            title="Reports"></i>
                        <span class=""> Reports</span></a>
                    <a href="#/settings" :class="[isActive('/settings'), 'px-3']">
                        <i class="fas fa-cog" data-bs-toggle="tooltip" data-bs-placement="right"
                            title="Settings"></i><span class=""> Settings</span></a>
                </nav>

                <!-- navbar  -->
                <div class="flex-grow-1 py-3 bg-nav d-flex fixed-top"
                    :style="{ marginLeft: collapsed ? '65px' : '250px', transition: 'margin-left 0.3s' }">

                    <div class="d-flex text-center align-items-center text-white">
                        <button @click.prevent="collapsed = !collapsed">
                            <i class="fas fa-bars"></i>
                            <span class="ms-1">
                                {{ route.meta.title || 'Dashboard' }}
                            </span>
                        </button>
                    </div>

                    <div class="col text-end text-white px-5">

                        <!-- signed in users  -->
                        <section class="nav-item dropdown dropdown small d-flex justify-content-end">
                            <div class="dropdown">
                                <button
                                    class="btn btn-outline-light border-none dropdown-toggle d-flex align-items-center gap-2 text-start"
                                    id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span
                                        class="rounded-circle flex-grow-1 bg-secondary text-white d-flex justify-content-center align-items-center"
                                        style="width: 32px; height: 32px;">
                                        <!-- {{ getInitials(auth.user.name) }} -->
                                       {{ getInitials('Vuyolwethu Dlamini') }}
                                    </span>
                                    <div class="flex-grow-1">
                                        <span id="menuBar" class="fw-medium">
                                            <!-- {{ auth.user.name }} -->
                                            Vuyolwethu Dlamini
                                        </span>
                                        <!-- <span class="text-muted fs-6 fst-italic d-block">{{ auth.user.email }}</span> -->
                                    </div>
                                </button>
                                <ul class="dropdown-menu w-100" aria-labelledby="userDropdown">
                                    <li class="d-flex align-items-center gap-2 px-1 py-2 text-start small">
                                        <span
                                            class="rounded bg-secondary text-white text-uppercase d-flex justify-content-center align-items-center"
                                            style="width: 32px; height: 32px;">
                                            <!-- auth.user.name -->
                                            {{ getInitials('Vuyolwethu Dlamini') }}
                                        </span>
                                        <div class="d-grid flex-grow-1 text-start lh-sm">
                                            <!-- {{ auth.user.name }} -->
                                            <span class="fw-medium text-truncate">Vuyolwethu Dlamini</span>
                                            <!-- {{ auth.user.email }} -->
                                            <span class="text-muted fs-8 fst-italic d-block text-truncate">vuyolwethudlamini@example.com</span>
                                        </div>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider" />
                                    </li>
                                    <li>
                                        <!-- @click="goToSettings" -->
                                        <button class="dropdown-item d-flex align-items-center gap-2">
                                            <i class="fa-solid fa-gear me-2"></i>
                                            Settings
                                        </button>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider" />
                                    </li>
                                    <li>
                                        <!-- logout() -->
                                        <button class="dropdown-item d-flex align-items-center gap-2 text-danger"
                                            @click.prevent="">
                                            <i class="fas fa-sign-out-alt"></i>
                                            Log out
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </section>
                    </div>
                </div>
                <!-- Main Content -->
                <main :style="{ marginLeft: collapsed ? '65px' : '250px', transition: 'margin-left 0.3s' }"
                    class="flex-grow-1 d-flex justify-content-center p-3 mt-16 pt-8">
                    <div class="w-100 px-5">
                        <router-view></router-view>
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>
<script setup>
import { useRoute } from 'vue-router';
import { useHead } from '@vueuse/head';
import { ref, computed } from 'vue';

const collapsed = ref(true)
const route = useRoute()
// const iconSize = ref(16);
const isActive = (path) => route.path.startsWith(path) ? 'bg-custom text-dark rounded' : '';

const getInitials = (fullName) => {
  const parts = fullName.trim().split(' ');
  const first = parts[0]?.[0] || '';
  const last = parts.length > 1 ? parts[parts.length - 1][0] : '';
  return (first + last).toUpperCase();
}

useHead(computed(() => ({
  title: route.meta.title || 'Erisn Accounting',
  meta: [
    { name: 'description', content: route.meta.description || 'Erisn Accounting Application' },
    { name: 'viewport', content: 'width=device-width, initial-scale=1.0' },
  ],
})))
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
