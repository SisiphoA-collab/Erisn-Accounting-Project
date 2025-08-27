<template>
  <!-- Flash message -->
  <div class="p-2">
    <FlashMessage :message="message" :messageType="messageType" @close="empty" @cleared="message = null" />
  </div>

  <div>
    <h1 class="text-outline">Banking Management</h1>
    <div class="mb-3">
      <p><strong>Active Accounts:</strong> {{ activeAccountsCount }}</p>
      <p><strong>Pending Reconciliations:</strong> {{ pendingReconciliationsCount }}</p>
    </div>
    <hr />
    <!-- Bank Account Management -->
    <div class="mb-3">
      <button class="btn btn-primary mb-3 p-2" @click="addAccount">Add Bank Account</button>
      <button class="btn btn-secondary mb-3 p-2 mx-2" @click="triggerReconciliation">Manual Reconciliation</button>
      <button class="btn btn-info mb-3 p-2 mx-2" @click="addTransaction">Log Transaction</button>
      <button class="btn btn-secondary mb-3 p-2 mx-2" @click="triggerCSVInput">Import Statements (CSV)</button>
      <input type="file" ref="csvInput" accept=".csv" style="display: none;" @change="importCSV" />
    </div>

    <!-- Filters -->
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

    <!-- Search Form -->
    <div class="d-flex flex-column flex-md-row mb-4">
      <form @submit.prevent="fetchBankingData" class="flex-grow-1">
        <div class="input-group">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search by account or transaction..."
            class="form-control inner-shadow"
          />
          <button type="submit" class="btn btn-primary">Search</button>
        </div>
      </form>
    </div>

    <!-- Enhanced Tab Buttons -->
    <div class="mb-3">
      <button
        class="btn btn-outline-primary me-2"
        :class="{ 'active': currentTab === 'accounts' }"
        @click="switchTab('accounts')"
      >
        Accounts
      </button>
      <button
        class="btn btn-outline-primary me-2"
        :class="{ 'active': currentTab === 'transactions' }"
        @click="switchTab('transactions')"
      >
        Transactions
      </button>
      <button
        class="btn btn-outline-primary"
        :class="{ 'active': currentTab === 'reconciliations' }"
        @click="switchTab('reconciliations')"
      >
        Reconciliations
      </button>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
      <!-- Accounts Tab -->
      <div class="tab-pane fade show active" id="accounts" role="tabpanel" aria-labelledby="accounts-tab" v-show="currentTab === 'accounts'">
        <table class="table table-striped">
          <thead>
            <tr>
              <th v-for="column in accountColumns" :key="column" class="text-uppercase">{{ column }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="account in accounts?.data || []" :key="account.id">
              <td>{{ account.id }}</td>
              <td>{{ account.account_number }}</td>
              <td>{{ account.account_holder }}</td>
              <td>{{ account.account_type }}</td>
              <td>
                <button class="btn btn-sm btn-primary ms-1" @click="editAccount(account.id)">Edit</button>
                <button class="btn btn-sm btn-danger ms-1" @click="delAccount(account)">Delete</button>
              </td>
            </tr>
            <tr v-if="!(accounts?.data && accounts.data.length)">
              <td colspan="5">No accounts found.</td>
            </tr>
          </tbody>
        </table>
        <Pagination :links="accounts?.links || []" :fetchData="fetchBankingData" />
      </div>

      <!-- Transactions Tab -->
      <div class="tab-pane fade" id="transactions" role="tabpanel" aria-labelledby="transactions-tab" v-show="currentTab === 'transactions'">
        <table class="table table-striped">
          <thead>
            <tr>
              <th v-for="column in transactionColumns" :key="column" class="text-uppercase">{{ column }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="transaction in transactions?.data || []" :key="transaction.id">
              <td>{{ transaction.id }}</td>
              <td>{{ transaction.bankAccount?.account_holder || 'N/A' }}</td>
              <td>{{ transaction.amount }}</td>
              <td>{{ transaction.status }}</td>
              <td>{{ transaction.date }}</td>
              <td>
                <button class="btn btn-sm btn-primary ms-1" @click="editTransaction(transaction.id)">Edit</button>
                <button class="btn btn-sm btn-danger ms-1" @click="delTransaction(transaction)">Delete</button>
              </td>
            </tr>
            <tr v-if="!(transactions?.data && transactions.data.length)">
              <td colspan="6">No transactions found.</td>
            </tr>
          </tbody>
        </table>
        <Pagination :links="transactions?.links || []" :fetchData="fetchBankingData" />
      </div>

      <!-- Reconciliations Tab -->
      <div class="tab-pane fade" id="reconciliations" role="tabpanel" aria-labelledby="reconciliations-tab" v-show="currentTab === 'reconciliations'">
        <table class="table table-striped">
          <thead>
            <tr>
              <th v-for="column in reconciliationColumns" :key="column" class="text-uppercase">{{ column }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="reconciliation in reconciliations?.data || []" :key="reconciliation.id">
              <td>{{ reconciliation.id }}</td>
              <td>{{ reconciliation.bankAccount?.account_holder || 'N/A' }}</td>
              <td>{{ reconciliation.date }}</td>
              <td>{{ reconciliation.status }}</td>
              <td>{{ reconciliation.notes || 'N/A' }}</td>
              <td>
                <button class="btn btn-sm btn-primary ms-1" @click="editReconciliation(reconciliation.id)">Edit</button>
                <button class="btn btn-sm btn-danger ms-1" @click="delReconciliation(reconciliation)">Delete</button>
              </td>
            </tr>
            <tr v-if="!(reconciliations?.data && reconciliations.data.length)">
              <td colspan="6">No reconciliations found.</td>
            </tr>
          </tbody>
        </table>
        <Pagination :links="reconciliations?.links || []" :fetchData="fetchBankingData" />
      </div>
    </div>
  </div>

  <!-- Account Modal -->
  <div class="modal" tabindex="-1" :class="{ 'd-block': showAccountModal }" style="background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ isEditAccount ? 'Edit Account' : 'Add Account' }}</h5>
          <button type="button" class="btn-close" @click="closeAccountModal"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="handleAccountSubmit">
            <div class="mb-3">
              <label class="form-label">Account Number</label>
              <input type="text" class="form-control" v-model="accountForm.account_number" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Account Holder</label>
              <input type="text" class="form-control" v-model="accountForm.account_holder" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Balance</label>
              <input type="number" step="0.01" class="form-control" v-model="accountForm.balance" required />
            </div>
            <div class="mt-5 p-2 d-flex">
              <div class="flex-grow-1">
                <button type="button" class="btn btn-secondary ms-2" @click="closeAccountModal">Cancel</button>
              </div>
              <button type="submit" class="btn btn-primary">{{ isEditAccount ? 'Update' : 'Save' }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Transaction Modal -->
  <div class="modal" tabindex="-1" :class="{ 'd-block': showTransactionModal }" style="background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ isEditTransaction ? 'Edit Transaction' : 'Log Transaction' }}</h5>
          <button type="button" class="btn-close" @click="closeTransactionModal"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="handleTransactionSubmit">
            <div class="mb-3">
              <label class="form-label">Account</label>
              <select class="form-select" v-model="transactionForm.bank_account_id" required>
                <option v-for="account in accounts?.data || []" :key="account.id" :value="account.id">{{ account.account_holder }}</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Amount</label>
              <input type="number" step="0.01" class="form-control" v-model="transactionForm.amount" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Status</label>
              <select class="form-select" v-model="transactionForm.status" required>
                <option v-for="status in statusOptions" :key="status" :value="status">{{ status }}</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Date</label>
              <input type="date" class="form-control" v-model="transactionForm.date" required />
            </div>
            <div class="mt-5 p-2 d-flex">
              <div class="flex-grow-1">
                <button type="button" class="btn btn-secondary ms-2" @click="closeTransactionModal">Cancel</button>
              </div>
              <button type="submit" class="btn btn-primary">{{ isEditTransaction ? 'Update' : 'Save' }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Reconciliation Modal -->
  <div class="modal" tabindex="-1" :class="{ 'd-block': showReconciliationModal }" style="background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ isEditReconciliation ? 'Edit Reconciliation' : 'Manual Reconciliation' }}</h5>
          <button type="button" class="btn-close" @click="closeReconciliationModal"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="handleReconciliationSubmit">
            <div class="mb-3">
              <label class="form-label">Account</label>
              <select class="form-select" v-model="reconciliationForm.bank_account_id" required>
                <option v-for="account in accounts?.data || []" :key="account.id" :value="account.id">{{ account.account_holder }}</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Date</label>
              <input type="date" class="form-control" v-model="reconciliationForm.date" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Status</label>
              <select class="form-select" v-model="reconciliationForm.status" required>
                <option v-for="status in reconciliationStatuses" :key="status" :value="status">{{ status }}</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Notes</label>
              <textarea class="form-control" v-model="reconciliationForm.notes" rows="3"></textarea>
            </div>
            <div class="mt-5 p-2 d-flex">
              <div class="flex-grow-1">
                <button type="button" class="btn btn-secondary ms-2" @click="closeReconciliationModal">Cancel</button>
              </div>
              <button type="submit" class="btn btn-primary">{{ isEditReconciliation ? 'Update' : 'Save' }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Delete Modals -->
  <div class="modal" tabindex="-1" :class="{ 'd-block': showConfModal }" style="background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <i class="fas fa-warning px-2 display-5"></i>
          <h5 class="modal-title">WARNING</h5>
          <button type="button" class="btn-close" aria-label="Close" @click="closeConfModal"></button>
        </div>
        <div class="modal-body">
          <p>
            Are you sure you want to delete
            <strong>{{ selectedItemType }} #{{ selectedItem?.id }}</strong>?
          </p>
        </div>
        <div class="modal-footer justify-content-end">
          <button type="button" class="btn btn-outline-secondary text-secondary mx-4" @click="closeConfModal">
            Cancel
          </button>
          <button
            type="button"
            class="btn btn-outline-danger text-danger mx-4"
            @click="deleteItem(selectedItem?.id)"
          >
            Yes, Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import Pagination from '../Components/Pagination.vue';
import FlashMessage from '../Components/FlashMessage.vue';

export default {
  components: { Pagination, FlashMessage },
  setup() {
    const accounts = ref({});
    const transactions = ref({});
    const reconciliations = ref({});
    const currentPage = ref(1);
    const showAccountModal = ref(false);
    const showTransactionModal = ref(false);
    const showReconciliationModal = ref(false);
    const isEditAccount = ref(false);
    const isEditTransaction = ref(false);
    const isEditReconciliation = ref(false);
    const links = ref([]);
    const selectedStatus = ref('All');
    const searchQuery = ref('');
    const message = ref('');
    const messageType = ref('');
    const showConfModal = ref(false);
    const selectedItem = ref(null);
    const selectedItemType = ref('');
    const csvInput = ref(null);
    const isLoading = ref(false);
    const activeAccountsCount = ref(0);
    const pendingReconciliationsCount = ref(0);
    const currentTab = ref('accounts');

    const accountColumns = ['ID', 'Account Number', 'Account Holder', 'Account Type', 'Action'];
    const transactionColumns = ['ID', 'Account Holder', 'Amount', 'Status', 'Date', 'Action'];
    const reconciliationColumns = ['ID', 'Account Holder', 'Date', 'Status', 'Notes', 'Action'];
    const statuses = ['All', 'Active', 'Inactive'];
    const statusOptions = ['Pending', 'Processed'];
    const reconciliationStatuses = ['Pending', 'Completed', 'Failed'];

    const accountForm = ref({
      id: null,
      account_number: '',
      account_holder: '',
      balance: 0,
    });

    const transactionForm = ref({
      id: null,
      bank_account_id: '',
      amount: '',
      status: 'Pending',
      date: '',
    });

    const reconciliationForm = ref({
      id: null,
      bank_account_id: '',
      date: '',
      status: 'Pending',
      notes: '',
    });

    const switchTab = (tab) => {
  currentTab.value = tab;
  console.log('Switched to tab:', tab); // Debug log
  fetchBankingData(); // Fetch data when switching tabs
};

    const triggerCSVInput = () => {
      csvInput.value.click();
    };

    const importCSV = async (event) => {
      const file = event.target.files[0];
      if (!file || !file.name.endsWith('.csv')) {
        message.value = 'Please select a valid CSV file.';
        messageType.value = 'error';
        scrollToTop();
        return;
      }

      const formData = new FormData();
      formData.append('csv_file', file);

      try {
        const response = await axios.post('/api/banking/import', formData, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });
        message.value = response.data.message || 'Statements imported successfully.';
        messageType.value = 'success';
        fetchBankingData();
        csvInput.value.value = '';
      } catch (error) {
        message.value = error.response?.data?.message || 'Error importing statements.';
        messageType.value = 'error';
      }
      scrollToTop();
    };

    const scrollToTop = () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    const handleAccountSubmit = () => {
      if (isEditAccount.value) updateAccount();
      else saveAccount();
      closeAccountModal();
      fetchBankingData();
    };

    const handleTransactionSubmit = () => {
      if (isEditTransaction.value) updateTransaction();
      else saveTransaction();
      closeTransactionModal();
      fetchBankingData();
    };

    const handleReconciliationSubmit = () => {
      if (isEditReconciliation.value) updateReconciliation();
      else saveReconciliation();
      closeReconciliationModal();
      fetchBankingData();
    };

    const fetchBankingData = async (page = currentPage.value) => {
      try {
        isLoading.value = true;
        const params = { page };
        if (selectedStatus.value && selectedStatus.value !== 'All') {
          params.status = selectedStatus.value;
        }
        if (searchQuery.value) {
          params.search = searchQuery.value;
        }

        const res = await axios.get('/api/banking', { params });
        accounts.value = res.data.accounts || { data: [], links: [] };
        transactions.value = res.data.transactions || { data: [], links: [] };
        reconciliations.value = res.data.reconciliations || { data: [], links: [] };
        links.value = accounts.value.links || [];
        activeAccountsCount.value = res.data.stats?.active_accounts || 0;
        pendingReconciliationsCount.value = res.data.stats?.pending_reconciliations || 0;
      } catch (error) {
        console.error('Error fetching banking data:', error.response ? error.response.data : error);
        message.value = error.response?.data?.message || 'Error fetching banking data.';
        messageType.value = 'error';
        scrollToTop();
      } finally {
        isLoading.value = false;
      }
    };

    const changePage = (page) => {
      currentPage.value = page;
      fetchBankingData(page);
    };

    const addAccount = () => {
      accountForm.value = { id: null, account_number: '', account_holder: '', balance: 0 };
      isEditAccount.value = false;
      showAccountModal.value = true;
    };

    const editAccount = (id) => {
      const account = accounts.value.data.find((a) => a.id === id);
      if (account) {
        accountForm.value = { ...account };
        isEditAccount.value = true;
        showAccountModal.value = true;
      }
    };

    const delAccount = (account) => {
      selectedItem.value = account;
      selectedItemType.value = 'Account';
      showConfModal.value = true;
    };

    const saveAccount = async () => {
      try {
        const res = await axios.post('/api/banking/accounts', accountForm.value);
        message.value = res.data.message || 'Account added successfully.';
        messageType.value = 'success';
      } catch (error) {
        message.value = 'Error adding account: ' + (error.response?.data?.message || error.message);
        messageType.value = 'error';
      }
    };

    const updateAccount = async () => {
      try {
        const res = await axios.put(`/api/banking/accounts/${accountForm.value.id}`, accountForm.value);
        message.value = res.data.message || 'Account updated successfully.';
        messageType.value = 'success';
      } catch (error) {
        message.value = 'Error updating account: ' + (error.response?.data?.message || error.message);
        messageType.value = 'error';
      }
    };

    const addTransaction = () => {
      transactionForm.value = { id: null, bank_account_id: '', amount: '', status: 'Pending', date: '' };
      isEditTransaction.value = false;
      showTransactionModal.value = true;
    };

    const editTransaction = (id) => {
      const transaction = transactions.value.data.find((t) => t.id === id);
      if (transaction) {
        transactionForm.value = { ...transaction, bank_account_id: transaction.bank_account_id };
        isEditTransaction.value = true;
        showTransactionModal.value = true;
      }
    };

    const delTransaction = (transaction) => {
      selectedItem.value = transaction;
      selectedItemType.value = 'Transaction';
      showConfModal.value = true;
    };

    const saveTransaction = async () => {
      try {
        const res = await axios.post('/api/banking/transactions', transactionForm.value);
        message.value = res.data.message || 'Transaction logged successfully.';
        messageType.value = 'success';
      } catch (error) {
        message.value = 'Error logging transaction: ' + (error.response?.data?.message || error.message);
        messageType.value = 'error';
      }
    };

    const updateTransaction = async () => {
      try {
        const res = await axios.put(`/api/banking/transactions/${transactionForm.value.id}`, transactionForm.value);
        message.value = res.data.message || 'Transaction updated successfully.';
        messageType.value = 'success';
      } catch (error) {
        message.value = 'Error updating transaction: ' + (error.response?.data?.message || error.message);
        messageType.value = 'error';
      }
    };

    const triggerReconciliation = () => {
      reconciliationForm.value = { id: null, bank_account_id: '', date: '', status: 'Pending', notes: '' };
      isEditReconciliation.value = false;
      showReconciliationModal.value = true;
    };

    const editReconciliation = (id) => {
      const reconciliation = reconciliations.value.data.find((r) => r.id === id);
      if (reconciliation) {
        reconciliationForm.value = { ...reconciliation, bank_account_id: reconciliation.bank_account_id };
        isEditReconciliation.value = true;
        showReconciliationModal.value = true;
      }
    };

    const delReconciliation = (reconciliation) => {
      selectedItem.value = reconciliation;
      selectedItemType.value = 'Reconciliation';
      showConfModal.value = true;
    };

    const saveReconciliation = async () => {
      try {
        const res = await axios.post('/api/banking/reconciliations', reconciliationForm.value);
        message.value = res.data.message || 'Reconciliation saved successfully.';
        messageType.value = 'success';
      } catch (error) {
        message.value = 'Error saving reconciliation: ' + (error.response?.data?.message || error.message);
        messageType.value = 'error';
      }
    };

    const updateReconciliation = async () => {
      try {
        const res = await axios.put(`/api/banking/reconciliations/${reconciliationForm.value.id}`, reconciliationForm.value);
        message.value = res.data.message || 'Reconciliation updated successfully.';
        messageType.value = 'success';
      } catch (error) {
        message.value = 'Error updating reconciliation: ' + (error.response?.data?.message || error.message);
        messageType.value = 'error';
      }
    };

    const deleteItem = async (id) => {
      try {
        let url = '';
        if (selectedItemType.value === 'Account') url = `/api/banking/accounts/${id}`;
        else if (selectedItemType.value === 'Transaction') url = `/api/banking/transactions/${id}`;
        else if (selectedItemType.value === 'Reconciliation') url = `/api/banking/reconciliations/${id}`;
        const res = await axios.delete(url);
        message.value = res.data.message || `${selectedItemType.value} deleted successfully.`;
        messageType.value = 'success';
        fetchBankingData();
        closeConfModal();
      } catch (error) {
        message.value = `Error deleting ${selectedItemType.value}: ` + error.message;
        messageType.value = 'error';
        closeConfModal();
      }
    };

    const closeAccountModal = () => {
      showAccountModal.value = false;
    };

    const closeTransactionModal = () => {
      showTransactionModal.value = false;
    };

    const closeReconciliationModal = () => {
      showReconciliationModal.value = false;
    };

    const closeConfModal = () => {
      showConfModal.value = false;
      selectedItem.value = null;
    };

    const updateSelected = (status) => {
      selectedStatus.value = status;
      fetchBankingData(1); // Reset to page 1 on status change
    };

    const empty = () => {
      message.value = '';
    };

    onMounted(() => {
      fetchBankingData();
    });

    return {
      accounts,
      transactions,
      reconciliations,
      currentPage,
      showAccountModal,
      showTransactionModal,
      showReconciliationModal,
      isEditAccount,
      isEditTransaction,
      isEditReconciliation,
      links,
      selectedStatus,
      searchQuery,
      message,
      messageType,
      showConfModal,
      accountColumns,
      transactionColumns,
      reconciliationColumns,
      statuses,
      statusOptions,
      reconciliationStatuses,
      selectedItem,
      selectedItemType,
      csvInput,
      triggerCSVInput,
      importCSV,
      activeAccountsCount,
      pendingReconciliationsCount,
      currentTab,
      switchTab,
      accountForm,
      transactionForm,
      reconciliationForm,
      handleAccountSubmit,
      handleTransactionSubmit,
      handleReconciliationSubmit,
      updateSelected,
      fetchBankingData,
      changePage,
      addAccount,
      editAccount,
      delAccount,
      saveAccount,
      updateAccount,
      addTransaction,
      editTransaction,
      delTransaction,
      saveTransaction,
      updateTransaction,
      triggerReconciliation,
      editReconciliation,
      delReconciliation,
      saveReconciliation,
      updateReconciliation,
      deleteItem,
      closeAccountModal,
      closeTransactionModal,
      closeReconciliationModal,
      closeConfModal,
      empty,
      isLoading,
    };
  },
};
</script>

<style scoped>
.p-2 {
  padding: 0.5rem;
}

.text-outline {
  text-shadow: -1px -1px 0 #000, 1px -1px 0 #000, -1px 1px 0 #000, 1px 1px 0 #000;
  color: white;
}

.mb-3 {
  margin-bottom: 1rem;
}

.btn {
  padding: 0.5rem 1rem;
  cursor: pointer;
}

.btn-primary {
  background-color: #007bff;
  color: white;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
}

.btn-info {
  background-color: #17a2b8;
  color: white;
}

.mx-2 {
  margin-left: 0.5rem;
  margin-right: 0.5rem;
}

.nav {
  display: flex;
  gap: 0.5rem;
}

.nav-link {
  padding: 0.5rem 1rem;
  border: 1px solid #ddd;
  border-radius: 0.25rem;
  background-color: #f8f9fa;
}

.nav-link.active {
  background-color: #007bff;
  color: white;
}

.input-group {
  display: flex;
}

.form-control {
  padding: 0.375rem 0.75rem;
  border: 1px solid #ced4da;
  border-radius: 0.25rem;
}

.inner-shadow {
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
}

.btn-outline-primary {
  border-color: #007bff;
  color: #007bff;
}

.btn-outline-primary.active {
  background-color: #007bff;
  color: white;
}

.me-2 {
  margin-right: 0.5rem;
}

.table {
  width: 100%;
  border-collapse: collapse;
}

.table th,
.table td {
  padding: 0.75rem;
  border: 1px solid #dee2e6;
}

.table-striped tbody tr:nth-of-type(odd) {
  background-color: rgba(0, 0, 0, 0.05);
}

.modal {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  justify-content: center;
  align-items: center;
}

.modal.d-block {
  display: flex;
}

.modal-content {
  position: relative;
  width: 500px;
  background-color: white;
  border-radius: 0.3rem;
}

.modal-header {
  padding: 1rem;
  border-bottom: 1px solid #dee2e6;
}

.modal-body {
  padding: 1rem;
}

.modal-footer {
  padding: 1rem;
  border-top: 1px solid #dee2e6;
}

.btn-close {
  background: none;
  border: none;
  font-size: 1.5rem;
}

.bg-danger {
  background-color: #dc3545;
}

.text-white {
  color: white;
}

.display-5 {
  font-size: 2rem;
}

.mx-4 {
  margin-left: 1rem;
  margin-right: 1rem;
}

.alert {
  padding: 0.75rem;
  margin-top: 1rem;
  border-radius: 0.25rem;
  background-color: #d1ecf1;
  color: #0c5460;
  border: 1px solid #bee5eb;
}
</style>