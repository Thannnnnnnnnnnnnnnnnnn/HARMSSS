/**
 * HR Management System - Main JavaScript Entry Point
 * Version: 1.24 (Added Transfer Employee Submodule to Core HR)
 */

// --- Import display functions from module files ---
import { API_BASE_URL } from './utils.js';
// Dashboard
import { displayDashboardSection } from './dashboard/dashboard.js';
// Core HR
import { displayEmployeeSection } from './core_hr/employees.js';
import { displayDocumentsSection } from './core_hr/documents.js';
import { displayOrgStructureSection } from './core_hr/org_structure.js';
// Time & Attendance
import { displayShiftsSection } from './time_attendance/shifts.js';
import { displaySchedulesSection } from './time_attendance/schedules.js';
import { displayAttendanceSection } from './time_attendance/attendance.js';
import { displayTimesheetsSection, closeTimesheetModal } from './time_attendance/timesheets.js';
// Payroll
import { displaySalariesSection } from './payroll/salaries.js';
import { displayBonusesSection } from './payroll/bonuses.js';
import { displayDeductionsSection } from './payroll/deductions.js';
import { displayPayrollRunsSection } from './payroll/payroll_runs.js';
import { displayPayslipsSection } from './payroll/payslips.js';
// Claims
import {
    displaySubmitClaimSection,
    displayMyClaimsSection,
    displayClaimsApprovalSection,
    displayClaimTypesAdminSection
} from './claims/claims.js';
// Leave Management
import {
    displayLeaveTypesAdminSection,
    displayLeaveRequestsSection,
    displayLeaveBalancesSection
 } from './leave/leave.js';
 // Compensation Management
 import {
    displayCompensationPlansSection,
    displaySalaryAdjustmentsSection,
    displayIncentivesSection
 } from './compensation/compensation.js';
// Analytics
import {
    displayAnalyticsDashboardsSection,
    displayAnalyticsReportsSection,
    displayAnalyticsMetricsSection
 } from './analytics/analytics.js';
 // Admin
 // MODIFIED: Import populateDepartmentDropdownAdmin from user_management.js.
 // Consider moving populateDepartmentDropdownAdmin to utils.js if used by more modules.
import { displayUserManagementSection, populateDepartmentDropdownAdmin } from './admin/user_management.js';
// User Profile
import { displayUserProfileSection } from './profile/profile.js';
// --- Import Notification Functions ---
import { initializeNotificationSystem, stopNotificationFetching, onNotificationDropdownOpen, onNotificationDropdownClose } from './notifications/notifications.js';
// --- Import Utility Functions ---
import { populateEmployeeDropdown } from './utils.js';


// --- Global Variables ---
window.currentUser = null;

