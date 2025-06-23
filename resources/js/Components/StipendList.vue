<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

// Get CSRF token directly from the DOM
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content')

// Local state
const stipends = ref([])
const columns = ['ID', 'Learner', 'Amount', 'Month', 'Status', 'Receipt', '']
const selectedStatus = ref('All')

const fetchStipends = async () => {
  try {
    const response = await axios.get('/api/stipends')
    stipends.value = response.data
  } catch (error) {
    console.error('Error fetching stipends:', error)
  }
}

const filterStatus = () => {
  if (selectedStatus.value === 'All') return stipends.value
  return stipends.value.filter(item => item.status === selectedStatus.value)
}

onMounted(() => {
  fetchStipends()
})
</script>


<template>
  <div>
    <!-- Stipends Table -->
    <div class="table-responsive bg-white shadow rounded">
      <table class="table w-100">
        <thead class="table-light">
          <tr>
            <th v-for="column in columns" :key="column" class="text-start small text-muted text-uppercase px-3 py-2">
              {{ column }}
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="stipend in stipends.data" :key="stipend.id">
            <td class="text-nowrap small text-dark px-3 py-2">{{ stipend.id }}</td>
            <td class="text-nowrap small text-dark px-3 py-2">
              {{ stipend.learner ? stipend.learner.name : 'No Learner Assigned' }}
            </td>
            <td class="text-nowrap small text-dark px-3 py-2">R{{ stipend.amount }}</td>
            <td class="text-nowrap small text-dark px-3 py-2">{{ stipend.month }}</td>
            <td class="text-nowrap small px-3 py-2">
              <!-- Empty cell or add status here -->
            </td>
            <td class="text-nowrap small px-3 py-2">
              <a
                v-if="stipend.receipt_path"
                :href="`/storage/${stipend.receipt_path}`"
                target="_blank"
                class="text-primary text-decoration-underline"
              >
                View Receipt
              </a>
              <span v-else class="text-muted fst-italic">Not Uploaded</span>
            </td>
            <td class="text-nowrap text-end small px-3 py-2">
              <!-- If receipt is not uploaded, show a file upload form -->
              <form
                v-if="!stipend.receipt_path"
                :action="route('stipend.upload', stipend.id)"
                method="POST"
                enctype="multipart/form-data"
              >
                <input type="hidden" name="_token" :value="csrfToken" />
                <input type="file" name="receipt" class="form-control form-control-sm mb-2" />
                <button type="submit" class="btn btn-sm btn-primary">
                  Upload Receipt
                </button>
              </form>

              <!-- Else show that receipt is already uploaded -->
              <button v-else class="btn btn-sm btn-secondary" disabled>
                Uploaded
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
