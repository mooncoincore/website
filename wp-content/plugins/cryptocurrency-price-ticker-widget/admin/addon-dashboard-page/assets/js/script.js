jQuery(document).ready(function($) {

    $('button.cool-plugins-addon').on('click', function() {

        if ($(this).hasClass('plugin-downloader')) {
            let nonce = $(this).attr('data-action-nonce');
            let nonceName = $(this).attr('data-action-name');
            let pluginTag = $(this).attr('data-plugin-tag');
            let p_url = $(this).attr('data-url');
            let btn = $(this);
            $.ajax({
                    type: 'POST',
                    url: cp_events.ajax_url,
                    data: { 'action': 'cool_plugins_install_' + pluginTag, 'url': p_url, 'wp_nonce': nonce, 'nonce_name': nonceName },
                    beforeSend: function(res) {
                        btn.text('Installing...');
                    }
                })
                .done(function(res) {
                    console.log(res);
                    window.location.reload();
                })
        }
        if ($(this).hasClass('plugin-activator')) {
            let nonce = $(this).attr('data-action-nonce');
            let nonceName = $(this).attr('data-action-name');
            let pluginFile = $(this).attr('data-plugin-id');
            let pluginTag = $(this).attr('data-plugin-tag');
            let p_url = $(this).attr('data-url');
            let btn = $(this);
            console.log('activation in progress');
            $.ajax({
                    type: 'POST',
                    url: cp_events.ajax_url,
                    data: { 'action': 'cool_plugins_activate_' + pluginTag, 'pluginbase': pluginFile, 'url': p_url, 'wp_nonce': nonce, 'nonce_name': nonceName },
                    beforeSend: function(res) {
                        btn.text('Activating...');
                    }
                })
                .done(function(res) {
                    console.log(res);
                    window.location.reload();
                })
        }

    })

    $('.plugins-list').each(function(el) {
        let $this = $(this);
        let message = $(this).attr('data-empty-message');

        if ($this.children('.plugin-block').length == 0) {
            $this.append('<div class="empty-message">' + message + '</div>');
        }

    })

})