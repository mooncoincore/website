<?php
namespace CryptocurrencyWidgetsProREG;
/*
|--------------------------------------
|   API DATA VERIFICATION SETTINGS PAGE
|--------------------------------------
*/
    class CCPWP_Settings
    {
            private $verification_status;
            private $PREFIX;
            private $PLUGIN_NAME;
            private $PLUGIN_VER;
            private $AUTH_PAGE;
            private $PLUGIN_URL;
            private $settings_api;
            private $licenseMessage;
            private $Response;
            private $Base_File;
            private $plugin_purchase_url;
            private $plugin_documentation_url;
        public function __construct()
        {
            $this->Base_File = CCPWP_PRO_FILE;
            $this->PLUGIN_NAME = CCPWP_ApiConf::PLUGIN_NAME;
            $this->PREFIX = CCPWP_ApiConf::PLUGIN_PREFIX;
            $this->AUTH_PAGE = CCPWP_ApiConf::PLUGIN_AUTH_PAGE;
            $this->PLUGIN_URL = CCPWP_ApiConf::PLUGIN_URL;
            $this->PLUGIN_VER = CCPWP_ApiConf::PLUGIN_VERSION;
            //$this->settings_api = new CCPWP_Settings_API;
            $this->plugin_purchase_url = 'https://1.envato.market/cryptocurrency';
            $this->plugin_documentation_url = 'https://docs.coolplugins.net/doc/cryptocurrency-widgets-pro/';
            $this->verification_status = 'License is not verified yet! ';

            $this->settings_api = \cool_plugins_registration_Settings::init();
            $this->settings_api->add_registration_page();
            add_action('admin_enqueue_scripts', array($this,'load_settings_scripts'));

            add_action('admin_init', array($this, 'admin_init'));

            $this->settings_api->add_section( $this->PREFIX . '_license_registration', __('Widget Pro Registration', 'cmb2') );
            $this->settings_api->add_field( $this->PREFIX .'_license_registration', array(   
                array(
                    'name'  => $this->PREFIX.'-purchase-code',
                    'id'    => $this->PREFIX.'-purchase-code',
                    'class' => $this->PREFIX.'-settings-field required',
                    'label'  => 'Enter License Key',
                    // 'desc'  => $this->save_purchase_code(),
                    'placeholder' => __('Your Envato Purchase Code', 'cmb2'),
                    'type'  => 'text',
                    'default' => '',
                ),
                array(
                    'name'  => $this->PREFIX.'-client-emailid',
                    'id'    => $this->PREFIX.'-client-emailid',
                    'class' => $this->PREFIX.'-settings-field required',
                    'label'  => 'Enter Email Id',
                    'desc'  => $this->save_purchase_code(),
                    'placeholder' => get_option('admin_email'),
                    'type'  => 'text',
                    'default' => get_option('admin_email'),
                ),
                array(
                    'name'  => $this->PREFIX.'-validate-purchase-code',
                    'id'    => $this->PREFIX.'-validate-purchase-code',
                    'class' => $this->PREFIX.'-settings-field',
                    'desc'  => $this->ValidatePurchase(),
                    'type'  => 'html',
                    'default' => '',
                ), 
                array(
                    'name'  => $this->PREFIX.'-find-purchase-code',
                    'id'    => $this->PREFIX.'-find-purchase-code',
                    'class' => $this->PREFIX.'-settings-field',
                    'label'  => 'How To Find Key?',
                    'desc'  => $this->find_purchase_code(),
                    'type'  => 'html',
                    'default' => '',
                ),
                array(
                    'name'  => $this->PREFIX.'-issue-with-registration',
                    'id'    => $this->PREFIX.'-issue-with-registration',
                    'class' => $this->PREFIX.'-settings-field',
                    'label'  => 'Having trouble with license activation?',
                    'desc'  => $this->trouble_with_activation(),
                    'type'  => 'html',
                    'default' => '',
                ),
            ));

            add_action('admin_init', array($this, 'admin_init'));
            add_action('admin_notices', array($this, 'admin_registration_notice') );

            add_action('wp_ajax_'.$this->PREFIX.'_uninstall_license',  array($this, 'uninstall_license' ) );
            
                // send ticket from wp-backend
                // add_action('wp_ajax_submit_ticket', array($this, 'submit_ticket') );
                
                // add_action('wsa_form_top_coolpluginslicense_registration', array($this, 'thankyou_note'));
                //  add_action('wsa_form_bottom_coolpluginslicense_registration', array($this, 'ValidatePurchase'));

        }

        /*
        |---------------------------------------------------
        |   Initialize settings
        |---------------------------------------------------
        */
        public function admin_init()
        {
                //initialize settings
                $this->settings_api->admin_init();
        }

        /*
        |---------------------------------------------------
        |   Add settings page to wordpress menu
        |---------------------------------------------------
        */
        public function admin_menu()
        {
            add_submenu_page('edit.php?post_type='.$this->PREFIX,'Cool Plugins Product Registration', 'Registration', 'manage_options', $this->AUTH_PAGE, array($this, 'auth_settings_page'));
        }

        /*
        |--------------------------------------------------------------------
        |   Create multiple section in settings page using array in $sections
        |--------------------------------------------------------------------
        */
        public function get_settings_sections()
        {
                $sections = array(

                    array(
                        'id' => $this->PREFIX.'_license_welcome',
                        'title' => __('Welcome', 'cmb2'),
                    ),
                    array(
                        'id' => $this->PREFIX.'_license_registration',
                        'title' => __('Registration', 'cmb2'),
                    ),
                /*     array(
                        'id' => $this->PREFIX.'_license_support',
                        'title' => __('Support', 'cmb2'),
                    ) */
                );
                return $sections;
        }

        /*
        |--------------------------------------------------------------------
        |   return all settings fields to be initialized in settings page
        |--------------------------------------------------------------------
        */
        public function get_settings_fields()
        {

            $settings_fields = array(

                $this->PREFIX.'_license_welcome'    =>  array(
                
                    array(
                        'name'  => $this->PREFIX.'-documentation',
                        'id'    => $this->PREFIX.'-documentation',
                        'class' => $this->PREFIX.'-settings-field',
                        'label'  => 'Documentation & Support',
                        'desc'  => $this->welcome_tab(),
                        'type'  => 'html',
                        'default' => '',
                    ),
                
                ),

               $this->PREFIX.'_license_registration' => array(   
                    array(
                        'name'  => $this->PREFIX.'-purchase-code',
                        'id'    => $this->PREFIX.'-purchase-code',
                        'class' => $this->PREFIX.'-settings-field required',
                        'label'  => 'Enter License Key',
                        // 'desc'  => $this->save_purchase_code(),
                        'placeholder' => __('Your Envato Purchase Code', 'cmb2'),
                        'type'  => 'text',
                        'default' => '',
                    ),
                    array(
                        'name'  => $this->PREFIX.'-client-emailid',
                        'id'    => $this->PREFIX.'-client-emailid',
                        'class' => $this->PREFIX.'-settings-field required',
                        'label'  => 'Enter Email Id',
                        'desc'  => $this->save_purchase_code(),
                        'placeholder' => get_option('admin_email'),
                        'type'  => 'text',
                        'default' => get_option('admin_email'),
                    ),
                    array(
                        'name'  => $this->PREFIX.'-validate-purchase-code',
                        'id'    => $this->PREFIX.'-validate-purchase-code',
                        'class' => $this->PREFIX.'-settings-field',
                        'desc'  => $this->ValidatePurchase(),
                        'type'  => 'html',
                        'default' => '',
                    ), 
                    array(
                        'name'  => $this->PREFIX.'-find-purchase-code',
                        'id'    => $this->PREFIX.'-find-purchase-code',
                        'class' => $this->PREFIX.'-settings-field',
                        'label'  => 'How To Find Key?',
                        'desc'  => $this->find_purchase_code(),
                        'type'  => 'html',
                        'default' => '',
                    ),
                    array(
                        'name'  => $this->PREFIX.'-issue-with-registration',
                        'id'    => $this->PREFIX.'-issue-with-registration',
                        'class' => $this->PREFIX.'-settings-field',
                        'label'  => 'Having trouble with license activation?',
                        'desc'  => $this->trouble_with_activation(),
                        'type'  => 'html',
                        'default' => '',
                    ),
                ), 
            );

            return $settings_fields;
        }

        function welcome_tab(){
            $html = "<div class='wrap'>";

            $html .= "<div class='wrap'>";
            $html .= "<div class='".$this->PREFIX."-message'>";
            $html .="Thank you for choosing ".$this->PLUGIN_NAME.".";
            $html .= "</div></div>";

            $html.= "<h3>Plugin's Documentation</h3>";
            $html.= "<p>Find the required documentation here for the plugin. The complete plugin is explained here for a smooth experience.</p>";
            $html.= "<a href='".$this->plugin_documentation_url."' target='_blank' class='button button-primary'>Documentation</a>";
            
            $html.="<h3>Support Forum</h3>";
            $html.="<p>Do you have question?</p><p>Feel free to contact us through our support forum. You can also write us at <a href='mailto:contact@coolplugins.net;'>contact@coolplugins.net</a></p>";
            $html.= "<a href='https://coolplugins.net/contact-plugin-support/' target='_blank' class='button button-primary'>Submit A Ticket</a>";

            $html.="<h3>Famous Cryptocurrency Plugins by <strong>CoolPlugins.net</strong></h3>";
            $html.='<p>
            <h4>Coins Marketcap</h4>
            <a href="https://1.envato.market/cmc" target="_blank" class="cmc-ad-banner"><img style="max-width:100%;height:auto;border:2px solid #d8d8d8;padding:4px;" src="https://res.cloudinary.com/pinkborder/image/upload/v1565162803/CoinMarketCap-Plugin/coins-marketcap-ad.png" /></a><br/>
            <h4>Cryptocurrency Exchanges List Pro</h4>
            <a href="https://1.envato.market/CryptoExchanges" target="_blank" class="celp-ad-banner"><img style="max-width:100%;height:auto;border:2px solid #d8d8d8;padding:4px;" src="https://res.cloudinary.com/pinkborder/image/upload/v1565162802/CoinMarketCap-Plugin/exchanges-plugin-ad.png" /></a><br/>
            <h4>Crypto ICO List Widgets Pro</h4>
            <a href="https://1.envato.market/ICO" target="_blank" class="cilwp-ad-banner"><img style="max-width:100%;height:auto;border:2px solid #d8d8d8;padding:4px;" src="https://res.cloudinary.com/pinkborder/image/upload/v1565162803/CoinMarketCap-Plugin/ico-plugin-ad.png" /></a><br/>
            <h4>Cryptocurrency Search Add-on</h4>
            <a href="https://1.envato.market/searchCrypto" target="_blank" class="csa-ad-banner"><img style="max-width:100%;height:auto;border:2px solid #d8d8d8;padding:4px;" src="https://res.cloudinary.com/pinkborder/image/upload/v1565162802/CoinMarketCap-Plugin/search-addon-ad.png" /></a><br/>
             </p>';

            $html.= "</div>";
            return $html;
        }

        
        function find_purchase_code(){
            $html  = "<ol>";
            $html .= "<li>Login your Envato Market / Codecanyon account.</li>";
            $html .= "<li>Hover the mouse over your username at the top of the screen.</li>";
            $html .= "<li>Click ‘Downloads’ from the drop down menu.</li>";
            $html .= "<li>Click ‘License certificate & purchase code’ (available as PDF or text file).</li>";
            $html .= "<li>A product certificate file with the purchase code will be downloaded.</li>";
            $html .= "<li><a href='https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-' target='_blank'>Check here for detail instructions...</a></li>";
            $html .= "</ol>";
            return $html;
        }

        function trouble_with_activation(){

            $html ='<div id="'.$this->PREFIX.'_registration_help_notice">Please contact support along with your license key and domain url at <a href="mailto:contact@coolplugins.net;">contact@coolplugins.net</a>.</div>';

            return $html;
        }

        function save_purchase_code(){
            $html = "<div id='".$this->PREFIX."-verify-permission'><span class='".$this->PREFIX."-notice-red'>&#9989; I agree to share my purchase code and email for plugin verification and to receive future updates notifications!</span></div><div id='".$this->PREFIX."-activation-button'>".$this->settings_api->_return_submit_button('Verify Key')."</div>
            <div id='".$this->PREFIX."-deactivation-button'><a id='".$this->PREFIX."-uninstall-license' class='button button-secondary button-hero'>Uninstall Licence</a><br/><span class='".$this->PREFIX."-notice-red uninstall'>(* Uninstall license to use it on other website or hosting.)</span></div>";            
            return $html;
        }

        /*
        |------------------------------------------------
        |   Create custom wrapper div for settings page
        |------------------------------------------------
        */
        public function auth_settings_page()
        {
                // $html =
                // "<div class='wrap'>
                // <div id='message' class='notice top'>
                        // <p><strong>Server info:</strong></p>
                        // <p><strong>Domain:</strong> ".get_site_url()."</p>
                        // <p><strong>Email Id:</strong> ".get_option('admin_email')."</p>
                    // </div>";
                
                // $html .="</div>";
                // echo $html;
                
                $this->settings_api->show_navigation();
                $this->settings_api->show_forms('Save',false);
           
        }

        /*
        |---------------------------------------------------------------
        | This function generate custom message on loading the settings 
        |---------------------------------------------------------------
        */
        public function ValidatePurchase()
        {
           $purchase    = $this->ce_get_option( $this->PREFIX.'-purchase-code' );
           $admin_email = $this->ce_get_option( $this->PREFIX.'-client-emailid' );
           if( isset( $_GET['settings-updated'] ) || !empty($purchase) )
           {
                if( !empty( $purchase ) ){
                    $registration = "<div class='wrap'>";
                    $registration ="<div class='".$this->PREFIX."-verification-notice'>
                    <p><strong>License Verification Status:</strong>";
                    $verified = CryptocurrencyWidgetsProBase::CheckWPPlugin($purchase,$admin_email,$this->licenseMessage,$this->Response,$this->Base_File);
 
                    if( $verified && $this->Response->is_valid){
                        $this->verification_status = "Verified!";
                        set_transient($this->PREFIX . '_api_data_verification','done',0);
                        $registration .= "<span class='".$this->PREFIX."_verification_enable'>&nbsp; &#9989; &nbsp;</span>";
                    }else{
                        $registration .= "<span class='".$this->PREFIX."_verification_disable'>&nbsp; &#10060; &nbsp;</span>";
                        $this->flush_cache();
                        $this->verification_status .= $this->licenseMessage;
                    }
                    
                    $registration .= $this->verification_status;
                    $registration .= "</p></div>";
                    
                   $registration .= "<p><strong>Developer's Support Validity Status:</strong>";
                    if( $verified && strpos($this->Response->support_end,'No support') == false ){
                        $date = new \DateTime($this->Response->support_end);
                        $nowDate = new \DateTime();
                        $diff = $date->diff($nowDate) ;
                        if( ( $diff->y>0 || $diff->m>0 || $diff->d>0 || $diff->h>0 || $diff->i>0 ) && $diff->invert == 0 ){
                            $registration .= "<span class='".$this->PREFIX."_verification_disable'>&nbsp; &#10060; &nbsp;</span>";
                        }else{
                            $registration .= "<span class='".$this->PREFIX."_verification_enable'>&nbsp; &#9989; &nbsp;</span>";
                        }
                        $registration .= $this->Response->support_end."</p></div>";
                    }else{
                        $registration .= "<span class='".$this->PREFIX."_verification_disable'>&nbsp; &#10060; &nbsp; N/A</span>";
                    }

                    return  $registration;
                }
                else {
                    $empty_code = "<span class='".$this->PREFIX."-notice-red'>**Purchase code can not be empty!</span>";
                    return $empty_code;
                }
            }
            else {
                $empty_code = "<span class='".$this->PREFIX."-notice-red'>&#9785; Don't have a license? <a href='".$this->plugin_purchase_url."' target='_blank'>Check Here To Purchase</a></span>";
                return $empty_code;
            }
        }

        /*
        |---------------------------------------------------------
        |   Gather settings field-values like get_options()
        |---------------------------------------------------------
        */
        public function ce_get_option($option, $default = '')
        {

            $section = $this->PREFIX.'_license_registration';
            $options = get_option($section);

            if (isset($options[$option])) {
                return $options[$option];
            }

            return $default;
        }

        /*
        |---------------------------------------------------------
        |   Display shortcode at the bottom of settings page.
        |---------------------------------------------------------
        |   An API token must be generated to use the shortcode
        |---------------------------------------------------------
        */
        function display_support_form(){
            $html = "<div class='notice alightleft'>
                <h3>Submit a ticket</h3>
                <textarea id='ccpa_plugin_support' rows='8' cols='50'></textarea><br/>
                <a class='button-primary' id='support_btn'>Submit</a></div>";
            echo $html;
        }

        /*
        |---------------------------------------------------------
        |   Function accessed through AJAX
		|---------------------------------------------------------        
		|	uninstall license
		|---------------------------------------------------------        
        */
        function uninstall_license(){
            $message = '';
            if( wp_verify_nonce( $_REQUEST['_password'], 'purchase-verify'  ) == true ){
               $response = CryptocurrencyWidgetsProBase::RemoveLicenseKey($this->Base_File,$message);
                
               if( $response == false ){
                   die( json_encode( array('Response'=>'403','Message'=>'Unable to contact to the server at the moment.') ) );
               }

            }else{
                die( json_encode( array('Response'=>'403','Message'=>'Access denied due to expired/unauthorized url access.') ) );
            }

            $this->flush_cache();
            die( json_encode( array('Response'=>'200','Message'=>$message) ) );
        }

        /*
        |---------------------------------------------------------
        |   Submit ticket from wordpress back-end
		|---------------------------------------------------------
        */
        /*
        function submit_ticket(){
            $email_from = get_option('admin_email');
            $email_to = "contact@cooltimeline.com";

            $message = '<html><body>';
            $message .= '<h3>A ticket is received from wordpress admin support form.</h3>';
            $message .= '<p><strong>URL</strong>:'.get_site_url().'</p>';
            $message .= '<p><strong>Admin Email</strong>:'.$email_from. '</p>';
            $message .= '<p>Message: '.$_POST['request'].'<p>';
            $message .= '</body></html>';

            $subject = '['.get_site_url().']Support ticket from wordpress back-end';
            
            $headers  = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From:<".$email_from."> \r\n";
            $headers .= "Reply-To: ".$email_to."\r\n";
            
            $mail=wp_mail( $email_to, $subject, $message, $headers);
            echo 'Ticket submited successfully';
            exit();
        }
        */

        /*
        |----------------------------------------------------------------
        |   Admin registration notice for un-registered admin users only
		|----------------------------------------------------------------
        */
        function admin_registration_notice(){
            if( !current_user_can('manage_options') || get_transient($this->PREFIX . '_api_data_verification')=='done' ){
                return;
            }
            $current_user  = wp_get_current_user();
            $user_name      = $current_user->display_name ;
            ?>
                <div class="license-warning notice notice-error is-dismissible">
                    <p>Hi, <strong><?php echo ucwords( $user_name ); ?></strong>! Please <strong><a href="<?php echo esc_url( get_admin_url( null, 'admin.php?page=cool-crypto-registration#ccpw_license_registration' ) ); ?>">enter and activate</a></strong> your license key for <strong><?php echo $this->PLUGIN_NAME;?></strong> plugin for unrestricted and full access of all premium features.</p>
                </div>
            <?php
        }

        /*
        |------------------------------------------------------------
        |   Load css/js script(s) file(s) for settings admin page
		|------------------------------------------------------------
        */
        function load_settings_scripts(){
            
            if( isset($_GET['page']) && $_GET['page']== 'cool-crypto-registration' ){
                
                wp_enqueue_style( $this->PREFIX.'-settings-style', $this->PLUGIN_URL . 'assets/css/api-auth-settings.css',null,$this->PLUGIN_VER);
                wp_enqueue_script( $this->PREFIX.'-settings-script', $this->PLUGIN_URL . 'assets/admin/js/api-auth-settings.js', array('jquery'), $this->PLUGIN_VER);
                wp_localize_script(
                    $this->PREFIX.'-settings-script',
                    'ajax_object',
                    array('ajax_url' => admin_url('admin-ajax.php'),
                    'verify'=>wp_create_nonce( 'purchase-verify' ))
                );
             }

        }

        /*
        |-----------------------------------------------------------|
        |   Flush cache: All Home sweeping code must be here        |
        |   Run after license uninstall or failed verification      |
		|-----------------------------------------------------------|
        */
        function flush_cache(){
            $settings = get_option($this->PREFIX.'_license_registration');
            unset($settings[$this->PREFIX.'-purchase-code']);
            update_option($this->PREFIX.'_license_registration',$settings);
            delete_transient($this->PREFIX . '_api_data_verification');
        }

    }   // end of class 