<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="py-2">
            <div class="mx-auto max-w-7x sm:px-6">
                <div class="financial-report">
        <h2>Financial Report</h2>

        <!-- Report Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form @submit.prevent="generateReport">
                    <div class="row">
                        <!-- Company Selector -->
                        <div class="col-md-4">
                            <label class="form-label">Company</label>
                            <select v-model="filters.company_id" class="form-select" required>
                                <option value="">Select Company</option>
                                <option v-for="company in companies" :key="company.id" :value="company.id">
                                    {{ company.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Report Type Selector -->
                        <div class="col-md-4">
                            <label class="form-label">Report Type</label>
                            <select v-model="filters.report_type" class="form-select" required>
                                <option value="balance-sheet">Balance Sheet</option>
                                <option value="profit-loss">Profit & Loss</option>
                                <option value="general-ledger">General Ledger</option>
                            </select>
                        </div>

                        <!-- Date Inputs -->
                        <div class="col-md-4" v-if="filters.report_type === 'balance-sheet'">
                            <label class="form-label">As of Date</label>
                            <input type="date" v-model="filters.as_of_date" class="form-control" required />
                        </div>

                        <div class="col-md-4" v-if="filters.report_type === 'profit-loss'">
                            <label class="form-label">Start Date</label>
                            <input type="date" v-model="filters.start_date" class="form-control" required />
                        </div>

                        <div class="col-md-4" v-if="filters.report_type === 'profit-loss'">
                            <label class="form-label">End Date</label>
                            <input type="date" v-model="filters.end_date" class="form-control" required />
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-end align-items-end">
                            <button type="submit" class="btn btn-primary px-4 m-2 text-white" :disabled="loading">
                                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                                Generate
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Report Results -->
        <div v-if="report && filters.report_type === 'balance-sheet'">
            <<div v-if="report && filters.report_type === 'balance-sheet'" class="card">
                <div class="card-header bg-light d-flex justify-content-between">
                    <h5 class="mb-0">Balance Sheet</h5>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary me-2" @click="printReport">
                            <i class="bi bi-printer"></i> Print
                        </button>
                        <button class="btn btn-sm btn-outline-primary" @click="exportToPdf">
                            <i class="bi bi-file-earmark-pdf"></i> Export PDF
                        </button>
                    </div>
                </div>

                <div class="card-body" id="printable-report">
                    <div class="text-center mb-4">
                        <h4>{{ getCompanyName() }}</h4>
                        <h5>Balance Sheet</h5>
                        <p class="text-muted">As of {{ formatDate(report.as_of_date) }}</p>
                    </div>

                    <!-- Assets Section -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 fw-bold">Assets</h6>

                        <!-- Current Assets -->
                        <div class="ms-3 mb-3">
                            <div class="fw-semibold mb-2">Current Assets</div>
                            <div v-for="asset in getCurrentAssets()" :key="'asset-' + asset.account_id"
                                class="d-flex justify-content-between mb-1">
                                <span class="ms-3">{{ asset.account_name }}</span>
                                <span>{{ formatCurrency(asset.balance) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mt-1 fw-medium">
                                <span>Total Current Assets</span>
                                <span>{{ formatCurrency(getCurrentAssetsTotal()) }}</span>
                            </div>
                        </div>

                        <!-- Non-Current Assets -->
                        <div class="ms-3 mb-3">
                            <div class="fw-semibold mb-2">Non-Current Assets</div>
                            <div v-for="asset in getNonCurrentAssets()" :key="'asset-' + asset.account_id"
                                class="d-flex justify-content-between mb-1">
                                <span class="ms-3">{{ asset.account_name }}</span>
                                <span>{{ formatCurrency(asset.balance) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mt-1 fw-medium">
                                <span>Total Non-Current Assets</span>
                                <span>{{ formatCurrency(getNonCurrentAssetsTotal()) }}</span>
                            </div>
                        </div>

                        <!-- Other Assets -->
                        <div class="ms-3 mb-3" v-if="getOtherAssets().length > 0">
                            <div class="fw-semibold mb-2">Other Assets</div>
                            <div v-for="asset in getOtherAssets()" :key="'asset-' + asset.account_id"
                                class="d-flex justify-content-between mb-1">
                                <span class="ms-3">{{ asset.account_name }}</span>
                                <span>{{ formatCurrency(asset.balance) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mt-1 fw-medium">
                                <span>Total Other Assets</span>
                                <span>{{ formatCurrency(getOtherAssetsTotal()) }}</span>
                            </div>
                        </div>

                        <!-- Total Assets -->
                        <div class="d-flex justify-content-between mt-2 fw-bold">
                            <span>Total Assets</span>
                            <span>{{ formatCurrency(report.assets.total) }}</span>
                        </div>
                    </div>

                    <!-- Liabilities Section -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 fw-bold">Liabilities</h6>

                        <!-- Current Liabilities -->
                        <div class="ms-3 mb-3">
                            <div class="fw-semibold mb-2">Current Liabilities</div>
                            <div v-for="liability in getCurrentLiabilities()" :key="'liability-' + liability.account_id"
                                class="d-flex justify-content-between mb-1">
                                <span class="ms-3">{{ liability.account_name }}</span>
                                <span>{{ formatCurrency(liability.balance) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mt-1 fw-medium">
                                <span>Total Current Liabilities</span>
                                <span>{{ formatCurrency(getCurrentLiabilitiesTotal()) }}</span>
                            </div>
                        </div>

                        <!-- Non-Current Liabilities -->
                        <div class="ms-3 mb-3">
                            <div class="fw-semibold mb-2">Non-Current Liabilities</div>
                            <div v-for="liability in getNonCurrentLiabilities()"
                                :key="'liability-' + liability.account_id" class="d-flex justify-content-between mb-1">
                                <span class="ms-3">{{ liability.account_name }}</span>
                                <span>{{ formatCurrency(liability.balance) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mt-1 fw-medium">
                                <span>Total Non-Current Liabilities</span>
                                <span>{{ formatCurrency(getNonCurrentLiabilitiesTotal()) }}</span>
                            </div>
                        </div>

                        <!-- Other Liabilities -->
                        <div class="ms-3 mb-3" v-if="getOtherLiabilities().length > 0">
                            <div class="fw-semibold mb-2">Other Liabilities</div>
                            <div v-for="liability in getOtherLiabilities()" :key="'liability-' + liability.account_id"
                                class="d-flex justify-content-between mb-1">
                                <span class="ms-3">{{ liability.account_name }}</span>
                                <span>{{ formatCurrency(liability.balance) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mt-1 fw-medium">
                                <span>Total Other Liabilities</span>
                                <span>{{ formatCurrency(getOtherLiabilitiesTotal()) }}</span>
                            </div>
                        </div>

                        <!-- Total Liabilities -->
                        <div class="d-flex justify-content-between mt-2 fw-bold">
                            <span>Total Liabilities</span>
                            <span>{{ formatCurrency(report.liabilities.total) }}</span>
                        </div>
                    </div>

                    <!-- Equity Section -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 fw-bold">Equity</h6>
                        <div v-for="equity in report.equity.details" :key="'equity-' + equity.account_id"
                            class="d-flex justify-content-between mb-1">
                            <span class="ms-3">{{ equity.account_name }}</span>
                            <span>{{ formatCurrency(equity.balance) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mt-2 fw-bold">
                            <span>Total Equity</span>
                            <span>{{ formatCurrency(report.equity.total) }}</span>
                        </div>
                    </div>

                    <!-- Total Liabilities & Equity -->
                    <div class="border-top pt-3 mt-3">
                        <div class="d-flex justify-content-between fw-bold fs-5">
                            <span>Total Liabilities & Equity</span>
                            <span>{{ formatCurrency(report.total_liabilities_and_equity) }}</span>
                        </div>

                        <div v-if="!report.is_balanced" class="alert alert-warning mt-3">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            The balance sheet is not balanced. There might be an error in your accounts.
                        </div>
                    </div>
                </div>
        </div>

    </div>

    <div v-if="report && filters.report_type === 'profit-loss'" class="card">
        <div class="card-header bg-light d-flex justify-content-between">
            <h5 class="mb-0">Profit & Loss</h5>
            <div>
                <button class="btn btn-sm btn-outline-secondary me-2" @click="printReport">
                    <i class="bi bi-printer"></i> Print
                </button>
                <button class="btn btn-sm btn-outline-primary" @click="exportToPdf">
                    <i class="bi bi-file-earmark-pdf"></i> Export PDF
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="text-center mb-4">
                <h4>{{ getCompanyName() }}</h4>
                <h5>Profit & Loss</h5>
                <p class="text-muted">
                    From {{ formatDate(filters.start_date) }} to {{ formatDate(filters.end_date) }}
                </p>
            </div>

            <div class="mb-3">
                <h6 class="fw-bold">Income</h6>
                <div v-for="item in report.income.details" :key="'income-' + item.account_id"
                    class="d-flex justify-content-between">
                    <span>{{ item.account_name }}</span>
                    <span>{{ formatCurrency(item.amount) }}</span>
                </div>
                <div class="d-flex justify-content-between fw-bold mt-2">
                    <span>Total Income</span>
                    <span>{{ formatCurrency(report.income.total) }}</span>
                </div>
            </div>

            <div class="mb-3">
                <h6 class="fw-bold">Expenses</h6>
                <div v-for="item in report.expense.details" :key="'expense-' + item.account_id"
                    class="d-flex justify-content-between">
                    <span>{{ item.account_name }}</span>
                    <span>{{ formatCurrency(item.amount) }}</span>
                </div>
                <div class="d-flex justify-content-between fw-bold mt-2">
                    <span>Total Expenses</span>
                    <span>{{ formatCurrency(report.expense.total) }}</span>
                </div>
            </div>

            <div class="border-top pt-3 mt-3 d-flex justify-content-between fw-bold fs-5">
                <span>Net Profit</span>
                <span>{{ formatCurrency(report.profit) }}</span>
            </div>
        </div>
    </div>

    <!-- General Ledger -->
    <div v-if="report && filters.report_type === 'general-ledger'" class="card">
        <div class="card-header bg-light d-flex justify-content-between">
            <h5 class="mb-0">General Ledger</h5>
            <div>
                <button class="btn btn-sm btn-outline-secondary me-2" @click="printReport">
                    <i class="bi bi-printer"></i> Print
                </button>
                <button class="btn btn-sm btn-outline-primary" @click="exportToPdf">
                    <i class="bi bi-file-earmark-pdf"></i> Export PDF
                </button>
            </div>
        </div>
        <div class="card-body" id="printable-report">
            <div class="text-center mb-4">
                <h4>{{ getCompanyName() }}</h4>
                <h5>General Ledger</h5>
                <p class="text-muted">
                    From {{ formatDate(filters.start_date) }} to {{ formatDate(filters.end_date) }}
                </p>
            </div>

            <div v-for="(entries, accountName) in report.ledger" :key="accountName" class="mb-4">
                <h6 class="fw-bold border-bottom pb-2">{{ accountName }}</h6>
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="entry in entries" :key="entry.id">
                            <td>{{ formatDate(entry.date) }}</td>
                            <td>{{ entry.description || '-' }}</td>
                            <td>{{ entry.type }}</td>
                            <td class="text-end">{{ formatCurrency(entry.amount) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- No Results Message -->
    <div v-else-if="reportGenerated && !loading" class="alert alert-info">
        No data available for the selected period.
    </div>
    </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import axios from 'axios';
import jsPDF from 'jspdf';
import html2canvas from 'html2canvas';

export default {
    name: 'FinancialReport',

    data() {
        return {
            companies: [],
            filters: {
                company_id: '',
                report_type: 'balance-sheet', // 'balance-sheet' or 'profit-loss'
                as_of_date: '',
                start_date: '',
                end_date: ''
            },
            report: null,
            loading: false,
            reportGenerated: false
        };
    },

    mounted() {
        this.loadCompanies();
        const today = new Date();
        this.filters.as_of_date = this.formatDateForInput(today);
        this.filters.start_date = this.formatDateForInput(today);
        this.filters.end_date = this.formatDateForInput(today);
    },

    methods: {
        loadCompanies() {
            axios.get('/api/reports')
                .then(response => {
                    this.companies = response.data.companies;
                    if (this.companies.length > 0) {
                        this.filters.company_id = this.companies[0].id;
                    }
                })
                .catch(error => {
                    console.error('Error loading companies:', error);
                });
        },

        generateReport() {
            this.loading = true;
            this.reportGenerated = true;
            this.report = null;

            let url = '';
            let params = {};

            if (this.filters.report_type === 'balance-sheet') {
                url = '/api/reports/balance-sheet';
                params = {
                    company_id: this.filters.company_id,
                    as_of_date: this.filters.as_of_date
                };
            } else {
                url = '/api/reports/profit-loss';
                params = {
                    company_id: this.filters.company_id,
                    start_date: this.filters.start_date,
                    end_date: this.filters.end_date
                };
            }

            axios.get(url, { params })
                .then(response => {
                    this.report = response.data;
                })
                .catch(error => {
                    console.error('Error generating report:', error);
                    alert('Failed to generate report. Please try again.');
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        getCompanyName() {
            const company = this.companies.find(c => c.id === parseInt(this.filters.company_id));
            return company ? company.name : '';
        },

        formatCurrency(value) {
            return new Intl.NumberFormat('en-ZA', {
                style: 'currency',
                currency: 'ZAR'
            }).format(value);
        },

        formatDate(dateString) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('en-ZA', options);
        },

        formatDateForInput(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        },

        // Balance Sheet helpers
        getCurrentAssets() {
            return this.report?.assets?.details?.filter(a => a.category === 'current') || [];
        },
        getNonCurrentAssets() {
            return this.report?.assets?.details?.filter(a => a.category === 'non-current') || [];
        },
        getOtherAssets() {
            return this.report?.assets?.details?.filter(a => !a.category || a.category === 'other') || [];
        },
        getCurrentAssetsTotal() {
            return this.getCurrentAssets().reduce((sum, a) => sum + parseFloat(a.balance), 0);
        },
        getNonCurrentAssetsTotal() {
            return this.getNonCurrentAssets().reduce((sum, a) => sum + parseFloat(a.balance), 0);
        },
        getOtherAssetsTotal() {
            return this.getOtherAssets().reduce((sum, a) => sum + parseFloat(a.balance), 0);
        },

        getCurrentLiabilities() {
            return this.report?.liabilities?.details?.filter(l => l.category === 'current') || [];
        },
        getNonCurrentLiabilities() {
            return this.report?.liabilities?.details?.filter(l => l.category === 'non-current') || [];
        },
        getOtherLiabilities() {
            return this.report?.liabilities?.details?.filter(l => !l.category || l.category === 'other') || [];
        },
        getCurrentLiabilitiesTotal() {
            return this.getCurrentLiabilities().reduce((sum, l) => sum + parseFloat(l.balance), 0);
        },
        getNonCurrentLiabilitiesTotal() {
            return this.getNonCurrentLiabilities().reduce((sum, l) => sum + parseFloat(l.balance), 0);
        },
        getOtherLiabilitiesTotal() {
            return this.getOtherLiabilities().reduce((sum, l) => sum + parseFloat(l.balance), 0);
        },

        printReport() {
            const printContent = document.getElementById('printable-report');
            const printWindow = window.open('', '', 'width=800,height=600');
            printWindow.document.write('<html><head><title>Print Report</title></head><body>');
            printWindow.document.write(printContent.innerHTML);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        },

        exportToPdf() {
            const element = document.getElementById('printable-report');
            html2canvas(element).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF('p', 'mm', 'a4');
                const imgWidth = 210;
                const pageHeight = 297;
                const imgHeight = canvas.height * imgWidth / canvas.width;
                let heightLeft = imgHeight;
                let position = 0;

                pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;

                while (heightLeft >= 0) {
                    position = heightLeft - imgHeight;
                    pdf.addPage();
                    pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;
                }

                const filename = this.filters.report_type === 'balance-sheet'
                    ? `balance-sheet-${this.filters.as_of_date}.pdf`
                    : `profit-loss-${this.filters.start_date}_to_${this.filters.end_date}.pdf`;

                pdf.save(filename);
            });
        }
    }
};
</script>
