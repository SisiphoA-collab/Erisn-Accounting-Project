<template>
  <!-- Flash message -->
  <div class="p-2">
    <FlashMessage :message="message" :messageType="messageType" @close="empty" @cleared="message = null" />
  </div>

  <div>
    <h1 class="text-outline">Stipends List</h1>
    <div class="mb-3">
      <p><strong>Paid Learners:</strong> {{ paidLearnersCount }}</p>
      <p><strong>Pending Learners:</strong> {{ pendingLearnersCount }}</p>
    </div>
    <hr />
   
    <button class="btn btn-primary mb-3 p-2" @click="addStipend">Add Learner Stipend</button>
    <button class="btn btn-secondary mb-3 p-2 mx-2" @click="triggerCSVInput">Import CSV</button>
    <input type="file" ref="csvInput" accept=".csv" style="display: none;" @change="importCSV" />
 <!-- Single instance of nav-pills -->
    <div class="mb-3">
      <ul class="nav nav-pills">
        <li class="nav-item" v-for="status in statuses" :key="status">
          <button
            class="nav-link"
            :class="{ 'active': selectedStatus === status }"
            @click="updateSelected(status)"
            :disabled="isLoading"
          >
            {{ status }}
          </button>
        </li>
      </ul>
    </div>
    <!-- Search Form and Status Filters in a single row -->
    <div class="d-flex flex-column flex-md-row mb-4">
      <!-- Status Filters -->
      <div class="flex-grow-1 mb-2 mb-md-0 me-md-2">
        <ul class="nav nav-pills">
          <!-- This instance is removed to avoid duplication -->
        </ul>
      </div>

      <!-- Search Form -->
      <form @submit.prevent="fetchStipend" class="flex-grow-1">
        <div class="input-group">
          <input
            key="search"
            v-model="searchQuery"
            type="text"
            placeholder="Search stipend by learner's name..."
            class="form-control inner-shadow"
          />
          <button type="submit" class="btn btn-primary">Search</button>
        </div>
      </form>
    </div>

    <!-- Display paid learners when 'Paid' is selected -->
    <div v-if="selectedStatus === 'Paid' && paidLearnersData.length > 0" class="mt-3">
      <h3>Paid Learners</h3>
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Learner ID</th>
            <th>Name</th>
            <th>Total Paid Amount</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="learner in paidLearnersData" :key="learner.learner_id">
            <td>{{ learner.learner_id }}</td>
            <td>{{ learner.learner_name }}</td>
            <td>${{ learner.total_amount.toFixed(2) }}</td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="2"><strong>Total Paid Amount:</strong></td>
            <td><strong>${{ totalPaidAmount.toFixed(2) }}</strong></td>
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- Existing stipend table for 'All' and 'Pending' -->
    <div v-else>
      <table class="table table-striped">
        <thead>
          <tr>
            <th v-for="column in columns" :key="column" class="text-uppercase">{{ column }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="stipend in stipends?.data || []" :key="stipend.id">
            <td>{{ stipend.id }}</td>
            <td>{{ stipend.learner?.name || 'Unknown' }}</td>
            <td>{{ stipend.amount }}</td>
            <td>{{ stipend.status }}</td>
            <td>{{ stipend.month }}</td>
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
            <td class="text-nowrap d-flex text-end small px-3 py-2">
              <div v-if="!stipend.receipt_path" class="d-flex flex-row">
                <input
                  type="file"
                  class="border form-control-sm mx-1"
                  @change="e => selectedFiles[stipend.id] = e.target.files[0]"
                />
                <button
                  @click="uploadReceipt(stipend.id, selectedFiles[stipend.id])"
                  class="btn btn-sm btn-primary"
                >
                  Upload Receipt
                </button>
              </div>
              <button v-else class="btn btn-sm btn-secondary" disabled>Uploaded</button>
            </td>
            <td>
              <button class="btn btn-sm btn-primary ms-1" @click="editStipend(stipend.id)">Edit</button>
              <button class="btn btn-sm btn-danger ms-1" @click="delStipend(stipend)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <Pagination :links="stipends.links" :fetchData="fetchStipend" />
  </div>

  <!-- Stipend Modal -->
  <div
    class="modal"
    tabindex="-1"
    :class="{ 'd-block': showModal }"
    style="background-color: rgba(0,0,0,0.5);"
  >
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ isEdit ? 'Edit Learner Stipend' : 'Add Learner Stipend' }}</h5>
          <button type="button" class="btn-close" @click="closeModal"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="handleSubmit">
            <div class="mb-3">
              <label class="form-label">Learner</label>
              <select
                class="form-select selectpicker"
                data-live-search="true"
                v-model="form.learner_id"
                :disabled="isEdit"
              >
                <option value="" disabled>Select learner...</option>
                <option v-for="learner in learners" :key="learner.id" :value="learner.id">
                  {{ !isEdit ? learner.id : form.id }} - {{ learner.name }}
                </option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Amount</label>
              <input type="text" class="form-control" v-model="form.amount" required />
            </div>

            <div class="mb-3">
              <label class="form-label">Status</label>
              <select class="form-select" v-model="form.status" required>
                <option v-for="status in statusOption" :key="status" :value="status">{{ status }}</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Month</label>
              <select class="form-select" v-model="form.month" required>
                <option v-for="month in months" :key="month" :value="month + ' ' + currentYear">
                  {{ month }} {{ currentYear }}
                </option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Upload Receipt:</label>
              <div v-if="form.status === 'Paid'" class="d-flex flex-row">
                <input
                  type="file"
                  class="border form-control-sm mx-1"
                  @change="e => selectedFiles[form.id] = e.target.files[0]"
                  required
                />
              </div>
              <p v-else class="form-control-sm p-2 text-muted border" disabled>
                Not uploaded - <span class="text-muted fst-italic">change status to upload receipt.</span>
              </p>
            </div>

            <div class="mt-5 p-2 d-flex">
              <div class="flex-grow-1">
                <button type="button" class="btn btn-secondary ms-2" @click="closeModal">Cancel</button>
              </div>
              <button type="submit" class="btn btn-primary">{{ isEdit ? 'Update' : 'Save' }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Delete Stipend Modal -->
  <div
    class="modal"
    tabindex="-1"
    :class="{ 'd-block': showConfModal }"
    style="background-color: rgba(0,0,0,0.5);"
  >
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <i class="fas fa-warning px-2 display-5"></i>
          <h5 class="modal-title">WARNING</h5>
          <button type="button" class="btn-close" aria-label="Close" @click="closeConfModal"></button>
        </div>
        <div class="modal-body">
          <p>
            Are you sure you want to delete stipend id number
            <strong>#{{ selectedStipend?.id }}</strong>?
          </p>
        </div>
        <div class="modal-footer justify-content-end">
          <button type="button" class="btn btn-outline-secondary text-secondary mx-4" @click="closeConfModal">
            Cancel
          </button>
          <button
            type="button"
            class="btn btn-outline-danger text-danger mx-4"
            @click="deleteStipend(selectedStipend?.id)"
          >
            Yes, Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, watchEffect } from 'vue';
