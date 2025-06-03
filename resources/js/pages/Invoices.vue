<template>
  <div>
    <h1>Invoices</h1>
    <button class="btn btn-primary mb-3" @click="addInvoice">Create Invoice</button>

    <table class="table table-striped">
      <thead>
        <tr>
          <th>Invoice ID</th>
          <th>Customer</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Due Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="invoice in invoices" :key="invoice.id">
          <td>{{ invoice.id }}</td>
          <td>{{ invoice.customer_id }}</td>
          <td>{{ invoice.amount }}</td>
          <td>{{ invoice.status }}</td>
          <td>{{ invoice.due_date }}</td>
          <td>
            <button class="btn btn-sm btn-primary" @click="editInvoice(invoice)">Edit</button>
            <button class="btn btn-sm btn-danger" @click="deleteInvoice(invoice.id)">Delete</button>
            <button class="btn btn-sm btn-success" @click="payInvoice(invoice.id)" v-if="invoice.status !== 'Paid'">Pay Now</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Invoice Modal -->
<div class="modal" tabindex="-1" :class="{ 'd-block': showModal }" style="background-color: rgba(0,0,0,0.5);">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ isEdit ? 'Edit Vendor' : 'Add Vendor' }}</h5>
        <button type="button" class="btn-close" @click="closeModal"></button>
      </div>
      <div class="modal-body">
        <form @submit.prevent="isEdit ? updatedInvoice() : saveInvoice()">
          <div class="mb-3">
            <label class="form-label">Customer ID</label>
            <input type="number" class="form-control" v-model="form.customer_id" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Amount</label>
            <input type="text" class="form-control" v-model="form.amount" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <input type="text" class="form-control" v-model="form.status" required>
          </div>
          <div class="mb-3">
            <label class="form-label">due_date</label>
            <input type="date" class="form-control" v-model="form.due_date" required>
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
      invoices: [],
      showmodal: false,
      isEdit: false,
      form: {
        id: null,
        customer_id: '',
        amount: '',
        status: '',
        due_date: ''
      }
    }
  },
  methods: {
    fetchInvoices(){
      axios.get('/api/invoices')
      .then(res => {
        this.invoices = res.data.invoices;
      });
    },
    addInvoice() {
      this.resetForm();
      this.isEdit = false;
      this.showModal = true;
    },
    editInvoice(id) {
      const invoice = this.invoices.find(i => i.id === id)
      if (invoice) {
        this.form = { ...invoice};
        thios.isEdit = true;
        this.showModal = true;
      }
    },
    saveInvoice() {
      axios.post('/api/invoices', this.form)
        .then(() => {
          this.fetchInvoices();
          this.closeModal();
        });
    },
    updatedInvoice(){
      axios,put(`/api/invoices/${this.form.id}`, this.form)
      .then(() => {
        this.fetchInvoices();
        this.closeModal();
      });
    },
    deleteInvoice(id) {
      if (confirm('Are you sure you want to delete this vendor?')) {
        axios.delete(`/api/invoices/${id}`)
          .then(() => this.fetchInvoices());
      }
    },
    payInvoice(invoiceId) {
      axios.post('/api/paystack/initialize', { invoice_id: invoiceId })
        .then(res => {
          const url = res.data.authorization_url;
          window.location.href = url; // Redirect to Paystack
        })
        .catch(err => {
          alert('Failed to initiate payment');
          console.error(err);
        });
    },
    closeModal(){
      this.showModal = false;
      this.resetForm();
    },
    resetForm(){
      this.form = {
        id: null,
        customer_id: '',
        amount: '',
        status: 'Unpaid',
        due_date: '' 
      };
    }
  },
  mounted() {
    this.fetchInvoices();
  }
};
</script>