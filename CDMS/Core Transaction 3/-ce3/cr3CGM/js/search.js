document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const clearButton = document.getElementById("clearSearch");
    const tableRows = document.querySelectorAll("#customerTable tbody tr");

    searchInput.addEventListener("keyup", function () {
        let filter = searchInput.value.toLowerCase();

        tableRows.forEach(row => {
            let customerID = row.cells[0].textContent.toLowerCase();
            let name = row.cells[1].textContent.toLowerCase();
            let phone = row.cells[2].textContent.toLowerCase();
            let status = row.cells[3].textContent.toLowerCase();

            if (customerID.includes(filter) || name.includes(filter) || phone.includes(filter) || status.includes(filter)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    });

    clearButton.addEventListener("click", function () {
        searchInput.value = "";
        tableRows.forEach(row => row.style.display = ""); // Show all rows again
    });
});