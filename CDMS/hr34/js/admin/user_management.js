/**
 * js/admin/user_management.js
 * Handles the User Management section in the Admin panel.
 * Focuses on listing employees and allowing admins to update their details,
 * department, and active status.
 *
 * Corresponds to php/api/add_employee_and_user.php (which now functions as an update endpoint)
 * and php/api/get_employees.php, php/api/get_org_structure.php (for department list).
 */

let currentEditEmployeeId = null; // To store the ID of the employee being edited

/**
 * Initializes and displays the User Management section.
 * Fetches and displays a list of employees.
 */
export async function displayUserManagementSection() {
    console.log('Displaying User Management Section');
    // Corrected ID to match main.js
    const mainContent = document.getElementById('main-content-area'); 
    if (!mainContent) {
        console.error('Main content area ("main-content-area") not found. Check HTML and main.js.');
        return;
    }

    // --- UI Structure ---
    mainContent.innerHTML = `
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 bg-[#f7e6ca] text-gray-100 min-h-screen">
            <div class="bg-gray-800 shadow-2xl rounded-xl p-6 md:p-8">
                <header class="mb-8">
                    <h2 class="text-3xl md:text-4xl font-bold text-purple-400 tracking-tight">Employee Management</h2>
                    <p class="mt-2 text-sm text-gray-400">View and update employee details, department, and status.</p>
                </header>
                
                <div id="employee-list-container" class="mb-10 bg-gray-800 shadow-lg rounded-lg p-1 md:p-0 overflow-x-auto">
                    <p class="text-gray-400 p-4">Loading employees...</p>
                </div>

                <div id="edit-employee-form-container" class="hidden bg-[#f7e6ea]/50 backdrop-blur-sm p-6 md:p-8 rounded-xl shadow-xl border border-[#f7e6ea]">
                    <h3 id="edit-form-title" class="text-2xl font-semibold text-purple-300 mb-6 pb-3 border-b border-gray-600">Edit Employee Details</h3>
                    <form id="edit-employee-form" class="space-y-6">
                        <input type="hidden" id="edit_employee_id" name="employee_id">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                            <div>
                                <label for="edit_FirstName" class="block text-sm font-medium text-gray-300 mb-1">First Name</label>
                                <input type="text" id="edit_FirstName" name="FirstName" class="w-full bg-gray-600 border-gray-500 text-gray-200 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 p-3 text-sm" required>
                            </div>
                            <div>
                                <label for="edit_LastName" class="block text-sm font-medium text-gray-300 mb-1">Last Name</label>
                                <input type="text" id="edit_LastName" name="LastName" class="w-full bg-gray-600 border-gray-500 text-gray-200 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 p-3 text-sm" required>
                            </div>
                            <div>
                                <label for="edit_MiddleName" class="block text-sm font-medium text-gray-300 mb-1">Middle Name</label>
                                <input type="text" id="edit_MiddleName" name="MiddleName" class="w-full bg-gray-600 border-gray-500 text-gray-200 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 p-3 text-sm">
                            </div>
                            <div>
                                <label for="edit_Suffix" class="block text-sm font-medium text-gray-300 mb-1">Suffix</label>
                                <input type="text" id="edit_Suffix" name="Suffix" class="w-full bg-gray-600 border-gray-500 text-gray-200 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 p-3 text-sm">
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label for="edit_Email" class="block text-sm font-medium text-gray-300 mb-1">Work Email</label>
                                <input type="email" id="edit_Email" name="Email" class="w-full bg-gray-600 border-gray-500 text-gray-200 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 p-3 text-sm" required>
                            </div>
                            <div>
                                <label for="edit_PersonalEmail" class="block text-sm font-medium text-gray-300 mb-1">Personal Email</label>
                                <input type="email" id="edit_PersonalEmail" name="PersonalEmail" class="w-full bg-gray-600 border-gray-500 text-gray-200 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 p-3 text-sm">
                            </div>
                            <div>
                                <label for="edit_PhoneNumber" class="block text-sm font-medium text-gray-300 mb-1">Phone Number</label>
                                <input type="tel" id="edit_PhoneNumber" name="PhoneNumber" class="w-full bg-gray-600 border-gray-500 text-gray-200 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 p-3 text-sm">
                            </div>
                             <div>
                                <label for="edit_DateOfBirth" class="block text-sm font-medium text-gray-300 mb-1">Date of Birth</label>
                                <input type="date" id="edit_DateOfBirth" name="DateOfBirth" class="w-full bg-gray-600 border-gray-500 text-gray-200 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 p-3 text-sm">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                           <div>
                                <label for="edit_Gender" class="block text-sm font-medium text-gray-300 mb-1">Gender</label>
                                <select id="edit_Gender" name="Gender" class="w-full bg-gray-600 border-gray-500 text-gray-200 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 p-3 text-sm">
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                    <option value="Prefer not to say">Prefer not to say</option>
                                </select>
                            </div>
                            <div>
                                <label for="edit_MaritalStatus" class="block text-sm font-medium text-gray-300 mb-1">Marital Status</label>
                                <select id="edit_MaritalStatus" name="MaritalStatus" class="w-full bg-gray-600 border-gray-500 text-gray-200 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 p-3 text-sm">
                                    <option value="">Select Status</option>
                                    <option value="Single">Single</option>
                                    <option value="Married">Married</option>
                                    <option value="Divorced">Divorced</option>
                                    <option value="Widowed">Widowed</option>
                                </select>
                            </div>
                             <div>
                                <label for="edit_Nationality" class="block text-sm font-medium text-gray-300 mb-1">Nationality</label>
                                <input type="text" id="edit_Nationality" name="Nationality" class="w-full bg-gray-600 border-gray-500 text-gray-200 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 p-3 text-sm">
                            </div>
                            <div>
                                <label for="edit_JobTitle" class="block text-sm font-medium text-gray-300 mb-1">Job Title</label>
                                <input type="text" id="edit_JobTitle" name="JobTitle" class="w-full bg-gray-600 border-gray-500 text-gray-200 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 p-3 text-sm" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                            <div>
                                <label for="edit_DepartmentID" class="block text-sm font-medium text-gray-300 mb-1">Department</label>
                                <select id="edit_DepartmentID" name="DepartmentID" class="w-full bg-gray-600 border-gray-500 text-gray-200 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 p-3 text-sm" required>
                                    <option value="">Loading departments...</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Employee Status</label>
                                <div class="flex items-center space-x-6 pt-2">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" id="edit_IsActive_true" name="IsActive" value="1" class="form-radio h-5 w-5 text-purple-500 bg-gray-600 border-gray-500 focus:ring-purple-500 focus:ring-offset-gray-800">
                                        <span class="ml-2 text-gray-300">Active</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" id="edit_IsActive_false" name="IsActive" value="0" class="form-radio h-5 w-5 text-purple-500 bg-gray-600 border-gray-500 focus:ring-purple-500 focus:ring-offset-gray-800">
                                        <span class="ml-2 text-gray-300">Inactive</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="pt-6 flex items-center justify-end space-x-4 border-t border-gray-600">
                            <button type="button" id="cancel-edit-employee-btn" class="px-5 py-2.5 bg-gray-600 hover:bg-gray-500 text-gray-200 font-semibold rounded-lg shadow-md hover:shadow-lg transition duration-150 ease-in-out text-sm">Cancel</button>
                            <button type="submit" class="px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition duration-150 ease-in-out text-sm">Save Changes</button>
                        </div>
                    </form>
                </div>
                 <div id="admin-action-message" class="mt-6 text-center text-sm"></div>
            </div>
        </div>
    `;

    await loadEmployeesForManagement();
    await populateDepartmentDropdownAdmin('edit_DepartmentID'); // Populate dropdown in edit form

    const editEmployeeForm = document.getElementById('edit-employee-form');
    if (editEmployeeForm) {
        editEmployeeForm.addEventListener('submit', handleUpdateEmployeeForm);
    }
    const cancelEditBtn = document.getElementById('cancel-edit-employee-btn');
    if (cancelEditBtn) {
        cancelEditBtn.addEventListener('click', hideEditEmployeeForm);
    }
}

