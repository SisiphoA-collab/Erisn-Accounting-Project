<template>
  <div>
        <h1>Expenses</h1>
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Id</th>
                <th>Vendor_Id</th>
                <th>Amount</th>
                <th>Category</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="expense in expenses" :key="expense.id">
                <td>{{ expense.id }}</td>
                <td>{{ expense.vendor_id }}</td>
                <td>{{ expense.amount }}</td>
                <td>R{{ expense.category }}</td>
                <td>{{ expense.date }}</td>
              </tr>
            </tbody>
          </table>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      expenses: [],
    }
  },
  methods: {
    fetchExpenses() {
      axios.get('/api/expenses')
        .then(res => {
          this.expenses = res.data.expenses;
      });
    },
  },
  mounted() {
    this.fetchExpenses();
  }
};
</script>