// Set body text to small
$('body').addClass('text-sm')

//Resolve conflict in jQuery UI tooltip with Bootstrap tooltip
$.widget.bridge('uibutton', $.ui.button)

// Enable the search box on jquery Select2 to be editable when using it in a bootstrap modal
// Solution from https://stackoverflow.com/questions/18487056/select2-doesnt-work-when-embedded-in-a-bootstrap-modal
$.fn.modal.Constructor.prototype._enforceFocus = function() {};

// App loading indicator
const loader = '<h1 class="text-center spinner"><i class="fas fa-spinner fa-pulse"></i></h1>';

// Load a create or update resource modal form here
function createOrUpdateModal(e, title) {
    e.preventDefault();
    $('.modal-title').text(title);
    $('.modal-body').html(loader);
    $('#create-update-resource-modal').modal('show')
        .find('.modal-body')
        .load($(this).attr('href'), function (e) {});
}

// Send change status request
function changeStatus(e, model) {
    e.preventDefault();
    const url = $(this).attr('href');
    const id = $(this).attr('data-id');
    const status = $(this).attr('data-status');
    const message = 'Are you sure you want to ' + status + ' this ' + model + '?';
    krajeeDialog.confirm(message, function (result) {
        if (result) {
            $('#app-is-loading-modal-title').html('<p class="text-center">' + capitaliseFirstLetter(status) +
                ' ' + model + '</p>');
            $('#app-is-loading-modal').modal('show');
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    'id': id,
                    'status': status
                },
                dataType: 'json',
                encode: true
            })
                .done(function (response) {
                    console.log(response);
                    if (response.status === 500) {
                        $('#app-is-loading-message')
                            .html('<p class="text-danger"> This ' + model + ' failed to ' + status + '<br/>' +
                                response.message + '</p>');
                    }
                })
                .fail(function () {});
        } else {
            krajeeDialog.alert('Operation cancelled');
        }
    });
}

// Capitalize first letter of status name
function capitaliseFirstLetter(string){
    console.log(string.charAt(0).toUpperCase() + string.slice(1));
    return string.charAt(0).toUpperCase() + string.slice(1);
}
