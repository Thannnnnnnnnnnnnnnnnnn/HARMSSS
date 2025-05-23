<script>
    //--------------------------------------FOR MODALS FUNCTIONALITY----------------------------------------------------//
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function openViewModal(data) {
        document.getElementById('viewGuestID').textContent = data.status_id;
        document.getElementById('viewreservationID').textContent = data.reservation_id;
        document.getElementById('viewRoomname').textContent = data.room_name;
        document.getElementById('viewGuestName').textContent = data.guest_name;
        document.getElementById('viewStatus').textContent = data.status;
        document.getElementById('viewUpdatedAt').textContent = data.updated_at;
        openModal('viewModal');
    }


    function openEditModal(data) {
        // Set the modal fields with the data passed from the button
        document.getElementById('editGuestName').value = data.guest_name;
        document.getElementById('editPhone').value = data.phone;
        document.getElementById('editAddress').value = data.address;
        document.getElementById('editDateOfBirth').value = data.date_of_birth;
        document.getElementById('editEmail').value = data.email;
        document.getElementById('editGender').value = data.gender;
        document.getElementById('editNationality').value = data.nationality;
        document.getElementById('editReservation').value = data.reservation;
        document.getElementById('editCheckIn').value = data.check_in;
        document.getElementById('editCheckOut').value = data.check_out;
        document.getElementById('editStatus').value = data.status;
        document.getElementById('editRoomId').value = data.room_id;

        // Display the edit modal
        document.getElementById('editModal').classList.remove('hidden');
    }

    // Function to close the modal
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }




    //--------------------------------------END MODALS FUNCTIONALITY-----------------------------------------------------------//

    //----------------------------------------SEARCH BAR---------------------------------------------------------------------//

    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("searchInput");
        const clearButton = document.getElementById("clearSearch");
        const tableRows = document.querySelectorAll("#customerTable tbody tr");

        searchInput.addEventListener("keyup", function() {
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

        clearButton.addEventListener("click", function() {
            searchInput.value = "";
            tableRows.forEach(row => row.style.display = ""); // Show all rows again
        });
    });
    //--------------------------------------------END-------------------------------------------//

    const menu = document.querySelector('.menu-btn');
    const sidebar = document.querySelector('.sidebar');
    const main = document.querySelector('.main');
    const overlay = document.getElementById('sidebar-overlay');
    const close = document.getElementById('close-sidebar-btn');

    function closeSidebar() {
        sidebar.classList.remove('mobile-active');
        overlay.classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    function openSidebar() {
        sidebar.classList.add('mobile-active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function toggleSidebar() {
        if (window.innerWidth <= 968) {
            sidebar.classList.add('sidebar-expanded');
            sidebar.classList.remove('sidebar-collapsed');
            sidebar.classList.contains('mobile-active') ? closeSidebar() : openSidebar();
        } else {
            sidebar.classList.toggle('sidebar-collapsed');
            sidebar.classList.toggle('sidebar-expanded');
            main.classList.toggle('md:ml-[85px]');
            main.classList.toggle('md:ml-[360px]');
        }
    }

    menu.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', closeSidebar);
    close.addEventListener('click', closeSidebar);

    window.addEventListener('resize', () => {
        if (window.innerWidth > 968) {
            closeSidebar();
            sidebar.classList.remove('mobile-active');
            overlay.classList.remove('active');
            sidebar.classList.remove('sidebar-collapsed');
            sidebar.classList.add('sidebar-expanded');
        } else {
            sidebar.classList.add('sidebar-expanded');
            sidebar.classList.remove('sidebar-collapsed');
        }
    });

    function toggleDropdown(dropdownId, element) {
        const dropdown = document.getElementById(dropdownId);
        const icon = element.querySelector('.arrow-icon');
        const allDropdowns = document.querySelectorAll('.menu-drop');
        const allIcons = document.querySelectorAll('.arrow-icon');

        allDropdowns.forEach(d => {
            if (d !== dropdown) d.classList.add('hidden');
        });

        allIcons.forEach(i => {
            if (i !== icon) {
                i.classList.remove('bx-chevron-down');
                i.classList.add('bx-chevron-right');
            }
        });

        dropdown.classList.toggle('hidden');
        icon.classList.toggle('bx-chevron-right');
        icon.classList.toggle('bx-chevron-down');
    }



    document.getElementById("reservation").addEventListener("click", function() {
        window.location.href = "index.php"; // Redirect to the reservation page
    });
</script>


</body>

</html>