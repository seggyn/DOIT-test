function request(url, method, data, success, error = {}, addToken = true, convertDataToJSON = true) {
    let ajax_error = function (xhr, textStatus, errorThrown) {
        if (error.hasOwnProperty(xhr.status)) error[xhr.status]();
    };
    if (addToken) data['token'] = getCookie('token');
    if (convertDataToJSON) data = JSON.stringify(data);
    let array = {url, data, dataType: 'json', method, success};
    if (Object.getOwnPropertyNames(error).length !== 0)
        array['error'] = ajax_error;
    $.ajax(array);
}
function dataAuth() {
    return {
        email: $('input[name=email]').val(),
        password: $('input[name=password]').val()
    };
}
function getPriority(num) {
    let array = ['High', 'Normal', 'Low'];
    return array[num-1];
}
$(document).ready(function() {

    // LOGIN PAGE

    // SIGN IN
    $('form#sign-in').submit(function (e) {
        e.preventDefault();
        let response_success = function(result) {
            setCookie('token', result['token']);
            location.href = base_url + 'list';
        };
        let response_error = {401: function () {
                $('.error').text('Email or password is wrong.');
            }};
        request('sign-in', 'POST', dataAuth(), response_success, response_error, false);
    });

    // REGISTRATION PAGE

    // SIGN UP
    $('form#sign-up').submit(function (e) {
        e.preventDefault();
        let response_success = function(result) {
            setCookie('token', result['token']);
            location.href = base_url + 'list';
        };
        let response_error = {403: function () {
            $('.error').text('User has already exist.');
        }};
        request('sign-up', 'POST', dataAuth(), response_success, response_error, false);
    });

    // LIST PAGE

    // CHANGE PRIORITY
    $('select').change(function() {
        let data = {
            task_id: $(this).data('task'),
            priority: $(this).val()
        };
        request('task', 'PUT', data);
    });

    // MARK AS DONE
    $('.btn-done').click(function () {
        let task_id = $(this).data('task');
        $(this).parent().parent().parent().addClass('bg-success');
        let select = $(this).parent().parent().parent().find('td select');
        let select_value = getPriority(select.val());
        select.parent().text(select_value);
        select.remove();
        $(this).parent().remove();
        let data = { task_id, is_done: 1};
        request('task', 'PUT', data);
    });

    // EDIT TASK MODAL
    $('.btn-edit').click(function () {
        $('form#editTask input[name=task_id]').val($(this).data('task'));
        let data = { task_id: $(this).data('task') };
        let success_response = function (result) {
            $('form#editTask input[name=title]').val(result.title);
            $('form#editTask input[name=due_date]').val(result.due_date);
        };
        request('task', 'GET', data, success_response, {}, true, false);
    });

    // REMOVE TASK
    $('.btn-remove').click(function () {
        let data = { task_id: $(this).data('task') };
        $(this).parent().parent().parent().remove();
        request('task', 'DELETE', data);
    });

    // SUBMIT NEW TASK FORM
    $('form#newTask').submit(function (e) {
        e.preventDefault();
        let data = {
            title: $('form#newTask input[name=title]').val(),
            due_date: $('form#newTask input[name=due_date]').val()
        };
        request('task', 'POST', data);
        setInterval(function () {
            location.reload();
        }, 3000);
    });

    // SUBMIT EDIT TASK FORM
    $('form#editTask').submit(function (e) {
        e.preventDefault();
        let data = {
            task_id: $('form#editTask input[name=task_id]').val(),
            title: $('form#editTask input[name=title]').val(),
            due_date: $('form#editTask input[name=due_date]').val()
        };
        request('task', 'PUT', data);
        setInterval(function () {
            location.reload();
        }, 1000);
    });
})