<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InvoiceChart from '@/Components/InvoiceChart.vue';
import DashboardCard from '@/Components/DashboardCard.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
// import InvoiceChart from '@/Components/InvoiceChart.vue'
// import DashboardCard from '@/Components/DashboardCard.vue'

const totalInvoices = ref(0)
const paidInvoices = ref(0)
const overdueInvoices = ref(0)
const chartData = ref({ labels: [], datasets: [] })
const invoice = ref(null)
const stipend = ref(null)
const loading = ref(true)

const today = new Date().toISOString().split('T')[0]

const cardList = ref([
  { icon: 'hand-holding-dollar', title: 'Income', value: 'R12,345.67', color: 'success' },
  { icon: 'wallet', title: 'Expenses', value: 'R8,765.43', color: 'danger' },
  { icon: 'chart-line', title: 'Profit/Loss', value: 'R3,580.24', color: 'dark' }
])

const formatDate = (dateStr) => {
  return dateStr?.split('T')[0]
}

const createInvoice = () => router.visit('/invoices')
const addExpense = () => router.visit('/expenses/new')
const manageStipends = () => router.visit('/stipends')

const fetchInvoiceStats = async () => {
  try {
    const res = await axios.get('/api/dashboard')
    totalInvoices.value = res.data.totalInvoices
    paidInvoices.value = res.data.paidInvoices
    overdueInvoices.value = res.data.overdueInvoices
    chartData.value = {
      labels: res.data.labels,
      datasets: res.data.datasets
    }
    invoice.value = res.data.invoice
    stipend.value = res.data.stipend
  } catch (error) {
    console.error('Error fetching stats:', error)
  } finally {
    loading.value = false
  }
}

onMounted(fetchInvoiceStats)
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="py-2">
            <div class="mx-auto max-w-7x sm:px-6">
                <div class="space-y-6">
        <!-- Dashboard Cards -->
        <div class="row">
            <DashboardCard
                v-for="card in cardList"
                :key="card.title"
                :icon="card.icon"
                :title="card.title"
                :value="card.value"
                :color="card.color"
            />
        </div>

        <hr />

        <!-- Activity & Actions -->
        <div class="row">
                <div class="col-md-6">
                    <h3 class="text-outline">Recent Activity</h3>
                    <ul class="list-group text-info">
                        <li v-if="invoice"
                            :class="`${formatDate(invoice.updated_at) === today ? 'text-info' : ''} list-group-item`">
                            Invoice #{{ invoice.id }} sent to {{ invoice.customer?.name }}
                        </li>
                        <li class="list-group-item">Expense R500 recorded</li>
                        <li v-if="stipend"
                            :class="`${formatDate(stipend.updated_at) === today ? 'text-info' : ''} list-group-item`">
                            Stipend paid to {{ stipend.learner?.name }}

                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h3 class="text-outline">Quick Actions</h3>
                    <button class="btn btn-primary me-2" @click="createInvoice">New Invoice</button>
                    <button class="btn btn-secondary me-2" @click="addExpense">Add Expense</button>
                    <button class="btn btn-success" @click="manageStipends">Manage Stipends</button>
                </div>
            </div>

        <!-- Chart -->
        <div class="p-2 bg-white rounded shadow">
            <InvoiceChart
                :totalInvoices="totalInvoices"
                :paidInvoices="paidInvoices"
                :overdueInvoices="overdueInvoices"
                :chartData="chartData"
            />
        </div>
    </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

