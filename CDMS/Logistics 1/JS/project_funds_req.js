function openModal(action, projectId) {
  const modal = document.getElementById('approvalModal');
  const title = document.getElementById('modalTitle');
  const message = document.getElementById('modalMessage');
  const actionInput = document.getElementById('action');
  const projectInput = document.getElementById('project_id');

  if (action === 'approve') {
    title.textContent = "Approve Project";
    message.textContent = "Are you sure you want to approve this project?";
  } else if (action === 'deny') {
    title.textContent = "Deny Project";
    message.textContent = "Are you sure you want to deny this project?";
  }

  actionInput.value = action;
  projectInput.value = projectId;

  modal.classList.remove('hidden');
}

function closeModal() {
  document.getElementById('approvalModal').classList.add('hidden');
}