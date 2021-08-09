<?php  if (  ! defined( 'AVIA_FW' ) ) exit( 'No direct script access allowed' );

/**
 * This file holds the avia_htmlhelper class which renders html elements based on the options passed.
 * Basically all backend html output for the option pages is defined within this file
 *
 * @author		Christian "Kriesi" Budschedl
 * @copyright	Copyright ( c ) Christian Budschedl
 * @link		http://kriesi.at
 * @link		http://aviathemes.com
 * @since		Version 1.0
 * @package 	AviaFramework
 */


/**
 * AVIA HTML HELPER
 *
 * This class receives an extended $avia_superobject file when called, which holds the information which page we are currently viewing
 * Based on that information it renders all form elements necessary for creating the option page.
 * Methods can also be called without looping over the $avia_superobject, but for html generating purposes in other parts of the theme
 * (for example meta boxes, widgets etc)
 * @package AviaFramework
 * 
 */
 
if( ! class_exists( 'avia_htmlhelper' ) )
{
	class avia_htmlhelper
	{
		/**
		 * This object holds the $avia_superobject with all the previously stored informations like theme/plugin data, options data, default values etc
		 * @var obj
		 */
		var $avia_superobject;
		
		/**
		 * This object holds the avia_database_set controller methods to check if an item is grouped or not
		 * @var obj
		 */
		var $set;
		
		/**
		 * Different behaviour for some methods based on the context (option_page/metabox)
		 * @var string
		 */
		var $context = 'options_page';
		
		/**
		 * Checks if a database entry with values is available and if so set to true to replace default values
		 * @var bool
		 */
		var $replace_default = array();
		
				
		######################################################################
		# Non rendering Functions
		######################################################################
		
				
		/**
         * Constructor
         *
         * The constructor sets up the superobject, if it was passed
         */
		function __construct($avia_superobject = false)
		{
		
			if(!$avia_superobject) { $avia_superobject = $GLOBALS['avia']; }
			$this->avia_superobject = $avia_superobject;
			
			$options = get_option($this->avia_superobject->option_prefix);
			
			
			//check which option pages were already saved yet and need replacement of the default values
			foreach($avia_superobject->option_pages as $page)
			{
				if(isset($options[$page['parent']]) && $options[$page['parent']] != '')
				{
					$this->replace_default[$page['slug']] = true;
				}
			}
		}
		
		

		function get_page_elements($slug)
		{
			$page_elements = array();
			if(isset($this->avia_superobject->option_page_data))
			{
				foreach($this->avia_superobject->option_page_data as $key => $value)
				{
					if($value['slug'] == $slug)
					{
						$page_elements[$key] = $value;
					}
				}
			}
	
			return $page_elements;
		}
		
		
		
		
		
		######################################################################
		# Rendering Functions
		######################################################################

		
		/**
		 * 
		 * @param array $option_page
		 * @param string $firstClass
		 * @return string
		 */
		function create_container_based_on_slug( $option_page, $firstClass = '' )
		{
			$output = '';
		
			//get all elements of the current page and save them to the page elements array
			$page_elements = $this->get_page_elements( $option_page['slug'] );
		
			//subpage heading
			$output .= $this->render_page_container( $option_page, $firstClass );
			
			//remove button if available:
//			if( isset( $option_page['removable'] ) ) 
//			{
//				$output .= "<a href='#{$option_page['slug']}' title='{$option_page['removable']}' class='avia_remove_dynamic_page'>{$option_page['removable']}</a>";
//			}
			
			//page elements
			foreach( $page_elements as $key => $element )
			{	
				$output .= $this->render_single_element( $element );
			}
			
			$output .= $this->render_page_container_end();
			
			
			return $output;
		}
		
		
		/**
         * render_single_element
         *
         * The function renders a single option-section which means it creates the divs around the form element, as well as descripio, adds values and sets ids
         * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
         * @return string $output the string returned contains the html code generated within the method
         */
         
		function render_single_element( $element )
		{

			if( method_exists( $this, $element['type'] ) || ( isset( $element['use_function'] ) && function_exists( $element['type'] ) ) )
			{
				//init indices that are not set yet to prevent php notice
				if( ! isset( $element['id'] ) ) 	  { $element['id'] = ''; 	}
				if( ! isset( $element['desc']  ) ) { $element['desc']  = ''; }
				if( ! isset( $element['name']  ) ) { $element['name']  = ''; }
				if( ! isset( $element['label'] ) ) { $element['label'] = ''; }
				if( ! isset( $element['std'] ) )   { $element['std'] = ''; }
				if( ! isset( $element['class'] ) ) { $element['class'] = ''; } 
				if( ! isset( $element['dynamic'] ) ) { $element['dynamic'] = false; } 
				
				
				if($this->context != 'metabox')
				{
					if(isset($this->avia_superobject->page_slug) && 
					   isset($this->replace_default[$element['slug']]) && 
					   isset($this->avia_superobject->options[$this->avia_superobject->page_slug][$element['id']]))
					{
						$element['std'] = $this->avia_superobject->options[$this->avia_superobject->page_slug][$element['id']];
					}
				}
							
				//start rendering
				$output = '';
					
				
				//check if its a dynamic (sortable) element	
				$dynamic_end = '';
				
//				if( $element['dynamic'] )
//				{
//					$output .= '<div class="avia_row">';
//					$output .= '	<div class="avia_style_wrap avia_style_wrap_portlet">';
//					$output .= '		<div class="avia-row-portlet-header">'.$element['name'].'<a href="#" class="avia-item-edit">+</a></div>';
//					$output .= '		<div class="avia-portlet-content">';
//					$output .= '		<span class="avia_clone_loading avia_removable_element_loading avia_hidden">Loading</span>';
//					$output .= "		<a href='#".$element['id']."' title='".$element['removable']."' class='avia_remove_dynamic_element'><span>".$element['removable']."</span></a>";
//					$dynamic_end = '<div class="avia_clear"></div></div></div></div>';
//				}				
					
					
					
				//check if we should only render the element itself or description as well
				if( $element['type'] == 'group' || ( isset( $element['nodescription'] ) && $element['nodescription'] != '' ) )
				{
					if( isset( $element['use_function'] ) )
					{
						$output .= $element['type']( $element );
					}
					else
					{
						$output .= $this->{$element['type']}( $element );
					}
				}
				else
				{
					$output .= $this->section_start( $element );
//					if( isset( $element['removable'] ) && ! isset( $element['dynamic'] ) ) 
//					{
//						$output .= '		<span class="avia_clone_loading avia_removable_element_loading avia_hidden">Loading</span>';
//						$output .= "		<a href='#".$element['id']."' title='".$element['removable']."' class='avia_remove_dynamic_element'><span>".$element['removable']."</span></a>";
//					}
					
					$output .= $this->description( $element );
					if( isset( $element['use_function'] ) )
					{
						$output .= $element['type']( $element );
					}
					else
					{
						$output .= $this->{$element['type']}( $element );
					}
					
					$output .= $this->section_end( $element );
				}
				$output .= $dynamic_end;
				return $output;
			}
		}
		
		
		/**
         * Creates a wrapper around a set of elements. This set can be cloned with javascript
         * @param array $element the array holds data like id, class and some js settings
         * @return string $output the string returned contains the html code generated within the method
         */
         
		function group( $element )
		{	
			$iterations = 1;
			$output = '';
			$real_id = $element['id'];
			
			if((isset($element['std']) && is_array($element['std'])) && !isset($element['ajax_request']))
			{
				$iterations = count($element['std']);
			}
			
			if(isset($element['ajax_request'])) $iterations = $element['ajax_request']; // ajax requests usually need only one element per default
			
		
			for ($i = 0; $i < $iterations; $i++)
			{
				if(!isset($element['linktext'])) $element['linktext'] = "add";
				if(!isset($element['deletetext'])) $element['deletetext'] = "remove";
	
				
				//start generating html output
				
				
				$element['id'] = $real_id.'-__-'.$i;
				$output   .= '<div class="avia_set '.$element['class'].'" id="avia_'.$element['id'].'">';
		
				$output   .= '<div class="avia_single_set">';
				
				$output	 .= $this->get_subelements($element, $i);
				
				$output  .= '	<span class="avia_clone_loading avia_hidden" href="#">Loading</span>';
				$output  .= '	<a class="avia_clone_set" href="#">'.$element['linktext'].'</a>';
				$output  .= '	<a class="avia_remove_set" href="#">'.$element['deletetext'].'</a>';
				$output  .= '</div>';
				$output  .= '</div>';
			}
			
			
			return $output;
		}
		
		/**
         * Creates the subelements for groups and specail objects like gallery upload
         * @param array $element the array holds data like id, class and some js settings
         * @return string $output the string returned contains the html code generated within the method
         */
		function get_subelements($element, $i = 1)
		{
			$output = '';
					
			foreach($element['subelements'] as $key => $subelement)
			{
				if(isset($element['std']) && is_array($element['std']) && isset($element['std'][$i][$subelement['id']]))
				{
					$subelement['std'] = $element['std'][$i][$subelement['id']];
				}
				
				if(isset($element['ajax_request']))
				{
					$subelement['ajax_request'] = $element['ajax_request'];
				}
				
				$subelement['subgroup_item'] = true;
				$subelement['id'] = $element['id']."-__-".$subelement['id'];
				
				if(isset($element['apply_all'])) $subelement['apply_all'] = $element['apply_all'];
				$output  .=      $this->render_single_element($subelement);
			}
			
			return $output;
		}
		
		

		
		/**
         * Renders the title and the page containing wrapper necessary for javascript sidebar tabs 
		 * 
         * @param array $pageinfo		the array holds data like slug, parent, icon, class
		 * @param string $firstClass
         * @return string				$output the string returned contains the html code generated within the method
         */
		function render_page_container( $pageinfo, $firstClass )
		{	
			if( ! isset( $pageinfo['sortable'] ) ) 
			{
				$pageinfo['sortable'] = '';
			}
			
			$class = ! empty( $pageinfo['class'] ) ? " {$pageinfo['class']}" : '';
			if( ( false === strpos( $pageinfo['icon'], 'http://' ) ) && ( false === strpos( $pageinfo['icon'], 'https://' ) ) )
			{
				$icon_url = AVIA_IMG_URL . 'icons/' . $pageinfo['icon'];
			}
			else
			{
				$icon_url = $pageinfo['icon'];
			}
			
			$output  = '<div class="avia_subpage_container ' . $firstClass . ' ' . $pageinfo['sortable'].'" id="avia_' . avia_backend_safe_string( $pageinfo['slug'] ) . '">';	
			$output .=		'<div class="avia_section_header' . $class . '">';	
			$output .=			'<strong class="avia_page_title" style="background-Image:url(' . $icon_url . ');">'; 
			$output .=				$pageinfo['title'];
			$output .=			'</strong>'; 
			$output .=		'</div>'; 
			
			return $output;
		}
		
		/**
         * Closes the page container
         * @return string $output the string returned contains the html code generated within the method
         */
		function render_page_container_end()
		{
			$output = '</div>'; 
			return $output;
		}

		
		/**
		 * Output a verification field with a callback button or 
		 * a simple verification button for a group of option fields
		 * 
		 * @since < 4.0
		 * @param array $element
		 * @return string
		 */
		public function verification_field( $element )
		{
			$callback 			= $element['ajax'];
			$js_callback 		= isset($element['js_callback']) ? $element['js_callback'] : '';
			$element['simple'] 	= true;
			$output  			= '';
			$ajax				= false;
			
			$ids_data = '';
			
			if( ! empty( $element['input_ids'] ) )
			{
				$ids_data = 'data-av-verify-fields="' . implode( ',', $element['input_ids'] ) . '"';
			}
			
			
			if(isset($element['button-relabel']) && !empty($element['std']))
			{
				$element['button-label'] = $element['button-relabel'];
			}
			
			$input_field = ( empty( $ids_data ) ) ? $this->text( $element ) : $this->hidden( $element );
			
			/**
			 * Filter to replace normal input field with a password input field
			 * 
			 * @since 4.5.6.2
			 * @param boolean
			 * @param array $element
			 * @return boolean
			 */
			if( false !== apply_filters( 'avf_verification_password_field', false, $element ) )
			{
				$input_field = str_replace( 'type="text"', 'type="password"', $input_field );
			}
			
			$output .=	'<span class="avia_style_wrap avia_verify_input avia_upload_style_wrap">';
			$output .=		$input_field;
			$output .=		'<a href="#" ' . $ids_data . ' data-av-verification-callback="' . $callback . '" data-av-verification-callback-javascript="' . $js_callback . '" class="avia_button avia_verify_button" id="avia_check' . $element['id'] . '">' . $element['button-label'] . '</a>';
			$output .=	'</span>';
			
			$output .= isset($element['help']) ? "<small>{$element['help']}</small>" : '';
			
			$output .=	"<div class='av-verification-result'>";
			
			if( ( $element['std'] != '' ) || ( ! empty( $element['force_callback'] ) ) )
			{
				$output .= str_replace( 'avia_trigger_save', '', $callback( $element['std'] , $ajax, null, $element ) );
			}
			
			$output .=	'</div>';
			
			return $output;
		}
				

		/**
		 * The text method renders a single input type:text element
		 * 
		 * @since 4.7.4.1			extended to support special types and custom attribute array
		 * @param array $element		the array holds data like type, value, id, class, description which are necessary to render the whole option-section
		 * @return string $output		the string returned contains the html code generated within the method
		 */
		function text( $element )
		{	
			$attr = array(
						'type'	=> isset( $element['subtype'] ) ? $element['subtype'] : 'text',
						'id'	=> $element['id'],
						'name'	=> ! empty( $element['id_name'] ) ? $element['id_name'] : $element['id'],
						'value'	=> $element['std'],
						'class'	=> (array) $element['class']
					);
			
			unset( $element['subtype'] );
			
			if( ! empty( $element['readonly'] ) )
			{
				$attr['readonly'] = 'readonly';
			}
			
			if( isset( $element['class_on_value'] ) && ! empty( $element['std'] ) ) 
			{
				$attr['class'][] = $element['class_on_value'];
			}
			
			if( isset( $element['el_attr'] ) )
			{
				$element['el_attr'] = (array) $element['el_attr'];
				$attr = $this->array_merge_recursive_distinct( $attr, $element['el_attr'] );
			}
			
			
			$text = '<input ' . $this->attributes_from_array( $attr ) . ' />';
			
			if( isset( $element['simple'] ) ) 
			{
				return $text;
			}
			
			return '<span class="avia_style_wrap">' . $text . '</span>';
		}
		
		
		/**
		 * The hidden method renders a single input type:hidden element
		 * 
		 * @param array	$element		the array holds data like type, value, id, class, description which are necessary to render the whole option-section
		 * @return string $output		the string returned contains the html code generated within the method
		 */
		function hidden( $element )
		{			
			$output  = '<div class="avia_section avia_hidden">';
			$output .=		'<input type="hidden" value="' . $element['std'] . '" id="' . $element['id'] . '" name="' . $element['id'] . '"/>';
			$output .= '</div>';

			return $output;
		}

		
		/**
		 * The checkbox method renders a single input type:checkbox element
		 * 
		 * @since 4.6.3 extended with toggle
		 * @param array $element	the array holds data like type, value, id, class, description which are necessary to render the whole option-section
		 * @return string $output	the string returned contains the html code generated within the method
		 * @todo: fix: checkboxes at metaboxes currently dont work
		 */
		function checkbox( $element )
		{	
			$output = '';

			$checked = '';
			if( $element['std'] != '' && $element['std'] != 'disabled' ) 
			{ 
				$checked = 'checked = "checked"';
			}

			$element['id_name'] = ! empty( $element['id_name'] ) ? $element['id_name'] : $element['id'];

			$input = '<input ' . $checked . ' type="checkbox" class="' . $element['class'] . '" value="' . $element['id'] . '" id="' . $element['id'] . '" name="' . $element['id_name'] . '" />';

			if( ! current_theme_supports( 'avia_option_pages_toggles' ) )
			{
				return $input;
			}

			$output .=	'<div class="av-switch-' . $element['id'] . ' av-toggle-switch active ' . $element['class'] . '">';
			$output .=		'<label>';
			$output .=			$input;
			$output .=			'<span class="toggle-track"></span>';
			$output .=		'</label>';
			$output .=	'</div>';

			return $output;
		}

		
		/**
         * 
         * The radio method renders one or more input type:radio elements, based on the definition of the $elements array
         * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
         * @return string $output the string returned contains the html code generated within the method
         */
		function radio( $element )
		{

			$output = '';
			$counter = 1;
			foreach($element['buttons'] as $radiobutton)
			{
				$checked = '';
				if( $element['std'] == $counter ) { $checked = 'checked = "checked"'; }

				$output  .= '<span class="avia_radio_wrap">';
				$output  .= '<input '.$checked.' type="radio" class="'.$element['class'].'" ';
				$output  .= 'value="'.$counter.'" id="'.$element['id'].$counter.'" name="'.$element['id'].'"/>';

				$output  .= '<label for="'.$element['id'].$counter.'">'.$radiobutton.'</label>';
				$output  .= '</span>';

				$counter++;
			}

			return $output;
		}


        /**
         *
         * The imgselect method renders one or more input type:radio elements, based on the definition of the $elements array with images
         * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
         * @return string $output the string returned contains the html code generated within the method
         */
        function imgselect( $element )
        {
            $output = '';
            $counter = 1;
            foreach($element['buttons'] as $key => $radiobutton)
            {
                $checked = '';
                $image = '';
                $extra_class = '';

                if( $element['std'] == $key ) {
                    $checked = 'checked = "checked"';
                }

                $output  .= '<span class="avia_radio_wrap'.$extra_class .'">';


                $output  .= '<input '.$checked.' type="radio" class="'.$element['class'].'" ';
                $output  .= 'value="'.$key.'" id="'.$element['id'].$counter.'" name="'.$element['id'].'"/>';

                $output  .= '<label for="'.$element['id'].$counter.'">';

                if( isset($element['images']) &&  !empty($element['images'][$key]) ) {

                    $output  .= "<img class='radio_image' src='".$element['images'][$key]."' />";
                    $extra_class = ' avia-image-radio';
                }

                $output  .= '<span>'.$radiobutton.'</span>';
                $output  .= '</label>';
                $output  .= '</span>';

                $counter++;
            }

            return $output;
        }


		/**
		 * The textarea method renders a single textarea element
		 * 
		 * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
		 * @return string $output the string returned contains the html code generated within the method
		 */
		function textarea( $element )
		{	
			$output  = '';
			
			$attr = array(
						'id'	=> $element['id'],
						'name'	=> ! empty( $element['id_name'] ) ? $element['id_name'] : $element['id'],
						'class'	=> (array) $element['class'],
						'rows'	=> 5,
						'cols'	=> 30
					);
			
			if( isset( $element['el_attr'] ) )
			{
				$element['el_attr'] = (array) $element['el_attr'];
				$attr = $this->array_merge_recursive_distinct( $attr, $element['el_attr'] );
			}
			
			$output .=	'<textarea ' . $this->attributes_from_array( $attr ) . ' >';
			$output .=		$element['std'];
			$output .=	'</textarea>';
					
			return $output;
		}

		/**
         * 
         * The link_controller method renders a bunch of links that are able to set values for other page elements
         * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
         * @return string $output the string returned contains the html code generated within the method
         */
		function link_controller( $element )
		{
			$output  = '';
			$output .= '<div class="'.$element['class'].'">';
			
			if(!empty($element['subtype']))
			{
				$counter = 0;
		
				foreach($element['subtype'] as $key=>$array)
				{
					$counter ++;
					$active = $style = $class = $data = '';
					if(isset($array[$element['id']]) && $array[$element['id']] == $element['std'] ) $active = " avia_link_controller_active";
					if(isset($array['style'])) { $style = " style='".$array['style']."' "; unset($array['style']); }
					if(isset($array['class'])) { $class = " ".$array['class']; unset($array['class']); }
					
					foreach($array as $datakey=> $datavalue)
					{
						$data .= "data-".$datakey."='".$datavalue."' ";
					}

					$output .= "<a href='#' ".$data." ".$style." class='avia_link_controller avia_link_controller_".$counter.$active.$class."'>".$key."</a>";
				}
			}
			
			$output .= '<input type="hidden" value="'.$element['std'].'" id="'.$element['id'].'" name="'.$element['id'].'"/>';
			
			$output .= "</div>";
			return $output;
		}
		

		
		/**
		 * The upload method renders a single upload element so users can add their own pictures
		 * the script first gets the id of a hidden post that should store the image. if no post is set it will create one
		 * then we check if a basic url based upload should be used or a more sophisticated id based for slideshows and feauted images, which need
		 * the images resized automatically
		 *
		 * @param array $element		holds data like type, value, id, class, description which are necessary to render the whole option-section
		 * @return string				the html code generated within the method
		 */
		function upload( array $element )
		{	
			$output  = '';
			
			$gallery_mode = false;
			$id_generated = false;
			$image_url = $element['std'];
			
			if( empty( $element['button-label'] ) ) 
			{
				$element['button-label'] = 'Upload';
			}
			
			//get post id of the hidden post that stores the image
			if( ! empty( $element['attachment-prefix'] ) )
			{
				if( empty( $element['std'] ) && empty( $element['no-attachment-id'] ) ) 
				{
					$element['std'] = uniqid();
					$id_generated = true;
				}
				
				$gallery_mode = true;
				$postId = avia_media::get_custom_post( $element['attachment-prefix'] . $element['std'] );
			}
			else
			{
				$postId = avia_media::get_custom_post( $element['name'] );
				if( is_numeric( $element['std'] ) )
				{
					$image_url = wp_get_attachment_image_src( $element['std'], 'full' );
					$image_url = is_array( $image_url ) ? $image_url[0] : '';
				}
			}
			//switch between normal url upload and advanced image id upload
			$mode = $prevImg = '';
			
			//video or image, advanced or default upload?
			
			if( isset( $element['subtype'] ) ) 
			{
				//	is not used in Enfold up tp 4.8.3 - can be removed in a future version
				$mode = ' avia_advanced_upload';

				if( ! is_numeric( $element['std'] ) && $element['std'] != '' )
				{
					$prevImg = '<a href="#" class="avia_remove_image">×</a><img src="' . AVIA_IMG_URL . 'icons/video.png" alt="" />';
				}
				else if( $element['std'] != '' )
				{
					$prevImg = '<a href="#" class="avia_remove_image">×</a>' . wp_get_attachment_image( $element['std'], array( 100, 100 ) );
				}
			}
			else
			{
				if( ! preg_match( '!\.jpg$|\.jpeg$|\.ico$|\.png$|\.gif$!', $image_url) && $image_url != '' )
				{
					$prevImg = '<a href="#" class="avia_remove_image">×</a><img src="'.AVIA_IMG_URL.'icons/video.png" alt="" />';
				}
				else if( $image_url != '' )
				{
					$prevImg = '<a href="#" class="avia_remove_image">×</a><img src="' . $image_url . '" alt="" />'; 
				}
			}

			if( $gallery_mode )
			{
				$image_url_array = array();
				$attachments = get_children( array(
									'post_parent'		=> $postId,
									'post_status'		=> 'inherit',
									'post_type'			=> 'attachment',
									'post_mime_type'	=> 'image',
									'order'				=> 'ASC',
									'orderby'			=> 'menu_order ID'
								));
	
				foreach( $attachments as $key => $attachment ) 
				{
					$image_url_array[] = avia_image_by_id( $attachment->ID, array( 'width' => 80, 'height' => 80 ) );
				}
				
				$output  .= "<div class='avia_thumbnail_container'>";
				
				if( isset( $image_url_array[0] ) )
				{
					foreach( $image_url_array as $key => $img ) 
					{
						$output .= "<div class='avia_gallery_thumb'><div class='avia_gallery_thumb_inner'>{$img}</div></div>";
					}
					
					$output .= "<div class='avia_clear'></div>";
				}
				
				$output .= '</div>';
			}
			
			$data = '';
			$upload_class = 'avia_uploader'; 
			
			global $wp_version;
			
			if( version_compare( $wp_version, '3.5', '>=' ) && empty( $element['force_old_media'] ) && empty( $element['subtype'] ) ) //check if new media upload is enabled
			{
				$upload_class = 'avia_uploader_35'; 
			
				if( empty( $element['data'] ) )
				{
					//	Layout changed, we need to adjust CSS for hidden elements
					$wp53 = version_compare( $wp_version, '5.3', '>=' ) ? ' avia-wp-53' : '';
					
					$element['data'] = array(	
											'target' => $element['id'], 
											'title'  => $element['name'], 
											'type'   => 'image', 
											'button' => $element['label'],
											'class'  => 'media-frame av-media-frame-image-only' . $wp53,
											'frame'  => 'select',
											'state'	 => 'av_select_single_image',
											'fetch'  => 'url',
									);
				}
				
				foreach( $element['data'] as $key => $value )
				{
					if( is_array( $value ) ) 
					{
						$value = implode( ', ', $value );
					}
					
					$data .= " data-$key='$value' ";
				}
			}
			
			$output .= '<div class="avia_upload_container avia_upload_container_' . $postId . $mode . '">';
			$output .= '	<span class="avia_style_wrap avia_upload_style_wrap">';
			
			$id = $element['id'];
			$id_name = empty( $element['id_name'] ) ? $element['id'] : $element['id_name'];

			$output .=			'<input type="text" class="avia_upload_input ' . $element['class'] . '" value="' . $element['std'] . '" name="'. $id_name .'" id="'. $id .'" />';
			$output .=			'<a ' . $data . ' href="#' . $postId . '" class="avia_button ' . $upload_class . '" title="' . $element['name'] . '" id="avia_upload' . $element['id'] . '">' . $element['button-label'] . '</a>';
			$output .=		'</span>';
			$output .=		'<div class="avia_preview_pic" id="div_' . $element['id'] . '">' . $prevImg . '</div>';
			$output .=		'<input class="avia_upload_insert_label" type="hidden" value="' . $element['label'] . '" />';
			
			if( $gallery_mode ) 
			{
				$output .=	'<input class="avia_gallery_mode" type="hidden" value="' . $postId . '" />';
			}
			
			$output .= '</div>';
				
			return $output;
		}
		
		/**
         * The upload gallery method renders a single upload element so users can add their own pictures and/or videos
		 * 
		 * This element is no longer in use in Enfold 4.6.4 (or prior versions ?)
         *
         * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
         * @return string $output the string returned contains the html code generated within the method
         */
		function upload_gallery( $element )
		{
			//first gernerate the sub_item_output
			$sub_output = '';
			$iterations = 0;
			$real_id = $element['id'];
			
			if((isset($element['std']) && is_array($element['std'])) && !isset($element['ajax_request']))
			{
				if(!empty($element['std'][0]['slideshow_image']) || !empty($element['std'][0]['slideshow_video']))
				{
					$iterations = count($element['std']);
				}
			}
			
			$video_button = 'Add external video by URL';
			if(isset($element['button_video'])) $video_button = $element['button_video']; // ajax requests usually need only one element per default
			if(isset($element['ajax_request'])) $iterations = $element['ajax_request']; // ajax requests usually need only one element per default
			
			for ($i = 0; $i < $iterations; $i++)
			{
				//start generating html output
				
				$element['id'] = $real_id.'-__-'.$i;
				
				$sub_output  .= '<div class="avia_set avia_row '.$element['class'].'" id="avia_'.$element['id'].'" >';
				$sub_output  .= 	'<div class="avia_single_set"><div class="avia_handle"></div>';
				$sub_output	 .= 		$this->get_subelements($element, $i);
				$sub_output  .= '		<a class="avia_remove_set remove_all_allowed" href="#">'.__('(remove)').'</a>';
				$sub_output  .= '		<a class="open_set" data-openset="'.__('Show').'" data-closedset="'.__('Hide').'" href="#">'.__('Show').'</a>';
				$sub_output  .= 	'</div>';
				$sub_output  .= '</div>';
			}
			
			//if this is an ajax request stop here
			if(isset($element['ajax_request'])) return $sub_output;
			
			
			global $post_ID;
			//if we want to retrieve the whole element and this is not an ajax call do the following as well:
			if(empty($element['button-label'])) $element['button-label'] = "Add Image to slideshow";
			$postId = $post_ID; //avia_media::get_custom_post($element['name']);
			$output = '';
			
			$output .= '<div class="avia_gallery_upload_container avia_gallery_upload_container'.$postId.' avia_delay_required">';
			$output .= '<div class="avia_sortable_gallery_container">';
			
			$output .= $sub_output; 

			$output .= '</div>';
			
			$output .= '<div class="button_bar">';
			$output .= '	<span class="avia_style_wrap avia_upload_style_wrap">';
				//generate the upload link
					$output .= '<a href="#" class="avia_button avia_gallery_uploader" title="'.$element['name'].'" id="avia_gallery_uploader '.$element['id'].'"';
					$output .= 'data-label="'.$element['label'].'" ';
					$output .= 'data-this-id="'.$element['id'].'" ';
					$output .= 'data-attach-to-post = "'.$postId.'" ';
					$output .= 'data-real-id="'.$real_id.'" ';
					$output .= '>'.$element['button-label'].'</a>';
				//end link
			$output .= '	</span>';
			
			if(!empty($video_button))
			{
			$output .= '	<span class="avia_style_wrap avia_upload_style_wrap">';
				//generate the upload link
					$output .= '<a href="#" class="avia_button avia_gallery_uploader" title="'.$element['name'].'" id="avia_gallery_uploader '.$element['id'].'"';
					$output .= 'data-label="'.$element['label'].'" data-video-insert = "avia_video_insert"';
					$output .= 'data-attach-to-post = "'.$postId.'" ';
					$output .= 'data-real-id="'.$real_id.'" ';
					$output .= 'data-this-id="'.$element['id'].'" ';
					$output .= '>'.$video_button.'</a>';
				//end link
			$output .= '	</span>';
			}
			
			//delete button
			$output .= '	<span class="avia_style_wrap avia_upload_style_wrap avia_delete_style_wrap">';
				//generate the upload link
					$output .= '<a href="#" class="avia_button avia_gallery_delete_all avia_button_grey" id="avia_gallery_delete_all"';
					$output .= '>Remove All</a>';
				//end link
			$output .= '	</span>';
			$output .= '</div>'; //end button bar
			
			
			
			$output .= '</div>';
			return $output;
		}
		
		
		
		//the gallery image is a helper to the upload_gallery method that displays a single image and enables you to change that image
		function gallery_image($element)
		{
			$prevImg = $extraClass = '';
			$real_id = explode('-__-', $element['id']);
			$real_id = $real_id[0];
			
			global $post_ID;
			if(empty($post_ID) && isset($element['apply_all'])) $post_ID = $element['apply_all'];
			
			if(!is_numeric($element['std']) || $element['std'] == '')
			{
				$prevImg = '<img src="'.AVIA_IMG_URL.'icons/video_insert_image.png" alt="" />';
				$extraClass = " avia_gallery_image_vid";
			}
			else if($element['std'] != '')
			{
				$prevImg = wp_get_attachment_image($element['std'], array(100,100));
				$extraClass = " avia_gallery_image_img";
			}
			
		
			$output ='';
			$output .=' <div class="avia_gallery_image'.$extraClass.'">';
			
				//generate the upload link
					$output .= '<a href="#" class="avia_gallery_uploader" title="'.$element['name'].'" id="avia_gallery_image '.$element['id'].'"';
					$output .= 'data-label="'.$element['label'].'" ';
					$output .= 'data-this-id="'.$element['id'].'" ';
					$output .= 'data-attach-to-post = "'.$post_ID.'" ';
					$output .= 'data-real-id="'.$real_id.'" ';
					$output .= 'data-overwrite="true" ';
					$output .= '>'.$prevImg.'</a>';
					$output .= '<input type="text" class="avia_gallery_image_value '.$element['class'].'" value="'.$element['std'].'" name="'.$element['id'].'" id="'.$element['id'].'" />';
				//end link
			
			$output .= '</div>';
			return $output;
		}
		
		
		
		/**
         * 
         * The text method renders a single input type:text element. If autodetect is set the color picker trys to get the color from an image upload element
         * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
         * @return string $output the string returned contains the html code generated within the method
         */
		function colorpicker( $element )
		{	
			$autodetect = $autodetectClass = '';
			
			if( isset($element['autodetect'] ) && function_exists( 'gd_info' ) )
			{
				$autodetect = '<a id="avia_autodetect_' . $element['id'] . '" class="avia_button avia_autodetect" href="#' . $element['autodetect'] . '">Auto detection</a><span class="avia_loading"></span>';
				$autodetectClass = ' avia_auto_detector';
			}
			
			if( empty( $element['id_name'] ) )
			{
				$element['id_name'] = $element['id'];
			}
				
			$output  = '<span class="avia_style_wrap avia_colorpicker_style_wrap' . $autodetectClass . '">';
			$output .=		'<input type="text" class="avia_color_picker ' . $element['class'] . '" value="' . $element['std'] . '" id="' . $element['id'] . '" name="' . $element['id_name'] . '"/>';
			$output .=		'<span class="avia_color_picker_div"></span>' . $autodetect;
			$output .= '</span>';
			return $output;
		}


		/**
         * 
         * The file_upload method renders a single zip file upload/insert element: user can select a zip file and a custom function/class will be executed then
         * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
         * @return string $output the string returned contains the html code generated within the method
         */
		
		function file_upload( $element )
		{
			# deny if user is no super admin
			$output = '';
			$cap = apply_filters( 'avf_file_upload_capability', 'update_plugins', $element );
			
			if( ! current_user_can( $cap ) ) 
			{
				return "<div class='av-error'><p>Using this feature is reserved for Super Admins</p><p>You unfortunately don't have the necessary permissions.</p></div>";
			}
			
			#check if its allowed on multisite
			if(is_multisite() && strpos(get_site_option('upload_filetypes'), $element['file_extension']) === false)
			{
				$file = strtoupper($element['file_extension']);
			
				return "<div class='av-error'><p>You are currently on a WordPress multisite installation and .{$file} file upload is disabled. <br/>Go to your <a href='".network_admin_url('settings.php')."'>Network settings page</a> and add the '{$file}' file extension to the list of allowed 'Upload file types'</p></div>";
			}
			
			if( !ini_get('allow_url_fopen') && !empty($element['fopen_check'])) 
			{
			   $output .= "<div class='av-error'><p>Your Server has disabled the 'allow_url_fopen' setting in your php.ini which might cause problems when uploading and processing files</p></div>";
			}
			

			
			# is user is alowed to extract files create the upload/insert button
			if( empty( $element['data'] ) )
			{
				$element['data'] =  array(	'target' => $element['id'], 
											'title'  => $element['title'], 
											'type'   => $element['file_type'], 
											'button' => $element['button'],
											'trigger' => $element['trigger'],
											'class'  => 'media-frame '
									);
				
				$filter_keys = array( 'filter_tabs', 'filter_values', 'skip_tabs', 'skip_values' );
				foreach( $filter_keys as $key ) 
				{
					if( ! empty( $element[ $key ] ) )
					{
						$element['data'][ $key ] = $element[ $key ];
					}
				}
			}
			
			$data = '';
			
			foreach( $element['data'] as $key => $value )
			{
				if(is_array($value)) $value = implode(", ",$value);
				$data .= " data-$key='$value' ";
			}
			
			
			$class 	 = 'avia_button avia-media-35 aviabuilder-file-upload avia-builder-file-insert '.$element['class'];
			$output .= '<span class="avia_style_wrap"><a href="#" class=" '.$class.'" '.$data.' title="'.esc_attr($element['title']).'">'.$element['title'].'</a></span>';
			$output .= '<span class="avia_loading avia_upload_loading"></span>';
			//$output .= $this->text($element);
			$output .= $this->hidden($element);
			
			$output .= apply_filters('avf_file_upload_extra', '', $element);
			
			
			return $output;
		}
		

		
		
		
		/**
         * The select method renders a single select element: it either lists custom values, all wordpress pages or all wordpress categories
         * 
		 * @since 4.2.7 - by Günter:
		 *		- Support for hierarchical display of pages, posts, custom post types and taxonomies was added by default (can be deselected)
		 *		- Filter for post_status other than publish is now possible - title is extended for those post_status
		 * 
         * @param array $element	the array holds data like type, value, id, class, description which are necessary to render the whole option-section
         * @return string $output	the string returned contains the html code generated within the method
         */
		public function select( $element )
		{	
			$base_url	 = '';
			$folder_data = '';
		
			if( $element['subtype'] == 'page' )
			{
				$select = isset( $element['option_none_text'] ) ? $element['option_none_text'] : __( 'Select page', 'avia_framework' );
				
				if( ! isset( $element['hierarchical'] ) || ( 'no' != $element['hierarchical'] ) )
				{
					$args = array(
								'option_none_text'	=> $select,
								'option_none_value'	=> '',
								'option_no_change'	=> ''
							);
					
					$element = wp_parse_args( $element, $args );
					
					$html = $this->select_hierarchical_post_types( $element, 'page' );
					
					if( false !== $html )
					{
						return $html;
					}
				}
				
				/**
				 * @since 4.2.7
				 */
				$limit = apply_filters( 'avf_dropdown_post_number', 9999, $element['subtype'], $element, 'avia_fw_select' );
				
				/**
				 * Make sure we have no spaces
				 */
				$post_status = is_array( $element['post_status'] ) ? $element['post_status'] : explode( ',', (string) $element['post_status'] );
				$element['post_status'] = array_map( function( $value ) { $value = trim($value); return $value;}, $post_status );
				
				$args = array(
							'post_type'		=> $element['subtype'],
							'post_status'	=> empty( $element['post_status'] ) ? 'publish' : $element['post_status'],
							'sort_column'	=> 'post_title',
							'sort_order'	=> 'ASC',
							'number'		=> $limit
						);
				
				$entries = get_pages( $args );
			}
			else if( $element['subtype'] == 'post' )
			{
				$select = isset( $element['option_none_text'] ) ? $element['option_none_text'] : __( 'Select post', 'avia_framework' );
				
				if( ! isset ( $element['hierarchical'] ) || ( 'no' != $element['hierarchical'] ) )
				{
					$args = array(
								'option_none_text'	=> $select,
								'option_none_value'	=> '',
								'option_no_change'	=> ''
							);
					
					$element = wp_parse_args( $element, $args );
					
					$html = $this->select_hierarchical_post_types( $element, 'portfolio' );
					
					if( false !== $html )
					{
						return $html;
					}
				}
				
				/**
				 * @since 4.2.7
				 */
				$limit = apply_filters( 'avf_dropdown_post_number', 9999, $element['subtype'], $element, 'avia_fw_select' );
				
				$args = array(
							'post_type'		=> $element['subtype'],
							'post_status'	=> empty( $element['post_status'] ) ? 'publish' : $element['post_status'],
							'orderby'		=> 'post_title',
							'order'			=> 'ASC',
							'numberposts'	=> $limit
					);
				
				$entries = get_posts( $args );
			}
			else if( $element['subtype'] == 'cat' )
			{
				$add_taxonomy = '';
				$sel_tax = 'category';		//	default taxonomy
				
				if( ! empty( $element['taxonomy'] ) ) 
				{
					$add_taxonomy = "&taxonomy=" . $element['taxonomy'];
					$sel_tax = $element['taxonomy'];
				}
			
				$select = isset ( $element['option_none_text'] ) ? $element['option_none_text'] : __( 'Select category', 'avia_framework' );
				
				if( ! isset ( $element['hierarchical'] ) || ( 'no' != $element['hierarchical'] ) )
				{
					$args = array(
								'option_none_text'	=> $select,
								'option_none_value'	=> '',
								'option_no_change'	=> ''
							);
					
					$element = wp_parse_args( $element, $args );
					
					$html = $this->select_hierarchical_taxonomy( $element, $sel_tax );
					
					if( false !== $html )
					{
						return $html;
					}
				}
				
				$entries = get_categories('title_li=&orderby=name&hide_empty=0' . $add_taxonomy );
				
			}
			else if( $element['subtype'] == 'post_type' )
			{
				$select = isset( $element['option_none_text'] ) ? $element['option_none_text'] : __( 'Select post types...', 'avia_framework' );
				
				$args = array(
								'_builtin' => true,
								'public'   => true
							);
				$post_types = get_post_types( $args, 'objects', 'or' );
				
				$entries = array();
				foreach ( $post_types  as $post_type )
				{
					if( isset( $element['features'] ) && is_array( $element['features'] ) )
					{
						$skip = false;
						foreach( $element['features'] as $feature ) 
						{
							if( ! post_type_supports( $post_type->name, $feature ) ) 
							{
								$skip = true;
								break;
							}
						}
						
						/**
						 * @used_by			Avia_Gutenberg					10
						 * @since 4.5.2
						 */
						$skip = apply_filters( 'avf_select_post_types', $skip, $post_type, 'option_page', $element );
						
						if( $skip )
						{
							continue;
						}
					}
					
					$label = array();
					if( $post_type->_builtin )
					{
						$label[] = __( 'built in', 'avia_framework' );
					}
					
					/**
					 * @used_by				Avia_Gutenberg				10
					 * @since 4.5.2
					 */
					$label = apply_filters( 'avf_select_post_types_status', $label, $post_type, 'option_page', $element );
					
					if( empty( $label ) )
					{
						$label = '';
					}
					else
					{
						$label = ' (' . implode( ', ', $label ) . ')';
					}
					
					$label = $post_type->label . $label;
					$entries[ $label ] = $post_type->name;
				}
			}
			else if( $element['subtype'] == 'option_page_tabs' )
			{
				global $avia;
				
				$entries = array();
				$avia_pages = is_array( $avia->option_pages ) ? $avia->option_pages : array();
				
				$pages = array();
				
				foreach( $avia_pages as $avia_page ) 
				{
					if( $avia_page['parent'] == $avia_page['slug'] )
					{
						$pages[ $avia_page['parent'] ] = $avia_page['title'];
					}
				}
				
				//	only add options page title if more than 1 exists
				if( count( $pages ) <= 1 )
				{
					$pages = array();
				}
				
				foreach( $avia_pages as $avia_page ) 
				{
					$desc = array_key_exists( $avia_page['parent'], $pages ) ? ' (' . $pages[ $avia_page['parent'] ] . ')' : '';
					$entries[ $avia_page['title'] . $desc ] = $avia_page['parent'] . ':' . $avia_page['slug'];
				}
			}
			else
			{	
				$select = __( 'Select...', 'avia_framework' );
				$entries = $element['subtype'];
				$add_entries = array();
				
				if(isset($element['folder']))
				{	
					$add_file_array = avia_backend_load_scripts_by_folder(AVIA_BASE.$element['folder']);
					$base_url = AVIA_BASE_URL;
					$folder_data = "data-baseurl='{$base_url}'";
					
					if(is_array($add_file_array))
					{
						foreach($add_file_array as $file)
						{
							$skip = false;
						
							if(!empty($element['exclude']))
							{
								foreach($element['exclude'] as $exclude) {
        							if (stripos($file,$exclude) !== false) $skip = true;
    							}
							}
							
							if(strpos($file, '.') !== 0 && $skip == false)
							{
								$add_entries[$element['folderlabel'].$file] = "{{AVIA_BASE_URL}}".$element['folder'].$file; 
							}
						}
					
					
						if(isset($element['group']))
						{
							$entries[$element['group']] = $add_entries;
						}
						else
						{
							$entries = array_merge($entries, $add_entries);
						}
					}
						
				}
			}
			
			
			//check for onchange function
			$onchange = '';
			if(isset($element['onchange'])) 
			{
				$onchange = " data-avia-onchange='".$element['onchange']."' ";
				$element['class'] .= " avia_onchange";
			}
			
			if( isset( $element['option_all_text'] ) )
			{
				$select_all_val = is_string( $element['option_all_text'] ) ? $element['option_all_text'] : __( 'Select all ......', 'avia_framework' );
				$all = array( $select_all_val => 'avia_all_elements' );
				$entries = array_merge( $all, $entries );
			}
			
			$multi = $multi_class = '';
			if(isset($element['multiple'])) 
			{
				$multi_class = " avia_multiple_select";
				$multi = 'multiple="multiple" size="'.$element['multiple'].'"';
				
				if(!empty($element['std']))
				{
					$element['std'] = explode(',', (string) $element['std']);
				}
			}
			
			$element['id_name'] = ! empty( $element['id_name'] ) ? $element['id_name'] : $element['id'];
			
			$output  = '<span class="avia_style_wrap avia_select_style_wrap'.$multi_class.'"><span class="avia_select_unify">';
			$output .= '<select '.$folder_data.' '.$onchange.' '.$multi.' class="'.$element['class'].'" id="'. $element['id'] .'" name="'. $element['id_name'] . '"> ';
			
			
			if( ! isset( $element['no_first'] ) ) 
			{ 
				$output .= '<option value="">' . $select . '</option>  '; 
				$fake_val = $select; 
			}
			
			$real_entries = array();
			foreach ($entries as $key => $entry)
			{
				if(!is_array($entry))
				{
					$real_entries[$key] = $entry;
				}
				else
				{
					$real_entries['option_group_'.$key] = $key;
				
					foreach($entry as $subkey => $subentry)
					{
						$real_entries[$subkey] = $subentry;
					}
					
					$real_entries['close_option_group_'.$key] = "close";
				}
			}
			
			$entries = $real_entries;
			
			foreach ($entries as $key => $entry)
			{
				
				if( $element['subtype'] == 'page' || $element['subtype'] == 'post' )
				{
					$id = $entry->ID;
					$title = $this->handler_wp_list_pages( avia_wp_get_the_title( $id ), $entry );
				}
				else if($element['subtype'] == 'cat')
				{
					if(isset($entry->term_id))
					{
						$id = $entry->term_id;
						$title = $entry->name;
					}
				}
				else
				{
					$id = $entry;
					$title = $key;				
				}
			
				if(!empty($title) || (isset($title) && $title === 0))
				{
					if(!isset($fake_val)) $fake_val = $title;
					$selected = '';
					if ($element['std'] == $id || (is_array($element['std']) && in_array($id, $element['std']))) { $selected = "selected='selected'"; $fake_val = $title;}
					if($base_url &&  str_replace($base_url, '{{AVIA_BASE_URL}}', $element['std']) == $id) {$selected = "selected='selected'"; $fake_val = $title;}
					
					if(strpos ( $title , 'option_group_') === 0) 
					{
						$output .= "<optgroup label='". $id."'>";
					}
					else if(strpos ( $title , 'close_option_group_') === 0) 
					{
						$output .= "</optgroup>";
					}
					else
					{
						$output .= "<option $selected value='". $id."'>". $title."</option>";
					}
				}
			}
			$output .= '</select>';
			$output .= '<span class="avia_select_fake_val">'.$fake_val.'</span>';
			$output .= '</span></span>';
			
			if(isset($element['hook'])) $output.= '<input type="hidden" name="'.$element['hook'].'" value="'.$element['hook'].'" />';
			
			return $output;
		}
		
		/**
		 * Return an indented dropdown list for hierarchical post types
		 * Takes care of complete output
		 * 
		 * @since 4.2.7
		 * @added_by Günter
		 * @param array $element
		 * @param string $post_type
		 * @return string|false
		 */
		 
		protected function select_hierarchical_post_types( array $element, $post_type = 'page' )
		{
			$defaults = array(
								'id'				=> '',
								'std'				=> 0,
								'label'				=> false,			//	for option group
								'class'				=> '',
								'hierarchical'		=> 'yes',			//	'yes' | 'no'
								'post_status'		=> 'publish',		//	array or separated by comma,
								'option_none_text'	=> '',				//	text to display for "Nothing selected"
								'option_none_value'	=> '',				//	value for 'option_none_text'
								'option_no_change'	=> ''				//	value for 'no change' - set to -1 by WP default
							);

			$element = array_merge( $defaults, $element );
			
			/**
			 * return, if element should not display a hierarchical structure
			 */
			if( 'no' == $element['hierarchical'] )
			{
				return false;
			}
			
			/**
			 * wp_dropdown_pages() does not support multiple selection by default.
			 * Would need to overwrite Walker_PageDropdown to add this feature.
			 * 
			 * Can be done in future if necessary.
			 */
			if( isset( $element['multiple'] ) )
			{
				return false;
			}

			$post_type_object = get_post_type_object( $post_type );

			if ( ! ( $post_type_object instanceof WP_Post_Type && $post_type_object->hierarchical ) )
			{
				return false;
			}
			
			/**
			 * If too many entries limit output and only show non hierarchical
			 * 
			 * @since 4.2.7
			 */
			$limit = apply_filters( 'avf_dropdown_post_number', 4000, $post_type, $element, 'avia_fw_select_hierarchical' );
			$count = wp_count_posts( $post_type );
			if( ! isset( $count->publish ) || ( $count->publish > $limit ) )
			{
				return false;
			}
			
			/**
			 * Make sure we have no spaces
			 */
			$post_status = is_array( $element['post_status'] ) ? $element['post_status'] : explode( ',', (string) $element['post_status'] );
			$element['post_status'] = array_map( function( $value ) { $value = trim($value); return $value;}, $post_status );
			
			/**
			 * check for onchange function
			 */
			$onchange = '';
			if( isset( $element['onchange'] ) ) 
			{
				$onchange = " data-avia-onchange='{$element['onchange']}' ";
				$element['class'] .= ' avia_onchange';
			}
			
			$selected = $element['std'];
			$fake_val = '';
			
			if( isset( $element['no_first'] ) ) 
			{ 
				$element['option_none_text'] = '';
				$element['option_none_value'] = '';
			}
			else 
			{
				$fake_val = $element['option_none_text'];
			}
			
			if( ! empty( $selected ) )
			{
				$post = get_post( $selected );
				if( $post instanceof WP_Post )
				{
					$fake_val = $this->handler_wp_list_pages( avia_wp_get_the_title( $post->ID ), $post );
				}
			}
			
			$multi = $multi_class = '';
			if( isset( $element['multiple'] ) ) 
			{
				$multi_class = " avia_multiple_select";
				$multi = ' multiple="multiple" size="' . $element['multiple'] . '" ';
			}
			
			$dropdown_args = array(
							'post_type'				=> $post_type,
							'exclude_tree'			=> false,
							'selected'				=> $selected,
							'name'					=> $element['id'],
							'id'					=> $element['id'],
							'show_option_none'		=> $element['option_none_text'],
							'option_none_value'		=> $element['option_none_value'],
							'show_option_no_change' => $element['option_no_change'],
							'sort_column'			=> 'post_title',
							'echo'					=> 0,
							'class'					=> $element['class'] . $multi_class,	
							'post_status'			=> $element['post_status'],
					//		'depth'					=> 0, 
					//		'child_of'				=> 0,
					//		'value_field'			=> 'ID',	
							);
			
			/**
			 * Allow to add info for non public post status
			 */
			add_filter( 'list_pages', array( $this, 'handler_wp_list_pages' ), 10, 2 );
			
			$html = wp_dropdown_pages( $dropdown_args );
			
			remove_filter( 'list_pages', array( $this, 'handler_wp_list_pages' ), 10, 2 );

			$html = str_replace( '<select', '<select ' . $multi . $onchange, $html );
			
			return $this->get_hierarchical_select_template( $element, $html, $multi_class, $fake_val );
		}
		
		/**
		 * Add post status in case of non public 
		 * 
		 * @since 4.2.7
		 * @added_by Günter
		 * @param string $title
		 * @param object $page
		 * @return string
		 */
		public function handler_wp_list_pages( $title, $page )
		{
			if( $page instanceof WP_Post || ( isset( $page->ID ) && isset( $page->post_status ) ) )
			{
				if( 'publish' != $page->post_status )
				{
					$title .= ' ( ----> ' . ucfirst( get_post_status( $page->ID ) ) . ' )';
				}
			}
			
			return $title;
		}		
		
		
		/**
		 * Return an indented dropdown list of terms for hierarchical $taxonomy
		 * 
		 * @since 4.2.7
		 * @added_by Günter
		 * @param array $element
		 * @param string $taxonomy
		 * @return string|false
		 */
		protected function select_hierarchical_taxonomy( array $element, $taxonomy = 'category' )
		{
			$defaults = array(
								'id'				=> '',
								'std'				=> array( '', 0 ),
								'label'				=> false,			//	for option group
								'class'				=> '',
								'hierarchical'		=> 'yes',			//	'yes' | 'no'
								'option_none_text'	=> '',				//	text to display for "Nothing selected"
								'option_none_value'	=> '',				//	value for 'option_none_text'
								'option_no_change'	=> ''				//	value for 'no change' - set to -1 by WP default
							);

			$element = array_merge( $defaults, $element );
			
			/**
			 * return, if element should not display a hierarchical structure
			 */
			if( 'no' == $element['hierarchical'] )
			{
				return false;
			}
			
			/**
			 * wp_dropdown_pages() does not support multiple selection by default.
			 * Would need to overwrite Walker_CategoryDropdown to add this feature.
			 * 
			 * Can be done in future if necessary.
			 */
			if( isset( $element['multiple'] ) )
			{
				return false;
			}
			
			$obj_ta = get_taxonomy( $taxonomy );
			
			if ( ! $obj_ta instanceof WP_Taxonomy )
			{
				return false;
			}
			
			/**
			 * check for onchange function
			 */
			$onchange = '';
			if(isset($element['onchange'])) 
			{
				$onchange = " data-avia-onchange='".$element['onchange']."' ";
				$element['class'] .= " avia_onchange";
			}
			
			$selected = $element['std'];
			$fake_val = '';
			
			if( isset( $element['no_first'] ) ) 
			{ 
				$element['option_none_text'] = '';
				$element['option_none_value'] = '';
			}
			else 
			{
				$fake_val = $element['option_none_text'];
			}
			
			if( ! empty( $selected ) )
			{
				$term = get_term_by( 'term_id', $selected, $taxonomy );
				if( $term instanceof WP_Term )
				{
					$fake_val = $term->name;
				}
			}
				
			$multi = $multi_class = '';
			if( isset( $element['multiple'] ) ) 
			{
				$multi_class = " avia_multiple_select";
				$multi = ' multiple="multiple" size="' . $element['multiple'] . '" ';
			}
			
			$args = array(
						'taxonomy'				=> $taxonomy,
						'hierarchical'			=> true,
						'depth'					=> 20,
						'selected'				=> $selected,
						'name'					=> $element['id'],
						'id'					=> $element['id'],
						'show_option_none'		=> $element['option_none_text'],
						'option_none_value'		=> $element['option_none_value'],
						'show_option_no_change' => $element['option_no_change'],
						'orderby'				=> 'name',
						'order'					=> 'ASC',
						'echo'					=> false,
						'class'					=> $element['class'] . $multi_class,
						'hide_empty'			=> false,
						'show_count'			=> true,
						'hide_if_empty'			=> false,
//						'child_of'				=> 0,
//						'exclude'				=> '',
//						'include'				=> '',
//						'tab_index'				=> 0,
//						'value_field'			=> 'term_id',
					);
			
			$html = wp_dropdown_categories( $args );

			$html = str_replace( '<select', '<select ' . $multi . $onchange, $html );
			
			return $this->get_hierarchical_select_template( $element, $html, $multi_class, $fake_val );
		}

		/**
		 * Returns the HTML template for a hierarchical select dropdown
		 * 
		 * @since 4.2.7
		 * @added_by Günter
		 * @param array $element
		 * @param string $html
		 * @param string $multi_class
		 * @param string $fake_val
		 * @return string
		 */
		protected function get_hierarchical_select_template( array $element, $html, $multi_class, $fake_val )
		{
			$output  = '<span class="avia_style_wrap avia_select_style_wrap' . $multi_class . '">';
			$output .=		'<span class="avia_select_unify">';
			$output .=			$html;
			$output .=			'<span class="avia_select_fake_val">' . $fake_val . '</span>';
			$output .=		'</span>';
			$output .=	'</span>';
			
			if( isset( $element['hook'] ) ) 
			{
				$output.= '<input type="hidden" name="' . $element['hook'] . '" value="' . $element['hook'] . '" />';
			}
			
			return $output;
		}
				
		
		function select_sidebar( $element )
		{
			$save_as = array();
			foreach($element['additions'] as $key => $additions)
			{
				if($additions == "%result%") 
				{
					$save_as = $key;
					unset($element['additions'][$key]);
				}
			}
			
			if(empty($save_as))
			{
				$element['subtype'] = av_backend_registered_sidebars($element['additions'] , $element['exclude']);
			}
			else
			{
				$element['subtype'] = $element['additions'];
				$element['subtype'][$save_as] = av_backend_registered_sidebars(array() , $element['exclude']);
			}
			
			return $this->select($element);
		}
		
		/**
		 * Returns a select box of available menus
		 * In case you need extra options in front add them to subtype array. Menus will be appended.
		 * 
		 * @since 4.5
		 * @added_by Günter
		 * @param array $element
		 * @return string
		 */
		public function select_menu( array $element )
		{
			$locations = get_registered_nav_menus();
			$nav_menus = wp_get_nav_menus();
			
			/**
			 * array_flip does not work because plugins like WPML might return '' or null for value which throws a warning
			 * 
			 * $menu_locations = array_flip( get_nav_menu_locations() );
			 */
			$menu_locations = array();
			$temp = get_nav_menu_locations();
			foreach ( $temp as $loc => $term_id ) 
			{
				if( is_numeric( $term_id ) && ( $term_id > 0 ) )
				{
					$menu_locations[ $term_id ] = $loc;
				}
			}
			
			if( ! isset( $element['subtype'] ) || ! is_array( $element['subtype'] ) )
			{
				$element['subtype'] = array();
			}
			
			foreach( $nav_menus as $menu ) 
			{
				$key = wp_html_excerpt( $menu->name, 40, '&hellip;' );
				if( isset( $menu_locations[ $menu->term_id ] ) )
				{
					if( isset( $locations[ $menu_locations[ $menu->term_id ] ] ) )
					{
						$key .= ' (--&gt; ' . wp_html_excerpt( $locations[ $menu_locations[ $menu->term_id ] ], 70, '&hellip;' ) . ')';
					}
				}
				
				$element['subtype'][ $key ] = $menu->term_id;
			}
			
			return $this->select( $element );
		}



		/**
         * 
         * The hidden method renders a div for a visually conected group
         * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
         * @return string $output the string returned contains the html code generated within the method
         */
		function visual_group_start( $element )
		{	
			$required = $extraclass = $data = '';
			
			if( isset( $element['required'] ) && ! empty( $element['required'] ) ) 
			{ 
				$required = '<input type="hidden" value="'.$element['required'][0].'::'.$element['required'][1].'" class="avia_required" />';  
				$extraclass = ' avia_hidden avia_required_container';
			} 
			$data = '';
			if( isset( $element['name'] ) ) 		
			{
				$data .= " data-group-name='".$element['name']."'";
			}
			
			if( isset( $element['global_class'] ) ) 
			{
				$data .= " data-av_set_global_tab_active='".$element['global_class']."'";
			}
			
			if( isset( $element['inactive'] ) ) 
			{ 
				$data .= " data-group-inactive='" . htmlspecialchars( $element['inactive'], ENT_QUOTES ) . "'"; 
				$extraclass .= " inactive_visible";
			}
			
			$output  = '<div class="avia_visual_set avia_' . $element['type'] . $extraclass.' ' . $element['class'] . '" id="' . $element['id'] . '" ' . $data . '>';
			$output .= $required;
				
			return $output;
		}
		
		/**
         * 
         * The hidden method ends the div of the visual group
         * @return string $output the string returned contains the html code generated within the method
         */
		function visual_group_end()
		{			
			$output  = '</div>';
			return $output;
		}
		
		/**
		 * Renders a single div element with class hr
		 * 
		 * @param array $element		holds data like type, value, id, class, description which are necessary to render the whole option-section
		 * @return string				the html code generated within the method
		 */
		function hr( array $element )
		{	
			$class = '';
			if( isset( $element['class'] ) ) 
			{
				$class = $element['class'];	
			}

			$id = isset( $element['id'] ) && ! empty( $element['id'] ) ? 'id="' . esc_attr( $element['id'] ) . '"' : '';

			$output  = '<div ' . $id . ' class="avia_hr ' . $class . '"><div class="avia_inner_hr"></div></div>';
			return $output;
		}
		
		/**
         * The import method adds the option to download demo files and import demo content. 
		 * 
         * @param array $element		holds data like type, value, id, class, description which are necessary to render the whole option-section
         * @return string				the html code generated within the method
         */
  		function import( array $element )
		{	
			global $avia_config;
			
			$demo_full_name = trim( str_replace( 'Import:', '', $element['name'] ) );

			$can_download = true;
			$downloaded = false;
			$extra = '';
			$show_delete = '';
			$data = 'data-demo_full_name="' . esc_attr( $demo_full_name ) . '"';
			$button_class = '';
			$image_url = '';
			
			$click_to_download = __( 'Click to download', 'avia_framework' );
			$click_to_import = __( 'Click to import', 'avia_framework' );
			
			/**
			 * Demo developers can store demo files locally. Other users have a 1 click import:
			 *		- Download demo zip file
			 *		- Import the demo
			 *		- Delete unzipped files
			 */
			if( ! current_theme_supports( 'avia_demo_store_downloaded_files' ) )
			{
				$click_to_download = __( 'Click to download and import', 'avia_framework' );;
				$extra .= ' av-demo-immediate-delete';
				$show_delete .= ' av-demo-immediate-delete';
			}
			
			if( ! empty( $element['demo_name'] ) )
			{
				//	Demo can be downloaded
				$image_url = isset( $element['demo_img'] ) ? $element['demo_img'] : AVIA_IMG_URL . 'misc/placeholder.jpg';
				
				//	e.g. https://kriesi.at/themes/demo-downloads/download/180/
				$data .= ' data-download="' . trailingslashit( $avia_config['demo_import']['download_manager_url'] . $element['download'] ) . '/' . '"';
				
				$import = trailingslashit( $avia_config['demo_import']['upload_folders']['main_dir'] . $element['demo_name'] );
				$data .= ' data-import="' . $import . '"';
				
				$extra .= ' av-import-with-image av-downloadable-zip';
				
				$downloaded = file_exists( $import . $element['demo_name'] . '.xml' );
				$extra .= ( $downloaded ) ? ' av-demo-downloaded' : ' av-demo-must-download';
				
				//	In case a demo is already downloaded we show delete button (fallback situation only in 1 click install) 
				if( $downloaded )
				{
					$extra = str_replace( 'av-demo-immediate-delete', '', $extra );
					$show_delete = str_replace( 'av-demo-immediate-delete', '', $show_delete );
				}
				
				$data .= ' data-demo_name="' . $element['demo_name'] . '"';
			}
			else
			{
				//	Demo is shipped with theme
				$can_download = false;
				$extra .= ' av-demo-downloaded av-demo-shipped';
				
				if( ! empty( $element['files'] ) )
				{
					$data .= " data-files='{$element['files']}'";
				}
				
				if( ! empty( $element['image'] ) ) 
				{
					$extra .= ' av-import-with-image';
					$image_url =  trailingslashit( get_template_directory_uri() ) . $element['image'];
				}
			}
			
			if( isset( $element['exists'] ) && is_array( $element['exists'] ) )
			{
				foreach( $element['exists'] as $class => $message ) 
				{
					if( ! class_exists( $class ) )
					{
						$extra .= ' av-disable-import';
						$click_to_download = $message;
						$click_to_import = $message;
						$button_class .= 'avia_button_inactive';
					}
				}
			}
			
			$msg_data = ' data-confirm_import="' . esc_attr( sprintf( __( 'Importing demo content %s will overwrite your current Theme Option Settings. It is highly recommended to use a clean install to import a demo to avoid conflict with existing content and break import. Proceed anyways?', 'avia_framework' ), $demo_full_name ) ) . '" ';
			$msg_data .= 'data-import_error="' . esc_attr( sprintf( __( 'Import for demo %s didn\'t work! <br/> You might want to try reloading the page and then try again.', 'avia_framework' ), $demo_full_name ) ) . '"';
			$msg_data .= 'data-download_error="' . esc_attr( sprintf( __( 'Download of files for demo %s didn\'t work! <br/> You might want to try reloading the page and then try again.', 'avia_framework' ), $demo_full_name ) ) . '"';
			$msg_data .= 'data-script_error="' . esc_attr( __( '<br/><br/>The script returned the following message: <br/>', 'avia_framework' ) ) . '" ';
					
			$output = '';
			$output .=	"<div class='av-import-wrap {$extra}' {$msg_data}>";
		
			$output .=		'<input type="hidden" name="avia-nonce-import" value="' . wp_create_nonce( 'avia_nonce_import_dummy_data' ) . '" />';
			
			
			if( empty( $image_url ) )
			{
				$output .=	'<span class="avia_style_wrap">';
				$output .=		'<a href="#" class="avia_button avia_import_button" ' . $data . '>' . __( 'Import dummy data', 'avia_framework' ) . '</a>';
				$output .=	'</span>';
			}
			else
			{
				$output .=	'<a href="#" class="avia_import_image avia_import_button ' . $button_class . '" ' . $data . '>';
				$output .=		'<div class="avia_import_overlay avia_download">' . $click_to_download . '</div>';
				$output .=		'<div class="avia_import_overlay avia_import">' . $click_to_import . '</div>';
				$output .=		'<img src="' . $image_url . '" alt="' . sprintf( __( 'Preview image for: %s', 'avia_framework' ), $element['name'] ) . '" title="" />';
				$output .=	'</a>';
			}
			
			$output .=		'<span class="avia_loading avia_import_loading"></span>';
			
			$output .=		'<div class="avia_import_wait import-wait">';
			$output .=			'<strong>' . __( 'Import started.', 'avia_framework' ) . '</strong><br/>';
			$output .=			__( 'Please don\'t reload the page. You will be notified as soon as the import has finished! (Usually within a few minutes) :)', 'avia_framework' );
			$output .=		'</div>';
			
			$output .=		'<div class="avia_import_wait download-wait">';
			$output .=			'<strong>' . __( 'Download started.', 'avia_framework' ) . '</strong><br/>';
			$output .=			__( 'Please don\'t reload the page. You will be notified as soon as the download has finished! (Usually within a few minutes) :)', 'avia_framework' );
			$output .=		'</div>';
			
			$output .=		'<div class="avia_import_wait delete-wait">';
			$output .=			'<strong>' . __( 'Deleting started.', 'avia_framework' ) . '</strong><br/>';
			$output .=			__( 'Please don\'t reload the page. You will be notified as soon as deleting the downloaded file has finished! (Usually within a few minutes) :)', 'avia_framework' );
			$output .=		'</div>';
			
			$output .=		'<div class="avia_import_result"></div>';
			
			$output .=	'</div>';
			
			if( $can_download )
			{
				$show_delete .= ( $downloaded ) ? ' av-demo-downloaded' : ' av-demo-must-download';
				$msg_data = ' data-error_msg="' . esc_attr( sprintf( __( 'Deleting the downloaded demo files %s didn\'t work! <br/> You might want to try reloading the page and then try again', 'avia_framework' ), $demo_full_name ) ) . '"';
				$msg_data .= ' data-script_error_msg="' . esc_attr( sprintf( __( 'Deleting the downloaded demo files %s didn\'t work! <br/> You might want to try reloading the page and then try again.<br/> The script returned the following message: <br/>', 'avia_framework' ), $demo_full_name ) ) . '"';
				
				$output .=	"<div class='av-import-wrap-delete-demo {$show_delete}'>";
				$output .=		'<a href="#" class="avia_button avia_import_delete_demo_button" ' . $data . $msg_data . '>' . __( 'Delete downloaded files', 'avia_framework' ) . '</a>';
				$output .=	'</div>';
			}
			
			return $output;
		}
		
		/**
	     *
	     * The parent_setting_import method adds the option to import settings from your parent theme
	     * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
	     * @return string $output the string returned contains the html code generated within the method
	     */
        function parent_setting_import($element)
        {

            $output = '';
            $nonce	 = 	wp_create_nonce ('avia_nonce_import_parent_settings');
            $output .= '<input type="hidden" name="avia-nonce-import-parent" value="'.$nonce.'" />';
            $output .= '<span class="avia_style_wrap"><a href="#" class="avia_button avia_import_parent_button">Import Parent Theme Settings</a></span>';
            $output .= '<span class="avia_loading avia_import_loading_parent"></span>';
            $output .= '<div class="avia_import_parent_wait"><strong>Import started.</strong><br/>Please wait a few seconds and dont reload the page. You will be notified as soon as the import has finished! :)</div>';
            $output .= '<div class="avia_import_result_parent"></div>';
            return $output;
        }



		/**
		 * The theme_settings_export method adds the option to export the theme settings
		 * 
		 * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
		 * @return string $output the string returned contains the html code generated within the method
		 */
		function theme_settings_export( $element )
		{
			$url = admin_url( 'admin.php?page=avia&avia_export=1&avia_generate_config_file=1' );

			$output = '';
			$output .= '<span class="avia_style_wrap"><a href="' . $url . '" class="avia_button avia_theme_settings_export_button">' . __( 'Export Theme Settings File', 'avia_framework' ) . '</a></span>';
			
			return $output;
		}
		
		/**
		 * The alb_templates_export method adds the option to export saved ALB templates
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return string
		 */
		protected function alb_templates_export( array $element )
		{
			$names = Avia_Builder()->get_AviaSaveBuilderTemplate()->template_names();
			
			$text  = __( 'Export Layout Builder Templates File', 'avia_framework' );
			$text .= '<br />';
			$text .= sprintf( __( '( %d templates found )', 'avia_framework' ), count( $names ) );
			
			$url = admin_url( 'admin.php?page=avia&avia_export=1&avia_generate_alb_templates_file=1' );

			$output = '';
			$output .= '<span class="avia_style_wrap"><a href="' . $url . '" class="avia_button avia_alb_templates_export_button">' . $text . '</a></span>';
			
			return $output;
		}
		
		/**
		 * Adds a button to allow customized reset of theme settings
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return string
		 */
		protected function reset_selected_button( $element ) 
		{
			$data = array();
			
			$filter_keys = array( 'filter_tabs', 'filter_values', 'skip_tabs', 'skip_values' );
			foreach( $filter_keys as $key ) 
			{
				if( ! empty( $element[ $key ] ) )
				{
					$data[ $key ] = $element[ $key ];
				}
			}
			
			$data_string = '';
			
			foreach( $data as $key => $value )
			{
				$data_string .= " data-$key='$value' ";
			}
			
			$id = isset( $element['id'] ) ? ' id="' . $element['id'] . '" ' : '';
			
			$output  = '';
			$output .= '<span class="avia_style_wrap"><a href="#" class="avia_button avia_button_grey avia_reset_selected" ' . $data_string . $id . '>' . __( 'Reset Selected Options', 'avia_framework' ) . '</a></span>';
			$output .= '<span class="avia_loading avia_upload_loading"></span>';
			
			return $output;
		}


		/**
         * 
         * The heading method renders a fullwidth extra description or title
         * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
         * @return string $output the string returned contains the html code generated within the method
         */
		function heading( $element )
		{	
			$extraclass = $required = '';	
			if( isset( $element['required'] ) && ! empty( $element['required'] ) ) 
			{ 
				$required = '<input type="hidden" value="' . $element['required'][0] . '::'.$element['required'][1] . '" class="avia_required" />';  
				$extraclass = ' avia_hidden avia_required_container';
			} 
			
			if( isset( $element['class'] ) ) 
			{
				$extraclass .= ' ' . $element['class'];
			}
		
			$output  = '<div class="avia_section avia_' . $element['type'] . ' ' . $extraclass . '"  id="avia_' . $element['id'] . '">';
			$output .= $required;
			
			if( $element['name'] != '' ) 
			{
				$output .= '<h4>' . $element['name'] . '</h4>';
			}
			
			$output .=		$element['desc'];
			$output .= '</div>';
			
			return $output;
		}
		
		/**
		 * Returns an overview of the responsive images grouped by aspect ratio
		 * 
		 * @since 4.7.5.1
		 * @param array $element
		 * @return string
		 */
		public function responsive_images_overview( array $element ) 
		{
			$extraclass = '';
			$required = '';
			
			if( isset( $element['required'] ) && ! empty( $element['required'] ) ) 
			{ 
				$required = '<input type="hidden" value="' . $element['required'][0] . '::' . $element['required'][1] . '" class="avia_required" />';  
				$extraclass = ' avia_hidden avia_required_container';
			}
			
			if( isset( $element['class'] ) ) 
			{
				$extraclass .= ' ' . $element['class'];
			}
			
			$output  = '<div class="avia_section avia_' . $element['type'] . ' ' . $extraclass . '"  id="avia_' . $element['id'] . '">';
			$output .= $required;
			
			if( $element['name'] != '' ) 
			{
				$output .= '<h4>' . $element['name'] . '</h4>';
			}
			
			$output .=		'<div class="av-plugin-check-wrap">';
			$output .=			Av_Responsive_Images()->options_page_overview();
			$output .=		'</div>';
			$output .= '</div>';
			
			return $output;
		}

		/**
         * 
         * The target method renders a div that is able to hold an image or a background color
         * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
         * @return string $output the string returned contains the html code generated within the method
         */
		function target( $element )
		{	
			$output  = $this->section_start( $element );
			$output .= '	<div><div class="avia_target_inside">';
			$output .= 		$element['std'];
			$output .= '	</div>';
			$output .= $this->section_end( $element );
			return $output;
		}
		
		/**
		 * The plugin_check method checks if a type of plugin is active and displays a suggestion to activate the plugin if not
		 *
         * @since 4.3 - by Kriesi
         * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
         * @return string $output the string returned contains the html code generated within the method
         */
		function plugin_check( $element )
		{	
			//check if a plugin is active
			$active = array();
			$recommend_count = isset($element['recommend_count']) ? $element['recommend_count'] : 2;
			
			//store the plugins in an extra unfilterable array so that no one can mess with our recommendations
			$recommend = $element['plugins'];
			
			//filter the plugins. allows plugin authors to add their plugin to the list of checked plugins
			$element['plugins'] = apply_filters( 'avf_plugin_check_plugins', $element['plugins'] , $element['id'] );

			
			foreach( $element['plugins'] as $name => $plugin )
			{
				if( is_plugin_active( $plugin['file'] ) ) 
				{
		            $active[$name] = $element['plugins'][$name];
		        }
			}
			
			$extraclass = $required = '';	
			if( isset( $element['required'] ) && ! empty( $element['required'] ) ) 
			{ 
				$required = '<input type="hidden" value="'.$element['required'][0].'::'.$element['required'][1].'" class="avia_required" />';  
				$extraclass = ' avia_hidden avia_required_container';
			} 
			
			if(isset($element['class'])) $extraclass .= ' '.$element['class'];
		
			$output  = '<div class="avia_section avia_'.$element['type'].' '.$extraclass.'"  id="avia_'.$element['id'].'">';
			$output .= $required;
			if($element['name'] != '') $output .= '<h4>'.$element['name'].'</h4>';
			$output .= $element['desc'];

			//no plugin active. lets recommend something
			if(empty($active))
			{
				$output .= "<div class='av-plugin-result-title av-text-notice'>";
				$output .=  $element['no_found'];
				$output .= "</div>";
				
				$iteration = 0;
				
				foreach($recommend as $name => $plugin)
				{
					if($recommend_count > $iteration ){
						$iteration++;
						
						$output .= "<div class='av-plugin-check-wrap av-plugin-check-wrap-recommend'>";
						$output .= "<div class='av-plugin-check-wrap-inner'>";
						$output .= "<div class='av-plugin-check-wrap-image'>";
						
						if(!empty($plugin['download'])){
						$output .= "<img src='https://ps.w.org/{$plugin['download']}/assets/icon-128x128.png' />";
						}
						
						$output .= "</div>";
						$output .= "<div class='av-plugin-check-wrap-content'>";
						$output .= '<h3>'.$name.'</h3>';
						$output .= "</div>";
						$output .= "</div>";
						
						if(!empty($plugin['desc'])){
						$output .= "<div class='av-plugin-check-wrap-desc'>";
						$output .= $plugin['desc'];
						$output .= "</div>";
						}
						
						//does not work in thickbox
						//$output .= '<a href="' . esc_url( network_admin_url('plugin-install.php?tab=plugin-information&plugin=' . $plugin['download'] . '&TB_iframe=true&width=600&height=550' ) ) . '" class="thickbox">'.__('Install','avia_framework').'</a>';
						
						if($plugin['download']){
						
						$action = 'install-plugin';
						$url = wp_nonce_url(
						    add_query_arg(
						        array(
						            'action' => $action,
						            'plugin' => $plugin['download'],
						        ), 
						    
						    admin_url( 'update.php' ) ), $action.'_'.$plugin['download']);
						$label = __('Install Plugin','avia_framework');
						
						
						if(isset($plugin['file']) && file_exists(trailingslashit(WP_PLUGIN_DIR) . $plugin['file']))
						{
							$action = 'activate';
							$url = wp_nonce_url(
							    add_query_arg(
							        array(
							            'action' => $action,
							            'plugin' => $plugin['file'],
							        ), 
							    
							    admin_url( 'plugins.php' ) ), 'activate-plugin_'.$plugin['file']);
							$label = __('Activate Plugin','avia_framework');
						}
					
									
						$output .= "<div class='av-plugin-check-wrap-button'>";
						$output .= avia_targeted_link_rel( '<a class="avia_button avia_button_small" target="_blank" href="' . esc_url( $url ) . '">' . $label . '</a>' );
						$output .= '<a class="avia_button avia_button_small" target="_blank" href="https://wordpress.org/plugins/'.$plugin['download'].'/" rel="noopener noreferrer">'.__('Plugin Details', 'avia_framework').'</a>';
						$output .= "</div>";
						}
						
						$output .= "</div>";
					}
				}
				
				
			}
			else
			{
				$output .= "<div class='av-plugin-result-title av-text-notice'>";

				if(count($active) > 1)
				{
					$output .=  $element['too_many'];
				}
				else
				{
					$output .=  $element['found'];
				}
				
				$output .= "</div>";

				foreach($active as $name => $plugin)
				{
					$output .= "<div class='av-plugin-check-wrap'>";
					$output .= "<div class='av-plugin-check-wrap-inner'>";
					$output .= "<div class='av-plugin-check-wrap-image'>";
					
					if($plugin['download']){
					$output .= "<img src='https://ps.w.org/{$plugin['download']}/assets/icon-128x128.png' />";
					}
					
					$output .= "</div>";
					$output .= "<div class='av-plugin-check-wrap-content'>";
					$output .= '<h4>'.__("Currently active:",'avia_framework').'</h4>';
					$output .= $name;
					$output .= "</div>";
					$output .= "</div>";
					$output .= "</div>";
				}
			}
			
			
			$output .= '</div>';
			return $output;
		}
		
		/**
		 * The template_builder_element_loader iterates over all template builder elements that can be disabled and lists them
		 *
		 * @since 4.3 - by Kriesi
		 * @since 4.8.2 - added info about usage - by Guenter
		 * @param array $element		the array holds data like type, value, id, class, description which are necessary to render the whole option-section
		 * @return string $output		contains the html code generated within the method
		 */
		protected function template_builder_element_loader( array $element )
		{
			$extraclass = '';
			$required = '';

			$usage_info = Avia_Builder()->element_manager()->get_usage_info();

			if( isset( $element['required'] ) && ! empty( $element['required'] ) ) 
			{ 
				$required = '<input type="hidden" value="' . $element['required'][0] . '::' . $element['required'][1] . '" class="avia_required" />';  
				$extraclass = ' avia_hidden avia_required_container';
			} 

			$iteration = 1;
			$checkboxes = '';

			foreach( Avia_Builder()->shortcode_class as $shortcode )
			{
				if( ! empty( $shortcode->config['disabling_allowed'] ) )
				{
					if( 'manually' !== $shortcode->config['disabling_allowed'] )
					{
						$desc = __( 'Check to disable', 'avia_framework' );
						
						$sc = $shortcode->config['shortcode'];
						
						$used = ( isset( $usage_info['blog'][ $sc ] ) && true === $usage_info['blog'][ $sc ] );
						if( ! $used )
						{
							if( isset( $usage_info['widgets'][ $sc ] ) && is_array( $usage_info['widgets'][ $sc ] ) )
							{
								$count = 0;
								foreach( $usage_info['widgets'][ $sc ] as $sidebar_count ) 
								{
									$count += $sidebar_count;
								}
								
								$used = $count > 0;
							}
						}
						
						if( $used )
						{
							$desc .= '<span class="avia-sc-element-used">' . __( ' - in use', 'avia_framework' ) . '</span>';
						}
						else
						{
							$desc .= '<span class="avia-sc-element-unused">' . __( ' - unused', 'avia_framework' ) . '</span>';
						}
						
						$checkbox[ $shortcode->config['name'] ] = array(
												'slug'	=> $element['slug'],						/*needs to inherit the original slug*/
												'name'	=> $shortcode->config['name'] . ' ' . __( 'Element', 'avia_framework' ),
												'desc'	=> $desc,
												'id'	=> 'av_alb_disable_' . $shortcode->config['shortcode'],
												'type'	=> 'checkbox',
												'std'	=> ''
											);
					}
				}
			}

			//sort all elements
			ksort( $checkbox );

			foreach( $checkbox as $single_checkbox )
			{
				$single_checkbox['class'] = 'av_3col av_col_' . $iteration;

				//iterate over checkbox by name
				$checkboxes .= $this->render_single_element( $single_checkbox );

				$iteration++;
				if( $iteration > 3 ) 
				{
					$iteration = 1;
				}
			}

			if( isset( $element['class'] ) ) 
			{
				$extraclass .= ' '.$element['class'];
			}

			$output  = '<div class="avia_section avia_' . $element['type'] . ' ' . $extraclass . '"  id="avia_' . $element['id'] . '">';
			$output .=		$required;
			$output .=		'<div class="avia-sc-element-usage_info av-text-notice">';
			$output .=			'<strong>' . __( 'Important: ', 'avia_framework' ) . ' </strong>';
			$output .=			__( 'Shortcodes that are added with filter or used e.g. in plugins are NOT recognised as &quot;in use&quot;.', 'avia_framework' );
			$output .=		'</div>';
			$output .=		$checkboxes;
			$output .= '</div>';

			return $output;
		}
		
		
		/**
		 * Renders the beginning of an option-section wich basically includes some wrapping divs and the elment name
		 * 
		 * @param array $element			holds data like type, value, id, class, description which are necessary to render the whole option-section
		 * @return string $output			contains the html code generated within the method
		 */
		protected function section_start( array $element )
		{
			$required = $extraclass = $target = '';
			
			if( isset( $element['required'] ) && ! empty( $element['required'] ) ) 
			{ 
				$required = '<input type="hidden" value="' . $element['required'][0] . '::' . $element['required'][1] . '" class="avia_required" />';  
				$extraclass = ' avia_hidden avia_required_container';
			} 
			
			if( isset( $element['target'] ) ) 
			{ 
				if( is_array( $element['target'] ) )
				{
					foreach( $element['target'] as $value ) 
					{ 
						$target .= '<input type="hidden" value="' . $value . '" class="avia_target_value" />';
					}
				}
				else
				{
					$target = '<input type="hidden" value="' . $element['target'] . '" class="avia_target_value" />';  
				}
				
			} 
			if( isset( $element['class'] ) ) 
			{
				$extraclass .= ' ' . $element['class'];
			}
			
			/**
			 * @used_by				aviaWPML				10
			 * @since 4.8
			 * @param string $element['name']
			 * @param array $element
			 * @return array
			 */
			$name = apply_filters( 'avf_theme_options_element_name', $element['name'], $element );
			
			$output  = '<div class="avia_section avia_' . $element['type'] . $extraclass . '" id="avia_' . $element['id'] . '">';
			$output .=		$required;
			$output .=		$target;
			
			if( $name != '' )
			{
				$output .= '<h4>' . $name . '</h4>';
			}
			
			$output .=		'<div class="avia_control_container">';

			return $output;
		}
		
		
		/**
         * 
         * The section_end method renders the end of an option-section by closing various divs
         * @return string $output the string returned contains the html code generated within the method
         */
		function section_end()
		{
			$output  = '</div>'; // <!--end avia_control-->
			$output .= '<div class="avia_clear"></div>';
			$output .= '</div>'; //<!--end avia_control_container-->
			$output .= '</div>'; //<!--end avia_section-->
			return $output;
		}
		
		
		/**
		 * Renders the description of the current option-section
		 * 
		 * @param array $element			holds data like type, value, id, class, description which are necessary to render the whole option-section
		 * @return string $output			contains the html code generated within the method
		 */
		protected function description( $element )
		{
			$tags = array( 'div', 'div' );
		
			if( $element['type'] == 'checkbox' )
			{
				$tags = array( 'label for="' . $element['id'] . '"', 'label' );
			}
			
			$desc = apply_filters( 'avf_theme_options_element_desc', $element['desc'], $element );
		
			$output  = "<{$tags[0]} class='avia_description'>";
			$output .=		$desc;
			$output .=	"</{$tags[1]}>"; // <!--end avia_description-->
			$output .=	'<div class="avia_control">';
			
			return $output;		
		}
		
		
		/**
         * 
         * The page_header method renders the beginning of the option page, with header area, logo and various other informations
         * @return string $output the string returned contains the html code generated within the method
         */
		function page_header()
		{
			$the_title = apply_filters( 'avia_filter_backend_page_title', $this->avia_superobject->base_data['Title'] );
			$class = current_theme_supports( 'avia_option_pages_toggles' ) ? 'av-use-toggles' : 'av-use-checkbox';
			
			$output  = '<form id="avia_options_page" action="#" method="post" class="' . $class . '">';
			$output .= '	<div class="avia_options_page_inner avia_sidebar_active">';
			$output .= '	<div class="avia_options_page_sidebar"><div class="avia_header">';
			$output .=      apply_filters('avia_options_page_header', '');
			$output .= '	</div><div class="avia_sidebar_content"></div></div>';
			$output .= '		<div class="avia_options_page_content">';
			$output .= '			<div class="avia_header">';
			$output .= '			<h2 class="avia_logo">'.$the_title.' '.$this->avia_superobject->currentpage.'</h2>';
			$output .= '				<ul class="avia_help_links">';
			$output .= '					<li><a class="thickbox" onclick="return false;" href="http://docs.kriesi.at/'.avia_backend_safe_string($this->avia_superobject->base_data['prefix']).'/changelog/index.php?TB_iframe=1">Changelog</a> |</li>';
			$output .= '					<li><a target="_blank" href="http://docs.kriesi.at/'.avia_backend_safe_string($this->avia_superobject->base_data['prefix']).'/documentation/" rel="noopener noreferrer">Docs</a></li>';
			$output .= '				</ul>';
			$output .= '				<a class="avia_shop_option_link" href="#">Show all Options [+]</a>';
			$output .= '				<span class="avia_loading"></span>';
			$output .= 					$this->save_button();
			$output .= '			</div>';
			$output .= '			<div class="avia_options_container">';
			
			return $output;
		}
		
		/**
		 * The page_footer method renders the end of the option page by closing various divs and appending a save and a reset button
		 * 
		 * @since ????
		 * @since 4.8							added $show_reset_button
		 * @param boolean $show_reset_button
		 * @return string						returnes the html code generated within the method
		 */
		function page_footer( $show_reset_button = true )
		{
			$output  = '			</div>'; // <!-- end .avia_options_container -->
			$output .= '			<div class="avia_footer">';
			$output .=  			$this->hidden_data();
			$output .= '			<span class="avia_loading"></span>';
			$output .= '				<ul class="avia_footer_links">';

			if( $show_reset_button === true )
			{
				$output .= '				<li class="avia_footer_reset">' . $this->reset_button() . '</li>';
			}
			
			$output .= '					<li class="avia_footer_save">' . $this->save_button() . '</li>';
			$output .= '				</ul>';
			$output .= '			</div>';
			$output .= '		</div>'; // <!--end avia_options_page_content-->
			$output .= '		<div class="avia_clear"></div>';
			$output .= '	</div>'; //<!--end avia_options_page_inner-->
			$output .= '</form>'; // <!-- end #avia_options_page -->
			$output .= '<div class="avia_bottom_shadow"></div>';


			return $output;
		}
		

		/**
         * 
         * Creates a button to save the form via ajax
         * @return string $output the string returned contains the html code generated within the method
         */
		function save_button()
		{
			$output = '<span class="avia_style_wrap"><a href="#" class="avia_button avia_button_inactive avia_submit">' . __( 'Save all changes', 'avia_framework' ) . '</a></span>';
			return $output;
		}

		
		
		/**
         * 
         * Creates a button to reset the form
         * @return string $output the string returned contains the html code generated within the method
         */
		function reset_button()
		{
			if( current_theme_supports( 'avia_disable_reset_options' ) ) 
			{
				return '';
			}
		
			$output = '<span class="avia_style_wrap"><a href="#" class="avia_button avia_button_grey avia_reset">' . __( 'Reset all options', 'avia_framework' ) . '</a></span>';
			return $output;
		}
		
		
		/**
         * 
         * A important function that sets various necessary parameters within hidden input elements that the ajax script needs to save the current page
         * @return string $output the string returned contains the html code generated within the method
         */
		function hidden_data()
		{
			$options = get_option($this->avia_superobject->option_prefix);
			
			$output  = '	<div id="avia_hidden_data" class="avia_hidden">';
			
			$nonce_reset = 		wp_create_nonce ('avia_nonce_reset_backend');
			$output .= 			wp_referer_field( false );			
			$output .= '		<input type="hidden" name="avia-nonce-reset" value="'.$nonce_reset.'" />';
			$output .= '		<input type="hidden" name="resetaction" value="avia_ajax_reset_options_page" />';
			$output .= '		<input type="hidden" name="admin_ajax_url" value="'.admin_url("admin-ajax.php").'" />';
			$output .= '		<input type="hidden" name="avia_options_prefix" value="'.$this->avia_superobject->option_prefix.'" />';
			
			//if we are viewing a page and not a meta box
			if($this->context == 'options_page')
			{
				$nonce	= 			wp_create_nonce ('avia_nonce_save_backend');
			    $output .= '		<input type="hidden" name="avia-nonce" value="'.$nonce.'" />';
				$output .= '		<input type="hidden" name="action" value="avia_ajax_save_options_page" />';
				$output .= '		<input type="hidden" name="avia_options_page_slug" value="'.$this->avia_superobject->page_slug.'" />';
				if(empty($options)) $output .= ' <input type="hidden" name="avia_options_first_call" value="true" />';
			}
			//if the code was rendered for a meta box
			if($this->context == 'metabox')
			{
				$nonce	= 			wp_create_nonce ('avia_nonce_save_metabox');
			    $output .= '		<input type="hidden" name="avia-nonce" value="'.$nonce.'" />';
				$output .= '		<input type="hidden" name="meta_active" value="true" />';
			}
			
			
			
			
			$output .= '	</div>';
			
			return $output;
		}
		
		/**
		 * Scans array of attributes and returns a concatenated string. Value of attributes can also be an array.
		 * In this case values joined by ' '.
		 * 
		 * @since 4.7.4.1
		 * @param array $attributes
		 * @param string $prepend
		 * @return string
		 */
		protected function attributes_from_array( array $attributes, $prepend = ' ' ) 
		{
			$attr = '';
			
			if( empty( $attributes ) )
			{
				return $attr;
			}
			
			foreach ( $attributes as $key => $value ) 
			{
				$val = is_array( $value ) ? implode( ' ', $value ) : $value;
				
				$atts[] = esc_attr( $key ) . '="' . esc_attr( $val ) . '"';
			}
			
			return $prepend . implode( ' ', $atts );
		}
		
		/**
		 * Merges $array2 into $array1.
		 * 
		 * array_merge_recursive_distinct does not change the datatypes of the values in the arrays.
		 * Matching keys' values in the second array overwrite those in the first array, as is the
		 * case with array_merge
		 * 
		 * Parameters are passed by reference, though only for performance reasons. They're not
		 * altered by this function.
		 * 
		 * @since 4.7.4.1
		 * @param array $array1
		 * @param array $array2
		 * @return array
		 */
		protected function array_merge_recursive_distinct ( array &$array1, array &$array2 )
		{
			$merged = $array1;

			foreach ( $array2 as $key => $value )
			{
				if ( is_array ( $value ) && isset ( $merged[ $key ] ) && is_array ( $merged[ $key ] ) )
				{
					$merged[ $key ] = self::array_merge_recursive_distinct ( $merged[ $key ], $value );
				}
							//	if null -> leave default
				else if ( isset ( $merged[ $key ] ) && is_array ( $merged[ $key ] ) && is_null( $value ) )
				{
					continue;
				}
				else
				{
					$merged [$key] = $value;
				}
			}

			return $merged;
		}




		######################################################################
		# Dynamic Template Creation:
		######################################################################

		/**
		 * 
		 * The create_options_page method renders an input field and button that lets you create options pages dynamically by entering the new of the new option page
		 * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
		 * @return string $output the string returned contains the html code generated within the method
		 * 
		 * @deprecated 4.8.2
		 */

		function create_options_page($element)
		{
			_deprecated_function( 'avia_htmlhelper::create_options_page', '4.8.2', 'removed - no longer needed' );
			
			
			$output = '';

			$output .= '<div class="avia_create_options_container">';
			$output .= '	<span class="avia_style_wrap avia_create_options_style_wrap">';
			$output .= '	<input type="text" class="avia_create_options_page_new_name avia_dont_activate_save_buttons'.$element['class'].'" value="" name="'.$element['id'].'" id="'.$element['id'].'" />';
			$output .= '	<a href="#" class="avia_button avia_create_options avia_button_inactive" title="'.$element['name'].'" id="avia_'.$element['id'].'">'.$element['label'].'</a>';
			$output .= '	<span class="avia_loading avia_beside_button_loading"></span>';
			$output .= '	</span>';
			$output .= '	<input class="avia_create_options_page_temlate_sortable" type="hidden" value="'.$element['template_sortable'].'" />';
			$output .= '	<input class="avia_create_options_page_temlate_parent" type="hidden" value="'.$element['temlate_parent'].'" />';
			$output .= '	<input class="avia_create_options_page_temlate_icon" type="hidden" value="'.$element['temlate_icon'].'" />';
			$output .= '	<input class="avia_create_options_page_temlate_remove_label" type="hidden" value="'.$element['remove_label'].'" />';
			if(isset($element['temlate_default_elements']))
			{
				$elString = base64_encode(serialize($element['temlate_default_elements']));
				$output .= '	<input class="avia_create_options_page_subelements_of" type="hidden" value="'.$elString.'" />';
			}
			$output .= '</div>';
			return $output;
		}
	
		
		/**
		 * 
		 * The dynamical_add_elements method adds a dropdown list of elements and a submit button that allows you add the selcted element to the dom
		 * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
		 * @return string $output the string returned contains the html code generated within the method
		 * 
		 * @deprecated 4.8.2
		 */
		function dynamical_add_elements($element)
		{
			_deprecated_function( 'avia_htmlhelper::dynamical_add_elements', '4.8.2', 'removed - no longer needed' );
			
			$output = '';

			$output .= '<div class="avia_dynamical_add_elements_container">';
			$output .= '	<span class="avia_style_wrap avia_dynamical_add_elements_style_wrap">';
			$output .= '<span class="avia_select_unify"><select class="'.$element['class'].' avia_dynamical_add_elements_select">';
			$output .= '<option value="">Select Element</option>  ';


			$link = false;
		
			switch($element['options_file'])
			{
				case "dynamic" :
				case AVIA_BASE."includes/admin/register-admin-dynamic-options.php" :
				case "includes/admin/register-admin-dynamic-options.php" : $link = AVIA_BASE."includes/admin/register-admin-dynamic-options.php"; break;
				case "one_page": 
				case "includes/admin/register-admin-dynamic-one-page-portfolio.php": $link = AVIA_BASE."includes/admin/register-admin-dynamic-one-page-portfolio.php"; break;

			}

			if($link) @include($link);


			foreach ($elements as $dynamic_element)
			{
				if(empty($dynamic_element['name'])) $dynamic_element['name'] = $dynamic_element['id'];
				$output .= "<option value='". $dynamic_element['id']."'>". $dynamic_element['name']."</option>";
			}
		
			$output .= '</select>';
			$output .= '<span class="avia_select_fake_val">Select Element</span></span>';
			$output .= '	<a href="#" class="avia_button avia_dynamical_add_elements" title="'.$element['name'].'" id="avia_'.$element['id'].'">Add Element</a>';
			$output .= '	<span class="avia_loading avia_beside_button_loading"></span>';
			$output .= '	</span>';
			$output .= '	<input class="avia_dynamical_add_elements_parent" type="hidden" value="'.$element['slug'].'" />';
			$output .= '	<input class="avia_dynamical_add_elements_config_file" type="hidden" value="'.$element['options_file'].'" />';

			$output .= '</div>';
			return $output;
		}
	
	
	
	
	
		######################################################################
		# STYLING WIZARD + HELPER FUNCTIONS
		######################################################################
		function styling_wizard($element)
		{
			$output 	= '';
			$select  	= "<select class='add_wizard_el'>";
			$fake_val	= __("Select an element to customize",'avia_framework');
			$iteration  = 0;

			$output  = '<span class="avia_style_wrap avia_select_style_wrap"><span class="avia_select_unify">';
			$select .= "<option value=''>{$fake_val}</option>";

			//dropdown menu with elements
			foreach($element['order'] as $order)
			{
				$select .= "<optgroup label='{$order}'>";

				foreach($element['elements'] as $sub)
				{
					if($sub['group'] == $order)
					{
						$select .= "<option value='".$sub['id']."'>".$sub['name']."</option>";
					}
				}

				$select .= "</optgroup>";
			}

			$select .= "</select>";
			$select .= '<span class="avia_select_fake_val">'.$fake_val.'</span>';
			$select .= '</span><a href="#" class="avia_button add_wizard_el_button" >'.__("Edit Element",'avia_framework').'</a></span>';


			$output .= $select;
			$output .= "<div class='av-wizard-element-container'>";
		
		
			//show active items
			if(is_array($element['std']))
			{
				foreach ($element['std'] as $key => $value)
				{
					if(empty($element['elements'][$value['id']])) continue;

					$sub 				= $element['elements'][$value['id']];
					$sub['std_a'] 		= $value;
					$sub['iteration'] 	= $key;
					$sub['master_id'] 	= $element['id'];
					$output 		   .= $this->styling_wizard_el($sub);
					$iteration ++;
				}
			}
		
		
			//generate the templates for new items
			$template = '';
			foreach($element['elements'] as $sub)
			{
				$sub['iteration'] 	= "{counter}";
				$sub['master_id'] 	= $element['id'];

				$template .= "\n<script type='text/html' id='avia-tmpl-wizard-{$sub['id']}'>\n";
				$template .= $this->styling_wizard_el($sub);
				$template .= "\n</script>\n\n";

			}
			$output .= $template;


			$output .= "</div>";

			return $output;
		}


		function styling_wizard_el($element)
		{
			extract($element);

			$extraClass		=  ($sections || $hover) ? "av-wizard-with-extra" : '';
			$name_string  	=  $master_id."-__-".$iteration."-__-";
			$blank_string 	=  $master_id."-__-{counter}-__-";

			$output  = '';
			$output .= "<div class='av-wizard-element  {$extraClass}'>";
			$output .= 		"<strong>{$name}</strong>";

			if($description)
			{
				$output .= 		"<span class='av-wizard-description'> - {$description}</span>";
			}
		
			$output .= 		"<div class='av-wizard-form-elements'>";
			$output .= 		"<input name='".$name_string."id' value='".$element['id']."' type='hidden' data-recalc='".$blank_string."id' />";
		
			foreach($edit as $key => $form)
			{
				$method = "styling_wizard_".$form['type'];
				
				if(method_exists($this, $method))
				{
					$element['html_name']  = $name_string.$key;
					$element['data_tmpl']  = $blank_string.$key;
					$element['sub_values'] = $form;
					$element['std'] = isset($element['std_a'][$key]) ? $element['std_a'][$key] : '';
					
					$output .= "<div class='av-wizard-subcontainer av-wizard-subcontainer-".$element['sub_values']['type']."'>";
					$output .= $this->$method($element);
					$output .= "</div>";
				}
			}
			
			
			if($extraClass) $output .= "<span class='av-wizard-delimiter'></span>";


			if($sections)
			{
				global $avia_config;

				$output .= "<div class='av-wizard-subcontainer av-wizard-subcontainer-checkbox av-wizard-subcontainer-bottom-box'>";
				$output .= "<span class='av-wizard-checkbox-label-section'>".__('Apply to Section: ','avia_framework')."</span>";

				foreach($avia_config['color_sets'] as $key => $name)
				{
					$element['name']  		= $name;
					$element['html_name']  	= $name_string.$key;
					$element['data_tmpl']  	= $blank_string.$key;
					$element['std'] 		= isset($element['std_a'][$key]) ? $element['std_a'][$key] : "true";
					$output .= $this->styling_wizard_checkbox($element);
				}

				$output .= "</div>";
			}

			if($hover)
			{
				$key 					= 'hover_active';
				$element['name']  		= __('Apply only to mouse hover state','avia_framework');
				$element['html_name']  	= $name_string.$key;
				$element['data_tmpl']  	= $blank_string.$key;
				$element['std'] 		= isset($element['std_a'][$key]) ? $element['std_a'][$key] : '';
				$output .= "<div class='av-wizard-subcontainer av-wizard-subcontainer-checkbox av-wizard-subcontainer-bottom-box'>";
				$output .= $this->styling_wizard_checkbox($element);
				$output .= "</div>";
			}

			if(isset($active) && $active)
			{
				$key 					= 'item_active';
				$element['name']  		= __('Apply only to active state','avia_framework');
				$element['html_name']  	= $name_string.$key;
				$element['data_tmpl']  	= $blank_string.$key;
				$element['std'] 		= isset($element['std_a'][$key]) ? $element['std_a'][$key] : '';
				$output .= "<div class='av-wizard-subcontainer av-wizard-subcontainer-checkbox av-wizard-subcontainer-bottom-box'>";
				$output .= $this->styling_wizard_checkbox($element);
				$output .= "</div>";
			}



			$output .= 		'<a class="avia_remove_wizard_set" href="#">×</a>';
			$output .= 		"</div>";
			$output .= "</div>";

			return $output;
		}
	
	
		function styling_wizard_hr($element)
		{
			$output  = '';
			$output .= "<div class='av-wizard-hr'></div>";

			return $output;

		}


		function styling_wizard_checkbox($element)
		{
			extract($element);

			$checked = ($std != '' && $std != 'disabled') ? "checked='checked'" : '';

			$output  = '';
			$output .= "<label><input type='checkbox' class='avia_color_picker' value='true' {$checked} name='{$html_name}' data-recalc='{$data_tmpl}' /> <span class='av-wizard-checkbox-label'>{$name}</span></label>";

			return $output;
		}




		function styling_wizard_colorpicker($element)
		{
			extract($element);

			$output  = '';
			$output .= "<span class='avia_style_wrap avia_colorpicker avia_colorpicker_style_wrap'>";
			$output .= "<input type='text' class='avia_color_picker' value='{$std}' name='{$html_name}' data-recalc='{$data_tmpl}' />";
			$output .= "<span class='avia_color_picker_div'></span></span>";
			$output .= "<div class='subname'>".$sub_values['name']."</div>";


			return $output;
		}



		function styling_wizard_size($element)
		{
			$range 		= is_array($element['sub_values']['range']) ? $element['sub_values']['range'] : explode("-", $element['sub_values']['range']);
			$unit 		= !isset($element['sub_values']['unit']) ? "px" : $element['sub_values']['unit'];
			$increment 	= !isset($element['sub_values']['increment']) ? 1 : $element['sub_values']['increment'];

			$element['sub_values']['options'] = array();
			$element['sub_values']['options']['Default'] = '';

			for ($i = $range[0]; $i <= $range[1]; $i += $increment)
			{
				$element['sub_values']['options'][$i . $unit] = $i . $unit;
			}

			return $this->styling_wizard_select($element);
		}



		function styling_wizard_font($element)
		{
			return $this->styling_wizard_select($element);
		}
	
	
		/**
		 * Displays a select box and supports optgroup tag
		 * 
		 * @param array $element
		 * @return string
		 */
		function styling_wizard_select($element)
		{
			extract($element);
			$output  = '';
			$output .= "<span class='avia_style_wrap avia_select_style_wrap'><span class='avia_select_unify'><select name='{$html_name}' data-recalc='{$data_tmpl}'>";

			foreach( $sub_values['options'] as $key => $option )
			{
				$is_optgroup = is_array( $option );
				$optgroup_sub = $is_optgroup ? $option : array( $key => $option );

				if( $is_optgroup )
				{
					$output .= '<optgroup label="' . $key . '">';
				}

				foreach( $optgroup_sub as $show => $value ) 
				{
					$selected = $value == $std ? "selected='selected'" : '';
					$output .= "<option {$selected} value='{$value}'>{$show}</option>";
				}

				if( $is_optgroup )
				{
					$output .= '</optgroup>';
				}

			}

			$output .= "</select><span class='avia_select_fake_val'>Default</span></span></span>";
			$output .= "<div class='subname'>".$sub_values['name']."</div>";
			return $output;
		}
		
	}
}

