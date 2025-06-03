<template>
  <div>
          <h1>Customers</h1>
          <button class="btn btn-primary mb-3" @click="addCustomer">Add Customer</button>
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Balance</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="customer in customers" :key="customer.id">
                <td>{{ customer.name }}</td>
                <td>{{ customer.email }}</td>
                <td>{{ customer.balance }}</td>
                <td>
                  <button class="btn btn-sm btn-primary" @click="editCustomer(customer.id)">Edit</button>
                  <button class="btn btn-sm btn-danger" @click="deleteCustomer(customer.id)">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
</template>

<script>
export default {
  data() {
        return {
          customers: [
            { id: 1, company_id: 2, name: 'customerA', balance: 'R500.00' },
            { id: 2, company_id: 1, name: 'customerB', balance: 'R1200.00' },
          ]
        };
      },
      methods: {
        addCustomer() {
          alert('Add customer form');
        },
        editCustomer(id) {
          alert(`Edit customer ${id}`);
        },
        deleteCustomer(id) {
          alert(`Delete customer ${id}`);
          this.customers = this.customers.filter(c => c.id !== id);
        }
      }
};

</script>