async function loadEmployeesForManagement() {
    const employeeListContainer = document.getElementById('employee-list-container');
    if (!employeeListContainer) return;

    try {
        const response = await fetch('php/api/get_employees.php');
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({ error: 'Failed to fetch employees. Server returned an error.' }));
            throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
        }
        const employees = await response.json();

        if (employees.length === 0) {
            employeeListContainer.innerHTML = '<p class="text-gray-400 p-4">No employees found.</p>';
            return;
        }

        let tableHTML = `
            <div class="overflow-hidden shadow-md rounded-lg border border-[#f7e6ea]">
                <table class="min-w-full divide-y divide-[#f7e6ea]">
                    <thead class="bg-[#f7e6ea]">
                        <tr>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-purple-300 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-purple-300 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-purple-300 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-purple-300 uppercase tracking-wider">Job Title</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-purple-300 uppercase tracking-wider">Department</th>
                            <th scope="col" class="px-5 py-3.5 text-center text-xs font-semibold text-purple-300 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-5 py-3.5 text-center text-xs font-semibold text-purple-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-gray-800 divide-y divide-[#f7e6ea]">
        `;

        employees.forEach(emp => {
            tableHTML += `
                <tr class="hover:bg-[#f7e6ea] transition duration-150 ease-in-out group">
                    <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-300">${emp.EmployeeID}</td>
                    <td class="px-5 py-4 whitespace-nowrap text-sm font-medium text-gray-200">${emp.FirstName} ${emp.LastName}</td>
                    <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-400">${emp.Email || 'N/A'}</td>
                    <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-400">${emp.JobTitle || 'N/A'}</td>
                    <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-400">${emp.DepartmentName || 'N/A'}</td>
                    <td class="px-5 py-4 whitespace-nowrap text-sm text-center">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${emp.IsActive == 1 ? 'bg-green-600/30 text-green-300 border border-green-500/50' : 'bg-red-600/30 text-red-300 border border-red-500/50'}">
                            ${emp.IsActive == 1 ? 'Active' : 'Inactive'}
                        </span>
                    </td>
                    <td class="px-5 py-4 whitespace-nowrap text-sm font-medium text-center">
                        <button onclick="showEditEmployeeForm(${emp.EmployeeID})" class="text-purple-400 hover:text-purple-300 transition duration-150 ease-in-out group-hover:opacity-100 opacity-70 py-1 px-2 rounded-md hover:bg-purple-500/10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" /><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" /></svg>
                            Edit
                        </button>
                    </td>
                </tr>
            `;
        });

        tableHTML += `
                    </tbody>
                </table>
            </div>
        `;
        employeeListContainer.innerHTML = tableHTML;

    } catch (error) {
        console.error('Error loading employees:', error);
        employeeListContainer.innerHTML = `<div class="p-4 bg-red-800/20 border border-red-700 rounded-lg text-red-300">Error loading employees: ${error.message}</div>`;
    }
}

