<?php if (isset($pdfBase64)): ?>
<div id="invoice-preview-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
    <div class="bg-[#FFF6E8] p-6 rounded-lg shadow-lg w-full max-w-2xl">
        <h2 class="text-xl font-bold mb-4 text-gray-800"><i class="fa-solid fa-file-pdf mr-2 text-blue-600"></i>Invoice Preview</h2>
        <div class="w-full h-[500px] border border-gray-300">
            <embed src="data:application/pdf;base64,<?= $pdfBase64 ?>" type="application/pdf" width="100%" height="100%">
        </div>
        <div class="flex justify-end gap-2 mt-4">
            <button id="print-invoice-btn" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors flex items-center">
                <i class="fa-solid fa-print mr-2"></i>Print
            </button>
            <button id="send-invoice-btn" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700 transition-colors flex items-center">
                <i class="fa-solid fa-envelope mr-2"></i>Send a Copy
            </button>
            <a href="./includes/InvoiceHandler.php?download_invoice=1&invoice_id=<?= $invoiceID ?>" id="download-invoice-btn" class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition-colors flex items-center">
                <i class="fa-solid fa-download mr-2"></i>Download
            </a>
            <button id="close-preview-btn" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 transition-colors flex items-center">
                <i class="fa-solid fa-times mr-2 text-gray-600"></i>Close
            </button>
        </div>
    </div>
</div>
<?php endif; ?>