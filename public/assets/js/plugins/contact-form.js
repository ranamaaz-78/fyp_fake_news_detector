/**
 *
 * -----------------------------------------------------------------------------
 *
 * Template : Flowbit Saas Landing HTML Template
 * Author : themewant
 * Author URI : https://themewant.com/ 
 *
 * -----------------------------------------------------------------------------
 *
 **/

(function ($) {
    'use strict';

    var form = $('#contact-form');
    var formMessages = $('#form-messages');

    if (!form.length) return;

    form.on('submit', function (e) {
        e.preventDefault();

        var fd = new FormData(this);

        $.ajax({
            type: 'POST',
            url: $(this).attr('action') || 'mailer.php',
            data: fd,
            processData: false,
            contentType: false
        }).done(function (response) {
            formMessages.removeClass('error').addClass('success').text(response);
            form[0].reset();
        }).fail(function (data) {
            formMessages.removeClass('success').addClass('error');
            if (data.responseText !== '') {
                formMessages.text(data.responseText);
            } else {
                formMessages.text('Oops! An error occured and your message could not be sent.');
            }
        });
    });

})(jQuery);
