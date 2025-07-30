<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="py-2">
            <div class="mx-auto max-w-7x sm:px-6">
                <div>
  <div>

    <div class="d-flex flex-column flex-md-row mb-4">
      <!-- Status Filters -->
      <div class="flex-grow-1">
        <ul class="nav nav-pills">
          <li v-for="status in statuses" :key="status" class="nav-item">
            <a href="#" @click.prevent="updateSelected(status)"
              :class="['nav-link', status === selectedStatus ? 'active' : '']">
              { status }
            </a>
          </li>
        </ul>
      </div>

      <!-- Search Form -->
      <form @submit.prevent="fetchItems" class="flex-grow-1">
        <div class="input-group">
          <input key="search" v-model="searchQuery" type="text" placeholder="Search by keyword..."
            class="form-control inner-shadow">
          <button type="submit" class="btn btn-primary">
            Search
          </button>
        </div>
      </form>
    </div>
    </div>
          <table ref="paymentTable" class="table table-striped">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Invoice_Id</th>
                    <th>Method</th>
                    <th>Amount</th>
                    <th>Payment_date</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="payment in payments?.data || []" :key="payment.id">
                    <td>{{ payment.id }}</td>
                    <td>{{ payment.invoice_id }}</td>
                    <td>{{ payment.method }}</td>
                    <td>R{{ payment.amount }}</td>
                    <td>{{ payment.payment_date }}</td>
                </tr>
            </tbody>
        </table>

         <Pagination :links="payments.links" :fetchData="changePage" />
    </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import axios from 'axios';
import Pagination from '../Components/Pagination.vue';
import FlashMessage from '../Components/FlashMessage.vue';

export default {
  components: { Pagination, FlashMessage },
  data() {
    return {
      payments: {
        data: [],
        links: []
      },
      form: {
        id: null,
        invoice_id: '',
        method: '',
        amount: '',
        payment_date: ''
      },
      showModal: false,
      isEdit: false,
      searchQuery: '',
      currentPage: 1,
      message: '',
      messageType: ''
    };
  },
  methods: {
    fetchPayments(page = this.currentPage) {
      axios.get('/api/payments', { params: { page, search: this.searchQuery } })
        .then(res => {
          this.payments = res.data;
          this.currentPage = page;
        })
        .catch(() => {
          this.message = 'Error fetching payments.';
          this.messageType = 'error';
        });
    },
    changePage(page) {
      this.currentPage = page;
      this.fetchPayments(page);
    },
    addPayment() {
      this.resetForm();
      this.isEdit = false;
      this.showModal = true;
    },
    editPayment(payment) {
      this.form = { ...payment };
      this.isEdit = true;
      this.showModal = true;
    },
    savePayment() {
      axios.post('/api/payments', this.form)
        .then(res => {
          this.message = res.data.message || 'Payment added.';
          this.messageType = 'success';
          this.fetchPayments();
          this.closeModal();
        })
        .catch(() => {
          this.message = 'Error saving payment.';
          this.messageType = 'error';
        });
    },
    updatePayment() {
      axios.put(`/api/payments/${this.form.id}`, this.form)
        .then(res => {
          this.message = res.data.message || 'Payment updated.';
          this.messageType = 'success';
          this.fetchPayments();
          this.closeModal();
        })
        .catch(() => {
          this.message = 'Error updating payment.';
          this.messageType = 'error';
        });
    },
    deletePayment(id) {
      if (confirm('Are you sure you want to delete this payment?')) {
        axios.delete(`/api/payments/${id}`)
          .then(res => {
            this.message = res.data.message || 'Payment deleted.';
            this.messageType = 'success';
            this.fetchPayments();
          })
          .catch(() => {
            this.message = 'Error deleting payment.';
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
        invoice_id: '',
        method: '',
        amount: '',
        payment_date: ''
      };
    },
    empty() {
      this.message = '';
      this.messageType = '';
    }
  },
  mounted() {
    this.fetchPayments();
  },
  watch: {
    message(val) {
      if (val) {
        setTimeout(() => this.message = '', 3000);
      }
    }
  }
};
</script>
