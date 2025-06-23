<template>
	<v-container elevation="0">
		<div id="invoice-content" ref="invoiceContent" class="bg-white w-custom mx-auto p-5 mb-3 rounded-lg">
			<!-- Title Row -->
			<div class="mb-2 p-2">
				<div v-if="invoice?.customer?.company" class="w-full d-flex p-2">
					<div class="flex-grow-1">
					</div>
					<div>
						<hr />
						<h1 class="display-6 font-monospace font-italic">
							I N V O I C E
						</h1>
						<hr />
					</div>
				</div>
			</div>

			<!-- Info Row -->
			<div class="d-flex flex-column px-2 flex-md-row">
				<!-- From Address -->
				<div class="flex-grow-1 md:mb-0">
					<strong class="fw-bold">
						From: {{ invoice?.customer?.company.name }}, {{ invoice?.customer?.company.industry }} Industry
					</strong>
					<address class="text-gray-600 text-sm">
						{{ invoice?.customer?.company?.street_number }}, {{ invoice?.customer?.company?.street_name
						}}<br>
						{{ invoice?.customer?.company?.city }}, {{ invoice?.customer?.company?.postal_code }}<br>
						{{ invoice?.customer?.company?.state_province }}, {{ invoice?.customer?.company?.country }}
					</address>
				</div>

				<!-- Invoice Info -->
				<div class="pr-4">
					<p class="text-gray-700 text-sm">
						<span class="text-sm text-gray-500">Date: {{ formattedDate }}</span><br />
						<span class="block font-semibold">Invoice #{{ invoice?.id }}</span><br />
						<span class="block text-muted">Order ID: 4F3S8J</span><br />
						<span class="block">Payment Due: {{ invoice?.due_date }}</span><br />
						<span class="block">Account: 968345674</span>
					</p>
				</div>
			</div>

			<!-- To Address -->
			<div class="w-100 w-md-50 px-2 pb-4 md:mb-0">
				<address class="text-gray-600 text-sm">
					<strong class="font-bold">To: {{ invoice?.customer?.name }}</strong><br>
					{{ invoice?.customer?.street_number }}, {{ invoice?.customer?.street_name }}<br>
					{{ invoice?.customer?.city }}, {{ invoice?.customer?.postal_code }}<br>
					{{ invoice?.customer?.state_province }}, {{ invoice?.customer?.country }}<br>
					Email: {{ invoice?.customer?.email }}
				</address>
			</div>

			<!-- Invoice Items Table -->
			<div class="w-full px-2 overflow-x-auto mb-2">
				<table class="table table-striped">
					<thead class="bg-gray-50">
						<tr>
							<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
							<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
							<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Unit Price</th>
							<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
						</tr>
					</thead>
					<tbody class="bg-white divide-y divide-gray-200">
						<tr>
							<td class="px-4 py-2">Call of Duty</td>
							<td class="px-4 py-2">1</td>
							<td class="px-4 py-2">R64.50</td>
							<td class="px-4 py-2">R64.50</td>
						</tr>
						<tr>
							<td class="px-4 py-2">Need for Speed IV</td>
							<td class="px-4 py-2">1</td>
							<td class="px-4 py-2">R50.00</td>
							<td class="px-4 py-2">R50.00</td>
						</tr>
						<tr>
							<td class="px-4 py-2">Detroit - Become Human</td>
							<td class="px-4 py-2">1</td>
							<td class="px-4 py-2">R10.70</td>
							<td class="px-4 py-2">R10.70</td>
						</tr>
						<tr>
							<td class="px-4 py-2">PUBG - Battlegrounds</td>
							<td class="px-4 py-2">1</td>
							<td class="px-4 py-2">R25.99</td>
							<td class="px-4 py-2">R25.99</td>
						</tr>
						<!-- <tr v-for="item in invoice.items" :key="item.id">
                        <td class="px-4 py-2">{{ item.product_name }}</td>
                        <td class="px-4 py-2">{{ item.quantity }}</td>
                        <td class="px-4 py-2">${{ item.unit_price }}</td>
                        <td class="px-4 py-2">${{ item.total_price }}</td>
                    </tr> -->
					</tbody>
				</table>
			</div>

			<!-- Payment Information -->
			<div class="d-flex px-2 pt-4 mb-6">
				<!-- Accepted Payments -->
				<div class="w-100 w-md-50 mb-4 flex-grow-1">
					<div class="inline-block">
						<div>
							<p class="text-lg font-semibold text-gray-700">Payment Methods:
								<!-- ><v-icon class="text-red">cash-100</v-icon> -->
								<span>Cash</span> {{ " || " }} <span>Card/EFT</span>
							</p>
						</div>
						<small class="fs-7 text-secondary">
							<strong>Payment Terms & Important Information:</strong> All payments are due within
							<strong class="italic">30 days</strong>. Late payments may be subject to penalties.
						</small>
					</div>
				</div>

				<!-- Amount Due -->
				<div class="w-100 w-md-50 px-5">
					<div class="overflow-x-auto mt-2">
						<table class="table  fs-6">
							<tbody>
								<!-- <tr>
                                <th class="w-1/2 text-left px-2 py-1">Subtotal:</th>
                                <td class="px-2 py-1">${{ invoice.subtotal }}</td>
                            </tr>
                            <tr class="border-b">
                                <th class="w-1/2 text-left px-2 py-1">Tax (15%):</th>
                                <td class="px-2 py-1">${{ invoice.tax }}</td>
                            </tr>
                            <tr>
                                <th class="w-1/2 text-left px-2 py-1">Total:</th>
                                <td class="px-2 font-bold py-1">${{ invoice.total }}</td>
                            </tr> -->
								<tr>
									<th class="w-1/2 text-left px-2 py-1">Subtotal:</th>
									<td class="px-2 py-1">R345</td>
								</tr>
								<tr style="border-bottom: 3px solid black;">
									<th class="text-left px-2 py-1">Tax (15%):</th>
									<td class="px-2 py-1">R234</td>
								</tr>
								<tr>
									<th class="w-1/2 text-left px-2 py-1">Total:</th>
									<th class="px-2 font-bold py-1">R123</th>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div class="text-xs text-center text-muted pb-5 px-5 mb-5 fst-italic">
				<small>
					If you have any questions regarding this invoice or payment methods, contact our support
					team at <strong class="italic">info@invoice.co.za</strong> or <strong class="italic">(033) 556
						8970</strong>.
				</small>
			</div>

		</div>
	</v-container>
</template>

<script setup>
import { defineProps, ref, computed, defineExpose } from 'vue';

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
