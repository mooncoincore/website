<?php
    // Do not use namespace to keep this on global space to keep the singleton initialization working
if ( !class_exists('cool_plugins_crypto_addons')) {

/**
 * 
 * This is the main class for creating dashbord addon page and all submenu items
 * 
 * Do not call or initialize this class directly, instead use the function mentioned at the bottom of this file
 */
    class cool_plugins_crypto_addons
    {

         /**
         * None of these variables should be accessable from the outside of the class
         */
            private static $instance;
            private $pro_plugins = array();
            private $pages = array();
            private $main_menu_slug = null;
            private $plugin_tag = null;
            private $dashboar_page_heading ;
            private $disable_plugins = array();
            private $addon_dir = __DIR__;    // point to the main addon-page directory
            private $addon_file = __FILE__;
            private $menu_title = 'Addon Dashboard';
            private $menu_icon = false;

             /**
             * initialize the class and create dashboard page only one time
             */
            public static function init( ){

                if( empty(self::$instance) ){
                    return self::$instance = new self;
                }
                return self::$instance;

            }

            /**
             * Initialize the dashboard with specific plugins as per plugin tag
             * 
             */
            public function show_plugins( $plugin_tag , $menu_slug , $dashboard_heading,  $main_menu_title, $icon  ){
                
                if( !empty($plugin_tag) && !empty($menu_slug) && !empty($dashboard_heading) ){
                    $this->plugin_tag = $plugin_tag;
                    $this->main_menu_slug = $menu_slug;
                    $this->dashboar_page_heading = $dashboard_heading;
                    $this->menu_title = $main_menu_title;
                    $this->menu_icon = $icon;
                }else{
                    return false;
                }
                add_action('admin_menu', array($this, 'init_plugins_dasboard_page'), 5 );
                add_action('wp_ajax_cool_plugins_install_'. $this->plugin_tag, array($this, 'cool_plugins_install'));
                add_action('wp_ajax_cool_plugins_activate_'. $this->plugin_tag, array($this, 'cool_plugins_activate'));
                add_action('admin_enqueue_scripts', array($this,'enqueue_required_scripts') );
            }

            /**
             * handle ajax request for activating plugin from dashboard
             */
            function cool_plugins_activate(){
                if( isset( $_POST['wp_nonce'] ) && isset( $_POST['nonce_name'] ) && wp_verify_nonce( $_POST['wp_nonce'] , $_POST['nonce_name'] )){
                    $pluginBase = ( isset( $_POST['pluginbase'] ) && !empty( $_POST['pluginbase'] ) )? $_POST['pluginbase'] : null;
                    if( $pluginBase != null ){
                        activate_plugin( $pluginBase );
                    }
                }else{
                    die('wp nonce verification failed!');
                }

            }

            /**
             * handle ajax for installing plugin from the dashboard.
             * This function use the core wordpress functionality of installing a plugin through URL
             */
            function cool_plugins_install(){
                if( isset( $_POST['wp_nonce'] ) && isset( $_POST['nonce_name'] ) && wp_verify_nonce( $_POST['wp_nonce'] , $_POST['nonce_name'] )){
                    require_once 'includes/cool_plugins_downloader.php';
                    $downloader = new cool_plugins_downloader();
                    return  $downloader->install( $_REQUEST['url'], 'install' );
                }else{
                    die('wp nonce verification failed!');
                }
                die();
            }

            /**
             * This function will initialize the main dashboard menu for all plugins
             */
            function init_plugins_dasboard_page(){
                add_menu_page(  $this->menu_title, $this->menu_title, 'manage_options', $this->main_menu_slug, array( $this, 'displayPluginAdminDashboard' ), $this->menu_icon, 9 );
                add_submenu_page($this->main_menu_slug, 'Dashboard', 'Dashboard', 'manage_options', $this->main_menu_slug ,  array( $this, 'displayPluginAdminDashboard' ),1 );
            }

            /**
             * This function will render and create the HTML display of dashboard page.
             * All the HTML can be located in other template files.
             * Avoid using any HTML here or use nominal HTML tags inside this function.
             */
            function displayPluginAdminDashboard(){

                $tag = $this->plugin_tag;
                $plugins = $this->request_wp_plugins_data( $tag );
                $this->request_pro_plugins_data( $tag );
                if( !empty( $plugins ) && count( $plugins ) > 0 ){

                    // merge free & pro plugins into one array
                    if( count($this->pro_plugins) > 0 ){
                        $plugins = array_merge($plugins, $this->pro_plugins);
                    }

                    require $this->addon_dir . '/includes/dashboard-header.php';

                    echo '<div class="cool-body-left">
                    <div class="plugins-list installed-addons" data-empty-message="You have not installed any addon at the moment"><h3>Currently Installed Plugins</h3>';

                    foreach($plugins as $plugin ){

                        $plugin_name = $plugin['name'];
                        $plugin_desc = $plugin['desc'];
                        $plugin_logo = $plugin['logo'];
                        $plugin_url = $plugin['download_link'];
                        $plugin_slug = $plugin['slug'];
                        $plugin_version = $plugin['version'];
 
                        if( file_exists( WP_PLUGIN_DIR . '/' . $plugin_slug ) ){
                            require $this->addon_dir . '/includes/dashboard-page.php';
                        }

                    }
                    echo "</div>";

                    echo "<div class='plugins-list more-addons' data-empty-message='No more free addons available at the moment'><h3>More Free Plugins</h3>";
                    foreach($plugins as $plugin ){

                        if( $plugin['download_link'] == null ){
                            continue;
                        }
                        $plugin_name = $plugin['name'];
                        $plugin_desc = $plugin['desc'];
                        $plugin_logo = $plugin['logo'];
                        $plugin_url = $plugin['download_link'];
                        $plugin_slug = $plugin['slug'];
                        $plugin_version = $plugin['version'];
                        
                        if( !file_exists( WP_PLUGIN_DIR . '/' . $plugin_slug ) ){
                            require $this->addon_dir . '/includes/dashboard-page.php';
                        }

                    }
                    echo '</div>';
                    if( !empty($this->pro_plugins) && count($this->pro_plugins) >0 ):
                        /**
                         * Load this Pro Plugin container only if there are any pro plugins available
                         */
                    echo "<div class='plugins-list pro-addons' data-empty-message='No more Pro plugins available at the moment'><h3>Pro Plugins</h3>";
                        foreach($this->pro_plugins as $plugin ){

                            $plugin_name = $plugin['name'];
                            $plugin_desc = $plugin['desc'];
                            $plugin_logo = $plugin['logo'];
                            $plugin_pro_url = $plugin['buyLink'];
                            $plugin_url = null;
                            $plugin_version = null;
                            $plugin_slug = $plugin['slug'];
                            
                            if( !file_exists( WP_PLUGIN_DIR . '/' . $plugin_slug ) ){
                                require $this->addon_dir . '/includes/dashboard-page.php';
                            }

                        }
                        echo '</div>';
                    endif;
                    echo '</div>';  // end of .cool-body-left
                    require $this->addon_dir . '/includes/dashboard-sidebar.php';
                    

                }else{
                    // plugins are not available under this tag.
                }
            }

            /**
             * Lets enqueue all the required CSS & JS
             */
            function enqueue_required_scripts(){
                // A common CSS file will be enqueued for admin panel
                wp_enqueue_style('cool-plugins-events-addon', plugin_dir_url(__FILE__) .'assets/css/styles.css', null, null, 'all');
                if( isset( $_GET['page'] ) && $_GET['page'] == $this->main_menu_slug ){
                    wp_enqueue_script( 'cool-plugins-events-addon', plugin_dir_url(__FILE__) .'assets/js/script.js', array('jquery'), null, true);
                    wp_localize_script( 'cool-plugins-events-addon', 'cp_events', array('ajax_url'=> admin_url('admin-ajax.php')));
                }
            }

            /**
             * This function will gather all information regarding pro plugins.
             * 
             */
            function request_pro_plugins_data( $tag = null ){
                $trans_name = $this->main_menu_slug . '_pro_api_cache' . $this->plugin_tag;
                $option_name = $this->main_menu_slug .'-'.$this->plugin_tag .'-pro';
                if( get_transient( $trans_name ) != false ){
                    return get_option( $option_name  , false);        
                }
                
                    $pro_api = 'https://coolplugins.net/plugins-data/api/premium/all';                
                    $response = wp_remote_get( $pro_api, array('timeout'=>300) );
                
                    if( is_wp_error($response)){
                        return;
                    }
                    $plugin_info = (array) json_decode( $response['body'] );
                    $all_plugins = array();
                    
                    foreach( $plugin_info as $plugin){
                        
                        if( $plugin->tag == $tag ){
                            
                                $this->pro_plugins[ $plugin->slug ] = array(
                                                             'name'=> $plugin->name,
                                                             'logo'=> $plugin->image_url,
                                                             'desc' =>$plugin->info,
                                                             'slug' => $plugin->slug,
                                                             'buyLink' => $plugin->buy_url,
                                                            'version'=>$plugin->version,
                                                        'download_link'=>null,
                                                        'incompatible'=>$plugin->free_version,
                                                        'buyLink'=>$plugin->buy_url
                                                    );
                                if( property_exists( $plugin , 'free_version' ) && $plugin->free_version != null ){
                                    $this->disable_plugins[ $plugin->free_version ] = array( 'pro'=>$plugin->slug );
                                }
                            }

                    }
    
                    if( !empty( $all_plugins ) && is_array( $all_plugins ) && count( $all_plugins ) ){
                        set_transient( $trans_name , $all_plugins, DAY_IN_SECONDS );
                        update_option( $option_name , $all_plugins );
                        return $all_plugins;
                    }else if( get_option( $option_name , false ) !=false ){
                        return get_option( $option_name );
                    }

            }

            /**
             * Gather all the free plugin information from wordpress.org API
             */
            function request_wp_plugins_data( $tag = null){
            
            if( get_transient( $this->main_menu_slug . '_api_cache' . $this->plugin_tag ) != false ){
                return get_option( $this->main_menu_slug .'-'.$this->plugin_tag , false);        
            }
//                $request = array( 'action' => 'plugin_information', 'timeout' => 300, 'request' => serialize( $args) );
            
                $url = 'https://api.wordpress.org/plugins/info/1.2/?action=query_plugins&request[author]=coolplugins&request[fields]=-description';
            
                $response = wp_remote_get( $url, array('timeout'=>300) );
            
                if( is_wp_error($response)){
                    return;
                }
                $plugin_info = json_decode( $response['body'] );
                $all_plugins = array();
                foreach( $plugin_info->plugins as $plugin){

                    if( !property_exists( $plugin->tags, $tag) ){
                        continue;
                    }
                        $plugins_data['name'] = $plugin->name;
                        foreach( $plugin->icons as $icon ){
                            $plugins_data['logo'] = $icon;
                        break;
                        }
                        $plugins_data['slug'] = $plugin->slug;
                        $plugins_data['desc'] = $plugin->short_description;
                        $plugins_data['version'] = $plugin->version;    
                        $plugins_data['tags'] = $plugin->tags;
                        $plugins_data['download_link'] = $plugin->download_link;
                        $all_plugins[ $plugin->slug ] = $plugins_data;
                }

                if( !empty( $all_plugins ) && is_array( $all_plugins ) && count( $all_plugins ) ){
                    set_transient( $this->main_menu_slug . '_api_cache' .$this->plugin_tag , $all_plugins, DAY_IN_SECONDS );
                    update_option( $this->main_menu_slug .'-'.$this->plugin_tag, $all_plugins );
                    return $all_plugins;
                }else if( get_option( $this->main_menu_slug .'-'.$this->plugin_tag , false ) !=false ){
                    return get_option( $this->main_menu_slug .'-'.$this->plugin_tag );
                }

            }
    }

    /**
     * 
     * initialize the main dashboard class with all required parameters
     */
    function cool_plugins_crypto_addon_settings_page($tag ,$settings_page_slug, $dashboard_heading, $main_menu_title, $icon ){
        $event_page = cool_plugins_crypto_addons::init();
        $event_page->show_plugins( $tag, $settings_page_slug, $dashboard_heading, $main_menu_title, $icon );
    }

}

