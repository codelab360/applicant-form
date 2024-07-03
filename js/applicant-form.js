// js/applicant-form.js

jQuery(document).ready(function($) {
    $('#applicant_form').submit(function(e) {
        var errors = false;

        if ($('#first_name').val().trim() === '') {
            $('#first_name').addClass('border-red-500');
            errors = true;
        } else {
            $('#first_name').removeClass('border-red-500');
        }

        if ($('#last_name').val().trim() === '') {
            $('#last_name').addClass('border-red-500');
            errors = true;
        } else {
            $('#last_name').removeClass('border-red-500');
        }

        if (errors) {
            e.preventDefault();
        }
    });

    $('#applicant_form input, #applicant_form textarea').focus(function() {
        $(this).removeClass('border-red-500');
    });
});
