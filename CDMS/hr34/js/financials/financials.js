/**
 * Financials Module
 * Handles display of various financial sections like Disbursement, Budget Management, etc.
 * v1.1 - Added data fetching and basic table rendering for financial sections.
 */
import { API_BASE_URL } from '../utils.js';

// --- DOM Element References ---
let pageTitleElement;
let mainContentArea;

/**
 * Initializes common elements used by the financials module.
 */
function initializeFinancialsElements() {
    pageTitleElement = document.getElementById('page-title');
    mainContentArea = document.getElementById('main-content-area');
    if (!pageTitleElement || !mainContentArea) {
        console.error("Financials Module: Core DOM elements (page-title or main-content-area) not found!");
        return false;
    }
    return true;
}

/**
 * Generic API response handler for financials module.
 * @param {Response} response - The fetch API response object.
 * @returns {Promise<any>} - The parsed JSON data.
 */
async function handleFinancialsApiResponse(response) {
    if (!response.ok) {
        const errorData = await response.json().catch(() => ({ error: `HTTP error! Status: ${response.status}` }));
        throw new Error(errorData.error || `HTTP error! Status: ${response.status}`);
    }
    const text = await response.text();
    if (!text) return []; // Return empty array for empty response, common for list endpoints
    try {
        return JSON.parse(text);
    } catch (e) {
        console.error("Failed to parse JSON response:", text);
        throw new Error("Invalid JSON response from server.");
    }
}


// --- Disbursement Section ---
export async function displayDisbursementSection() {
    if (!initializeFinancialsElements()) return;
    pageTitleElement.textContent = 'Disbursement Management';
    mainContentArea.innerHTML = `
        <div class="bg-white p-6 rounded-lg shadow-md border border-[#F7E6CA] space-y-6">
            <h3 class="text-lg font-semibold text-[#4E3B2A] mb-3 font-header">Disbursement Requests</h3>
            <div id="disbursement-requests-table-container" class="overflow-x-auto mb-6">Loading disbursement requests...</div>
            <h3 class="text-lg font-semibold text-[#4E3B2A] mb-3 font-header">Approvals</h3>
            <div id="disbursement-approvals-table-container" class="overflow-x-auto">Loading approvals...</div>
        </div>
    `;
    console.log("Displaying Disbursement Section");
    try {
        const response = await fetch(`${API_BASE_URL}financials/get_disbursements.php`);
        const data = await handleFinancialsApiResponse(response);
        renderDisbursementsTables(data);
    } catch (error) {
        console.error("Error fetching disbursement data:", error);
        if(document.getElementById('disbursement-requests-table-container')) document.getElementById('disbursement-requests-table-container').innerHTML = `<p class="text-red-500">${error.message}</p>`;
        if(document.getElementById('disbursement-approvals-table-container')) document.getElementById('disbursement-approvals-table-container').innerHTML = `<p class="text-red-500">${error.message}</p>`;
    }
}

function renderDisbursementsTables(data) {
    const requestsContainer = document.getElementById('disbursement-requests-table-container');
    const approvalsContainer = document.getElementById('disbursement-approvals-table-container');

    if (requestsContainer) {
        if (data.requests && data.requests.length > 0) {
            requestsContainer.innerHTML = createHtmlTable(data.requests, ['RequestID', 'EmployeeID', 'AllocationID', 'Amount', 'DateOfRequest', 'Status']);
        } else {
            requestsContainer.innerHTML = '<p class="text-gray-500">No disbursement requests found.</p>';
        }
    }
    if (approvalsContainer) {
        if (data.approvals && data.approvals.length > 0) {
            approvalsContainer.innerHTML = createHtmlTable(data.approvals, ['ApprovalID', 'RequestID', 'AllocationID', 'Amount', 'ApproverID', 'Status', 'DateOfApproval', 'RejectReason']);
        } else {
            approvalsContainer.innerHTML = '<p class="text-gray-500">No approval records found.</p>';
        }
    }
}

