

function openModal() {
    document.getElementById("visitModal").classList.remove("hidden");
  }

  function closeModal() {
    document.getElementById("visitModal").classList.add("hidden");
  }

  // Auto-fill today's date
  window.addEventListener("DOMContentLoaded", () => {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById("date").value = today;
  });

  function showViewModal(ID, RD, ST, IT, TN, QU, EB, PE) {
    document.getElementById("ID").textContent = ID;
    document.getElementById("RD").textContent = RD;
    document.getElementById("ST").textContent = ST;
    document.getElementById("IT").textContent = IT;
    document.getElementById("TN").textContent = TN;
    document.getElementById("QU").textContent = QU;
    document.getElementById("EB").textContent = EB;
    document.getElementById("PE").textContent = PE;


    document.getElementById("viewModal").classList.remove("hidden");
}

function closeViewModal() {
  document.getElementById("viewModal").classList.add("hidden");
}