// --- Wait for the DOM to be fully loaded ---
document.addEventListener('DOMContentLoaded', () => {
    console.log("DOM fully loaded and parsed. Initializing HR System JS (Simplified Admin)...");

    // --- DOM Elements ---
    const appContainer = document.getElementById('app-container');
    const mainContentArea = document.getElementById('main-content-area');
    const pageTitleElement = document.getElementById('page-title');
    const timesheetModal = document.getElementById('timesheet-detail-modal');
    const modalOverlayTs = document.getElementById('modal-overlay-ts');
    const modalCloseBtnTs = document.getElementById('modal-close-btn-ts');
    const userDisplayName = document.getElementById('user-display-name');
    const userDisplayRole = document.getElementById('user-display-role');

    // --- Navbar Profile & Notification Elements ---
    const userProfileButton = document.getElementById('user-profile-button');
    const userProfileDropdown = document.getElementById('user-profile-dropdown');
    const userProfileArrow = document.getElementById('user-profile-arrow');
    const viewProfileLink = document.getElementById('view-profile-link');
    const notificationBellButton = document.getElementById('notification-bell-button');
    const notificationDropdown = document.getElementById('notification-dropdown');
    const notificationDot = document.getElementById('notification-dot');
    const notificationListElement = document.getElementById('notification-list');

    // --- Sidebar Links & Dropdown Triggers ---
    const sidebarItems = {
        dashboard: document.getElementById('dashboard-link')?.closest('.menu-option'),
        coreHr: document.querySelector('[onclick*="core-hr-dropdown"]')?.closest('.menu-option'),
        employees: document.getElementById('employees-link')?.closest('li'),
        documents: document.getElementById('documents-link')?.closest('li'),
        orgStructure: document.getElementById('org-structure-link')?.closest('li'),
        transferEmployee: document.getElementById('transfer-employee-link')?.closest('li'), // ADDED
        timeAttendance: document.querySelector('[onclick*="time-attendance-dropdown"]')?.closest('.menu-option'),
        attendance: document.getElementById('attendance-link')?.closest('li'),
        timesheets: document.getElementById('timesheets-link')?.closest('li'),
        schedules: document.getElementById('schedules-link')?.closest('li'),
        shifts: document.getElementById('shifts-link')?.closest('li'),
        payroll: document.querySelector('[onclick*="payroll-dropdown"]')?.closest('.menu-option'),
        payrollRuns: document.getElementById('payroll-runs-link')?.closest('li'),
        salaries: document.getElementById('salaries-link')?.closest('li'),
        bonuses: document.getElementById('bonuses-link')?.closest('li'),
        deductions: document.getElementById('deductions-link')?.closest('li'),
        payslips: document.getElementById('payslips-link')?.closest('li'),
        claims: document.querySelector('[onclick*="claims-dropdown"]')?.closest('.menu-option'),
        submitClaim: document.getElementById('submit-claim-link')?.closest('li'),
        myClaims: document.getElementById('my-claims-link')?.closest('li'),
        claimsApproval: document.getElementById('claims-approval-link')?.closest('li'),
        claimTypesAdmin: document.getElementById('claim-types-admin-link')?.closest('li'),
        leave: document.querySelector('[onclick*="leave-dropdown"]')?.closest('.menu-option'),
        leaveRequests: document.getElementById('leave-requests-link')?.closest('li'),
        leaveBalances: document.getElementById('leave-balances-link')?.closest('li'),
        leaveTypes: document.getElementById('leave-types-link')?.closest('li'),
        compensation: document.querySelector('[onclick*="compensation-dropdown"]')?.closest('.menu-option'),
        compPlans: document.getElementById('comp-plans-link')?.closest('li'),
        salaryAdjust: document.getElementById('salary-adjust-link')?.closest('li'),
        incentives: document.getElementById('incentives-link')?.closest('li'),
        analytics: document.querySelector('[onclick*="analytics-dropdown"]')?.closest('.menu-option'),
        analyticsDashboards: document.getElementById('analytics-dashboards-link')?.closest('li'),
        analyticsReports: document.getElementById('analytics-reports-link')?.closest('li'),
        analyticsMetrics: document.getElementById('analytics-metrics-link')?.closest('li'),
        admin: document.querySelector('[onclick*="admin-dropdown"]')?.closest('.menu-option'),
        userManagement: document.getElementById('user-management-link')?.closest('li'),
    };

    // --- Error Handling for Missing Core Elements ---
     if (!mainContentArea || !pageTitleElement || !appContainer) {
        console.error("CRITICAL: Essential App DOM elements not found!");
        document.body.innerHTML = '<p style="color: red; padding: 20px;">Application Error: Core UI elements are missing.</p>';
        return;
    }
    if (!userProfileButton || !userProfileDropdown || !viewProfileLink || !userProfileArrow) {
        console.warn("Navbar profile elements not fully found. Profile dropdown might not work.");
    }
    if (!notificationBellButton || !notificationDropdown || !notificationDot || !notificationListElement) {
        console.warn("Notification elements not fully found. Notifications might not work.");
    }

    // --- Setup Modal Close Listeners ---
    if (timesheetModal && modalOverlayTs && modalCloseBtnTs) {
         if (typeof closeTimesheetModal === 'function') {
             modalCloseBtnTs.addEventListener('click', closeTimesheetModal);
             modalOverlayTs.addEventListener('click', closeTimesheetModal);
         } else {
             console.warn("closeTimesheetModal function not found/imported from timesheets.js.");
             modalCloseBtnTs.addEventListener('click', () => timesheetModal.classList.add('hidden'));
             modalOverlayTs.addEventListener('click', () => timesheetModal.classList.add('hidden'));
         }
    } else {
        console.warn("Timesheet modal elements (modal, overlay, or close button) not found in HTML.");
    }

    // --- Event Listeners for Navbar Profile Dropdown ---
    if (userProfileButton && userProfileDropdown && userProfileArrow) {
        userProfileButton.addEventListener('click', (event) => {
            event.stopPropagation();
            userProfileDropdown.classList.toggle('hidden');
            userProfileArrow.classList.toggle('bx-chevron-down');
            userProfileArrow.classList.toggle('bx-chevron-up');
            if (notificationDropdown && !notificationDropdown.classList.contains('hidden')) {
                notificationDropdown.classList.add('hidden');
                onNotificationDropdownClose();
            }
        });
    }

    if (viewProfileLink) {
        viewProfileLink.addEventListener('click', (e) => {
            e.preventDefault();
            if (typeof displayUserProfileSection === 'function') {
                displayUserProfileSection();
            } else {
                console.error("displayUserProfileSection function is not defined or imported.");
                 if(mainContentArea) mainContentArea.innerHTML = '<p class="text-red-500 p-4">Error: Profile display function not available.</p>';
            }
            if (userProfileDropdown) userProfileDropdown.classList.add('hidden');
            if (userProfileArrow) {
                userProfileArrow.classList.remove('bx-chevron-up');
                userProfileArrow.classList.add('bx-chevron-down');
            }
        });
    }

    // --- Event Listeners for Notification Bell ---
    if (notificationBellButton && notificationDropdown) {
        notificationBellButton.addEventListener('click', (event) => {
            event.stopPropagation();
            const isNowHidden = notificationDropdown.classList.toggle('hidden');
            if (userProfileDropdown && !userProfileDropdown.classList.contains('hidden')) {
                userProfileDropdown.classList.add('hidden');
                if(userProfileArrow) {
                    userProfileArrow.classList.remove('bx-chevron-up');
                    userProfileArrow.classList.add('bx-chevron-down');
                }
            }

            if (!isNowHidden) {
                onNotificationDropdownOpen();
            } else {
                onNotificationDropdownClose();
            }
        });
    }

    // Global click listener to close dropdowns when clicking outside
    document.addEventListener('click', (event) => {
        if (userProfileDropdown && userProfileButton && !userProfileButton.contains(event.target) && !userProfileDropdown.contains(event.target)) {
            if (!userProfileDropdown.classList.contains('hidden')) {
                userProfileDropdown.classList.add('hidden');
                if(userProfileArrow) {
                    userProfileArrow.classList.remove('bx-chevron-up');
                    userProfileArrow.classList.add('bx-chevron-down');
                }
            }
        }
        if (notificationDropdown && notificationBellButton && !notificationBellButton.contains(event.target) && !notificationDropdown.contains(event.target)) {
            if (!notificationDropdown.classList.contains('hidden')) {
                notificationDropdown.classList.add('hidden');
                onNotificationDropdownClose();
            }
        }
    });

    // --- Sidebar Listener Setup ---
    const addClickListenerOnce = (element, handler) => {
        const targetElement = element?.querySelector('a');
        if (targetElement && !targetElement.hasAttribute('data-listener-added')) {
            targetElement.addEventListener('click', (e) => {
                e.preventDefault();
                const sidebar = document.querySelector('.sidebar');
                if(sidebar && sidebar.classList.contains('mobile-active')) { closeSidebar(); } // Assuming closeSidebar is defined globally or in scope
                if (typeof handler === 'function') {
                    try { handler(); } catch (error) {
                         console.error(`Error executing handler for ${targetElement.id || 'sidebar link'}:`, error);
                         if(mainContentArea) { mainContentArea.innerHTML = `<p class="text-red-500 p-4">Error loading section. Please check the console.</p>`; }
                    }
                } else { console.error("Handler is not a function for element:", targetElement); }
                 updateActiveSidebarLink(targetElement);
            });
            targetElement.setAttribute('data-listener-added', 'true');
        } else if (!targetElement && handler && typeof handler === 'function') {
             console.warn(`Sidebar link element (<a>) not found within provided element for handler: ${handler.name}. Check ID/structure.`);
        }
    };

    function attachSidebarListeners() {
        console.log("Attaching sidebar listeners...");
        if (sidebarItems.dashboard && !sidebarItems.dashboard.classList.contains('hidden')) {
            addClickListenerOnce(sidebarItems.dashboard, displayDashboardSection);
        }
        if (sidebarItems.employees && !sidebarItems.employees.classList.contains('hidden')) {
             addClickListenerOnce(sidebarItems.employees, displayEmployeeSection);
        }
        if (sidebarItems.documents && !sidebarItems.documents.classList.contains('hidden')) {
             addClickListenerOnce(sidebarItems.documents, displayDocumentsSection);
        }
        if (sidebarItems.orgStructure && !sidebarItems.orgStructure.classList.contains('hidden')) {
             addClickListenerOnce(sidebarItems.orgStructure, displayOrgStructureSection);
        }
        if (sidebarItems.transferEmployee && !sidebarItems.transferEmployee.classList.contains('hidden')) { // ADDED
             addClickListenerOnce(sidebarItems.transferEmployee, displayTransferEmployeeSection);
        }
        if (sidebarItems.attendance && !sidebarItems.attendance.classList.contains('hidden')) {
             addClickListenerOnce(sidebarItems.attendance, displayAttendanceSection);
        }
        if (sidebarItems.timesheets && !sidebarItems.timesheets.classList.contains('hidden')) {
             addClickListenerOnce(sidebarItems.timesheets, displayTimesheetsSection);
        }
        if (sidebarItems.schedules && !sidebarItems.schedules.classList.contains('hidden')) {
             addClickListenerOnce(sidebarItems.schedules, displaySchedulesSection);
        }
         if (sidebarItems.shifts && !sidebarItems.shifts.classList.contains('hidden')) {
             addClickListenerOnce(sidebarItems.shifts, displayShiftsSection);
        }
        if (sidebarItems.payrollRuns && !sidebarItems.payrollRuns.classList.contains('hidden')) {
             addClickListenerOnce(sidebarItems.payrollRuns, displayPayrollRunsSection);
        }
        if (sidebarItems.salaries && !sidebarItems.salaries.classList.contains('hidden')) {
             addClickListenerOnce(sidebarItems.salaries, displaySalariesSection);
        }
        if (sidebarItems.bonuses && !sidebarItems.bonuses.classList.contains('hidden')) {
             addClickListenerOnce(sidebarItems.bonuses, displayBonusesSection);
        }
        if (sidebarItems.deductions && !sidebarItems.deductions.classList.contains('hidden')) {
             addClickListenerOnce(sidebarItems.deductions, displayDeductionsSection);
        }
        if (sidebarItems.payslips && !sidebarItems.payslips.classList.contains('hidden')) {
            addClickListenerOnce(sidebarItems.payslips, displayPayslipsSection);
        }
        if (sidebarItems.submitClaim && !sidebarItems.submitClaim.classList.contains('hidden')) {
             addClickListenerOnce(sidebarItems.submitClaim, displaySubmitClaimSection);
        }
        if (sidebarItems.myClaims && !sidebarItems.myClaims.classList.contains('hidden')) {
             addClickListenerOnce(sidebarItems.myClaims, displayMyClaimsSection);
        }
        if (sidebarItems.claimsApproval && !sidebarItems.claimsApproval.classList.contains('hidden')) {
             addClickListenerOnce(sidebarItems.claimsApproval, displayClaimsApprovalSection);
        }
        if (sidebarItems.claimTypesAdmin && !sidebarItems.claimTypesAdmin.classList.contains('hidden')) {
             addClickListenerOnce(sidebarItems.claimTypesAdmin, displayClaimTypesAdminSection);
        }
        if (sidebarItems.leaveRequests && !sidebarItems.leaveRequests.classList.contains('hidden')) {
             addClickListenerOnce(sidebarItems.leaveRequests, displayLeaveRequestsSection);
        }
        if (sidebarItems.leaveBalances && !sidebarItems.leaveBalances.classList.contains('hidden')) {
             addClickListenerOnce(sidebarItems.leaveBalances, displayLeaveBalancesSection);
        }
        if (sidebarItems.leaveTypes && !sidebarItems.leaveTypes.classList.contains('hidden')) {
             addClickListenerOnce(sidebarItems.leaveTypes, displayLeaveTypesAdminSection);
        }
        if (sidebarItems.compPlans && !sidebarItems.compPlans.classList.contains('hidden')) {
             addClickListenerOnce(sidebarItems.compPlans, displayCompensationPlansSection);
        }
        if (sidebarItems.salaryAdjust && !sidebarItems.salaryAdjust.classList.contains('hidden')) {
             addClickListenerOnce(sidebarItems.salaryAdjust, displaySalaryAdjustmentsSection);
        }
        if (sidebarItems.incentives && !sidebarItems.incentives.classList.contains('hidden')) {
             addClickListenerOnce(sidebarItems.incentives, displayIncentivesSection);
        }
        if (sidebarItems.analyticsDashboards && !sidebarItems.analyticsDashboards.classList.contains('hidden')) {
             addClickListenerOnce(sidebarItems.analyticsDashboards, displayAnalyticsDashboardsSection);
        }
        if (sidebarItems.analyticsReports && !sidebarItems.analyticsReports.classList.contains('hidden')) {
             addClickListenerOnce(sidebarItems.analyticsReports, displayAnalyticsReportsSection);
        }
        if (sidebarItems.analyticsMetrics && !sidebarItems.analyticsMetrics.classList.contains('hidden')) {
             addClickListenerOnce(sidebarItems.analyticsMetrics, displayAnalyticsMetricsSection);
        }
        if (sidebarItems.userManagement && !sidebarItems.userManagement.classList.contains('hidden')) {
            addClickListenerOnce(sidebarItems.userManagement, displayUserManagementSection);
        }
        console.log("Sidebar listeners attached/reattached.");
    }

    // --- UI Visibility Functions ---
    function showAppUI() {
        const loginContainer = document.getElementById('login-container');
        if(loginContainer) loginContainer.style.display = 'none';
        if(appContainer) appContainer.style.display = 'flex'; // Ensure app container is visible
    }

    // --- Update User Display in Navbar ---
    function updateUserDisplay(userData) {
        if (userData && userDisplayName && userDisplayRole) {
            userDisplayName.textContent = userData.full_name || 'Admin User';
            userDisplayRole.textContent = userData.role_name || 'System Admin';
        } else {
             if(userDisplayName) userDisplayName.textContent = 'Admin';
             if(userDisplayRole) userDisplayRole.textContent = 'System Admin';
        }
    }

    // --- Role-Based UI Access Control ---
    function updateSidebarAccess(roleName) {
        console.log(`Updating sidebar access for role: ${roleName}`);
        const allMenuItems = document.querySelectorAll('.sidebar .menu-option');
        const allSubMenuItems = document.querySelectorAll('.sidebar .menu-drop li');

        allMenuItems.forEach(item => item?.classList.remove('hidden'));
        allSubMenuItems.forEach(item => item?.classList.remove('hidden'));

        if (roleName === 'System Admin') {
            console.log(`Executing System Admin access rules (default).`);
            // All items are shown by default for System Admin
        } else {
            console.warn("Unknown role or no role for sidebar access, hiding most items.");
            allMenuItems.forEach(item => {
                if (item !== sidebarItems.dashboard) item?.classList.add('hidden');
            });
            allSubMenuItems.forEach(item => item?.classList.add('hidden'));
            if(sidebarItems.dashboard) sidebarItems.dashboard.classList.remove('hidden');
        }
        console.log("Sidebar access update complete.");
    }
    
    // --- Simplified Initial Load ---
    function initializeDefaultAdminView() {
        console.log("Initializing default admin view...");
        window.currentUser = {
            user_id: 5,
            employee_id: 1,
            username: 'sysadmin',
            full_name: 'System Administrator',
            role_id: 1,
            role_name: 'System Admin'
        };

        showAppUI();
        updateUserDisplay(window.currentUser);
        updateSidebarAccess(window.currentUser.role_name);
        attachSidebarListeners();

        const defaultAdminSection = window.DESIGNATED_DEFAULT_SECTION || 'dashboard';
        console.log(`Loading default admin section: ${defaultAdminSection}`);
        if (typeof window.navigateToSectionById === 'function') {
            window.navigateToSectionById(defaultAdminSection);
            const defaultLink = document.getElementById(`${defaultAdminSection}-link`) || sidebarItems.dashboard.querySelector('a');
            updateActiveSidebarLink(defaultLink);
        } else {
            console.error("navigateToSectionById function is not defined.");
            if(mainContentArea) mainContentArea.innerHTML = `<p class="text-red-500 p-4">Error: Could not load default admin section.</p>`;
        }
        
        initializeNotificationSystem();
    }


    // --- Navigation Logic for Notifications & Direct Section Access ---
    const sectionDisplayFunctions = {
        'dashboard': displayDashboardSection,
        'employees': displayEmployeeSection,
        'documents': displayDocumentsSection,
        'org-structure': displayOrgStructureSection,
        'transfer-employee': displayTransferEmployeeSection, // ADDED
        'attendance': displayAttendanceSection,
        'timesheets': displayTimesheetsSection,
        'schedules': displaySchedulesSection,
        'shifts': displayShiftsSection,
        'payroll-runs': displayPayrollRunsSection,
        'salaries': displaySalariesSection,
        'bonuses': displayBonusesSection,
        'deductions': displayDeductionsSection,
        'payslips': displayPayslipsSection,
        'submit-claim': displaySubmitClaimSection,
        'my-claims': displayMyClaimsSection,
        'claims-approval': displayClaimsApprovalSection,
        'claim-types-admin': displayClaimTypesAdminSection,
        'leave-requests': displayLeaveRequestsSection,
        'leave-balances': displayLeaveBalancesSection,
        'leave-types': displayLeaveTypesAdminSection,
        'comp-plans': displayCompensationPlansSection,
        'salary-adjust': displaySalaryAdjustmentsSection,
        'incentives': displayIncentivesSection,
        'analytics-dashboards': displayAnalyticsDashboardsSection,
        'analytics-reports': displayAnalyticsReportsSection,
        'analytics-metrics': displayAnalyticsMetricsSection,
        'user-management': displayUserManagementSection,
        'profile': displayUserProfileSection
    };

    window.navigateToSectionById = function(sectionId) {
        console.log(`[Main Navigation] Attempting to navigate to section: ${sectionId}`);
        const displayFunction = sectionDisplayFunctions[sectionId];
        const mainContentArea = document.getElementById('main-content-area'); 

        if (typeof displayFunction === 'function') {
            try {
                displayFunction();
                const sidebarLink = document.getElementById(`${sectionId}-link`);
                if (sidebarLink) {
                    updateActiveSidebarLink(sidebarLink);
                } else {
                     const directSidebarItem = document.querySelector(`.sidebar a[href="#${sectionId}"]`);
                     if (directSidebarItem) {
                         updateActiveSidebarLink(directSidebarItem);
                     } else {
                        console.warn(`[Main Navigation] Sidebar link for sectionId '${sectionId}' not found for highlighting.`);
                     }
                }

            } catch (error) {
                console.error(`Error navigating to section '${sectionId}':`, error);
                if (mainContentArea) {
                    mainContentArea.innerHTML = `<p class="text-red-500 p-4">Error loading section: ${sectionId}. Please check the console.</p>`;
                }
            }
        } else {
            console.warn(`[Main Navigation] No display function found for sectionId: ${sectionId}`);
            if (mainContentArea) {
                 mainContentArea.innerHTML = `<p class="text-orange-500 p-4">Section '${sectionId}' not found or not yet implemented.</p>`;
            }
        }
    };
    
    // --- Function to update active sidebar link styling ---
    function updateActiveSidebarLink(clickedLinkElement) {
        if (!clickedLinkElement) return;

        document.querySelectorAll('.sidebar .menu-name').forEach(el => {
            el.classList.remove('bg-[#EADDCB]', 'text-[#4E3B2A]', 'font-semibold');
            el.classList.remove('active-link-style'); 
        });
        document.querySelectorAll('.sidebar .menu-drop a').forEach(el => {
            el.classList.remove('bg-white', 'text-[#4E3B2A]', 'font-semibold');
            el.classList.remove('active-link-style');
        });

        let elementToStyle = clickedLinkElement.closest('.menu-name');
        if (!elementToStyle && clickedLinkElement.classList.contains('menu-name')) {
            elementToStyle = clickedLinkElement; 
        } else if (!elementToStyle) { 
             elementToStyle = clickedLinkElement;
        }

        if (elementToStyle) {
            if (elementToStyle.closest('.menu-drop')) { 
                 elementToStyle.classList.add('bg-white', 'text-[#4E3B2A]', 'font-semibold');
                 elementToStyle.classList.add('active-link-style');
                 const parentDropdownTrigger = elementToStyle.closest('.menu-drop').previousElementSibling;
                 if (parentDropdownTrigger && parentDropdownTrigger.classList.contains('menu-name')) {
                     parentDropdownTrigger.classList.add('bg-[#EADDCB]', 'text-[#4E3B2A]', 'font-semibold');
                     parentDropdownTrigger.classList.add('active-link-style');
                 }
            } else { 
                elementToStyle.classList.add('bg-[#EADDCB]', 'text-[#4E3B2A]', 'font-semibold');
                elementToStyle.classList.add('active-link-style');
            }
        }
    }

    // --- Initial Load ---
    initializeDefaultAdminView(); 

    console.log("HR System JS Initialized (Simplified Admin).");

}); // End DOMContentLoaded


