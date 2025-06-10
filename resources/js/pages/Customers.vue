<template>
  <div>
    <!-- Authentication Section -->
    <div v-if="!isAuthenticated" class="auth-section mb-4">
      <h2>{{ isRegistering ? 'Register' : 'Login' }}</h2>
      <form @submit.prevent="isRegistering ? register() : login()">
        <div v-if="isRegistering" class="mb-3">
          <label class="form-label">Company ID</label>
          <input type="number" class="form-control" v-model="authForm.company_id" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Name</label>
          <input type="text" class="form-control" v-model="authForm.name" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" v-model="authForm.email" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" class="form-control" v-model="authForm.password" required>
        </div>
        <div v-if="isRegistering" class="mb-3">
          <label class="form-label">Confirm Password</label>
          <input type="password" class="form-control" v-model="authForm.password_confirmation" required>
        </div>
        <div v-if="isRegistering" class="mb-3">
          <label class="form-label">Balance</label>
          <input type="number" class="form-control" v-model="authForm.balance" required>
        </div>
        <button type="submit" class="btn btn-primary">
          {{ isRegistering ? 'Register' : 'Login' }}
        </button>
        <button type="button" class="btn btn-link" @click="toggleRegister">
          {{ isRegistering ? 'Switch to Login' : 'Switch to Register' }}
        </button>
      </form>
    </div>

    <!-- Customer Section (Visible only when authenticated) -->
    <div v-if="isAuthenticated">
      <h1>Customers</h1>
      <button class="btn btn-primary mb-3" @click="logout">Logout</button>
      <button class="btn btn-primary mb-3 ms-2" @click="openAddModal">Add Customer</button>
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Id</th>
            <th>Company_id</th>
            <th>Name</th>
            <th>Email</th>
            <th>Balance</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="customer in customers" :key="customer.id">
            <td>{{ customer.id }}</td>
            <td>{{ customer.company_id }}</td>
            <td>{{ customer.name }}</td>
            <td>{{ customer.email }}</td>
            <td>R{{ customer.balance }}</td>
            <td>
              <button class="btn btn-sm btn-primary" @click="editCustomer(customer.id)">Edit</button>
              <button class="btn btn-sm btn-danger" @click="deleteCustomer(customer.id)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Customer Modal -->
    <div class="modal" tabindex="-1" :class="{ 'd-block': showModal }" style="background-color: rgba(0,0,0,0.5);">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEdit ? 'Edit Customer' : 'Add Customer' }}</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="isEdit ? updateCustomer() : saveCustomer()">
              <div class="mb-3">
                <label class="form-label">Company ID</label>
                <input type="number" class="form-control" v-model="form.company_id" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" class="form-control" v-model="form.name" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="text" class="form-control" v-model="form.email" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Balance</label>
                <input type="number" class="form-control" v-model="form.balance" required>
              </div>
              <button type="submit" class="btn btn-primary">
                {{ isEdit ? 'Update' : 'Save' }}
              </button>
              <button type="button" class="btn btn-secondary ms-2" @click="closeModal">
                Cancel
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      customers: [],
      showModal: false,
      isEdit: false,
      isAuthenticated: !!localStorage.getItem('token'),
      isRegistering: false,
      authForm: {
        company_id: '',
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        balance: '0',
      },
      form: {
        id: null,
        company_id: '',
        name: '',
        email: '',
        balance: '',
      },
    };
  },
  methods: {
    setAuthHeader() {
      const token = localStorage.getItem('token');
      if (token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
      } else {
        delete axios.defaults.headers.common['Authorization'];
      }
    },
    async register() {
      try {
        const response = await axios.post('/api/register', this.authForm);
        localStorage.setItem('token', response.data.token);
        this.isAuthenticated = true;
        this.setAuthHeader();
        this.fetchCustomers();
        this.resetAuthForm();
      } catch (error) {
        alert('Registration failed: ' + (error.response?.data?.message || 'Unknown error'));
      }
    },
    async login() {
      try {
        const response = await axios.post('/api/login', {
          email: this.authForm.email,
          password: this.authForm.password,
        });
        localStorage.setItem('token', response.data.token);
        this.isAuthenticated = true;
        this.setAuthHeader();
        this.fetchCustomers();
        this.resetAuthForm();
      } catch (error) {
        alert('Login failed: ' + (error.response?.data?.message || 'Unknown error'));
      }
    },
    async logout() {
      try {
        await axios.post('/api/logout');
        localStorage.removeItem('token');
        this.isAuthenticated = false;
        this.setAuthHeader();
        this.customers = [];
      } catch (error) {
        alert('Logout failed');
      }
    },
    async fetchCustomers() {
      try {
        const response = await axios.get('/api/customers');
        this.customers = response.data.customers;
      } catch (error) {
        alert('Failed to fetch customers');
      }
    },
    openAddModal() {
      this.resetForm();
      this.isEdit = false;
      this.showModal = true;
    },
    editCustomer(id) {
      const customer = this.customers.find((c) => c.id === id);
      if (customer) {
        this.form = { ...customer };
        this.isEdit = true;
        this.showModal = true;
      }
    },
    async saveCustomer() {
      try {
        await axios.post('/api/customers', this.form);
        this.fetchCustomers();
        this.closeModal();
      } catch (error) {
        alert('Failed to save customer');
      }
    },
    async updateCustomer() {
      try {
        await axios.put(`/api/customers/${this.form.id}`, this.form);
        this.fetchCustomers();
        this.closeModal();
      } catch (error) {
        alert('Failed to update customer');
      }
    },
    async deleteCustomer(id) {
      if (confirm('Are you sure you want to delete this customer?')) {
        try {
          await axios.delete(`/api/customers/${id}`);
          this.fetchCustomers();
        } catch (error) {
          alert('Failed to delete customer');
        }
      }
    },
    closeModal() {
      this.showModal = false;
      this.resetForm();
    },
    resetForm() {
      this.form = {
        id: null,
        company_id: '',
        name: '',
        email: '',
        balance: '0',
      };
    },
    resetAuthForm() {
      this.authForm = {
        company_id: '',
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        balance: '0',
      };
    },
    toggleRegister() {
      this.isRegistering = !this.isRegistering;
      this.resetAuthForm();
    },
  },
  mounted() {
    this.setAuthHeader();
    if (this.isAuthenticated) {
      this.fetchCustomers();
    }
  },
};
</script>

<style scoped>
.auth-section {
  max-width: 400px;
  margin: 0 auto;
}
</style>