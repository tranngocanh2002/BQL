$(document).ready(function () {
    var action = '';

    $('#btn-send-mail, #btn-save-mail').click(function () {

        if (this.id == 'btn-send-mail') {
            action = 'send';
        }
        if (this.id == 'btn-save-mail') {
            action = 'save';
        }
    });

    $(".sendemail-form form").validate({
        rules: {
            'email': {
                required: true
            },
            'subject': {
                required: true
            },
            'content': {
                required: true
            }
        },
        submitHandler: function (form, event) {
            event.preventDefault();

            var to = $('input[name="email"]').val()
            var subject = $('input[name="subject"]').val()
            var content = $('textarea[name="content"]').val()

            $.ajax({
                url: 'sendmail',
                type: 'post',
                dataType: 'json',
                data: {
                    to: to,
                    subject: subject,
                    content: content,
                    action: action
                },
                success: function (data) {
                    if (data.status == 'success') {
                        var element = '<div class="alert alert-success alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a><p>' + data.message + '</p></div>';
                        $('.alert-sendmail').html(element);
                        return;
                    } else {
                        var element = '<div class="alert alert-danger alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a><p>' + data.message + '</p></div>';
                        $('.alert-sendmail').html(element);
                        return;
                    }
                },
                error: function (data) {
                    // window.location.href = '/home/index'; return;
                }
            })
        }
    });

    setTimeout(function () {
        $('.alert').remove();
    }, 5000);

    $('.home-is-active').click(function () {
        if (!confirm('Are you sure?')) {
            return true;
        }
        var anchor = this;
        var home_id = $(anchor).closest('tr').data('key');
        var data_old_value = $(anchor).attr('data-old-value');
        var is_active_value = parseInt(data_old_value) == 1 ? 0 : 1;

        $.ajax({
            url: '/home/change-state-active',
            type: 'post',
            dataType: 'json',
            data: {is_active_value: is_active_value, home_id: home_id},
            success: function (response) {
                console.log(anchor);
                $(anchor).attr('data-old-value', is_active_value);
                if (is_active_value == 1) {
                    $(anchor).removeClass('btn-default');
                    $(anchor).addClass('btn-primary');
                }
                else {
                    $(anchor).removeClass('btn-primary');
                    $(anchor).addClass('btn-default');
                }
                if (response.message) {
                    $.notify(response.message, 'success');
                }
            },
            error: function (e) {
                $(anchor).attr('data-old-value', data_old_value);
                console.log(e);
                if (e.responseJSON.message) {
                    $.notify(e.responseJSON.message, 'error');
                }
            }
        });
    });

    $('.home-expire-time').change(function (item) {
        if (!confirm('Are you sure?')) {
            return true;
        }
        var anchor = this;
        var old_value = item.target.defaultValue;
        var expire_time_value = $(this).children('.hasDatepicker').val();
        var home_id = $(this).closest('tr').data('key');

        $.ajax({
            url: '/home/change-state-active',
            type: 'post',
            dataType: 'json',
            data: {expire_time_value: expire_time_value, home_id: home_id},
            success: function (response) {
                console.log(response);
                if (response.message) {
                    $.notify(response.message, 'success');
                }
            },
            error: function (e) {
                if ($.trim(old_value) != '') {
                    var obj_old_value = old_value.split('-');
                    $(anchor).children('.hasDatepicker').datepicker('setDate', new Date(obj_old_value[2], obj_old_value[1], obj_old_value[0]));
                }
                else {
                    $(anchor).children('.hasDatepicker').datepicker('setDate', '');
                }
                console.log(e);
                if (e.responseJSON.message) {
                    $.notify(e.responseJSON.message, 'error');
                }
            }
        });

    });

    $('.firmware-published').click(function () {
        if (!confirm('Are you sure?')) {
            return true;
        }
        var anchor = this;
        var firmware_id = $(anchor).closest('tr').data('key');
        $.ajax({
            url: '/firmware/active-firmware',
            type: 'post',
            dataType: 'json',
            data: {firmware_id: firmware_id},
            success: function (response) {
                window.location.href = '/firmware/test';
            },
            error: function (e) {
                console.log(e);
                if (e.responseJSON.message) {
                    $.notify(e.responseJSON.message, 'error');
                }
            }
        });
    });
});

function download_file(path_file) {
    window.location = path_file;
}