// --- NEW: Transfer Employee Submodule Section ---
/**
 * Displays the Transfer Employee section.
 */
async function displayTransferEmployeeSection() {
    console.log("[Display] Displaying Transfer Employee Section...");
    const pageTitleElement = document.getElementById('page-title');
    const mainContentArea = document.getElementById('main-content-area');
    if (!pageTitleElement || !mainContentArea) {
        console.error("displayTransferEmployeeSection: Core DOM elements not found.");
        if(mainContentArea) mainContentArea.innerHTML = `<p class="text-red-500 p-4">Error initializing transfer employee section.</p>`;
        return;
    }

    pageTitleElement.textContent = 'Transfer Employee';
    mainContentArea.innerHTML = `
        <div class="bg-white p-6 rounded-lg shadow-md border border-[#F7E6CA] space-y-6">
            <h3 class="text-lg font-semibold text-[#4E3B2A] mb-3 font-header">Employee Department Transfer</h3>
            <form id="transfer-employee-form" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="transfer-employee-select" class="block text-sm font-medium text-gray-700 mb-1">Select Employee:</label>
                        <select id="transfer-employee-select" name="employee_id" required class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#4E3B2A] focus:border-[#4E3B2A]">
                            <option value="">Loading employees...</option>
                        </select>
                    </div>
                    <div>
                        <label for="transfer-new-department-select" class="block text-sm font-medium text-gray-700 mb-1">New Department:</label>
                        <select id="transfer-new-department-select" name="new_department_id" required class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#4E3B2A] focus:border-[#4E3B2A]">
                            <option value="">Loading departments...</option>
                        </select>
                    </div>
                    <div>
                        <label for="transfer-effective-date" class="block text-sm font-medium text-gray-700 mb-1">Effective Date of Transfer:</label>
                        <input type="date" id="transfer-effective-date" name="effective_date" required class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#4E3B2A] focus:border-[#4E3B2A]">
                    </div>
                    <div class="md:col-span-2">
                        <label for="transfer-reason" class="block text-sm font-medium text-gray-700 mb-1">Reason for Transfer (Optional):</label>
                        <textarea id="transfer-reason" name="reason" rows="3" placeholder="Reason for transfer..." class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#4E3B2A] focus:border-[#4E3B2A]"></textarea>
                    </div>
                </div>
                <p class="text-sm text-red-500 mt-2"><strong>Note:</strong> Transferring an employee will update their department in this system. The process to update/transfer their account details to another USM database needs to be handled by a separate backend process initiated by this action.</p>
                <div class="pt-2">
                    <button type="submit" class="px-4 py-2 bg-[#594423] text-white rounded-md hover:bg-[#4E3B2A] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#594423] transition duration-150 ease-in-out">
                        Initiate Transfer
                    </button>
                </div>
            </form>
        </div>
    `;

    requestAnimationFrame(async () => {
        await populateEmployeeDropdown('transfer-employee-select');
        // Assuming populateDepartmentDropdownAdmin is imported from user_management.js or moved to utils.js
        if (typeof populateDepartmentDropdownAdmin === 'function') {
            await populateDepartmentDropdownAdmin('transfer-new-department-select');
        } else {
            console.warn("populateDepartmentDropdownAdmin function not available for Transfer Employee section.");
            const deptSelect = document.getElementById('transfer-new-department-select');
            if(deptSelect) deptSelect.innerHTML = '<option value="">Error loading departments</option>';
        }

        const transferForm = document.getElementById('transfer-employee-form');
        if (transferForm && !transferForm.hasAttribute('data-listener-attached')) {
            transferForm.addEventListener('submit', handleTransferEmployeeSubmit);
            transferForm.setAttribute('data-listener-attached', 'true');
        }
    });
}

