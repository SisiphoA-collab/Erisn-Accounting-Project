<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <div class="py-2">
      <div class="mx-auto max-w-7x sm:px-6">

        <!-- Flash message -->
        <div class="p-2">
          <FlashMessage :message="message" :messageType="messageType" @close="empty" @cleared="message = null" />
        </div>

        <!-- Add button -->
        <div>
          <button class="btn btn-primary mb-3 p-2" @click="addExpense">Add Expense</button>

          <!-- Filter and Search -->
          <div class="d-flex flex-column flex-md-row mb-4">
            <div class="flex-grow-1">
              <ul class="nav nav-pills">
                <li v-for="category in categories" :key="category" class="nav-item">
                  <a href="#" @click.prevent="updateCategory(category)"
                     :class="['nav-link', category === selectedCategory ? 'active' : '']">
                    {{ category }}
                  </a>
                </li>
              </ul>
            </div>

            <!-- Search -->
            <form @submit.prevent="fetchExpenses" class="flex-grow-1 ms-md-3 mt-2 mt-md-0">
              <div class="input-group">
                <input v-model="searchQuery" type="text" placeholder="Search by vendor or amount..." class="form-control">
                <button type="submit" class="btn btn-primary">Search</button>
              </div>
            </form>
          </div>

          <!-- Expense Table -->
          <table class="table table-striped">
            <thead>
              <tr>
                <th>ID</th>
                <th>Vendor</th>
                <th>Amount</th>
                <th>Category</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="expense in expenses?.data || []" :key="expense.id">
                <td>{{ expense.id }}</td>
                <td>{{ expense.vendor?.name || 'Unknown' }}</td>
                <td>R{{ expense.amount }}</td>
                <td>{{ expense.category }}</td>
                <td>{{ expense.date }}</td>
                <td>
                  <button class="btn btn-sm btn-primary" @click="editExpense(expense)">Edit</button>
                  <button class="btn btn-sm btn-danger ms-1" @click="deleteExpense(expense.id)">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Pagination -->
          <Pagination v-if="expenses.links" :links="expenses.links" :fetchData="changePage" />

          <!-- Expense Modal -->
          <div class="modal" tabindex="-1" :class="{ 'd-block': showModal }" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
              <div class="modal-content">
                <form @submit.prevent="isEdit ? updateExpense() : saveExpense()">
                  <div class="modal-header">
                    <h5 class="modal-title">{{ isEdit ? 'Edit Expense' : 'Add Expense' }}</h5>
                    <button type="button" class="btn-close" @click="closeModal"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                    <label class="form-label">Vendor</label>
                    <select class="form-select selectpicker" data-live-search="true"
                                    v-model="form.vendor_id" :disabled="isEdit">
                        <option disabled value="">Select Vendor</option>
                        <option v-for="vendor in vendors" :key="vendor.id" :value="vendor.id">
                         {{ vendor.id }} - {{ vendor.name }}
                        </option>
                    </select>
                    </div>

                    <div class="mb-3">
                      <label class="form-label">Amount</label>
                      <input type="number" class="form-control" v-model="form.amount" required />
                    </div>

                    <div class="mb-3">
                      <label class="form-label">Category</label>
                      <input type="text" class="form-control" v-model="form.category" required />
                    </div>

                    <div class="mb-3">
                      <label class="form-label">Date</label>
                      <input type="date" class="form-control" v-model="form.date" required />
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

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script>
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Pagination from '../Components/Pagination.vue';
import FlashMessage from '../Components/FlashMessage.vue';

export default {
  components: { AuthenticatedLayout, Pagination, FlashMessage },
  data() {
    return {
      expenses: {
        data: [],
        links: [],
      },
      vendors: [],
      showModal: false,
      isEdit: false,
      form: {
        id: null,
        vendor_id: '',
        amount: '',
        category: '',
        date: '',
      },
      message: '',
      messageType: '',
      searchQuery: '',
      selectedCategory: 'All',
      categories: ['All', 'Travel', 'Office', 'Supplies', 'Other'],
      currentPage: 1
    };
  },
  methods: {
    fetchExpenses(page = 1) {
      const params = {
        page,
        search: this.searchQuery,
      };
      if (this.selectedCategory !== 'All') {
        params.category = this.selectedCategory;
      }

      axios.get('/api/expenses', { params })
        .then(res => {
          this.expenses = res.data;
          this.currentPage = page;
        })
        .catch(() => {
          this.message = 'Error fetching expenses.';
          this.messageType = 'error';
        });
    },
    fetchVendors() {
    axios.get('/api/vendors')
      .then(res => {
        this.vendors = res.data.data;
      })
      .catch(() => {
        this.message = 'Error loading vendors.';
        this.messageType = 'error';
      });
    },
    changePage(page) {
      this.fetchExpenses(page);
    },
    updateCategory(category) {
      this.selectedCategory = category;
      this.fetchExpenses(1);
    },
    addExpense() {
      this.resetForm();
      this.isEdit = false;
      this.fetchVendors();
      this.showModal = true;
    },
    editExpense(expense) {
      this.form = {
        id: expense.id,
        vendor_id: expense.vendor_id,
        amount: expense.amount,
        category: expense.category,
        date: expense.date,
      };
      this.fetchVendors();
      this.isEdit = true;
      this.showModal = true;
    },
    saveExpense() {
      axios.post('/api/expenses', this.form)
        .then(res => {
          this.message = res.data.message || 'Expense added successfully.';
          this.messageType = 'success';
          this.fetchExpenses();
          this.closeModal();
        })
        .catch(() => {
          this.message = 'Error saving expense.';
          this.messageType = 'error';
        });
    },
    updateExpense() {
      axios.put(`/api/expenses/${this.form.id}`, this.form)
        .then(res => {
          this.message = res.data.message || 'Expense updated.';
          this.messageType = 'success';
          this.fetchExpenses();
          this.closeModal();
        })
        .catch(() => {
          this.message = 'Error updating expense.';
          this.messageType = 'error';
        });
    },
    deleteExpense(id) {
      if (confirm('Are you sure you want to delete this expense?')) {
        axios.delete(`/api/expenses/${id}`)
          .then(res => {
            this.message = res.data.message || 'Expense deleted.';
            this.messageType = 'success';
            this.fetchExpenses();
          })
          .catch(() => {
            this.message = 'Error deleting expense.';
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
        vendor_id: '',
        amount: '',
        category: '',
        date: ''
      };
    },
    empty() {
      this.message = '';
      this.messageType = '';
    }
  },
  mounted() {
    this.fetchExpenses();
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