import axios from 'axios';
import Pagination from '../Components/Pagination.vue';
import FlashMessage from '../Components/FlashMessage.vue';

export default {
  components: { Pagination, FlashMessage },
  setup() {
    const stipends = ref([]);
    const currentPage = ref(1);
    const learners = ref([]);
    const statusOption = ['Pending', 'Paid'];
    const showModal = ref(false);
    const isEdit = ref(false);
    const links = ref([]);
    const selectedStatus = ref('All');
    const searchQuery = ref('');
    const message = ref('');
    const messageType = ref('');
    const showConfModal = ref(false);
    const columns = ['ID', "Learner's", 'Amount', 'Status', 'Month', 'Receipt', '', 'Action'];
    const months = [
      'January',
      'February',
      'March',
      'April',
      'May',
      'June',
      'July',
      'August',
      'September',
      'October',
      'November',
      'December',
    ];
    const currentYear = new Date().getFullYear();
    const currentMonth = new Date().toLocaleString('en-US', { month: 'long' });
    const selectedStipend = ref(null);
    const upload_receipt = ref(null);
    const selectedFiles = ref({});
    const csvInput = ref(null);
    const isLoading = ref(false);
    const paidLearnersCount = ref(0);
    const pendingLearnersCount = ref(0);
    const paidLearnersData = ref([]);
    const totalPaidAmount = ref(0);

    // Trigger the hidden file input
    const triggerCSVInput = () => {
      csvInput.value.click();
    };

    // Handle CSV file import
    const importCSV = async (event) => {
      const file = event.target.files[0];
      if (!file) {
        message.value = 'Please select a CSV file to import.';
        messageType.value = 'error';
        scrollToTop();
        return;
      }

      // Validate file type
      if (!file.name.endsWith('.csv')) {
        message.value = 'Please select a valid CSV file.';
        messageType.value = 'error';
        scrollToTop();
        return;
      }

      const formData = new FormData();
      formData.append('csv_file', file);

      try {
        const response = await axios.post('/api/stipends/import', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
            // Uncomment if auth:sanctum is required
            // 'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
          },
        });

        message.value = response.data.message || 'CSV imported successfully.';
        messageType.value = response.data.type || 'success';
        fetchStipend(); // Refresh the stipend list
        scrollToTop();

        // Reset the file input
        csvInput.value.value = '';
      } catch (error) {
        message.value = error.response?.data?.message || 'Error importing CSV.';
        messageType.value = error.response?.data?.type || 'error';
        scrollToTop();
      }
    };

    const scrollToTop = () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    const form = ref({
      id: null,
      learner_id: '',
      amount: '',
      status: 'Draft',
      month: currentMonth,
      receipt_path: '',
    });

    const handleSubmit = () => {
      if (isEdit.value) {
        updateStipend();
      } else {
        saveStipend();
      }
      if (form.value.status === 'Paid' && selectedFiles.value[form.value.id]) {
        uploadReceipt(form.value.id, selectedFiles.value[form.value.id]);
      }
      fetchStipend();
    };

    // Function to handle file uploads
    const uploadReceipt = async (stipendId, file) => {
      if (!file) {
        message.value = 'Please select a file to upload.';
        messageType.value = 'error';
        scrollToTop();
        return;
      }

      const formData = new FormData();
      formData.append('receipt', file);

      try {
        const response = await axios.post(`/api/stipends/${stipendId}/upload`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        });

        message.value = response.data.message || 'Receipt uploaded successfully.';
        messageType.value = response.data.type || 'success';
        scrollToTop();
        fetchStipend();
      } catch (error) {
        console.error('Error uploading Receipt:', error);
        message.value = 'Error uploading receipt.';
        messageType.value = 'error';
        scrollToTop();
      }
    };

    const updateSelected = (status) => {
      selectedStatus.value = status;
      fetchStipend();
      message.value = `Showing stipends with status: ${status === 'All' ? 'All' : status}.`;
      messageType.value = 'info';
      scrollToTop();

      if (status === 'Paid') {
        message.value = `Displaying ${paidLearnersData.value.length} paid learners with a total of $${totalPaidAmount.value.toFixed(2)}.`;
        messageType.value = 'success';
      }
    };

    const fetchStipend = async (page = currentPage.value) => {
      try {
        const params = { page };
        if (selectedStatus.value && selectedStatus.value !== 'All') {
          params.status = selectedStatus.value;
        }
        if (searchQuery.value) {
          params.search = searchQuery.value;
        }

        const res = await axios.get(`/api/stipends`, { params });
        stipends.value = res.data.stipends || [];
        links.value = res.data.links || [];
        learners.value = res.data.learners || [];
        paidLearnersCount.value = res.data.stats?.paid_learners || 0;
        pendingLearnersCount.value = res.data.stats?.pending_learners || 0;
        paidLearnersData.value = res.data.paid_learners_data || []; // Update paid learners list
        totalPaidAmount.value = res.data.total_paid_amount || 0;
      } catch (error) {
        console.error('Error fetching stipends:', error.response ? error.response.data : error);
        message.value = error.response?.data?.message || 'Error fetching stipends.';
        messageType.value = 'error';
        scrollToTop();
      }
    };

    const changePage = (page) => {
      currentPage.value = page;
      fetchStipend(page);
    };

    const editStipend = (id) => {
      const stipend = stipends.value?.data?.find((i) => i.id === id);
      if (stipend) {
        form.value = { ...stipend };
        isEdit.value = true;
        showModal.value = true;
      } else {
        console.warn('Stipend not found:', id);
      }
    };

    const delStipend = (stipend) => {
      if (stipend) {
        selectedStipend.value = stipend;
        showConfModal.value = true;
      } else {
        console.warn('Stipend not found:', stipend.id);
      }
    };

    const addStipend = () => {
      resetForm();
      isEdit.value = false;
      showModal.value = true;
    };

    const saveStipend = async () => {
      try {
        const res = await axios.post('/api/stipends', form.value);
        fetchStipend();
        message.value = res.data.message || 'Stipend saved successfully.';
        messageType.value = res.data.type || 'success';
        closeModal();
        scrollToTop();
      } catch (error) {
        message.value = 'Error saving stipend: ' + (error.response?.data?.message || error.message);
        messageType.value = 'error';
        scrollToTop();
        closeModal();
      }
    };

    const updateStipend = async () => {
      try {
        const res = await axios.put(`/api/stipends/${form.value.id}`, form.value);
        message.value = res.data.message || 'Stipend updated successfully.';
        messageType.value = res.data.type || 'success';
        fetchStipend();
        closeModal();
        scrollToTop();
      } catch (error) {
        message.value = res.data.message || 'Error updating stipend: ' + error.message;
        messageType.value = res.data.type || 'error';
        closeModal();
        scrollToTop();
      }
    };

    const deleteStipend = async (id) => {
      try {
        const res = await axios.delete(`/api/stipends/${id}`);
        message.value = res.data.message || 'Stipend deleted successfully.';
        messageType.value = res.data.type || 'success';
        fetchStipend();
        showConfModal.value = false;
        scrollToTop();
      } catch (error) {
        message.value = 'Error deleting stipend: ' + error.message;
        messageType.value = 'error';
        showConfModal.value = false;
        scrollToTop();
      }
    };

    const closeModal = () => {
      showModal.value = false;
      selectedStipend.value = null;
    };

    const closeConfModal = () => {
      showConfModal.value = false;
      resetForm();
    };

    const resetForm = () => {
      form.value = {
        id: null,
        learner_id: '',
        amount: '',
        status: 'Pending',
        month: '',
        receipt_path: '',
      };
    };

    onMounted(() => {
      fetchStipend();
    });

    watchEffect(() => {
      setTimeout(() => {
        message.value = '';
      }, 3000);
    });

    const empty = () => {
      message.value = '';
    };

    return {
      stipends,
      currentPage,
      learners,
      statusOption,
      showModal,
      isEdit,
      links,
      delStipend,
      columns,
      upload_receipt,
      handleSubmit,
      form,
      searchQuery,
      statuses: ['All', ...statusOption],
      selectedStatus,
      message,
      messageType,
      selectedFiles,
      currentMonth,
      updateSelected,
      fetchStipend,
      changePage,
      addStipend,
      editStipend,
      saveStipend,
      empty,
      uploadReceipt,
      months,
      importCSV,
      updateStipend,
      deleteStipend,
      closeModal,
      resetForm,
      closeConfModal,
      showConfModal,
      selectedStipend,
      currentYear,
      csvInput,
      triggerCSVInput,
      importCSV,
      paidLearnersCount,
      pendingLearnersCount,
      paidLearnersData,
      totalPaidAmount,
    };
  },
};
</script>