// --- Budget Management Section ---
export async function displayBudgetManagementSection() {
    if (!initializeFinancialsElements()) return;
    pageTitleElement.textContent = 'Budget Management';
    mainContentArea.innerHTML = `
        <div class="bg-white p-6 rounded-lg shadow-md border border-[#F7E6CA] space-y-6">
            <h3 class="text-lg font-semibold text-[#4E3B2A] mb-3 font-header">Budgets</h3>
            <div id="budgets-table-container" class="overflow-x-auto mb-6">Loading budgets...</div>
            <h3 class="text-lg font-semibold text-[#4E3B2A] mb-3 font-header">Budget Allocations</h3>
            <div id="budget-allocations-table-container" class="overflow-x-auto mb-6">Loading budget allocations...</div>
            <h3 class="text-lg font-semibold text-[#4E3B2A] mb-3 font-header">Budget Adjustments</h3>
            <div id="budget-adjustments-table-container" class="overflow-x-auto">Loading budget adjustments...</div>
        </div>
    `;
    console.log("Displaying Budget Management Section");
    try {
        const response = await fetch(`${API_BASE_URL}financials/get_budgets.php`);
        const data = await handleFinancialsApiResponse(response);
        renderBudgetManagementTables(data);
    } catch (error) {
        console.error("Error fetching budget management data:", error);
         if(document.getElementById('budgets-table-container')) document.getElementById('budgets-table-container').innerHTML = `<p class="text-red-500">${error.message}</p>`;
         if(document.getElementById('budget-allocations-table-container')) document.getElementById('budget-allocations-table-container').innerHTML = `<p class="text-red-500">${error.message}</p>`;
         if(document.getElementById('budget-adjustments-table-container')) document.getElementById('budget-adjustments-table-container').innerHTML = `<p class="text-red-500">${error.message}</p>`;
    }
}

function renderBudgetManagementTables(data) {
    const budgetsContainer = document.getElementById('budgets-table-container');
    const allocationsContainer = document.getElementById('budget-allocations-table-container');
    const adjustmentsContainer = document.getElementById('budget-adjustments-table-container');

    if (budgetsContainer) {
        if (data.budgets && data.budgets.length > 0) {
            budgetsContainer.innerHTML = createHtmlTable(data.budgets, ['BudgetID', 'BudgetName', 'TotalAmount', 'StartDate', 'EndDate']);
        } else {
            budgetsContainer.innerHTML = '<p class="text-gray-500">No budgets found.</p>';
        }
    }
    if (allocationsContainer) {
        if (data.allocations && data.allocations.length > 0) {
            allocationsContainer.innerHTML = createHtmlTable(data.allocations, ['AllocationID', 'BudgetID', 'BudgetName', 'TotalAmount', 'AllocatedAmount', 'DepartmentName']);
        } else {
            allocationsContainer.innerHTML = '<p class="text-gray-500">No budget allocations found.</p>';
        }
    }
    if (adjustmentsContainer) {
        if (data.adjustments && data.adjustments.length > 0) {
            adjustmentsContainer.innerHTML = createHtmlTable(data.adjustments, ['AdjustmentID', 'BudgetID', 'AllocationID', 'BudgetName', 'BudgetAllocated', 'DepartmentName', 'AdjustmentReason', 'AdjustmentAmount']);
        } else {
            adjustmentsContainer.innerHTML = '<p class="text-gray-500">No budget adjustments found.</p>';
        }
    }
}

// --- Collection Section ---
export async function displayCollectionSection() {
    if (!initializeFinancialsElements()) return;
    pageTitleElement.textContent = 'Collection Management';
    mainContentArea.innerHTML = `
        <div class="bg-white p-6 rounded-lg shadow-md border border-[#F7E6CA] space-y-6">
            <h3 class="text-lg font-semibold text-[#4E3B2A] mb-3 font-header">Invoices</h3>
            <div id="invoices-table-container" class="overflow-x-auto mb-6">Loading invoices...</div>
            <h3 class="text-lg font-semibold text-[#4E3B2A] mb-3 font-header">Collection Payments</h3>
            <div id="collection-payments-table-container" class="overflow-x-auto mb-6">Loading collection payments...</div>
            <h3 class="text-lg font-semibold text-[#4E3B2A] mb-3 font-header">Accounts Receivable</h3>
            <div id="accounts-receivable-table-container" class="overflow-x-auto">Loading accounts receivable...</div>
        </div>
    `;
    console.log("Displaying Collection Section");
    try {
        const response = await fetch(`${API_BASE_URL}financials/get_collections.php`);
        const data = await handleFinancialsApiResponse(response);
        renderCollectionsTables(data);
    } catch (error) {
        console.error("Error fetching collection data:", error);
        if(document.getElementById('invoices-table-container')) document.getElementById('invoices-table-container').innerHTML = `<p class="text-red-500">${error.message}</p>`;
        if(document.getElementById('collection-payments-table-container')) document.getElementById('collection-payments-table-container').innerHTML = `<p class="text-red-500">${error.message}</p>`;
        if(document.getElementById('accounts-receivable-table-container')) document.getElementById('accounts-receivable-table-container').innerHTML = `<p class="text-red-500">${error.message}</p>`;
    }
}

