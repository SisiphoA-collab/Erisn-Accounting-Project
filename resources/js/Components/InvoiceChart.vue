<template>
  <hr />
  <div class="container rounded">
    <h4 class="text-center text-muted fw-bold ">Invoices Overview</h4>
    <div class="container">
      <div class="row">
        <!-- Invoice Bar Graph -->
        <div class="col-8 d-flex justify-content-center align-items-center">
          <div class="d-flex p-4 rounded" style="height: 400px;">
            <canvas ref="invoiceChart"></canvas>
          </div>
        </div>

        <!-- Invoice stats cards  -->
        <div class="col">
          <div class="container items-center">
            <div class="col">
              <div class="row mb-3">
                <!-- Total Invoices -->
                <StatCard icon="file-invoice-dollar" title="Total Invoices"
                  description="Number of invoices issued this month" :value="props.totalInvoices" color="custom-blue" />
              </div>
              <div class="row mb-3">
                <!-- Total Revenue -->
                <StatCard icon="dollar-sign" title="Total Revenue" description="Summarize payments received"
                  :value="props.paidInvoices" color="success" />
              </div>
              <div class="row mb-3">
                <!-- Outstanding Amounts -->
                <StatCard icon="exclamation-circle" title="Outstanding Amount"
                  description="Show unpaid invoices & overdue amounts" :value="props.overdueInvoices" color="danger" />
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import StatCard from '@/Components/StatCard.vue'
import { Chart } from 'chart.js/auto'

const props = defineProps({
  totalInvoices: {
    type: Number,
    default: 0
  },
  paidInvoices: String,
  overdueInvoices: String,
  chartData: {
    type: Object,
    required: true
  }
});
const chartInstance = ref(null);
const invoiceChart = ref(null);

const renderChart = () => {
  const ctx = invoiceChart.value.getContext('2d')
  if (!ctx) return

  // delete old chart if it exists
  if (chartInstance.value) {
    chartInstance.value.destroy()
    chartInstance.value = null
  }

  // Create a new chart
  chartInstance.value = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: props.chartData.labels,
      datasets: props.chartData.datasets.map(set => ({
        ...set,
        data: props.chartData.labels.map(m => parseFloat(set.data[m]) || 0)
      }))
    },
    options: {
      responsive: true,
      scales: {
        x: {
          title: {
            display: true,
            text: 'Months',
            font: {
              family: 'Times New Roman',
              size: 16,
              weight: 'bold'
            },
            padding: { top: 10 },
            color: '#000'
          }
        },
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Invoice Total (ZAR)',
            font: {
              family: 'Times New Roman',
              size: 16,
              weight: 'bold'
            },
            padding: { left: 100 },
            color: '#000'
          }
        }
      },
      plugins: {
        legend: {
          position: 'top',
        },
        title: {
          display: true,
          text: 'Monthly Invoice Totals by Status',
          font: {
            family: 'Arial',
            size: 18,
            weight: 'bold',
            style: 'italic',
          },
          padding: { left: 100 },
          color: '#000'
        }
      }
    }
  })
}


// onMounted(async () => {
//   await nextTick() // ensure DOM is ready
//   renderChart()
// })
onMounted(() => {
  setTimeout(() => {
    renderChart()
  }, 1000)
})
</script>
