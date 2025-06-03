<template>
  <div>
          <h1>Dashboard</h1>
         <div>
          <h1>Dashboard</h1>
          <div class="row">
            <div class="col-md-4">
              <div class="card card-stats">
                <div class="card-body">
                  <h5 class="card-title">Income</h5>
                  <p class="card-text">$12,345.67</p>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card card-stats">
                <div class="card-body">
                  <h5 class="card-title">Expenses</h5>
                  <p class="card-text">$8,765.43</p>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card card-stats">
                <div class="card-body">
                  <h5 class="card-title">Profit/Loss</h5>
                  <p class="card-text">$3,580.24</p>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <h3>Recent Activity</h3>
              <ul class="list-group">
                <li class="list-group-item">Invoice #1234 sent to Customer A</li>
                <li class="list-group-item">Expense $500 recorded</li>
                <li class="list-group-item">Stipend paid to Learner B</li>
              </ul>
            </div>
            <div class="col-md-6">
              <h3>Quick Actions</h3>
              <button class="btn btn-primary me-2" @click="createInvoice">New Invoice</button>
              <button class="btn btn-secondary me-2" @click="addExpense">Add Expense</button>
              <button class="btn btn-success" @click="manageStipends">Manage Stipends</button>
            </div>
          </div>
        </div>
  </div>
</template>

<script>
export default {
  methods: {
        createInvoice() {
          alert('Redirect to create invoice page');
          this.$router.push('/invoices/new');
        },
        addExpense() {
          alert('Redirect to add expense page');
          this.$router.push('/expenses/new');
        },
        manageStipends() {
          alert('Redirect to stipends page');
          this.$router.push('/stipends');
        }
      }
};
</script>