function renderCollectionsTables(data) {
    const invoicesContainer = document.getElementById('invoices-table-container');
    const paymentsContainer = document.getElementById('collection-payments-table-container');
    const receivablesContainer = document.getElementById('accounts-receivable-table-container');

    if (invoicesContainer) {
        if (data.invoices && data.invoices.length > 0) {
            invoicesContainer.innerHTML = createHtmlTable(data.invoices, ['InvoiceID', 'AccountID', 'GuestName']);
        } else {
            invoicesContainer.innerHTML = '<p class="text-gray-500">No invoices found.</p>';
        }
    }
    if (paymentsContainer) {
        if (data.payments && data.payments.length > 0) {
            paymentsContainer.innerHTML = createHtmlTable(data.payments, ['PaymentID', 'InvoiceID', 'TotalAmount', 'AmountPay']);
        } else {
            paymentsContainer.innerHTML = '<p class="text-gray-500">No collection payments found.</p>';
        }
    }
    if (receivablesContainer) {
        if (data.receivables && data.receivables.length > 0) {
            receivablesContainer.innerHTML = createHtmlTable(data.receivables, ['ReceivableID', 'InvoiceID', 'Status', 'IsViewed']);
        } else {
            receivablesContainer.innerHTML = '<p class="text-gray-500">No accounts receivable found.</p>';
        }
    }
}

// --- General Ledger Section ---
export async function displayGeneralLedgerSection() {
    if (!initializeFinancialsElements()) return;
    pageTitleElement.textContent = 'General Ledger';
    mainContentArea.innerHTML = `
        <div class="bg-white p-6 rounded-lg shadow-md border border-[#F7E6CA] space-y-6">
            <h3 class="text-lg font-semibold text-[#4E3B2A] mb-3 font-header">Accounts</h3>
            <div id="gl-accounts-table-container" class="overflow-x-auto mb-6">Loading accounts...</div>
            <h3 class="text-lg font-semibold text-[#4E3B2A] mb-3 font-header">Journal Entries</h3>
            <div id="gl-journal-entries-table-container" class="overflow-x-auto mb-6">Loading journal entries...</div>
            <h3 class="text-lg font-semibold text-[#4E3B2A] mb-3 font-header">Transactions</h3>
            <div id="gl-transactions-table-container" class="overflow-x-auto">Loading transactions...</div>
        </div>
    `;
    console.log("Displaying General Ledger Section");
     try {
        const response = await fetch(`${API_BASE_URL}financials/get_general_ledger.php`);
        const data = await handleFinancialsApiResponse(response);
        renderGeneralLedgerTables(data);
    } catch (error) {
        console.error("Error fetching general ledger data:", error);
        if(document.getElementById('gl-accounts-table-container')) document.getElementById('gl-accounts-table-container').innerHTML = `<p class="text-red-500">${error.message}</p>`;
        if(document.getElementById('gl-journal-entries-table-container')) document.getElementById('gl-journal-entries-table-container').innerHTML = `<p class="text-red-500">${error.message}</p>`;
        if(document.getElementById('gl-transactions-table-container')) document.getElementById('gl-transactions-table-container').innerHTML = `<p class="text-red-500">${error.message}</p>`;
    }
}

function renderGeneralLedgerTables(data) {
    const accountsContainer = document.getElementById('gl-accounts-table-container');
    const journalEntriesContainer = document.getElementById('gl-journal-entries-table-container');
    const transactionsContainer = document.getElementById('gl-transactions-table-container');

    if (accountsContainer) {
        if (data.accounts && data.accounts.length > 0) {
            accountsContainer.innerHTML = createHtmlTable(data.accounts, ['AccountID', 'AccountName', 'AccountType']);
        } else {
            accountsContainer.innerHTML = '<p class="text-gray-500">No accounts found.</p>';
        }
    }
    if (journalEntriesContainer) {
        if (data.journal_entries && data.journal_entries.length > 0) {
            journalEntriesContainer.innerHTML = createHtmlTable(data.journal_entries, ['EntryID', 'AccountID', 'TransactionID', 'EntryType', 'Amount', 'EntryDate', 'Description']);
        } else {
            journalEntriesContainer.innerHTML = '<p class="text-gray-500">No journal entries found.</p>';
        }
    }
    if (transactionsContainer) {
        if (data.transactions && data.transactions.length > 0) {
            transactionsContainer.innerHTML = createHtmlTable(data.transactions, ['TransactionID', 'EntryID', 'PaymentID', 'AllocationID', 'AdjustmentID', 'PayablePaymentID', 'TransactionFrom', 'TransactionDate', 'BudgetAllocated', 'BudgetName', 'Allocated_Department', 'AdjustmentAmount', 'PaymentMethod', 'GuestName', 'TotalAmount']);
        } else {
            transactionsContainer.innerHTML = '<p class="text-gray-500">No transactions found.</p>';
        }
    }
}

