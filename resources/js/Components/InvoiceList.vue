<template>
    <!-- Model for confirmation -->
    <Modal :show="showModal" @close="closeModal" maxWidth="lg">
        <div class="p-6  text-red-800 rounded-sm ">
            <h2 class="text-xl font-mono text-red-800 bg-red-100 p-4 rounded-sm font-bold">Warning</h2>
            <p>Are you sure you want to delete <strong class="text-lg">{{ selectedInvoice.customer.name }}</strong>
                invoice number <strong class="text-lg">#{{ selectedInvoice.id }}</strong> ?</p>

            <div class="flex justify-end mt-4">
                <div class="flex-1">
                    <button @click="closeModal" class="mr-3  px-4 py-2 bg-gray-300 rounded">Cancel</button>
                </div>
                <button @click="deleteInvoice(selectedInvoice.id)" class="px-4 py-2 bg-red-500 text-white rounded">Yes,
                    Delete
                </button>
            </div>
        </div>
    </Modal>

    <!-- Flash Message Display -->
    <Transition name="fade">
        <div v-if="message" :class="['p-3 rounded mb-4 text-sm', messageType]" role="alert">
            {{ message }}
        </div>
    </Transition>

    <div class="container flex-1 mx-auto px-4 py-2">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Invoices</h1>
            <Link :href="route('invoices.create')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            Create New Invoice
            </Link>
        </div>

        <div class="xs:flex-col mb-4 md:flex">
            <!-- Status Filters -->
            <div class="flex-1">
                <ul class="flex space-x-4">
                    <li v-for="status in statuses" :key="status">
                        <Link :href="status === 'All' ? route('invoices.index') : route('invoices.index', { status })"
                            class="px-3 py-1 rounded"
                            :class="status === selectedStatus ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700'">
                        {{ status }}
                        </Link>
                    </li>
                </ul>
            </div>

            <!-- Search Form -->
            <form @submit.prevent="searchInvoices" class="flex-auto">
                <div class="flex items-center">
                    <input v-model="searchQuery" type="text" placeholder="Search invoices by customer name..."
                        class="flex-grow px-2 text-sm py-2 border border-gray-300 rounded-l focus:outline-none">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-r hover:bg-blue-600">
                        Search
                    </button>
                </div>
            </form>
        </div>

        <!-- Invoices Table -->
        <div class="overflow-x-auto bg-white rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th v-for="column in columns" :key="column"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ column }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="invoice in invoices.data" :key="invoice.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">{{ invoice.id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ invoice.customer.name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">R{{ invoice.amount }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ invoice.due_date }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                :class="statusColors[invoice.status]">
                                {{ invoice.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <Link :href="route('invoices.show', invoice.id)"
                                class="text-indigo-600 hover:underline hover:text-indigo-900">View</Link>
                            <Link :href="route('invoices.edit', invoice.id)"
                                class="text-green-600 hover:underline hover:text-green-900">Edit</Link>
                            <button @click="selectInvoice(invoice)"
                                class="text-red-600 hover:underline hover:text-red-900">
                                Delete</button>

                            <a :href="route('invoice.pdf', invoice.id)"
                                class="text-gray-600 hover:underline hover:text-blue-500">PDF
                            </a>
                        </td>
                    </tr>
                    <tr v-if="invoices.data.length === 0">
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No invoices found.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { defineProps, ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import Modal from './Modal.vue';

defineProps({
    invoices: Object
});

const form = useForm({});
const searchQuery = ref('');
const statuses = ['All', 'Draft', 'Sent', 'Paid', 'Overdue'];
const selectedStatus = ref(route().params.status || 'All');
const selectedInvoice = ref(null)

function updateSelected(status) {
    selectedStatus.value = status;
}
const columns = ['ID', 'Customer', 'Amount', 'Due Date', 'Status', 'Actions'];

function searchInvoices() {
    router.get(route('invoices.index'), { search: searchQuery.value });
}
const message = ref('')
const messageType = ref('')


const selectInvoice = (invoice) => {
    selectedInvoice.value = invoice;
    openModal();
}



const deleteInvoice = (id) => {
    try {
        if (!id) {
            message.value = "Invoice ID is undefined";
            messageType.value = "bg-red-100 text-red-800";
            return;
        } else {

            form.delete(route('invoices.destroy', id), {
                onSuccess: () => {
                    closeModal();
                    message.value = "Invoice deleted successfully!";
                    messageType.value = "bg-green-100 text-green-800";
                }
            });
            setTimeout(() => {
                message.value = ''
                router.get(route('invoices.index'));
            }, 3000)
        }
    } catch (error) {
        message.value = error.response?.data?.message || 'An error occurred while deleting the invoice.'
        messageType.value = 'bg-red-100 text-red-800'
    }


};
const statusColors = {
    'Draft': 'bg-blue-100 text-blue-900',
    'Sent': 'bg-yellow-100 text-yellow-900',
    'Paid': 'bg-green-100 text-green-900',
    'Overdue': 'bg-red-100 text-red-900'
};

const showModal = ref(false);
const openModal = () => {
    showModal.value = true;
};
const closeModal = () => {
    showModal.value = false;
};

</script>
