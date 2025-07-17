<template>
    <div class="container mt-4">
        <!-- <h2 class="mb-4">📘 Chart of Accounts</h2> -->

        <form @submit.prevent="createAccount" class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label">Company</label>
                <select v-model="form.company_id" class="form-select">
                    <option value="" disabled selected>select company...</option>
                    <option v-for="company in companies" :value="company.id">{{ company.name }}</option>
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
                <button type="submit" class="btn btn-primary text-white w-100">➕ Add Account</button>
            </div>
        </form>

        <div class="card">
            <div class="card-header">📋 Account List</div>
            <ul class="list-group list-group-flush">
                <li v-for="account in accounts" :key="account.id"
                    class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ account.name }}</strong> — {{ account.type }}
                        <span v-if="account.category">({{ account.category }})</span>
                        <br />
                        <small class="text-muted"><strong class="font-italic">{{ account.company.name }}</strong></small>
                        <br />
                        <small class="text-muted">{{ formatCurrency(account.balance) }}</small>
                    </div>
                    <button @click="deleteAccount(account.id)" class="btn btn-sm btn-outline-danger">🗑 Delete</button>
                </li>
            </ul>
        </div>

        <Pagination :links="links" :fetchData="fetchAccounts" />
    </div>
</template>

<script>
import axios from 'axios';
import Pagination from '@/components/Pagination.vue';

export default {
    name: 'ChartOfAccount',
    components: {
        Pagination
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
                balance: 0
            }
        };
    },
    mounted() {
        this.fetchCompanies();
        this.fetchAccounts();
    },
    methods: {
        fetchCompanies() {
            axios.get('/api/accounts').then(res => {
                this.companies = res.data.company;
                if (this.companies.length) {
                    this.form.company_id = this.companies[0].id;
                }
            });
        },
        fetchAccounts(page = 1) {
            axios.get(`/api/accounts?page=${page}`).then(res => {
                this.accounts = res.data[0].data;
                this.links = res.data[0].links;
                this.currentPage = res.data[0].current_page;
            });
        },
        createAccount() {
            axios.post('/api/accounts', this.form).then(() => {
                this.fetchAccounts();
                this.form.name = '';
                this.form.category = '';
                this.form.balance = 0;
            });
        },
        deleteAccount(id) {
            axios.delete(`/api/accounts/${id}`).then(() => {
                this.fetchAccounts();
            });
        },
        formatCurrency(value) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'ZAR'
            }).format(value);
        }
    }
};
</script>