// --- Accounts Payable Section ---
export async function displayAccountsPayableSection() {
    if (!initializeFinancialsElements()) return;
    pageTitleElement.textContent = 'Accounts Payable';
    mainContentArea.innerHTML = `
        <div class="bg-white p-6 rounded-lg shadow-md border border-[#F7E6CA] space-y-6">
            <h3 class="text-lg font-semibold text-[#4E3B2A] mb-3 font-header">Payable Invoices</h3>
            <div id="ap-invoices-table-container" class="overflow-x-auto mb-6">Loading payable invoices...</div>
            <h3 class="text-lg font-semibold text-[#4E3B2A] mb-3 font-header">Payment Schedules</h3>
            <div id="ap-schedules-table-container" class="overflow-x-auto mb-6">Loading payment schedules...</div>
            <h3 class="text-lg font-semibold text-[#4E3B2A] mb-3 font-header">Vendor Payments</h3>
            <div id="ap-vendor-payments-table-container" class="overflow-x-auto">Loading vendor payments...</div>
        </div>
    `;
    console.log("Displaying Accounts Payable Section");
    try {
        const response = await fetch(`${API_BASE_URL}financials/get_accounts_payable.php`);
        const data = await handleFinancialsApiResponse(response);
        renderAccountsPayableTables(data);
    } catch (error) {
        console.error("Error fetching accounts payable data:", error);
        if(document.getElementById('ap-invoices-table-container')) document.getElementById('ap-invoices-table-container').innerHTML = `<p class="text-red-500">${error.message}</p>`;
        if(document.getElementById('ap-schedules-table-container')) document.getElementById('ap-schedules-table-container').innerHTML = `<p class="text-red-500">${error.message}</p>`;
        if(document.getElementById('ap-vendor-payments-table-container')) document.getElementById('ap-vendor-payments-table-container').innerHTML = `<p class="text-red-500">${error.message}</p>`;
    }
}

function renderAccountsPayableTables(data) {
    const invoicesContainer = document.getElementById('ap-invoices-table-container');
    const schedulesContainer = document.getElementById('ap-schedules-table-container');
    const paymentsContainer = document.getElementById('ap-vendor-payments-table-container');

    if (invoicesContainer) {
        if (data.payable_invoices && data.payable_invoices.length > 0) {
            invoicesContainer.innerHTML = createHtmlTable(data.payable_invoices, ['PayableInvoiceID', 'AllocationID', 'AccountID', 'BudgetName', 'Department', 'Types', 'Amount', 'StartDate', 'Status']);
        } else {
            invoicesContainer.innerHTML = '<p class="text-gray-500">No payable invoices found.</p>';
        }
    }
    if (schedulesContainer) {
        if (data.payment_schedules && data.payment_schedules.length > 0) {
            schedulesContainer.innerHTML = createHtmlTable(data.payment_schedules, ['ScheduleID', 'PayableInvoiceID', 'PaymentSchedule']);
        } else {
            schedulesContainer.innerHTML = '<p class="text-gray-500">No payment schedules found.</p>';
        }
    }
    if (paymentsContainer) {
        if (data.vendor_payments && data.vendor_payments.length > 0) {
            paymentsContainer.innerHTML = createHtmlTable(data.vendor_payments, ['PayablePaymentID', 'PayableInvoiceID', 'PaymentStatus', 'AmountPaid', 'PaymentMethod']);
        } else {
            paymentsContainer.innerHTML = '<p class="text-gray-500">No vendor payments found.</p>';
        }
    }
}

/**
 * Helper function to create an HTML table from an array of objects.
 * @param {Array<Object>} dataArray - Array of data objects.
 * @param {Array<string>} columns - Array of column keys to display.
 * @returns {string} HTML string for the table.
 */
function createHtmlTable(dataArray, columns) {
    if (!dataArray || dataArray.length === 0) return '<p class="text-gray-500">No data available.</p>';

    let tableHtml = '<table class="min-w-full divide-y divide-gray-200 border border-gray-300">';
    // Table Head
    tableHtml += '<thead class="bg-gray-50"><tr>';
    columns.forEach(column => {
        tableHtml += `<th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">${column.replace(/([A-Z])/g, ' $1').trim()}</th>`;
    });
    tableHtml += '</tr></thead>';

    // Table Body
    tableHtml += '<tbody class="bg-white divide-y divide-gray-200">';
    dataArray.forEach(row => {
        tableHtml += '<tr>';
        columns.forEach(column => {
            const value = row[column] !== null && row[column] !== undefined ? row[column] : '-';
            tableHtml += `<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">${value}</td>`;
        });
        tableHtml += '</tr>';
    });
    tableHtml += '</tbody></table>';
    return tableHtml;
}
