jQuery(document).ready(function($) {
    $(document).on('click', '#show-register', function(e) {
        e.preventDefault();
        $.post(customAuthAjax.ajaxurl, { action: 'load_register_form' }, function(response) {
            $('#custom-auth-content').html(response);
        });
    });

    $(document).on('click', '#show-login', function(e) {
        e.preventDefault();
        $.post(customAuthAjax.ajaxurl, { action: 'load_login_form' }, function(response) {
            $('#custom-auth-content').html(response);
        });
    });

    $(document).on('submit', '#login-form', function(e) {
        e.preventDefault();
        $.post(customAuthAjax.ajaxurl, $(this).serialize() + '&action=ajax_login', function(response) {
            let result = JSON.parse(response);
            if (result.success) {
                window.location.reload();
            } else {
                $('#login-message').text(result.error);
            }
        });
    });

    $(document).on('submit', '#register-form', function(e) {
        e.preventDefault();
        $.post(customAuthAjax.ajaxurl, $(this).serialize() + '&action=ajax_register', function(response) {
            let result = JSON.parse(response);
            if (result.success) {
                $.post(customAuthAjax.ajaxurl, { action: 'load_login_form' }, function(response) {
                    $('#custom-auth-content').html(response);
                    $('#login-message').text(result.success);
                });
            } else {
                $('#register-message').text(result.error);
            }
        });
    });
});
