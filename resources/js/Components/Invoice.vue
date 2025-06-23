<template>

  <!-- Flash message -->
  <div class="p-2">
    <Transition name="fade">
      <div v-if="message" :class="['p-3 rounded mx-4 text-sm', flash]" role="alert">
        {{ message }}
      </div>
    </Transition>
  </div>


  <div :id="attr">
    <InvoiceDetails ref="invoiceDetailsRef" :invoice="invoice" />
  </div>

  <div class="d-flex w-custom mx-auto">
    <!-- Back Button -->
    <div class="d-flex w-50 pb-4">
      <button href="#" class="btn btn-outline-secondary" @click="$router.go(-1)">⬅️ Back</button>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex flex-row w-50 justify-end pb-4">
      <!-- <button class="btn btn-success ms-2" @click="emailInvoice(invoice.id)">
        Email Invoice
      </button> -->

      <button class="btn btn-success ms-2" @click="emailInvoice" :disabled="sending">
        <i class="fas fa-envelope px-2"></i>{{ sending ? 'Sending Email...' : 'Email Invoice' }}
      </button>

      <div v-if="sending" class="spinner-border text-primary px-2" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>


      <button class="btn btn-primary ms-2" @click="downloadInvoice" :disabled="loading">
        <i class="fas fa-download px-2"></i>{{ loading ? 'Downloding PDF...' : 'Download PDF' }}
      </button>
      <div v-if="loading" class="spinner-border text-primary px-2" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, useAttrs, watchEffect } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import InvoiceDetails from './InvoiceDetails.vue';

const route = useRoute();
const invoiceId = ref(route.params.id);
const invoice = ref(null);
const loading = ref(false);
const sending = ref(false);
const attr = useAttrs();
const url = ref(null);
const message = ref('');
const messageType = ref('');
const flash = ref('bg-primary-subtle text-primary-emphasis');

// Ref to the child component
const invoiceDetailsRef = ref(null);

onMounted(async () => {
  try {
    const response = await axios.get(`/api/invoices/${invoiceId.value}`);
    invoice.value = response.data;
    const res = await axios.post('/api/paystack/initialize', { invoice_id: invoiceId.value });
    url.value = res.data.authorization_url;
  } catch (error) {
    console.error("Error fetching invoice details:", error);
  }
});

const payInvoice = async () => {
  try {
    const res = await axios.post('/api/paystack/initialize', { invoice_id: invoiceId });
    res.data.authorization_url;
  } catch (error) {
    console.error('Failed to initiate payment:', error);
    // messageType.value = res.data.type || 'error';
  }
};

const emailInvoice = async () => {
  sending.value = true;
  const invoiceHtml = invoiceDetailsRef.value?.invoiceContent?.outerHTML;

  if (!invoiceHtml) {
    message.value = 'Invoice content is missing.';
    messageType.value = 'error';
    window.scrollTo({ top: 0, behavior: 'smooth' })
    sending.value = false;
    return;
  }

  try {
    const response = await axios.post('/api/send-invoice', {
      invoice_id: invoice.value.id,
      html: invoiceHtml,
      url: url.value,
    });
    messageType.value = response?.data.type || 'success';
    message.value = response?.data.message || 'Invoice emailed successfully!';
    window.scrollTo({ top: 0, behavior: 'smooth' });

  } catch (error) {
    message.value = 'Failed to send invoice email. See console for details.';
    console.error("Error sending invoice email:", error.response?.data || error.message);
    messageType.value = 'error';
    window.scrollTo({ top: 0, behavior: 'smooth' })
  } finally {
    sending.value = false;
  }
};

const downloadInvoice = async () => {
  loading.value = true;
  const invoiceHtml = invoiceDetailsRef.value?.invoiceContent?.outerHTML;

  if (!invoiceHtml) {
    message.value = 'Invoice content is missing.';
    messageType.value = 'error';
    window.scrollTo({ top: 0, behavior: 'smooth' })
    loading.value = false;
    return;
  }

  try {
    const response = await axios.post('/api/invoice/download-pdf', {
      html: invoiceHtml,
      customer_name: invoice.value.customer.name,
      invoice_id: invoice.value.id,
    });

    const pdfUrl = response.data.url;
    if (!pdfUrl) throw new Error("No PDF URL returned");

    // Open the generated PDF in a new tab
    // window.open(pdfUrl, '_blank');
    downloadPDF(pdfUrl,invoice.value.customer.name+'_invoice_' + invoice.value.id +  '.pdf');



    messageType.value = response.response?.data?.type || 'success';
    message.value = response?.data?.message || 'Invoice opened successfully!';
    window.scrollTo({ top: 0, behavior: 'smooth' })

  } catch (err) {
    console.error("PDF generation failed:", err.response?.data || err.message);
    message.value = 'Failed to open PDF. See console for details.';
    messageType.value = 'error';
    window.scrollTo({ top: 0, behavior: 'smooth' })
  } finally {
    loading.value = false;
  }
};
 const downloadPDF = (pdfUrl, fileName = 'invoice.pdf') => {
      const link = document.createElement('a')
      link.href = pdfUrl
      link.download = fileName
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
    };

watchEffect(() => {
  if (messageType.value === 'error') {
    flash.value = 'bg-danger-subtle text-danger-emphasis'
  } else if (messageType.value === 'success') {
    flash.value = 'bg-success-subtle text-success-emphasis'
  } else if (messageType.value === 'message') {
    flash.value = 'bg-primary-subtle text-primary-emphasis'
  }

  setTimeout(() => {
    message.value = '';
  }, 3000)
})
</script>
