<template>
  <div>
    <div>
      <h1 class="text-outline">Dashboard</h1>
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
      <hr/>
      <div class="row">
        <div class="col-md-6">
          <h3>Recent Activity</h3>
          <ul class="list-group text-info">
            <li v-if="invoice"
              :class="`${formatDate(invoice.updated_at) === today ? 'text-info' : ''} list-group-item`">
              Invoice #{{ invoice.id }} sent to {{ invoice.customer?.name }}
            </li>
            <li class="list-group-item">Expense $500 recorded</li>
            <li v-if="stipend" :class="`${formatDate(stipend.updated_at) === today ? 'text-info' : ''} list-group-item`">
              Stipend paid to {{ stipend.learner?.name }}

            </li>
          </ul>
        </div>
        <div class="col-md-6">
          <h3>Quick Actions</h3>
          <button class="btn btn-primary me-2" @click="createInvoice">New Invoice</button>
          <button class="btn btn-secondary me-2" @click="addExpense">Add Expense</button>
          <button class="btn btn-success" @click="manageStipends">Manage Stipends</button>
        </div>
      </div>
      <div class="p-2">
        <InvoiceChart :totalInvoices="totalInvoices" :paidInvoices="paidInvoices" :overdueInvoices="overdueInvoices"
          :chartData="chartData" />
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import InvoiceChart from '../Components/InvoiceChart.vue';

export default {
  components: { InvoiceChart },
  setup() {
    const totalInvoices = ref(0);
    const paidInvoices = ref(null);
    const overdueInvoices = ref(null);
    const chartData = ref({ labels: [], datasets: [] });
    const invoice = ref(null);
    const stipend = ref(null);
    const today = new Date().toISOString().split('T')[0];

      //`updated_at` is ISO format (e.g., "2025-06-21T12:00:00Z")
    const formatDate= (dateStr) => {
      return dateStr.split('T')[0];
    };
    const createInvoice = () => {
      this.$router.push('/invoices');
    };
    const addExpense = () => {
      alert('Redirect to add expense page');
      this.$router.push('/expenses/new');
    };
    const manageStipends = () => {
      this.$router.push('/stipends');
    };

    const fetchInvoiceStats = async () => {
      try {
        const res = await axios.get('/api/dashboard');

        totalInvoices.value = res.data.totalInvoices;
        paidInvoices.value = res.data.paidInvoices;
        overdueInvoices.value = res.data.overdueInvoices;
        chartData.value = { labels: res.data.labels, datasets: res.data.datasets };
        invoice.value = res.data.invoice;
        stipend.value = res.data.stipend;
      } catch (error) {
        console.error('Error fetching stats:', error)
      }
    };

    onMounted(() => {
      fetchInvoiceStats()
    });
    return {
      fetchInvoiceStats, manageStipends, createInvoice, addExpense, InvoiceChart,stipend,
      totalInvoices, paidInvoices, overdueInvoices, chartData, invoice, today,formatDate
    };
  }
}
</script>