async function populateDepartmentDropdownAdmin(selectElementId) {
    const selectElement = document.getElementById(selectElementId);
    if (!selectElement) {
        console.error(`Department select element with ID '${selectElementId}' not found.`);
        return;
    }

    try {
        const response = await fetch('php/api/get_org_structure.php'); 
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const departments = await response.json();
        
        selectElement.innerHTML = '<option value="" class="text-gray-500">Select Department</option>'; 
        if (departments && departments.length > 0) {
            departments.forEach(dept => {
                const option = document.createElement('option');
                option.value = dept.DepartmentID; 
                option.textContent = dept.DepartmentName;
                option.classList.add('bg-gray-600', 'text-gray-200');
                selectElement.appendChild(option);
            });
        } else {
            selectElement.innerHTML = '<option value="" class="text-gray-500">No departments found</option>';
        }
    } catch (error) {
        console.error('Error fetching departments for dropdown:', error);
        selectElement.innerHTML = '<option value="" class="text-gray-500">Error loading departments</option>';
    }
}

async function showEditEmployeeForm(employeeId) {
    console.log(`Editing employee ID: ${employeeId}`);
    currentEditEmployeeId = employeeId;
    const formContainer = document.getElementById('edit-employee-form-container');
    const formTitle = document.getElementById('edit-form-title');
    const form = document.getElementById('edit-employee-form');

    if (!formContainer || !form || !formTitle) {
        console.error('Edit form elements not found.');
        return;
    }
    formTitle.textContent = `Edit Employee (ID: ${employeeId})`;
    form.reset(); 
    document.getElementById('edit_employee_id').value = employeeId;
    displayAdminActionMessage('', 'info');

    try {
        // Attempt to fetch a single employee if API supports it
        // Otherwise, fallback to filtering the main list
        let employeeData = null;
        try {
            const singleEmpResponse = await fetch(`php/api/get_employees.php?employee_id=${employeeId}`);
            if (singleEmpResponse.ok) {
                const singleEmpData = await singleEmpResponse.json();
                // Assuming API returns an array for single, or just the object. Adjust if needed.
                if (Array.isArray(singleEmpData) && singleEmpData.length > 0) {
                    employeeData = singleEmpData[0];
                } else if (!Array.isArray(singleEmpData) && singleEmpData) {
                    employeeData = singleEmpData; 
                }
            }
        } catch (e) {
            console.warn("Fetching single employee failed, will try to find in full list.", e);
        }

        if (!employeeData) { // Fallback if single fetch failed or not supported
            console.log("Single employee fetch failed or data not found, fetching all employees to find the specific one.");
            const allEmployeesResponse = await fetch('php/api/get_employees.php');
            if (!allEmployeesResponse.ok) throw new Error('Failed to fetch employee list for editing.');
            const allEmployees = await allEmployeesResponse.json();
            employeeData = allEmployees.find(emp => emp.EmployeeID == employeeId);
        }
        
        if (!employeeData) throw new Error(`Employee with ID ${employeeId} not found.`);
        
        populateEditFormFields(employeeData);
        formContainer.classList.remove('hidden');
        formContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });

    } catch (error) {
        console.error('Error fetching employee details for edit:', error);
        displayAdminActionMessage(`Error loading employee data: ${error.message}`, 'error');
    }
}

