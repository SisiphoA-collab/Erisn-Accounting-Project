<template>
  <div>
    <!-- Flash message -->
  <div class="p-2">
    <FlashMessage :message="message" :messageType="messageType" @close="empty" @cleared="message = null" />
  </div>

  <div>
    <h1 class="text-outline">Payments</h1>
    <hr />
    <button class="btn btn-primary mb-3 p-2" @click="addItem">Add Payments</button>

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
              <tr v-for="payment in payments" :key="payment.id">
                <td>{{ payment.id }}</td>
                <td>{{ payment.invoice_id }}</td>
                <td>{{ payment.method }}</td>
                <td>R{{ payment.amount }}</td>
                <td>{{ payment.payment_date }}</td>
              </tr>
            </tbody>
          </table>
  </div>
</template>

<script>
import axios from 'axios';
import $ from 'jquery';
import 'datatables.net-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';

export default {
  data() {
    return {
      payments: [],
    }
  },
  methods: {
    fetchPayments() {
      axios.get('/api/payments')
        .then(res => {
          this.payments = res.data.payments;
        });
    },
    
  },
  mounted() {
    this.fetchPayments();
  }
};
</script>