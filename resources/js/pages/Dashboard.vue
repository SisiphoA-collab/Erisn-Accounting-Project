<template>
    <div class="flex-grow-1 w-100">
        <div>
            <!-- <h1 class="">Dashboard</h1> -->
            <div class="row">
                <!-- <div class="col-md-4">
                    <div class="d-flex gap-3">
                        <div class=" text-success card flex-grow-1 rounded-2 p-1 position-relative overflow-hidden">
                            <h1 class="card-title">Income</h1>
                            <p class="card-text">R12,345.67</p>

                            <div class="position-absolute top-0 end-0 opacity-25 display-1">
                                <span class="pe-2"><i class="fa fa-hand-holding-dollar" /></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-3">
                        <div class=" text-danger card flex-grow-1 rounded-2 p-1 position-relative overflow-hidden">
                            <h1 class="card-title">Expenses</h1>
                            <p class="card-text">R8,765.43</p>

                            <div class="position-absolute top-0 end-0 opacity-25 display-1">
                                <span class="pe-2"><i class="fa fa-wallet" /></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-3">
                        <div class="card flex-grow-1 rounded-2 p-1 position-relative overflow-hidden">
                            <h1 class="card-title">Profit/Loss</h1>
                            <p class="card-text">R3,580.24</p>

                            <div class="position-absolute top-0 end-0 opacity-25 display-1">
                                <span class="pe-2"><i class="fa fa-chart-line" /></span>
                            </div>
                        </div>
                    </div>
                </div> -->


                <DashboardCard v-for="card in cardList" :key="card.title" :icon="card.icon" :title="card.title"
                    :value="card.value" :color="card.color" />
            </div>
            <hr />
            <div class="row">
                <div class="col-md-6">
                    <h3 class="text-outline">Recent Activity</h3>
                    <ul class="list-group text-info">
                        <li v-if="invoice"
                            :class="`${formatDate(invoice.updated_at) === today ? 'text-info' : ''} list-group-item`">
                            Invoice #{{ invoice.id }} sent to {{ invoice.customer?.name }}
                        </li>
                        <li class="list-group-item">Expense $500 recorded</li>
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
            <div class="p-2">
                <InvoiceChart :totalInvoices="totalInvoices" :paidInvoices="paidInvoices"
                    :overdueInvoices="overdueInvoices" :chartData="chartData" />
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import InvoiceChart from '../Components/InvoiceChart.vue';
import DashboardCard from '../Components/DashboardCard.vue';

export default {
    components: { InvoiceChart, DashboardCard },
    setup() {
        const totalInvoices = ref(0);
        const paidInvoices = ref(null);
        const overdueInvoices = ref(null);
        const chartData = ref({ labels: [], datasets: [] });
        const invoice = ref(null);
        const stipend = ref(null);
        const today = new Date().toISOString().split('T')[0];
        const cardList = ref([
            { icon: 'hand-holding-dollar', title: 'Income', value: 'R12,345.67', color: 'success' },
            { icon: 'wallet', title: 'Expenses', value: 'R8,765.43', color: 'danger' },
            { icon: 'chart-line', title: 'Profit/Loss', value: 'R3,580.24', color: 'dark' }
        ]);
        const route = useRouter();

        //`updated_at` is ISO format (e.g., "2025-06-21T12:00:00Z")
        const formatDate = (dateStr) => {
            return dateStr.split('T')[0];
        };
        const createInvoice = () => {
            route.push('/invoices');
        };
        const addExpense = () => {
            alert('Redirect to add expense page');
            route.push('/expenses/new');
        };
        const manageStipends = () => {
            route.push('/stipends');
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
            fetchInvoiceStats, manageStipends, createInvoice, addExpense, InvoiceChart, stipend,cardList,
            totalInvoices, paidInvoices, overdueInvoices, chartData, invoice, today, formatDate,DashboardCard
        };
    }
}
</script>
