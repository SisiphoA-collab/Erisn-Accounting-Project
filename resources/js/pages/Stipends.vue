<template>
  <div>
          <h1>Stipends</h1>
          <button class="btn btn-primary mb-3" @click="addStipend">Add Stipend</button>
          <button class="btn btn-secondary mb-3" @click="importCSV">Import CSV</button>
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Learner</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Month</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="stipend in stipends" :key="stipend.id">
                <td>{{ stipend.learner }}</td>
                <td>{{ stipend.amount }}</td>
                <td>{{ stipend.status }}</td>
                <td>{{ stipend.month }}</td>
                <td>
                  <button class="btn btn-sm btn-primary" @click="uploadReceipt(stipend.id)">Upload Receipt</button>
                  <button class="btn btn-sm btn-danger" @click="deleteStipend(stipend.id)">Delete</button>
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
          stipends: [
            { id: 1, learner: 'Learner A', amount: '$200.00', status: 'Pending', month: 'May 2025' },
            { id: 2, learner: 'Learner B', amount: '$200.00', status: 'Paid', month: 'April 2025' }
          ]
        };
      },
      methods: {
        addStipend() {
          alert('Add stipend form');
        },
        importCSV() {
          alert('Import CSV file');
        },
        uploadReceipt(id) {
          alert(`Upload receipt for stipend ${id}`);
        },
        deleteStipend(id) {
          alert(`Delete stipend ${id}`);
          this.stipends = this.stipends.filter(s => s.id !== id);
        }
      }
    };
</script>
