/**
 * Ajax call to test connection
 */
jQuery("#avatax_test_connection").on("click", function () {
    let btn = jQuery(this);
    btn.attr('disabled', true);
    jQuery("#avatax_test_result").remove();

    btn.parent().append('<span id="avatax_test_result" style="margin-left: 10px;">Testing Connection...</span>');

    var data = {
        'action': 'avatax_test_connection',
        'test_con_nonce': avatax_test_connection.test_con_nonce
    };

    // We can also pass the url value separately from ajaxurl for front end AJAX implementations
    jQuery.post(avatax_test_connection.ajax_url, data, function (response) {
        btn.attr('disabled', false);

        var success = response.connection_success;
        if (success) {
            jQuery("#avatax_test_result").html(response.status_text);
            jQuery("#avatax_test_result").css("color", "green");
        } else {
            jQuery("#avatax_test_result").html(response.status_text);
            jQuery("#avatax_test_result").css("color", "red");
        }
    });
});
