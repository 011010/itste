$(document).on('click', '.edit-profile', function (event) {
    $('#editProfileUserId').val(loggedInUser.id);
    $('#pfName').val(loggedInUser.name);
    $('#pfEmail').val(loggedInUser.email);
    $('#EditProfileModal').appendTo('body').modal('show');
});

$(document).on('change', '#pfImage', function () {
    let ext = $(this).val().split('.').pop().toLowerCase();
    // Usar extensiones permitidas globales definidas en custom.js
    if ($.inArray(ext, window.allowedImageExtensions || ['gif', 'png', 'jpg', 'jpeg']) == -1) {
        $(this).val('');
        $('#editProfileValidationErrorsBox').
            html(
                'The profile image must be a file of type: ' + (window.allowedImageExtensions || ['gif', 'png', 'jpg', 'jpeg']).join(', ') + '.').
            show();
    } else {
        // La función displayPhoto está definida en custom.js
        displayPhoto(this, '#edit_preview_photo');
    }
});

$(document).on('submit', '#editProfileForm', function (event) {
    event.preventDefault();
    let userId = $('#editProfileUserId').val();
    var loadingButton = jQuery(this).find('#btnPrEditSave');
    loadingButton.button('loading');
    $.ajax({
        url: usersUrl + '/' + userId,
        type: 'post',
        data: new FormData($(this)[0]),
        processData: false,
        contentType: false,
        success: function success(result) {
            if (result.success) {
                $('#EditProfileModal').modal('hide');
                setTimeout(function () {
                    location.reload();
                }, 1500);
            }
        },
        error: function error(result) {
            console.log(result);
        },
        complete: function complete() {
            loadingButton.button('reset');
        }
    });
});
