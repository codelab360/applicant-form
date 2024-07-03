jQuery(document).ready(function($) {
    $('#applicant_form').submit(function(e) {
        e.preventDefault(); 

        var errors = false;

        // Validation logic
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
            return; 
        }

        var formData = new FormData(this);

        $.ajax({
            url: applicant_form_ajax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#form-message').html('<div class="bg-blue-500 text-white font-bold py-2 px-4 rounded">Processing your request...</div>');
            },
            success: function(response) {
                if (response.success) {
                    $('#form-message').html('<div class="bg-green-500 text-white font-bold py-2 px-4 rounded">' + response.data.message + '</div>');
                    $('#applicant_form')[0].reset();
                } else {
                    $('#form-message').html('<div class="bg-red-500 text-white font-bold py-2 px-4 rounded">' + response.data.message + '</div>');
                }
            },
            error: function(xhr, status, error) {
                console.log('error', xhr, status, error);
                $('#form-message').html('<div class="bg-red-500 text-white font-bold py-2 px-4 rounded">An error occurred while processing your request. Please try again later.</div>');
            }
        });

        $('#form-message').fadeOut(5000, function() {
            $(this).html('');
        });
    });

    $('#applicant_form input, #applicant_form textarea').focus(function() {
        $(this).removeClass('border-red-500');
    });
});
