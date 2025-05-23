function openModal(modalID) {
    document.getElementById(modalID).style.display = 'flex';
}

function closeModal(modalID) {
    document.getElementById(modalID).style.display = 'none';
}

function openEditModal(id, fullName, email) {
    document.getElementById('editEmployeeID').value = id;
    document.getElementById('editFullName').value = fullName;
    document.getElementById('editEmail').value = email;
    openModal('editEmployeeModal');
}

function viewEmployee(fullName, email) {
    document.getElementById('viewFullName').innerText = fullName;
    document.getElementById('viewEmail').innerText = email;
    openModal('viewEmployeeModal');
}

function deleteEmployee(id) {
    if (confirm('Are you sure you want to delete this employee?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'employees_actions.php';

        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'delete';

        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'employeeID';
        idInput.value = id;

        form.appendChild(actionInput);
        form.appendChild(idInput);
        document.body.appendChild(form);
        form.submit();
    }
}