$(document).ready(function() {
    // Function to handle click event for view button
    $('.view-button').on('click', function(event) {
        event.preventDefault(); 
        var caseId = $(this).data('case-id');
     
        $.get('view_case.php', {id: caseId}, function(data) {
            $('#caseDetails').html(data);
            $('#viewModal').modal('show'); 
        });
    });

    // Function to handle click event for edit button
    $(document).on('click', '.edit-button', function(event) {
        event.preventDefault(); 
        var caseId = $(this).data('case-id');
       
        $.get('edit_case_form.php', {id: caseId}, function(data) {
            $('#edit-case-form').html(data);
            $('#editModal').modal('show'); 
        });
    });

    // Function to handle submit event for dynamically added edit case form
    $(document).on('submit', '#edit-case-form', function(event) {
        event.preventDefault(); 
        var formData = new FormData($(this)[0]); // Use FormData to handle file uploads
        $.ajax({
            url: 'edit_case.php',
            method: 'POST',
            data: formData,
            processData: false, // Important for file uploads
            contentType: false, // Important for file uploads
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#editModal').modal('hide');
                    location.reload(); 
                } else {
                    alert('Failed to update reservation.');
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });

    // Function to handle toggling of navbar
    const showNavbar = (toggleId, navId, bodyId, headerId) =>{
        const toggle = document.getElementById(toggleId),
            nav = document.getElementById(navId),
            bodypd = document.getElementById(bodyId),
            headerpd = document.getElementById(headerId);
        
        if(toggle && nav && bodypd && headerpd){
            toggle.addEventListener('click', ()=>{
                nav.classList.toggle('show');
                toggle.classList.toggle('bx-x');
                bodypd.classList.toggle('body-pd');
                headerpd.classList.toggle('body-pd');
            });

            const navLinks = nav.querySelectorAll('.nav_link');
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    nav.classList.remove('show');
                    toggle.classList.remove('bx-x');
                    bodypd.classList.remove('body-pd');
                    headerpd.classList.remove('body-pd');
                });
            });
        }
    };

    // Call the function to show navbar
    showNavbar('header-toggle','nav-bar','body-pd','header');
    
    // Function to handle link color change
    const linkColor = document.querySelectorAll('.nav_link');
    function colorLink(){
        if(linkColor){
            linkColor.forEach(l=> l.classList.remove(''));
            this.classList.add('');
        }
    }
    linkColor.forEach(l=> l.addEventListener('click', colorLink));
});
