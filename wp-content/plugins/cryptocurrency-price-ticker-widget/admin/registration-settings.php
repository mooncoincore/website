<?php
/***
 *
 *
 *
 */

use CryptocurrencyWidgetsProREG\CCPWP_Settings_API as CCPWP_Settings_API;

if (!class_exists('cool_plugins_registration_Settings')):
    require_once __DIR__ . '/class.settings-api.php';

    class cool_plugins_registration_Settings
{

        private static $instance = null;
        private $settings = null;
        private $sections = array();
        private $fields = array();
        private $menu_options = array();

        public static function init()
        {
            // do not initialize this class on frontend
            if (!is_admin()) {
            //    return false;
            }

            if (self::$instance == null) {
                return self::$instance = new self;
            }
            return self::$instance;
        }

        public function add_registration_page()
        {
            if ($this->settings == null) {
                $this->settings = new CCPWP_Settings_API();
            }

            add_action('admin_menu', array($this, 'admin_menu'), 10);
        }

        /**
         * 
         * This is just a wrapup for parent class function
         */
        public function show_navigation(){
            $this->settings->show_navigation();
        }

        public function show_forms($button="submit",$op = true){
            $this->settings->show_forms($button,$op);
        }

        public function admin_menu()
        {
            add_submenu_page('cool-crypto-plugins','Cool Plugins Product Registration', 'Registration', 'manage_options', 'cool-crypto-registration', array($this, 'show_Page'), 10 );
        }

        public function show_Page()
        {
            $this->settings->show_navigation();
            $this->settings->show_forms('Save', false);
        }

        public function add_section($id, $title = 'untitled')
        {
            if ($id != null && gettype($id) == "string") {
                array_push($this->sections, array('id' => $id, 'title' => $title));
            }
            
            add_action('admin_init', array($this, 'create_section'));
        }

        /**
         * access and return HTML of submit button for form
         */
        public function _return_submit_button($string){
            require_once ABSPATH . 'wp-admin/includes/template.php';
            return \get_submit_button('Verify Key');
        }
        public function admin_init(){
            $this->settings->admin_init();
        }
       
        public function add_field( $id, array $options)
        {
            if( isset( $id ) && $id != null ){
                $this->fields[ $id ] = $options;
            }
            add_action('admin_init', array($this, 'create_fields'));

        }

        public function create_section()
        {
            $this->settings->set_sections($this->get_all_sections());
        }

        public function create_fields()
        {
            $this->settings->set_fields($this->get_all_fields());
            $this->settings->admin_init();            
        }

        public function get_all_sections()
        {
            return $this->sections;
        }

        public function get_all_fields()
        {
            return $this->fields;
        }

    }

endif;
