<!-- Invoices Template  -->
<template>
    <!-- <PageHeading :header="'Invoices'" /> -->
    <!-- Invoice Statistics -->
    <div>
        <InvoiceStats :invoiceStats="invoiceStats?.data || []" />
    </div>

    <hr class="text-white" />

    <div>
        <!-- Flash message -->
        <div class="p-2">
            <FlashMessage :message="message" :messageType="messageType" @close="empty" @cleared="message = null" />
        </div>

        <div class="d-flex flex-row justify-content-center align-items-center">
            <!-- Invoices  -->
            <div class="flex-grow-1 justify-content-start align-items-start">
                <div>
                    <button class="btn btn-primary mb-3 p-2" @click="addInvoice">Create Invoice</button>

                    <!-- Filter section  -->
                    <div class="d-flex flex-column flex-md-row mb-4">
                        <!-- Status Filters -->
                        <div class="flex-grow-1">
                            <ul class="nav nav-pills">
                                <li v-for="status in statuses" :key="status" class="nav-item">
                                    <a href="#" @click.prevent="updateSelected(status)"
                                        :class="['nav-link', status === selectedStatus ? 'active' : '']">
                                        {{ status }}
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Search Form -->
                        <form @submit.prevent="fetchInvoices" class="flex-grow-1">
                            <div class="input-group">
                                <input key="search" v-model="searchQuery" type="text"
                                    placeholder="Search invoices by customer name..." class="form-control inner-shadow">
                                <button type="submit" class="btn btn-primary">
                                    Search
                                </button>
                            </div>
                        </form>
                    </div>
                    <!-- Invoices list -->
                    <div class="table-responsive">
                        <table class="table table-striped ">
                            <thead>
                                <tr>
                                    <th v-for="column in columns" :key="column">
                                        {{ column }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="invoice in invoices?.data || []" :key="invoice.id" class="table-row"
                                    style="cursor: pointer;">
                                    <td @click.prevent="goToInvoice(invoice.id)">{{ invoice.id }}</td>
                                    <td @click.prevent="goToInvoice(invoice.id)">{{ invoice.customer?.name || 'Unknown'
                                    }}</td>
                                    <td @click.prevent="goToInvoice(invoice.id)">{{ invoice.amount }}</td>
                                    <td @click.prevent="goToInvoice(invoice.id)"
                                        :class="`${statusColor(invoice.status)}`">{{ invoice.status }}</td>
                                    <td @click.prevent="goToInvoice(invoice.id)">{{ invoice.due_date }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary ms-1"
                                            @click="editInvoice(invoice.id)">Edit</button>
                                        <button class="btn btn-sm btn-danger ms-1"
                                            @click="delInvoice(invoice)">Delete</button>
                                        <button class="btn btn-sm btn-success ms-1" @click="payInvoice(invoice.id)"
                                            v-if="invoice.status !== 'Paid'">
                                            Pay Now
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-if="!invoices?.data" class="text-center">
                        <p>No invoices found.</p>
                    </div>
                </div>

                <!-- Pagination -->
                <Pagination :links="invoices.links" :fetchData="fetchInvoices" />
            </div>

        </div>

        <!-- Invoice Modal -->
        <div class="modal" tabindex="-1" :class="{ 'd-block': showModal }" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ isEdit ? 'Edit Invoice' : 'Add Invoice' }}</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="isEdit ? updateInvoice() : saveInvoice()">

                            <div class="mb-3">
                                <label class="form-label">Customer</label>
                                <select class="form-select selectpicker" data-live-search="true"
                                    v-model="form.customer_id" :disabled="isEdit">
                                    <option value="" disabled>Select customer...</option>
                                    <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                        {{ !isEdit ? customer.id : form.id }} - {{ customer.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Amount</label>
                                <input type="text" class="form-control" v-model="form.amount" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" v-model="form.status" required>
                                    <option v-for="status in statusOption" key="status" :value="status">
                                        {{ status }}
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Due Date</label>
                                <input type="date" class="form-control" v-model="form.due_date" required>
                            </div>

                            <div class="mt-5 p-2 d-flex">
                                <div class="flex-grow-1">
                                    <button type="button" class="btn btn-secondary ms-2 " @click="closeModal">
                                        Cancel
                                    </button>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    {{ isEdit ? 'Update' : 'Save' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Invoice Modal  -->
        <div class="modal" tabindex="-1" :class="{ 'd-block': showConfModal }"
            style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white"><i class="fas fa-warning px-2 display-5"></i>
                        <h5 class="modal-title">WARNING</h5>
                        <button type="button" class="btn-close" aria-label="Close" @click="closeConfModal"></button>
                    </div>
                    <div class="modal-body">
                        <p>
                            Are you sure you want to delete invoice number <strong>#{{ selectedInvoice?.id }}</strong>?
                        </p>
                    </div>
                    <div class="modal-footer justify-content-end">
                        <button type="button" class="btn btn-outline-secondary mx-4"
                            @click="closeConfModal">Cancel</button>
                        <button type="button" class="btn btn-outline-danger mx-4"
                            @click="deleteInvoice(selectedInvoice?.id)">Yes, Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted, watchEffect } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import Pagination from '../Components/Pagination.vue';
import InvoiceStats from '../Components/InvoiceStats.vue';
import InvoiceDetails from '../Components/Invoice.vue';
import FlashMessage from '../Components/FlashMessage.vue';

export default {
    components: { Pagination, InvoiceStats, InvoiceDetails, FlashMessage },
    setup() {
        const invoices = ref([]);
        const currentPage = ref(1);
        const customers = ref([]);
        const statusOption = ['Draft', 'Sent', 'Paid', 'Overdue'];
        const showModal = ref(false);
        const isEdit = ref(false);
        const links = ref([]);
        const invoiceStats = ref([]);
        const selectedStatus = ref('All');
        const searchQuery = ref('');
        const message = ref('');
        const messageType = ref('');
        const showConfModal = ref(false);
        const selectedInvoice = ref(null);
        const columns = ['Invoice No.', "Customer's", 'Amount', 'Status', 'Due Date', 'Actions'];
        const router = useRouter();

        const scrollToTop = () => {
            window.scrollTo({ top: 0, behavior: 'smooth' })
        };
        const form = ref({
            id: null,
            customer_id: '',
            amount: '',
            status: 'Draft',
            due_date: ''
        });

        const updateSelected = (status) => {
            selectedStatus.value = status;
            fetchInvoices();
        };
        const goToInvoice = (id) => {
            router.push({ name: 'invoice.show', params: { id } });
        };
        const fetchInvoices = async (page = currentPage.value) => {
            try {
                const params = { page };
                if (selectedStatus.value && selectedStatus.value !== 'All') {
                    params.status = selectedStatus.value;
                }
                if (searchQuery.value) {
                    params.search = searchQuery.value;
                }

                const res = await axios.get(`/api/invoices`, { params });
                invoices.value = res.data.invoices || [];
                links.value = res.data.links || [];
                customers.value = res.data.customers || [];
            } catch (error) {
                console.error('Error fetching invoices:', error.response ? error.response.data : error);
                message.value = 'Error fetching invoices' || '';
                messageType.value = 'error';
                scrollToTop();
            }
        };


        const changePage = (page) => {
            currentPage.value = page;
            fetchInvoices(page);
        };

        const editInvoice = (id) => {
            const invoice = invoices.value?.data?.find(i => i.id === id);
            if (invoice) {
                form.value = { ...invoice };
                isEdit.value = true;
                showModal.value = true;
            } else {
                message.value = "Invoice not found:", invoice.id;
                messageType.value = 'error';
                scrollToTop();
            }
        };

        const delInvoice = (invoice) => {
            if (invoice) {
                selectedInvoice.value = invoice;
                showConfModal.value = true;
            } else {
                message.value = "Invoice not found:", invoice.id;
                messageType.value = 'error';
                scrollToTop();
            }
        };

        const addInvoice = () => {
            resetForm();
            isEdit.value = false;
            showModal.value = true;
        };
        const saveInvoice = async () => {
            try {
                const res = await axios.post('/api/invoices', form.value);
                fetchInvoices();
                message.value = res.data.message;
                messageType.value = res.data.type;
                closeModal();
                scrollToTop();
            } catch (error) {
                console.error('Error saving invoice:', error);
                message.value = 'Error saving invoice.';
                messageType.value = 'error';
                scrollToTop();
                closeModal();
            }
        };

        const updateInvoice = async () => {
            try {
                const res = await axios.put(`/api/invoices/${form.value.id}`, form.value);
                message.value = res.data.message;
                messageType.value = res.data.type;

                fetchInvoices();
                closeModal();
                scrollToTop();
            } catch (error) {
                console.log('Error updating invoice:', error);
                message.value = 'Error updating invoice.';
                messageType.value = 'error';
                closeModal();
                scrollToTop();
            }
        };

        const deleteInvoice = async (id) => {
            try {
                const res = await axios.delete(`/api/invoices/${id}`);
                fetchInvoices();
                message.value = res.data.message;
                messageType.value = res.data.type;
                showConfModal.value = false;
                scrollToTop();
            } catch (error) {
                console.error('Error deleting invoice:', error);
                message.value = 'Error deleting invoice.';
                messageType.value = 'error';
                showConfModal.value = false;
                scrollToTop();
            }
        };

        const fetchInvoiceStats = async () => {
            try {
                const res = await axios.get('/api/invoice-stats');
                invoiceStats.value = res.data;
            } catch (error) {
                console.log('Error fetching invoice stats:', error);
                message.value = 'Error fetching invoice stats.';
                messageType.value = 'error';
            }
        };

        const payInvoice = async (invoiceId) => {
            try {
                const res = await axios.post('/api/paystack/initialize', { invoice_id: invoiceId });
                window.location.href = res.data.authorization_url;
            } catch (error) {
                console.log('Failed to initiate payment:', error);
                message.value = 'Failed to initiate payment.';
                messageType.value = 'error';
            }
        };

        const closeModal = () => {
            showModal.value = false;
            selectedInvoice.value = null;
        };

        const closeConfModal = () => {
            showConfModal.value = false;
            resetForm();
        };

        const statusColor = (status) => {
            switch (status) {
                case 'Sent':
                    return 'bg-custom-yellow'
                case 'Paid':
                    return 'bg-custom-green'
                case 'Overdue':
                    return 'bg-custom-red'
                default:
                    return 'bg-custom-blue'
            }
        }

        const resetForm = () => {
            form.value = {
                id: null,
                customer_id: '',
                amount: '',
                status: 'Draft',
                due_date: ''
            };
        };

        onMounted(() => {
            fetchInvoices();
            fetchInvoiceStats();
            const script = document.createElement('script');
            script.src = 'https://js.paystack.co/v1/inline.js';
            script.async = true;
            script.onload = () => {
                console.log('Paystack loaded in Vue component');
            };
            document.head.appendChild(script);
        });


        watchEffect(() => {
            setTimeout(() => {
                message.value = '';
            }, 3000)
        })

        const empty = () => {
            message.value = '';
            messageType.value = '';
        }

        return {
            invoices, currentPage, customers, statusOption, showModal, isEdit, links, delInvoice, columns,statusColor,
            invoiceStats, form, searchQuery, statuses: ['All', ...statusOption], selectedStatus, message, messageType,
            updateSelected, fetchInvoices, changePage, addInvoice, editInvoice, saveInvoice, selectedInvoice, empty, goToInvoice,
            updateInvoice, deleteInvoice, fetchInvoiceStats, payInvoice, closeModal, resetForm, closeConfModal, showConfModal
        };
    }
};
</script>
