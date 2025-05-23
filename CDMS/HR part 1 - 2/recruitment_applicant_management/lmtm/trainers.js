function editTrainer(id, fullName, email) {
    document.getElementById('edit_trainer_id').value = id;
    document.getElementById('edit_full_name').value = fullName;
    document.getElementById('edit_email').value = email;
}

function openEditTrainerModal() {
    document.getElementById('editTrainerModal').style.display = 'block';
}

function closeEditTrainerModal() {
    document.getElementById('editTrainerModal').style.display = 'none';
}

function viewTrainer(id, fullName, email) {
    document.getElementById('view_full_name').textContent = fullName;
    document.getElementById('view_email').textContent = email;
}

function deleteTrainer(id) {
    if (confirm('Are you sure you want to delete this trainer?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'trainers_actions.php';

        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'delete';

        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'trainerID';
        idInput.value = id;

        form.appendChild(actionInput);
        form.appendChild(idInput);
        document.body.appendChild(form);
        form.submit();
    }
}