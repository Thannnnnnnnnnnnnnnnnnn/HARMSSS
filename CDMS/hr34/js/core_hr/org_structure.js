/**
 * Core HR - Organizational Structure / Departments Module
 * v3.2 - Simplified to manage a flat list of departments with employee counts.
 * - Removed hierarchical features and related form fields.
 * - Form now only handles Department Name.
 * - Display changed to a list/card view for departments.
 * v3.1 - Renamed exported function to displayOrgStructureSection for consistency.
 * v3.0 - Displays hierarchical structure from DB and allows adding new departments/modules.
 * v2.2 - Simplified Existing Departments table to show only Department Name.
 */
import { API_BASE_URL } from '../utils.js'; // Assuming populateEmployeeDropdown is not needed here anymore

/**
 * Displays the Departments section.
 */
export async function displayOrgStructureSection() {
    console.log("[Display] Displaying Departments Section...");
    const pageTitleElement = document.getElementById('page-title');
    const mainContentArea = document.getElementById('main-content-area');

    if (!pageTitleElement || !mainContentArea) {
        console.error("displayOrgStructureSection: Core DOM elements not found.");
        if(mainContentArea) mainContentArea.innerHTML = `<p class="text-red-500 p-4">Error initializing department section elements.</p>`;
        return;
    }

    pageTitleElement.textContent = 'Manage Departments';
    mainContentArea.innerHTML = `
        <div class="bg-white p-6 rounded-lg shadow-md border border-[#F7E6CA] space-y-6">
            <div class="border-b border-gray-200 pb-4">
                 <h3 class="text-lg font-semibold text-[#4E3B2A] mb-3 font-header">Add New Department</h3>
                 <form id="add-department-form" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="dept-name" class="block text-sm font-medium text-gray-700 mb-1">Department Name:</label>
                            <input type="text" id="dept-name" name="department_name" required placeholder="e.g., Marketing, Finance" class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#4E3B2A] focus:border-[#4E3B2A]">
                        </div>
                        <!-- 
                        <div>
                            <label for="dept-description" class="block text-sm font-medium text-gray-700 mb-1">Description (Optional):</label>
                            <textarea id="dept-description" name="description" rows="2" placeholder="Brief description..." class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#4E3B2A] focus:border-[#4E3B2A]"></textarea>
                        </div>
                        -->
                    </div>
                     <div class="pt-2 space-x-3">
                        <button type="submit" class="px-4 py-2 bg-[#594423] text-white rounded-md hover:bg-[#4E3B2A] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#594423] transition duration-150 ease-in-out">
                            Add Department
                        </button>
                    </div>
                 </form>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-[#4E3B2A] mb-3 font-header">Existing Departments</h3>
                <div id="departments-list-display-container" class="space-y-3">
                     <p class="text-center py-4">Loading departments...</p>
                </div>
            </div>
        </div>
    `;
    
    requestAnimationFrame(async () => {
        const addDepartmentForm = document.getElementById('add-department-form');
        if (addDepartmentForm) {
            if (!addDepartmentForm.hasAttribute('data-listener-attached')) {
                addDepartmentForm.addEventListener('submit', handleAddDepartment);
                addDepartmentForm.setAttribute('data-listener-attached', 'true');
            }
        } else {
            console.error("Add Department form not found.");
        }
        await loadAndRenderDepartmentsList();
    });
}

/**
 * Fetches department data (including employee count) and renders it as a list.
 */
