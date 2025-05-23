/**
 * HR Management System - Main JavaScript Entry Point
 * Version: 1.22 (Restored Analytics to sidebar, reverted dashboard to simpler version)
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
// MODIFICATION: Import Analytics functions
import {
    displayAnalyticsDashboardsSection,
    displayAnalyticsReportsSection,
    displayAnalyticsMetricsSection
 } from './analytics/analytics.js';
 // Admin
import { displayUserManagementSection } from './admin/user_management.js';
// User Profile
import { displayUserProfileSection } from './profile/profile.js';
// --- Import Notification Functions ---
import { initializeNotificationSystem, stopNotificationFetching, onNotificationDropdownOpen, onNotificationDropdownClose } from './notifications/notifications.js';


// --- Global Variables ---
window.currentUser = null;

// --- Wait for the DOM to be fully loaded ---
document.addEventListener('DOMContentLoaded', () => {
    console.log("DOM fully loaded and parsed. Initializing HR System JS...");

    // --- DOM Elements ---
    const loginContainer = document.getElementById('login-container');
    const appContainer = document.getElementById('app-container');
    const loginForm = document.getElementById('login-form');
    const loginStatus = document.getElementById('login-status');
    const twoFaForm = document.getElementById('2fa-form');
    const twoFaStatus = document.getElementById('2fa-status');
    const twoFaMessage = document.getElementById('2fa-message');
    const twoFaUserIdInput = document.getElementById('2fa-user-id');
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
    const logoutLinkNav = document.getElementById('logout-link-nav'); 
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
        // MODIFICATION: Restored Analytics items
        analytics: document.querySelector('[onclick*="analytics-dropdown"]')?.closest('.menu-option'),
        analyticsDashboards: document.getElementById('analytics-dashboards-link')?.closest('li'),
        analyticsReports: document.getElementById('analytics-reports-link')?.closest('li'),
        analyticsMetrics: document.getElementById('analytics-metrics-link')?.closest('li'),
        admin: document.querySelector('[onclick*="admin-dropdown"]')?.closest('.menu-option'),
        userManagement: document.getElementById('user-management-link')?.closest('li'),
    };

    // --- Error Handling for Missing Core Elements ---
     if (!mainContentArea || !pageTitleElement || !loginContainer || !appContainer) {
        console.error("CRITICAL: Essential App/Login DOM elements not found!");
        document.body.innerHTML = '<p style="color: red; padding: 20px;">Application Error: Core UI elements are missing.</p>';
        return;
    }
    if (!loginForm || !loginStatus || !twoFaForm || !twoFaStatus || !twoFaMessage || !twoFaUserIdInput) {
        console.error("CRITICAL: Login/2FA form elements missing!");
        return;
    }
    if (!userProfileButton || !userProfileDropdown || !viewProfileLink || !logoutLinkNav || !userProfileArrow) {
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

    // --- Event Listener for Login Form ---
    loginForm.addEventListener('submit', handleLogin);

    // --- Event Listener for 2FA Form ---
    twoFaForm.addEventListener('submit', handleVerify2FA);

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

    if (logoutLinkNav) {
        logoutLinkNav.addEventListener('click', handleLogout);
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
                if(sidebar && sidebar.classList.contains('mobile-active')) { closeSidebar(); }
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
        // MODIFICATION: Restored Analytics listeners
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

    // --- Login Handler ---
    async function handleLogin(event) {
        event.preventDefault();
        loginStatus.textContent = 'Logging in...';
        loginStatus.className = 'text-center text-sm text-blue-600 h-4';
        twoFaStatus.textContent = '';
        twoFaMessage.textContent = '';
        const loginButton = loginForm.querySelector('button[type="submit"]');
        if(loginButton) loginButton.disabled = true;

        const username = loginForm.elements['username'].value;
        const password = loginForm.elements['password'].value;

        try {
            const response = await fetch(`${API_BASE_URL}login.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', },
                body: JSON.stringify({ username, password }),
                credentials: 'include'
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.error || `Login failed (Status: ${response.status})`);

            if (result.two_factor_required) {
                loginStatus.textContent = '';
                twoFaMessage.textContent = result.message || 'Check your email for the authentication code.';
                twoFaUserIdInput.value = result.user_id_temp;
                loginForm.classList.add('hidden');
                twoFaForm.classList.remove('hidden');
                document.getElementById('2fa-code').focus();
            } else if (result.user) {
                loginStatus.textContent = '';
                window.currentUser = result.user;
                showAppUI();
                updateUserDisplay(window.currentUser);
                updateSidebarAccess(window.currentUser.role_name);
                attachSidebarListeners();
                displayDashboardSection(); 
                updateActiveSidebarLink(sidebarItems.dashboard.querySelector('a')); 
                initializeNotificationSystem();
                twoFaForm.classList.add('hidden');
                loginForm.classList.remove('hidden');
            } else {
                throw new Error('Login failed. Unexpected response from server.');
            }
        } catch (error) {
            console.error('Login error:', error);
            loginStatus.textContent = error.message || 'An error occurred during login.';
            loginStatus.className = 'text-center text-sm text-red-600 h-4';
            window.currentUser = null;
            loginForm.classList.remove('hidden');
            twoFaForm.classList.add('hidden');
        } finally {
             if(loginButton) loginButton.disabled = false;
        }
    }

    // --- 2FA Verification Handler ---
    async function handleVerify2FA(event) {
        event.preventDefault();
        twoFaStatus.textContent = 'Verifying code...';
        twoFaStatus.className = 'text-center text-sm text-blue-600 h-4';
        const verifyButton = twoFaForm.querySelector('button[type="submit"]');
        if(verifyButton) verifyButton.disabled = true;

        const userId = twoFaUserIdInput.value;
        const code = twoFaForm.elements['code'].value;

        if (!userId || !code) {
            twoFaStatus.textContent = 'User ID or Code missing.';
            twoFaStatus.className = 'text-center text-sm text-red-600 h-4';
            if(verifyButton) verifyButton.disabled = false;
            return;
        }

        try {
            const response = await fetch(`${API_BASE_URL}verify_2fa.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', },
                body: JSON.stringify({ user_id: parseInt(userId), code: code }),
                credentials: 'include'
            });
            const result = await response.json();
            if (response.ok && result.user) {
                twoFaStatus.textContent = '';
                window.currentUser = result.user;
                showAppUI();
                updateUserDisplay(window.currentUser);
                updateSidebarAccess(window.currentUser.role_name);
                attachSidebarListeners();
                displayDashboardSection(); 
                updateActiveSidebarLink(sidebarItems.dashboard.querySelector('a')); 
                initializeNotificationSystem();
                twoFaForm.reset();
                loginForm.reset();
                twoFaForm.classList.add('hidden');
                loginForm.classList.remove('hidden');
                twoFaMessage.textContent = '';
            } else {
                throw new Error(result.error || 'Verification failed.');
            }
        } catch (error) {
            console.error('2FA Verification error:', error);
            twoFaStatus.textContent = error.message || 'An error occurred during verification.';
            twoFaStatus.className = 'text-center text-sm text-red-600 h-4';
        } finally {
            if(verifyButton) verifyButton.disabled = false;
        }
    }

    // --- Logout Handler ---
    async function handleLogout(event) {
        event.preventDefault();
        console.log("Logout initiated...");
        stopNotificationFetching();
        try {
            await fetch(`${API_BASE_URL}logout.php`, {
                 method: 'POST',
                 credentials: 'include'
            });
        } catch (error) {
            console.error('Error during logout API call:', error);
        } finally {
            window.currentUser = null;
            showLoginUI();
            updateSidebarAccess(null);
            if(userProfileDropdown) userProfileDropdown.classList.add('hidden');
            if(userProfileArrow) {
                userProfileArrow.classList.remove('bx-chevron-up');
                userProfileArrow.classList.add('bx-chevron-down');
            }
            if(notificationDropdown) notificationDropdown.classList.add('hidden');
            if(notificationListElement) notificationListElement.innerHTML = '<p class="p-4 text-sm text-gray-500 text-center">Please log in to see notifications.</p>';
            if(notificationDot) notificationDot.classList.add('hidden');
        }
    }

    // --- UI Visibility Functions ---
    function showLoginUI() {
        if(loginContainer) loginContainer.style.display = 'flex';
        if(appContainer) appContainer.style.display = 'none';
        if(userDisplayName) userDisplayName.textContent = 'Guest';
        if(userDisplayRole) userDisplayRole.textContent = '';
        if(mainContentArea) mainContentArea.innerHTML = '';
        if(loginForm) {
             loginForm.reset();
             loginForm.classList.remove('hidden');
        }
        if(loginStatus) loginStatus.textContent = '';
        if(twoFaForm) {
             twoFaForm.reset();
             twoFaForm.classList.add('hidden');
        }
        if(twoFaStatus) twoFaStatus.textContent = '';
        if(twoFaMessage) twoFaMessage.textContent = '';
        document.querySelectorAll('.menu-drop').forEach(d => d.classList.add('hidden'));
        document.querySelectorAll('.arrow-icon').forEach(i => {
            i.classList.remove('bx-chevron-down');
            i.classList.add('bx-chevron-right');
        });
    }
    function showAppUI() {
        if(loginContainer) loginContainer.style.display = 'none';
        if(appContainer) appContainer.style.display = 'flex';
    }

    // --- Update User Display in Navbar ---
    function updateUserDisplay(userData) {
        if (userData && userDisplayName && userDisplayRole) {
            userDisplayName.textContent = userData.full_name || 'User';
            userDisplayRole.textContent = userData.role_name || 'Role';
        } else {
             if(userDisplayName) userDisplayName.textContent = 'Guest';
             if(userDisplayRole) userDisplayRole.textContent = '';
        }
    }

    // --- Role-Based UI Access Control ---
    function updateSidebarAccess(roleName) {
        console.log(`Updating sidebar access for role: ${roleName}`);
        const allMenuItems = document.querySelectorAll('.sidebar .menu-option');
        const allSubMenuItems = document.querySelectorAll('.sidebar .menu-drop li');

        allMenuItems.forEach(item => item?.classList.add('hidden'));
        allSubMenuItems.forEach(item => item?.classList.add('hidden'));

        const show = (element, elementName = 'Unknown') => {
            if (element) {
                element.classList.remove('hidden');
                element.style.display = ''; 
                const parentMenuOption = element.closest('.menu-option');
                if(parentMenuOption) {
                    parentMenuOption.classList.remove('hidden');
                    parentMenuOption.style.display = '';
                }
            } else {
                 console.warn(`Attempted to show a non-existent sidebar element: ${elementName}`);
            }
        };
         const hide = (element, elementName = 'Unknown') => {
             if (element) {
                element.classList.add('hidden');
             }
        };

        show(sidebarItems.dashboard, 'Dashboard');

        switch (roleName) {
            case 'System Admin':
            case 'HR Admin': // HR Admin gets Analytics too
                console.log(`Executing ${roleName} access rules.`);
                show(sidebarItems.coreHr, 'Core HR');
                show(sidebarItems.employees, 'Employees');
                show(sidebarItems.documents, 'Documents');
                show(sidebarItems.orgStructure, 'Org Structure');
                show(sidebarItems.timeAttendance, 'Time & Attendance');
                show(sidebarItems.attendance, 'Attendance');
                show(sidebarItems.timesheets, 'Timesheets');
                show(sidebarItems.schedules, 'Schedules');
                show(sidebarItems.shifts, 'Shifts');
                show(sidebarItems.payroll, 'Payroll');
                show(sidebarItems.payrollRuns, 'Payroll Runs');
                show(sidebarItems.salaries, 'Salaries');
                show(sidebarItems.bonuses, 'Bonuses');
                show(sidebarItems.deductions, 'Deductions');
                show(sidebarItems.payslips, 'Payslips');
                show(sidebarItems.claims, 'Claims');
                show(sidebarItems.claimsApproval, 'Claims Approval');
                show(sidebarItems.claimTypesAdmin, 'Claim Types Admin');
                show(sidebarItems.leave, 'Leave');
                show(sidebarItems.leaveRequests, 'Leave Requests');
                show(sidebarItems.leaveBalances, 'Leave Balances');
                show(sidebarItems.leaveTypes, 'Leave Types');
                show(sidebarItems.compensation, 'Compensation');
                show(sidebarItems.compPlans, 'Comp Plans');
                show(sidebarItems.salaryAdjust, 'Salary Adjust');
                show(sidebarItems.incentives, 'Incentives');
                show(sidebarItems.analytics, 'Analytics'); // MODIFICATION: Show Analytics
                show(sidebarItems.analyticsDashboards, 'Analytics Dashboards');
                show(sidebarItems.analyticsReports, 'Analytics Reports');
                show(sidebarItems.analyticsMetrics, 'Analytics Metrics');
                if (roleName === 'System Admin') {
                    show(sidebarItems.admin, 'Admin');
                    show(sidebarItems.userManagement, 'User Management');
                } else { // HR Admin doesn't see User Management by default
                    hide(sidebarItems.admin, 'Admin');
                }
                hide(sidebarItems.submitClaim, 'Submit Claim (Hiding)');
                hide(sidebarItems.myClaims, 'My Claims (Hiding)');
                break;
            case 'Manager':
                console.log("Executing Manager access rules.");
                show(sidebarItems.claims, 'Claims');
                show(sidebarItems.submitClaim, 'Submit Claim');
                show(sidebarItems.myClaims, 'My Claims');
                show(sidebarItems.claimsApproval, 'Claims Approval');
                show(sidebarItems.leave, 'Leave');
                show(sidebarItems.leaveRequests, 'Leave Requests');
                show(sidebarItems.leaveBalances, 'Leave Balances');
                show(sidebarItems.timeAttendance, 'Time & Attendance');
                show(sidebarItems.attendance, 'Attendance');
                show(sidebarItems.timesheets, 'Timesheets');
                show(sidebarItems.payroll, 'Payroll');
                show(sidebarItems.payslips, 'Payslips');
                hide(sidebarItems.coreHr, 'Core HR');
                hide(sidebarItems.payrollRuns, 'Payroll Runs');
                hide(sidebarItems.salaries, 'Salaries');
                hide(sidebarItems.bonuses, 'Bonuses');
                hide(sidebarItems.deductions, 'Deductions');
                hide(sidebarItems.claimTypesAdmin, 'Claim Types Admin');
                hide(sidebarItems.leaveTypes, 'Leave Types');
                hide(sidebarItems.compensation, 'Compensation');
                hide(sidebarItems.analytics, 'Analytics'); // Managers don't see Analytics by default
                hide(sidebarItems.admin, 'Admin');
                hide(sidebarItems.userManagement, 'User Management');
                break;
            case 'Employee':
                console.log("Executing Employee access rules.");
                show(sidebarItems.claims, 'Claims');
                show(sidebarItems.submitClaim, 'Submit Claim');
                show(sidebarItems.myClaims, 'My Claims');
                show(sidebarItems.leave, 'Leave');
                show(sidebarItems.leaveRequests, 'Leave Requests');
                show(sidebarItems.leaveBalances, 'Leave Balances');
                show(sidebarItems.payroll, 'Payroll');
                show(sidebarItems.payslips, 'Payslips');
                hide(sidebarItems.coreHr, 'Core HR');
                hide(sidebarItems.timeAttendance, 'Time & Attendance');
                hide(sidebarItems.payrollRuns, 'Payroll Runs');
                hide(sidebarItems.salaries, 'Salaries');
                hide(sidebarItems.bonuses, 'Bonuses');
                hide(sidebarItems.deductions, 'Deductions');
                hide(sidebarItems.claimsApproval, 'Claims Approval');
                hide(sidebarItems.claimTypesAdmin, 'Claim Types Admin');
                hide(sidebarItems.leaveTypes, 'Leave Types');
                hide(sidebarItems.compensation, 'Compensation');
                hide(sidebarItems.analytics, 'Analytics');
                hide(sidebarItems.admin, 'Admin');
                hide(sidebarItems.userManagement, 'User Management');
                break;
            default:
                console.log("Executing Default access rules (logged out or unknown role).");
                hide(sidebarItems.coreHr, 'Core HR');
                hide(sidebarItems.timeAttendance, 'Time & Attendance');
                hide(sidebarItems.payroll, 'Payroll');
                hide(sidebarItems.claims, 'Claims');
                hide(sidebarItems.leave, 'Leave');
                hide(sidebarItems.compensation, 'Compensation');
                hide(sidebarItems.analytics, 'Analytics');
                hide(sidebarItems.admin, 'Admin');
                break;
        }
        console.log("Sidebar access update complete.");
    }

    // --- Initial Check for Existing Session ---
    async function checkLoginStatus() {
        console.log("Checking initial login status...");
        try {
             const response = await fetch(`${API_BASE_URL}check_session.php`, {
                 method: 'GET',
                 credentials: 'include'
             });

             if (response.ok) {
                 const result = await response.json();
                 if (result.logged_in && result.user) {
                     console.log("User already logged in:", result.user);
                     window.currentUser = result.user;
                     showAppUI();
                     updateUserDisplay(window.currentUser);
                     updateSidebarAccess(window.currentUser.role_name);
                     attachSidebarListeners();
                     displayDashboardSection(); 
                     updateActiveSidebarLink(sidebarItems.dashboard.querySelector('a')); 
                     initializeNotificationSystem();
                     return;
                 }
             }
             console.log("No active session found or session check failed.");

        } catch (error) {
            console.error("Error checking session status:", error);
        }
        showLoginUI();
        updateSidebarAccess(null);
        stopNotificationFetching();
    }

    // --- Navigation Logic for Notifications & Direct Section Access ---
    const sectionDisplayFunctions = {
        'dashboard': displayDashboardSection,
        'employees': displayEmployeeSection,
        'documents': displayDocumentsSection,
        'org-structure': displayOrgStructureSection,
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
        // MODIFICATION: Restored Analytics section mappings
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
    checkLoginStatus(); 

    console.log("HR System JS Initialized.");

}); // End DOMContentLoaded
