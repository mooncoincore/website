<?php
/**
* Class that lets you add new tiny mce buttons
*
* recources:
* http://wp.tutsplus.com/tutorials/theme-development/wordpress-shortcodes-the-right-way/
*/

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) { die('-1'); }

if ( ! class_exists( 'avia_tinyMCE_button' ) ) 
{
	class avia_tinyMCE_button
	{
		static $count = 0;
		
		/**
		 *
		 * @var array
		 */
		protected $button;
		
		/**
		 * 
		 * @param array $button
		 */
		public function __construct( $button = array() )
		{
			$defaults 		= array(
									'id'					=> '',
									'title'					=> '',
									'access_key'			=> '',
									'content_open'			=> '',
									'content_close'			=> '',
									'image'					=> '',
									'js_plugin_file'		=> '',
									'qtag_display'			=> '',
									'qtag_access_key'		=> '',
									'qtag_content_open'		=> '',
									'qtag_content_close'	=> '',
									'shortcodes'		=> array() 
									);
									
			$this->button 	= array_merge( $defaults, $button );
			
			$this->add_button();
		}
		
		/**
		 * @since 4.5.5
		 */
		public function __destruct() 
		{
			unset( $this->button );
		}
		
		// add button
		protected function add_button() 
		{  
			if ( current_user_can( 'edit_posts' ) &&  current_user_can( 'edit_pages' ) )
			{  
				$prio = 100 + self::$count;
				add_filter( 'mce_external_plugins' 	, array( &$this, 'add_javascript' ), $prio );  
				add_filter( 'mce_buttons' 			, array( &$this, 'display_in_editor' ), $prio );  
				add_filter( 'admin_print_scripts' 	, array( &$this, 'create_js_globals' ), $prio );  
				
				if( ! empty( $this->button['qtag_display'] ) && ! empty( $this->button['qtag_content_open'] ) )
				{
					add_action( 'admin_print_footer_scripts', array( $this, 'add_quicktag_script' ), $prio );
				}
				
				self::$count ++;
			}  
		}  
		
		
		/**
		 * Add current button to the $buttons array in the tinymce visual editor
		 * 
		 * @param array $buttons
		 * @return array
		 */
		public function display_in_editor( $buttons ) 
		{  
			array_push( $buttons, $this->button['id'] );
			return $buttons;  
		}  
		
		/**
		 * add the javascript that holds the tinyce plugin
		 * 
		 * @param array $plugin_array
		 * @return array
		 */
		public function add_javascript( $plugin_array ) 
		{  
			$plugin_array[ $this->button['id'] ] = $this->button['js_plugin_file'];
			
			return $plugin_array;  
		}
		
		/**
		 * 
		 * @since 4.5.5
		 */
		public function add_quicktag_script()
		{
			if( wp_script_is( 'quicktags' ) )
			{
				$out = '';
				
				$out .=	'<script type="text/javascript">';
				$out .=		"QTags.addButton( '{$this->button['id']}', '{$this->button['qtag_display']}', '{$this->button['qtag_content_open']}', '{$this->button['qtag_content_close']}', '{$this->button['qtag_access_key']}', '{$this->button['title']}' );";
				$out .=	'</script>';

				echo $out;
			}	
		}

		/**
		 * print js globals so the tinymce plugin can fetch them
		 */
		public function create_js_globals()
		{
			
			echo "\n <script type='text/javascript'>\n /* <![CDATA[ */  \n";
			echo "var avia_globals = avia_globals || {};\n";
			echo "    avia_globals.sc = avia_globals.sc || {};\n";
			echo "    avia_globals.sc['" . $this->button['id'] . "'] = [];\n";
			echo "    avia_globals.sc['" . $this->button['id'] . "'].title = '" . $this->button['title'] . "';\n";
			echo "    avia_globals.sc['" . $this->button['id'] . "'].access_key = '" . $this->button['access_key'] . "';\n";
			echo "    avia_globals.sc['" . $this->button['id'] . "'].content_open = '" . $this->button['content_open'] . "';\n";
			echo "    avia_globals.sc['" . $this->button['id'] . "'].content_close = '" . $this->button['content_close'] . "';\n";
			
			echo "    avia_globals.sc['" . $this->button['id'] . "'].image = '" . $this->button['image'] . "';\n";
			echo "    avia_globals.sc['" . $this->button['id'] . "'].config = [];\n";
			
			foreach( $this->button['shortcodes'] as $config )
			{    
			    if( empty( $config['tinyMCE']['disable'] ) )
			    {
				    echo "    avia_globals.sc['" . $this->button['id'] . "'].config['".$config['php_class']."'] = " . json_encode( $config ) . ";\n";
			    }
			}
			echo "/* ]]> */ \n";
			echo "</script>\n \n ";
		}
	
	
		
	} // end class

} // end if !class_exists