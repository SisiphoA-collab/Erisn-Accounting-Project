<template>
  <div>
          <h1>Customers</h1>
          <button class="btn btn-primary mb-3" @click="openAddModal">Add Customer</button>
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
</template>

<script>

import axios from 'axios';

export default {
  data() {
  return {
    customers: [],
    showModal: false,
    isEdit: false,
    form: {
      id: null,
      company_id: '',
      name: '',
      email: '',
      balance: ''
    }
  }
},
methods: {
  fetchCustomers() {
    axios.get('/api/customers')
      .then(res => {
        this.customers = res.data.customers;
      });
  },
  openAddModal() {
    this.resetForm();
    this.isEdit = false;
    this.showModal = true;
  },
  editCustomer(id) {
    const customer = this.customers.find(c => c.id === id);
    if (customer) {
      this.form = { ...customer };
      this.isEdit = true;
      this.showModal = true;
    }
  },
  saveCustomer() {
    axios.post('/api/customers', this.form)
      .then(() => {
        this.fetchCustomers();
        this.closeModal();
      });
  },
  updateCustomer() {
    axios.put(`/api/customers/${this.form.id}`, this.form)
      .then(() => {
        this.fetchCustomers();
        this.closeModal();
      });
  },
  deleteCustomer(id) {
    if (confirm('Are you sure you want to delete this customer?')) {
      axios.delete(`/api/customers/${id}`)
        .then(() => this.fetchCustomers());
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
      balance: '0'
    };
  }
},
mounted() {
  this.fetchCustomers();
}
}
</script>
