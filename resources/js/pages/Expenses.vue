<template>
  <!-- Flash message -->
  <div class="p-2">
    <FlashMessage :message="message" :messageType="messageType" @close="empty" @cleared="message = null" />
  </div>

  <div>
    <h1 class="text-outline">Expenses</h1>
    <hr />
    <button class="btn btn-primary mb-3 p-2" @click="addExpense">Add Expense</button>

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

      <!-- Search Form -->
      <form @submit.prevent="fetchExpenses" class="flex-grow-1">
        <div class="input-group">
          <input key="search" v-model="searchQuery" type="text" placeholder="Search by keyword..."
            class="form-control inner-shadow">
          <button type="submit" class="btn btn-primary">
            Search
          </button>
        </div>
      </form>
    </div>
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