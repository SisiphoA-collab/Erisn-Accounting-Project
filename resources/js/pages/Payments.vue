<template>
  <div>
          <h1>Payments</h1>
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