function populateEditFormFields(employeeData) {
    if (!employeeData) return;
    
    document.getElementById('edit_FirstName').value = employeeData.FirstName || '';
    document.getElementById('edit_LastName').value = employeeData.LastName || '';
    document.getElementById('edit_MiddleName').value = employeeData.MiddleName || '';
    document.getElementById('edit_Suffix').value = employeeData.Suffix || '';
    document.getElementById('edit_Email').value = employeeData.Email || '';
    document.getElementById('edit_PersonalEmail').value = employeeData.PersonalEmail || '';
    document.getElementById('edit_PhoneNumber').value = employeeData.PhoneNumber || '';
    document.getElementById('edit_DateOfBirth').value = employeeData.DateOfBirth || ''; // Assumes YYYY-MM-DD
    document.getElementById('edit_Gender').value = employeeData.Gender || '';
    document.getElementById('edit_MaritalStatus').value = employeeData.MaritalStatus || '';
    document.getElementById('edit_Nationality').value = employeeData.Nationality || '';
    document.getElementById('edit_JobTitle').value = employeeData.JobTitle || '';
    document.getElementById('edit_DepartmentID').value = employeeData.HR12_DepartmentID || employeeData.DepartmentID || ''; 

    if (String(employeeData.IsActive) === "1") { // Ensure comparison is robust
        document.getElementById('edit_IsActive_true').checked = true;
    } else {
        document.getElementById('edit_IsActive_false').checked = true;
    }
}

function hideEditEmployeeForm() {
    const formContainer = document.getElementById('edit-employee-form-container');
    if (formContainer) {
        formContainer.classList.add('hidden');
    }
    currentEditEmployeeId = null;
    displayAdminActionMessage('', 'info');
}

async function handleUpdateEmployeeForm(event) {
    event.preventDefault();
    console.log('Handling update employee form submission.');

    const form = event.target;
    const formData = new FormData(form);
    const employeeDataToUpdate = {};
    
    for (let [key, value] of formData.entries()) {
        employeeDataToUpdate[key] = value;
    }
    
    if (!employeeDataToUpdate.employee_id) {
        displayAdminActionMessage('Error: Employee ID is missing for update.', 'error');
        return;
    }

    console.log('Data to send for update:', employeeDataToUpdate);

    try {
        const response = await fetch('php/api/add_employee_and_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(employeeDataToUpdate),
        });

        const result = await response.json();

        if (response.ok && !result.error) {
            displayAdminActionMessage(result.message || 'Employee updated successfully!', 'success');
            await loadEmployeesForManagement(); 
            hideEditEmployeeForm();
        } else {
            let errorMessage = 'Failed to update employee.';
            if (result.error) {
                errorMessage = result.error;
                if (result.details) {
                    errorMessage += ' Details: ' + Object.values(result.details).join(', ');
                }
            }
            displayAdminActionMessage(errorMessage, 'error');
        }
    } catch (error) {
        console.error('Error submitting employee update form:', error);
        displayAdminActionMessage(`Network or server error: ${error.message}`, 'error');
    }
}

function displayAdminActionMessage(message, type = 'info') {
    const messageArea = document.getElementById('admin-action-message');
    if (messageArea) {
        messageArea.textContent = message;
        messageArea.className = 'mt-6 text-center text-sm '; 
        if (type === 'success') {
            messageArea.classList.add('text-green-400');
        } else if (type === 'error') {
            messageArea.classList.add('text-red-400');
        } else {
            messageArea.classList.add('text-blue-400');
        }
    }
}

window.showEditEmployeeForm = showEditEmployeeForm;
window.hideEditEmployeeForm = hideEditEmployeeForm; 
window.populateDepartmentDropdownAdmin = populateDepartmentDropdownAdmin; 

console.log('user_management.js loaded with UI enhancements.');
