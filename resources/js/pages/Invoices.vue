<template>
<div>
          <h1>Invoices</h1>
          <button class="btn btn-primary mb-3" @click="createInvoice">Create Invoice</button>
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
                <td>{{ invoice.customer }}</td>
                <td>{{ invoice.amount }}</td>
                <td>{{ invoice.status }}</td>
                <td>{{ invoice.due_date }}</td>
                <td>
                  <button class="btn btn-sm btn-primary" @click="editInvoice(invoice.id)">Edit</button>
                  <button class="btn btn-sm btn-danger" @click="deleteInvoice(invoice.id)">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
</template>

<script>

</script>
