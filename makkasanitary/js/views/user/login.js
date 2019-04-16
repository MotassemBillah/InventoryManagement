$(document).ready(function() {
    $("#frmLogin").validate({
        submitHandler: function(form) {
            $("#ajaxMessage").showAjaxMessage({html: "Authenticating...", type: 'success'});
            if (navigator.onLine) {
                $(form).doLogin();
            } else {
                $("#ajaxMessage").showAjaxMessage({html: "No Internet Connection", type: 'warning'});
            }
            return false;
        },
        rules: {
            LoginForm_username: {
                required: true
            },
            LoginForm_password: {
                required: true,
                minlength: 4
            }
        },
        messages: {
            LoginForm_username: {
                required: "Username must be supplied!"
            },
            LoginForm_password: {
                required: "Password must be supplied!",
                minlength: "Password must be at least 4 characters."
            }
        }
    });

    $(document).on("click", "#show_charecter", function() {
        var field = document.getElementById('LoginForm_password');
        var icon = $(this).find('i');
        if (field.type == 'password') {
            field.type = 'text';
            $(icon).removeClass('fa-eye-slash').addClass('fa-eye');
        } else {
            field.type = 'password';
            $(icon).removeClass('fa-eye').addClass('fa-eye-slash');
        }
    });
});

$.fn.doLogin = function() {
    var _returnUrl = location.search.split('returnUrl=')[1] ? location.search.split('returnUrl=')[1] : '';
    var _freshReturnUrl = unescape(_returnUrl);
    var _goToUrl = '';

    if (_returnUrl == "") {
        _goToUrl = baseUrl + '/dashboard';
    } else {
        _goToUrl = _freshReturnUrl;
    }

    var _form = $(this);
    var _config = {};

    _config.url = ajaxUrl + '/user/login';
    _config.data = _form.serialize();
    _config.respTo = $("#ajaxMessage");
    _config.done = function(data) {
        data = filterAjaxResponse(data);
        if (data.success === true) {
            $("#ajaxMessage").showAjaxMessage({html: data.message, type: 'success'});
            redirectTo(_goToUrl);
        } else {
            $("#ajaxMessage").showAjaxMessage({html: data.exception, type: 'error'});
        }
    };

    var _xhr = $("#ajaxMessage").callAjax(_config);
}
