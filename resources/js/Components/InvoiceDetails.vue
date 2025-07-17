<template>
  <div class="container py-4">
    <div id="invoice-content" ref="invoiceContent" class="bg-white w-custom mx-auto p-4 mb-4 rounded shadow-sm">

      <!-- Title Row -->
      <div class="mb-3">
        <div v-if="invoice?.customer?.company" class="d-flex justify-content-end">
          <div>
            <hr />
            <h1 class="display-6 font-monospace fst-italic text-center">I N V O I C E</h1>
            <hr />
          </div>
        </div>
      </div>

      <!-- Info Row -->
      <div class="row px-2">
        <!-- From Address -->
        <div class="col-md-6 mb-3 flex-grow-1">
          <strong class="fw-bold">
            From: {{ invoice?.customer?.company.name }}, {{ invoice?.customer?.company.industry }} Industry
          </strong>
          <address class="text-secondary small">
            {{ invoice?.customer?.company?.street_number }}, {{ invoice?.customer?.company?.street_name }}<br>
            {{ invoice?.customer?.company?.city }}, {{ invoice?.customer?.company?.postal_code }}<br>
            {{ invoice?.customer?.company?.state_province }}, {{ invoice?.customer?.company?.country }}
          </address>
        </div>

        <!-- Invoice Info -->
        <div class="col-md-3">
          <p class="text-secondary small">
            <span>Date: {{ formattedDate }}</span><br />
            <span class="fw-semibold">Invoice #{{ invoice?.id }}</span><br />
            <span class="text-muted">Order ID: 4F3S8J</span><br />
            <span>Payment Due: {{ invoice?.due_date }}</span><br />
            <span>Account: 968345674</span>
          </p>
        </div>
      </div>

      <!-- To Address -->
      <div class="col-md-6 px-2 pb-4">
        <address class="text-secondary small">
          <strong>To: {{ invoice?.customer?.name }}</strong><br>
          {{ invoice?.customer?.street_number }}, {{ invoice?.customer?.street_name }}<br>
          {{ invoice?.customer?.city }}, {{ invoice?.customer?.postal_code }}<br>
          {{ invoice?.customer?.state_province }}, {{ invoice?.customer?.country }}<br>
          Email: {{ invoice?.customer?.email }}
        </address>
      </div>

      <!-- Invoice Items Table -->
      <div class="table-responsive px-2 mb-3">
        <table class="table table-bordered table-striped">
          <thead class="table-light">
            <tr>
              <th>Product</th>
              <th>Qty</th>
              <th>Unit Price</th>
              <th>Amount</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Call of Duty</td>
              <td>1</td>
              <td>R64.50</td>
              <td>R64.50</td>
            </tr>
            <tr>
              <td>Need for Speed IV</td>
              <td>1</td>
              <td>R50.00</td>
              <td>R50.00</td>
            </tr>
            <tr>
              <td>Detroit - Become Human</td>
              <td>1</td>
              <td>R10.70</td>
              <td>R10.70</td>
            </tr>
            <tr>
              <td>PUBG - Battlegrounds</td>
              <td>1</td>
              <td>R25.99</td>
              <td>R25.99</td>
            </tr>
            <!-- <tr v-for="item in invoice.items" :key="item.id">
              <td>{{ item.product_name }}</td>
              <td>{{ item.quantity }}</td>
              <td>R{{ item.unit_price }}</td>
              <td>R{{ item.total_price }}</td>
            </tr> -->
          </tbody>
        </table>
      </div>

      <!-- Payment Information -->
      <div class="row px-2 pt-4 mb-4">
        <!-- Accepted Payments -->
        <div class="col-md-6 mb-3">
          <p class="h6 fw-semibold text-dark">Payment Methods: <span>Cash</span> || <span>Card/EFT</span></p>
          <small class="text-secondary">
            <strong>Payment Terms & Important Information:</strong> All payments are due within
            <em>30 days</em>. Late payments may be subject to penalties.
          </small>
        </div>

        <!-- Amount Due -->
        <div class="col-md-6">
          <table class="table table-sm">
            <tbody>
              <tr>
                <th class="text-start">Subtotal:</th>
                <td>R345</td>
              </tr>
              <tr style="border-bottom: 3px solid black;">
                <th class="text-start">Tax (15%):</th>
                <td>R234</td>
              </tr>
              <tr>
                <th class="text-start">Total:</th>
                <th class="fw-bold">R123</th>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Footer -->
      <div class="text-center text-muted small fst-italic px-5 pb-4">
        <small>
          If you have any questions regarding this invoice or payment methods, contact our support
          team at <strong>info@invoice.co.za</strong> or <strong>(033) 556 8970</strong>.
        </small>
      </div>
    </div>
  </div>
</template>


<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
	invoice: Object
});

const invoiceContent = ref(null);

// Format date as YYYY-MM-DD
const formattedDate = computed(() => {
	return new Date().toISOString().slice(0, 10);
});

// Expose the ref to parent
defineExpose({ invoiceContent });
</script>
