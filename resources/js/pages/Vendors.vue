<template>
        <div>
          <h1>Vendors</h1>
          <button class="btn btn-primary mb-3" @click="addVendor">Add Vendor</button>
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
              <tr v-for="vendor in vendors" :key="vendor.id">
                <td>{{ vendor.id }}</td>
                <td>{{ vendor.company_id }}</td>
                <td>{{ vendor.name }}</td>
                <td>{{ vendor.email }}</td>
                <td>R{{ vendor.balance }}</td>
                <td>
                  <button class="btn btn-sm btn-primary" @click="editVendor(vendor.id)">Edit</button>
                  <button class="btn btn-sm btn-danger" @click="deleteVendor(vendor.id)">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

<!-- Vendor Modal -->
<div class="modal" tabindex="-1" :class="{ 'd-block': showModal }" style="background-color: rgba(0,0,0,0.5);">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ isEdit ? 'Edit Vendor' : 'Add Vendor' }}</h5>
        <button type="button" class="btn-close" @click="closeModal"></button>
      </div>
      <div class="modal-body">
        <form @submit.prevent="isEdit ? updateVendor() : saveVendor()">
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
      vendors: [],
      showModal: false,
      isEdit: false,
      form: {
        id: null,
        company_id: '',
        name: '',
        email: '',
        balance: ''
      }
    };
  },
  methods: {
    fetchVendors() {
      axios.get('/api/vendors')
        .then(res => {
          this.vendors = res.data.vendors;
        });
    },
    addVendor() {
      this.resetForm();
      this.isEdit = false;
      this.showModal = true;
    },
    editVendor(id) {
      const vendor = this.vendors.find(v => v.id === id);
      if (vendor) {
        this.form = { ...vendor };
        this.isEdit = true;
        this.showModal = true;
      }
    },
    saveVendor() {
      axios.post('/api/vendors', this.form)
        .then(() => {
          this.fetchVendors();
          this.closeModal();
        });
    },
    updateVendor() {
      axios.put(`/api/vendors/${this.form.id}`, this.form)
        .then(() => {
          this.fetchVendors();
          this.closeModal();
        });
    },
    deleteVendor(id) {
      if (confirm('Are you sure you want to delete this vendor?')) {
        axios.delete(`/api/vendors/${id}`)
          .then(() => this.fetchVendors());
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
    }
  },
  mounted() {
    this.fetchVendors();
  }
};
</script>
