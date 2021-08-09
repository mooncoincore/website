jQuery(document).ready(function($) {

    $('.ccpwp-major-update.notice .notice-dismiss').on('click', function(evt) {
        $.ajax({
            url: ccpwp_data.ajax_url,
            type: 'POST',
            data: { action: 'ccpwp_remove_major_update_notice' },
            success: function(res) {
                console.log(res);
            }
        });

    });

});