async function loadAndRenderDepartmentsList() {
    console.log("[Load] Loading Departments List...");
    const displayContainer = document.getElementById('departments-list-display-container');

    if (!displayContainer) {
         console.error("Departments list display container not found!");
         if(displayContainer) displayContainer.innerHTML = '<p class="text-red-500">Error: UI elements missing for department list.</p>';
         return;
    }
    displayContainer.innerHTML = '<p class="text-center py-4">Loading departments...</p>';

    try {
        const response = await fetch(`${API_BASE_URL}get_org_structure.php`); // This API now returns DepartmentID, DepartmentName, EmployeeCount
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`HTTP error! status: ${response.status}, Response: ${errorText.substring(0,150)}`);
        }
        
        const departmentsData = await response.json();

        if (departmentsData.error) {
             console.error("[loadAndRenderDepartmentsList] API returned error:", departmentsData.error);
             displayContainer.innerHTML = `<p class="text-red-500 text-center py-4">Error: ${departmentsData.error}</p>`;
        } else {
            renderDepartmentsList(displayContainer, departmentsData);
            // No parent dropdown to populate in this simplified version
        }
    } catch (error) {
        console.error('[loadAndRenderDepartmentsList] Error:', error);
        displayContainer.innerHTML = `<p class="text-red-500 text-center py-4">Could not load departments. ${error.message}</p>`;
    }
}

/**
 * Renders the list of departments.
 * @param {HTMLElement} container - The HTML element to render into.
 * @param {Array} departments - The array of department data from API (should include DepartmentName and EmployeeCount).
 */
function renderDepartmentsList(container, departments) {
    container.innerHTML = ''; // Clear loading or previous content

    if (!departments || departments.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center py-4">No departments defined yet.</p>';
        return;
    }

    const ul = document.createElement('ul');
    ul.className = 'space-y-2';

    departments.forEach(dept => {
        const li = document.createElement('li');
        li.className = 'p-3 rounded-md border bg-gray-50 border-gray-200 flex justify-between items-center';
        
        const departmentNameSpan = document.createElement('span');
        departmentNameSpan.className = 'text-md font-medium text-[#4E3B2A]';
        departmentNameSpan.textContent = dept.DepartmentName;

        const employeeCountSpan = document.createElement('span');
        employeeCountSpan.className = 'text-sm text-gray-600 bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full';
        employeeCountSpan.textContent = `${dept.EmployeeCount} employee${dept.EmployeeCount !== 1 ? 's' : ''}`;
        
        li.appendChild(departmentNameSpan);
        li.appendChild(employeeCountSpan);
        ul.appendChild(li);
    });
    container.appendChild(ul);
}


/**
 * Handles the submission of the add department form.
 */
async function handleAddDepartment(event) {
    event.preventDefault();
    const form = event.target;
    const submitButton = form.querySelector('button[type="submit"]');
    if (!submitButton) return;

    const departmentName = form.elements['department_name'].value.trim();
    // const description = form.elements['description'] ? form.elements['description'].value.trim() : null; // If description field were present

    if (!departmentName) {
        Swal.fire({ icon: 'error', title: 'Validation Error', text: 'Department Name is required.', confirmButtonColor: '#4E3B2A' });
        return;
    }

    const formData = {
        department_name: departmentName,
        // description: description // Include if description is part of the form and API
    };

    Swal.fire({
        title: 'Processing...',
        text: 'Adding new department, please wait.',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
    submitButton.disabled = true;

    try {
        const response = await fetch(`${API_BASE_URL}add_department.php`, { // New API endpoint
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        
        const resultText = await response.text();
        let result;
        try { 
            result = JSON.parse(resultText); 
        } catch (e) { 
            console.error("Failed to parse add_department response:", resultText);
            throw new Error("Invalid response from server after adding department.");
        }

        if (!response.ok) {
            let errorMessage = result.error || `HTTP error! Status: ${response.status}`;
            if (result.details && typeof result.details === 'object') {
                errorMessage += ' Details: ' + Object.values(result.details).join(', ');
            } else if (result.details) {
                errorMessage += ' Details: ' + result.details;
            }
            throw new Error(errorMessage);
        }
        
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: result.message || 'Department added successfully!',
            confirmButtonColor: '#4E3B2A',
            timer: 2000
        });
        form.reset();
        await loadAndRenderDepartmentsList(); // Refresh the list
    } catch (error) {
        console.error('Error adding department:', error);
        Swal.fire({
            icon: 'error',
            title: 'Operation Failed',
            text: `Error: ${error.message}`,
            confirmButtonColor: '#4E3B2A'
        });
    } finally {
        submitButton.disabled = false;
        if (Swal.isLoading()) { Swal.close(); }
    }
}
