<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="py-2">
            <div class="mx-auto max-w-7x sm:px-6">
                 <!-- Account Form -->
    <form @submit.prevent="createAccount" class="row g-3 mb-4">
      <div class="col-md-4">
        <label class="form-label">Company</label>
        <select v-model="form.company_id" class="form-select">
          <option value="" disabled>Select company...</option>
          <option v-for="company in companies" :key="company.id" :value="company.id">
            {{ company.name }}
          </option>
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label">Account Name</label>
        <input v-model="form.name" class="form-control" placeholder="Account Name" />
      </div>

      <div class="col-md-4">
        <label class="form-label">Type</label>
        <select v-model="form.type" class="form-select">
          <option value="asset">Asset</option>
          <option value="liability">Liability</option>
          <option value="equity">Equity</option>
          <option value="income">Income</option>
          <option value="expense">Expense</option>
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label">Category</label>
        <input v-model="form.category" class="form-control" placeholder="Category (optional)" />
      </div>

      <div class="col-md-4">
        <label class="form-label">Balance</label>
        <input v-model="form.balance" type="number" class="form-control" placeholder="Balance" />
      </div>

      <div class="col-md-4 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100">Add Account</button>
      </div>
    </form>

    <!-- Account List -->
    <div class="card">
      <div class="card-header">Account List</div>
      <ul class="list-group list-group-flush">
        <li
          v-for="account in accounts"
          :key="account.id"
          class="list-group-item d-flex justify-content-between align-items-center"
        >
          <div>
            <strong>{{ account.name }}</strong> — {{ account.type }}
            <span v-if="account.category">({{ account.category }})</span>
            <br />
            <small class="text-muted">
              <strong class="font-italic">{{ account.company?.name }}</strong>
            </small>
            <br />
            <small class="text-muted">{{ formatCurrency(account.balance) }}</small>
          </div>
          <button @click="deleteAccount(account.id)" class="btn btn-sm btn-outline-danger">🗑 Delete</button>
        </li>
      </ul>
    </div>

    <!-- Pagination -->
    <Pagination :links="links" :fetchData="fetchAccounts" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import axios from 'axios';
import Pagination from '@/Components/Pagination.vue';

export default {
  name: 'ChartOfAccount',
  components: {
    Pagination,
  },
  data() {
    return {
      companies: [],
      accounts: [],
      links: [],
      currentPage: 1,
      form: {
        company_id: '',
        name: '',
        type: 'asset',
        category: '',
        balance: 0,
      },
    };
  },
  mounted() {
    this.fetchCompanies();
    this.fetchAccounts();
  },
  methods: {
    async fetchCompanies() {
      try {
        const res = await axios.get('/api/companies');
        this.companies = res.data;
        if (this.companies.length && !this.form.company_id) {
          this.form.company_id = this.companies[0].id;
        }
      } catch (error) {
        console.error('Error loading companies', error);
      }
    },
    async fetchAccounts(page = 1) {
      try {
        const res = await axios.get(`/api/accounts?page=${page}`);
        this.accounts = res.data.data;
        this.links = res.data.links;
        this.currentPage = res.data.current_page;
      } catch (error) {
        console.error('Error loading accounts', error);
      }
    },
    async createAccount() {
      try {
        await axios.post('/api/accounts', this.form);
        this.fetchAccounts(this.currentPage);
        this.form.name = '';
        this.form.category = '';
        this.form.balance = 0;
      } catch (error) {
        console.error('Error creating account', error);
      }
    },
    async deleteAccount(id) {
      try {
        await axios.delete(`/api/accounts/${id}`);
        this.fetchAccounts(this.currentPage);
      } catch (error) {
        console.error('Error deleting account', error);
      }
    },
    formatCurrency(value) {
      return new Intl.NumberFormat('en-ZA', {
        style: 'currency',
        currency: 'ZAR',
      }).format(value);
    },
  },
};
</script>