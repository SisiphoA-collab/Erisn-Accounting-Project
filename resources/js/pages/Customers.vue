<template>
  <!-- Flash message -->
  <div class="p-2">
    <FlashMessage :message="message" :messageType="messageType" @close="empty" @cleared="message = null" />
  </div>

  <div>
    <h1 class="text-outline">Customers</h1>
    <hr />
    <button class="btn btn-primary mb-3 p-2" @click="addCustomer">Add Customer</button>

    <!-- Filter and Search Section -->
    <div class="d-flex flex-column flex-md-row mb-4">
      <!-- Status Filter -->
      <div class="flex-grow-1">
        <ul class="nav nav-pills">
          <li v-for="status in statuses" :key="status" class="nav-item">
            <a href="#" @click.prevent="updateSelected(status)"
              :class="['nav-link', status === selectedStatus ? 'active' : '']">
              {{ status }}
            </a>
          </li>
        </ul>
      </div>

      <!-- Search -->
      <form @submit.prevent="fetchCustomers" class="flex-grow-1">
        <div class="input-group">
          <input v-model="searchQuery" type="text" placeholder="Search by keyword..."
            class="form-control inner-shadow">
          <button type="submit" class="btn btn-primary">Search</button>
        </div>
      </form>
    </div>

    <!-- Customer Table -->
    <table class="table table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Company ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Balance</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="customer in customers?.data || []" :key="customer.id">
          <td>{{ customer.id }}</td>
          <td>{{ customer.company_id }}</td>
          <td>{{ customer.name }}</td>
          <td>{{ customer.email }}</td>
          <td>R{{ customer.balance }}</td>
          <td>
            <button class="btn btn-sm btn-primary" @click="editCustomer(customer)">Edit</button>
            <button class="btn btn-sm btn-danger ms-1" @click="deleteCustomer(customer.id)">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination -->
    <Pagination :links="customers.links" :fetchData="changePage" />

    <!-- Customer Modal -->
    <div class="modal" tabindex="-1" :class="{ 'd-block': showModal }" style="background-color: rgba(0,0,0,0.5);">
      <div class="modal-dialog">
        <div class="modal-content">
          <form @submit.prevent="isEdit ? updateCustomer() : saveCustomer()">
            <div class="modal-header">
              <h5 class="modal-title">{{ isEdit ? 'Edit Customer' : 'Add Customer' }}</h5>
              <button type="button" class="btn-close" @click="closeModal"></button>
            </div>
            <div class="modal-body">
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
                <input type="email" class="form-control" v-model="form.email" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Balance</label>
                <input type="number" class="form-control" v-model="form.balance" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">{{ isEdit ? 'Update' : 'Save' }}</button>
              <button type="button" class="btn btn-secondary ms-2" @click="closeModal">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Pagination from '../Components/Pagination.vue';
import FlashMessage from '../Components/FlashMessage.vue';

export default {
  components: { Pagination, FlashMessage },
  data() {
    return {
      customers: {
        data: [],
        links: [],
      },
      showModal: false,
      isEdit: false,
      form: {
        id: null,
        name: '',
        email: '',
        phone: '',
        balance: ''
      },
      message: '',
      messageType: '',
      searchQuery: '',
      selectedStatus: 'All',
      statuses: ['All'],
      currentPage: 1
    };
  },
  methods: {
    fetchCustomers(page = this.currentPage) {
      const params = {
        page,
        search: this.searchQuery
      };
      if (this.selectedStatus !== 'All') {
        params.status = this.selectedStatus;
      }

      axios.get('/api/customers', { params })
        .then(res => {
          this.customers = res.data;
          this.currentPage = page;
        })
        .catch(() => {
          this.message = 'Error fetching customers.';
          this.messageType = 'error';
        });
    },
    changePage(page) {
      this.currentPage = page;
      this.fetchCustomers(page);
    },
    updateSelected(status) {
      this.selectedStatus = status;
      this.fetchCustomers(1);
    },
    addCustomer() {
      this.resetForm();
      this.isEdit = false;
      this.showModal = true;
    },
    editCustomer(customer) {
      this.form = { ...customer };
      this.isEdit = true;
      this.showModal = true;
    },
    saveCustomer() {
      axios.post('/api/customers', this.form)
        .then(res => {
          this.message = res.data.message || 'Customer added successfully.';
          this.messageType = 'success';
          this.fetchCustomers();
          this.closeModal();
        })
        .catch(() => {
          this.message = 'Error saving customer.';
          this.messageType = 'error';
        });
    },
    updateCustomer() {
      axios.put(`/api/customers/${this.form.id}`, this.form)
        .then(res => {
          this.message = res.data.message || 'Customer updated.';
          this.messageType = 'success';
          this.fetchCustomers();
          this.closeModal();
        })
        .catch(() => {
          this.message = 'Error updating customer.';
          this.messageType = 'error';
        });
    },
    deleteCustomer(id) {
      if (confirm('Are you sure you want to delete this customer?')) {
        axios.delete(`/api/customers/${id}`)
          .then(res => {
            this.message = res.data.message || 'Customer deleted.';
            this.messageType = 'success';
            this.fetchCustomers();
          })
          .catch(() => {
            this.message = 'Error deleting customer.';
            this.messageType = 'error';
          });
      }
    },
    closeModal() {
      this.showModal = false;
      this.resetForm();
    },
    resetForm() {
      this.form = {
        id: null,
        name: '',
        email: '',
        phone: '',
        balance: ''
      };
    },
    empty() {
      this.message = '';
      this.messageType = '';
    }
  },
  mounted() {
    this.fetchCustomers();
  },
  watch: {
    message(val) {
      if (val) {
        setTimeout(() => {
          this.message = '';
        }, 3000);
      }
    }
  }
};
</script>

