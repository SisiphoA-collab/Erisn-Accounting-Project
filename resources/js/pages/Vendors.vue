<template>
  <!-- Flash message -->
  <div class="p-2">
    <FlashMessage :message="message" :messageType="messageType" @close="empty" @cleared="message = null" />
  </div>

  <div>
    <h1 class="text-outline">Vendors</h1>
    <hr />
    <button class="btn btn-primary mb-3 p-2" @click="addVendor">Add Vendor</button>

    <!-- Filter & Search -->
    <div class="d-flex flex-column flex-md-row mb-4">
      <!-- Status Filters -->
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
      <form @submit.prevent="fetchVendors" class="flex-grow-1">
        <div class="input-group">
          <input v-model="searchQuery" type="text" placeholder="Search by keyword..."
            class="form-control inner-shadow">
          <button type="submit" class="btn btn-primary">Search</button>
        </div>
      </form>
    </div>

    <!-- Table -->
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Id</th>
          <th>Company ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Balance</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="vendor in vendors?.data || []" :key="vendor.id">
          <td>{{ vendor.id }}</td>
          <td>{{ vendor.company_id }}</td>
          <td>{{ vendor.name }}</td>
          <td>{{ vendor.email }}</td>
          <td>R{{ vendor.balance }}</td>
          <td>
            <button class="btn btn-sm btn-primary ms-1" @click="editVendor(vendor)">Edit</button>
            <button class="btn btn-sm btn-danger ms-1" @click="deleteVendor(vendor.id)">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination -->
    <Pagination :links="vendors.links" :fetchData="changePage" />
  </div>

  <!-- Vendor Modal -->
  <div class="modal" tabindex="-1" :class="{ 'd-block': showModal }" style="background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog">
      <div class="modal-content">
        <form @submit.prevent="isEdit ? updateVendor() : saveVendor()">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEdit ? 'Edit Vendor' : 'Add Vendor' }}</h5>
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
</template>

<script>
import axios from 'axios';
import Pagination from '../Components/Pagination.vue';
import FlashMessage from '../Components/FlashMessage.vue';

export default {
  components: { Pagination, FlashMessage },
  data() {
    return {
      vendors: {
        data: [],
        links: [],
      },
      showModal: false,
      isEdit: false,
      form: {
        id: null,
        company_id: '',
        name: '',
        email: '',
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
    fetchVendors(page = this.currentPage) {
      const params = {
        page,
        search: this.searchQuery
      };
      if (this.selectedStatus !== 'All') {
        params.status = this.selectedStatus;
      }

      axios.get('/api/vendors', { params })
        .then(res => {
          this.vendors = res.data; // use full response like invoices.vue
          this.currentPage = page;
        })
        .catch(() => {
          this.message = 'Error fetching vendors.';
          this.messageType = 'error';
        });
    },
    changePage(page) {
      this.currentPage = page;
      this.fetchVendors(page);
    },
    updateSelected(status) {
      this.selectedStatus = status;
      this.fetchVendors(1);
    },
    addVendor() {
      this.resetForm();
      this.isEdit = false;
      this.showModal = true;
    },
    editVendor(vendor) {
      this.form = { ...vendor };
      this.isEdit = true;
      this.showModal = true;
    },
    saveVendor() {
      axios.post('/api/vendors', this.form)
        .then(res => {
          this.message = res.data.message || 'Vendor added successfully.';
          this.messageType = 'success';
          this.fetchVendors();
          this.closeModal();
        })
        .catch(() => {
          this.message = 'Error saving vendor.';
          this.messageType = 'error';
        });
    },
    updateVendor() {
      axios.put(`/api/vendors/${this.form.id}`, this.form)
        .then(res => {
          this.message = res.data.message || 'Vendor updated.';
          this.messageType = 'success';
          this.fetchVendors();
          this.closeModal();
        })
        .catch(() => {
          this.message = 'Error updating vendor.';
          this.messageType = 'error';
        });
    },
    deleteVendor(id) {
      if (confirm('Are you sure you want to delete this vendor?')) {
        axios.delete(`/api/vendors/${id}`)
          .then(res => {
            this.message = res.data.message || 'Vendor deleted.';
            this.messageType = 'success';
            this.fetchVendors();
          })
          .catch(() => {
            this.message = 'Error deleting vendor.';
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
        company_id: '',
        name: '',
        email: '',
        balance: ''
      };
    },
    empty() {
      this.message = '';
      this.messageType = '';
    }
  },
  mounted() {
    this.fetchVendors();
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