/**
 * Handles the submission of the employee transfer form.
 * (Stub for now, as backend is complex)
 */
async function handleTransferEmployeeSubmit(event) {
    event.preventDefault();
    const form = event.target;
    const submitButton = form.querySelector('button[type="submit"]');

    const employeeId = form.elements['employee_id'].value;
    const newDepartmentId = form.elements['new_department_id'].value;
    const effectiveDate = form.elements['effective_date'].value;
    const reason = form.elements['reason'].value.trim();

    if (!employeeId || !newDepartmentId || !effectiveDate) {
        Swal.fire({
            icon: 'error',
            title: 'Missing Information',
            text: 'Please select an employee, new department, and effective date.',
            confirmButtonColor: '#4E3B2A'
        });
        return;
    }

    Swal.fire({
        title: 'Confirm Transfer',
        html: `Are you sure you want to transfer Employee ID <strong>${employeeId}</strong> to Department ID <strong>${newDepartmentId}</strong>, effective <strong>${effectiveDate}</strong>?<br><small>This will update the local record and initiate the process for other systems.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, proceed with transfer!'
    }).then(async (result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Processing Transfer...',
                text: 'Please wait.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            if(submitButton) submitButton.disabled = true;

            const formData = {
                employee_id: employeeId,
                new_department_id: newDepartmentId,
                effective_date: effectiveDate,
                reason: reason
            };

            console.log("Employee Transfer Data to be sent to backend:", formData);
            // TODO: Implement API call to a backend endpoint (e.g., transfer_employee.php)
            // This backend endpoint would handle:
            // 1. Updating the Employee's DepartmentID in the current HR34 database.
            // 2. Logging the transfer.
            // 3. Initiating communication/data transfer to the "other USM database"
            //    This could involve another API call, a message queue, a shared DB procedure, etc.
            //    The details (DepartmentID, User_ID, Name, Password, Role, Status, Email)
            //    would need to be securely transmitted.
            //
            // For now, simulate success/failure:
            setTimeout(() => {
                const isSuccess = Math.random() > 0.2; // Simulate 80% success rate
                if (isSuccess) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Transfer Initiated',
                        text: `Employee transfer process for ID ${employeeId} has been initiated successfully. Department updated locally. Other system transfers are being processed.`,
                        confirmButtonColor: '#4E3B2A'
                    });
                    form.reset();
                    // Optionally, refresh relevant parts of the UI, e.g., employee list if it shows department
                    if (typeof displayEmployeeSection === 'function' && document.getElementById('employee-list-container')) {
                        // displayEmployeeSection(); // Or a more targeted refresh
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Transfer Failed',
                        text: 'Could not initiate the employee transfer. Please check logs or try again later.',
                        confirmButtonColor: '#4E3B2A'
                    });
                }
                if(submitButton) submitButton.disabled = false;
                if (Swal.isLoading()) { Swal.close(); } // Close loading Swal
            }, 1500);
        }
    });
}
// --- End Transfer Employee ---
