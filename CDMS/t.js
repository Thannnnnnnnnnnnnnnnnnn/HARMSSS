$(document).ready(function () {
    $('#sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');

        // Ensure icons remain visible when collapsed
        if ($('#sidebar').hasClass('active')) {
            $('.list-unstyled.components li a i').css('margin-right', '0');
        } else {
            $('.list-unstyled.components li a i').css('margin-right', '10px');
        }
    });
});
