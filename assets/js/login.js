/* global MedbookLogin, jQuery */
(function ($) {
    'use strict';

    $(function () {
        var $loginForm = $('#medbook-login-form');
        var $loginSubmit = $('#medbook-login-submit');
        var $error = $('#medbook-login-error');
        var $errorText = $('#medbook-login-error-text');

        var $forgotForm = $('#medbook-forgot-form');
        var $forgotSubmit = $('#medbook-forgot-submit');
        var $forgotSuccess = $('#medbook-forgot-success');
        var $forgotSuccessText = $('#medbook-forgot-success-text');

        $('#medbook-pwd-toggle').on('click', function () {
            var $pwd = $('#medbook-pwd');
            var isHidden = $pwd.attr('type') === 'password';
            $pwd.attr('type', isHidden ? 'text' : 'password');
        });

        // Swaps the login form out for the "forgot password" form in place —
        // same card, no navigation — and back again via the back link.
        $('#medbook-forgot-link').on('click', function (e) {
            e.preventDefault();
            $error.hide();
            $loginForm.hide();
            $forgotForm.show();
            $('#medbook-forgot-login').trigger('focus');
        });
        $('#medbook-back-to-login').on('click', function (e) {
            e.preventDefault();
            $forgotSuccess.hide();
            $forgotForm.hide();
            $loginForm.show();
            $('#medbook-log').trigger('focus');
        });

        $loginForm.on('submit', function (e) {
            e.preventDefault();
            if ($loginSubmit.hasClass('is-loading')) {
                return;
            }

            $error.hide();
            $loginSubmit.addClass('is-loading').prop('disabled', true);

            $.post(MedbookLogin.ajaxUrl, {
                action: 'medbook_login',
                nonce: MedbookLogin.nonce,
                log: $('#medbook-log').val(),
                pwd: $('#medbook-pwd').val(),
                rememberme: $('#medbook-rememberme').is(':checked') ? 1 : 0
            })
                .done(function (response) {
                    if (response && response.success) {
                        window.location.href = (response.data && response.data.redirect) || window.location.href;
                        return;
                    }
                    var message = (response && response.data && response.data.message) || 'Incorrect username or password.';
                    $errorText.text(message);
                    $error.show();
                    $loginSubmit.removeClass('is-loading').prop('disabled', false);
                })
                .fail(function () {
                    $errorText.text('Request failed. Please try again.');
                    $error.show();
                    $loginSubmit.removeClass('is-loading').prop('disabled', false);
                });
        });

        $forgotForm.on('submit', function (e) {
            e.preventDefault();
            if ($forgotSubmit.hasClass('is-loading')) {
                return;
            }

            $forgotSuccess.hide();
            $forgotSubmit.addClass('is-loading').prop('disabled', true);

            $.post(MedbookLogin.ajaxUrl, {
                action: 'medbook_forgot_password',
                nonce: MedbookLogin.nonce,
                user_login: $('#medbook-forgot-login').val()
            })
                .done(function (response) {
                    var message = response && response.data && response.data.message;
                    $forgotSuccessText.text(message || 'Request completed.');
                    $forgotSuccess.show();
                    if (response && response.success) {
                        $forgotForm[0].reset();
                    }
                })
                .fail(function () {
                    $forgotSuccessText.text('Request failed. Please try again.');
                    $forgotSuccess.show();
                })
                .always(function () {
                    $forgotSubmit.removeClass('is-loading').prop('disabled', false);
                });
        });
    });
})(jQuery);
