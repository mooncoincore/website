<?php
/**
 * Table
 *
 * Creates a data or pricing table
 */


if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if ( ! class_exists( 'avia_sc_table' ) )
{
	class avia_sc_table extends aviaShortcodeTemplate
	{
		/**
		 *
		 * @var int
		 */
		static $table_count = 0;

		/**
		 *
		 * @since 4.5.6
		 * @var array
		 */
		protected $screen_options;

		/**
		 *
		 * @since 4.5.6
		 * @param AviaBuilder $builder
		 */
		public function __construct( $builder )
		{
			$this->screen_options = array();

			parent::__construct( $builder );
		}


		/**
		 * @since 4.5.6
		 */
		public function __destruct()
		{
			parent::__destruct();

			unset( $this->screen_options );
		}


		/**
		 * Create the config array for the shortcode button
		 */
		function shortcode_insert_button()
		{
			$this->config['version']		= '1.0';
			$this->config['self_closing']	= 'no';
			$this->config['auto_repair']	= 'no';

			$this->config['name']		= __( 'Table', 'avia_framework' );
			$this->config['tab']		= __( 'Content Elements', 'avia_framework' );
			$this->config['icon']		= AviaBuilder::$path['imagesURL'] . 'sc-table.png';
			$this->config['order']		= 35;
			$this->config['target']		= 'avia-target-insert';
			$this->config['shortcode'] 	= 'av_table';
			$this->config['modal_data'] = array( 'modal_class' => 'bigscreen', 'before_save' => 'before_table_save' );
			$this->config['shortcode_nested'] = array( 'av_row', 'av_cell', 'av_button' );
			$this->config['tooltip'] 	= __( 'Creates a data or pricing table', 'avia_framework' );
			$this->config['preview'] 	= false;
			$this->config['disabling_allowed'] = true;

			$this->config['id_name']	= 'id';
			$this->config['id_show']	= 'yes';
			$this->config['alb_desc_id']	= 'alb_description';
		}


		function admin_assets()
		{
			$ver = AviaBuilder::VERSION;

			wp_register_script('avia_table_js', AviaBuilder::$path['assetsURL'] . 'js/avia-table.js', array( 'avia_modal_js' ), $ver, true );
			Avia_Builder()->add_registered_admin_script( 'avia_table_js' );
		}


		function extra_assets()
		{
			//load css
			wp_enqueue_style( 'avia-module-table', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/table/table.css', array( 'avia-layout' ), false );
		}



		/**
		 * Popup Elements
		 *
		 * If this function is defined in a child class the element automatically gets an edit button, that, when pressed
		 * opens a modal window that allows to edit the element properties
		 *
		 * @return void
		 */
		function popup_elements()
		{

			$this->elements = array(

				array(
						'type' 	=> 'tab_container',
						'nodescription' => true
					),

				array(
						'type' 	=> 'tab',
						'name'  => __( 'Content', 'avia_framework' ),
						'nodescription' => true
					),

					array(
							'type'			=> 'template',
							'template_id'	=> $this->popup_key( 'content_table' )
						),

				array(
						'type' 	=> 'tab_close',
						'nodescription' => true
					),

				array(
						'type' 	=> 'tab',
						'name'  => __( 'Styling', 'avia_framework' ),
						'nodescription' => true
					),

					array(
							'type'			=> 'template',
							'template_id'	=> 'toggle_container',
							'templates_include'	=> array(
													$this->popup_key( 'styling_table' ),
													$this->popup_key( 'styling_caption' )
												),
							'nodescription' => true
						),

				array(
						'type' 	=> 'tab_close',
						'nodescription' => true
					),

				array(
						'type' 	=> 'tab',
						'name'  => __( 'Advanced', 'avia_framework' ),
						'nodescription' => true
					),

					array(
							'type' 	=> 'toggle_container',
							'nodescription' => true
						),

						array(
								'type'				=> 'template',
								'template_id'		=> 'screen_options_toggle',
								'templates_include'	=> array(
															$this->popup_key( 'advanced_table_responsive' ),
															'screen_options_visibility'
														)
							),

						array(
								'type'			=> 'template',
								'template_id'	=> 'developer_options_toggle',
								'args'			=> array( 'sc' => $this )
							),

					array(
							'type' 	=> 'toggle_container_close',
							'nodescription' => true
						),

				array(
						'type' 	=> 'tab_close',
						'nodescription' => true
					),

				array(
						'type' 	=> 'tab_container_close',
						'nodescription' => true
					)

				);
		}

		/**
		 * Create and register templates for easier maintainance
		 *
		 * @since 4.6.4
		 */
		protected function register_dynamic_templates()
		{

			/**
			 * Content Tab
			 * ===========
			 */

			$c = array(
						array(
							'name' 	=> __( 'Table Builder', 'avia_framework' ),
							'desc' 	=> __( 'Start by adding columns and rows, then add content and styling to each.', 'avia_framework' ),
							'id' 	=> 'table',
							'container_class' => 'avia-element-fullwidth',
							'type' 	=> 'table',
							'row_style'		=> array(
													__( 'Default Row', 'avia_framework' )	=> '',
													__( 'Heading Row', 'avia_framework' )	=> 'avia-heading-row',
													__( 'Pricing Row', 'avia_framework' )	=> 'avia-pricing-row',
													__( 'Button Row', 'avia_framework' )	=> 'avia-button-row'
												),
							'column_style'	=> array(
													__('Default Column', 'avia_framework' )		=> '',
													__('Highlight Column', 'avia_framework' )	=> 'avia-highlight-col',
													__('Description Column', 'avia_framework' )	=> 'avia-desc-col',
													__('Center Text Column', 'avia_framework' )	=> 'avia-center-col'
												)
						),

				);

			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_table' ), $c );

			/**
			 * Styling Tab
			 * ===========
			 */

			$c = array(
						array(
							'name' 	=> __( 'Table Purpose', 'avia_framework' ),
							'desc' 	=> __( 'Choose if the table should be used to display tabular data or to display pricing options. (Difference: Pricing tables are flashier and try to stand out)', 'avia_framework' ),
							'id' 	=> 'purpose',
							'type' 	=> 'select',
							'std' 	=> 'pricing',
							'subtype'	=> array(
												__( 'Use the table as a Pricing Table', 'avia_framework' )		=> 'pricing',
												__( 'Use the table to display tabular data', 'avia_framework' )	=> 'tabular'
											)
							),

						array(
						'name' 	=> __( 'Table Design', 'avia_framework' ),
						'desc' 	=> __( 'Use either the default or minimal design', 'avia_framework' ),
						'id' 	=> 'pricing_table_design',
						'type' 	=> 'select',
						'std' 	=> 'avia_pricing_default',
						'subtype'	=> array(
											__( 'Default', 'avia_framework')	=> 'avia_pricing_default',
											__( 'Minimal', 'avia_framework')	=> 'avia_pricing_minimal'
										)
						),

						array(
							'name' 	=> __( 'Empty Cells', 'avia_framework' ),
							'desc' 	=> __( 'Empty Cells are by default hidden. If you want to force equal height across all columns set them to display', 'avia_framework' ),
							'id' 	=> 'pricing_hidden_cells',
							'type' 	=> 'select',
							'std' 	=> '',
							'required' => array( 'purpose', 'equals', 'pricing' ),
							'subtype' => array(
											__( 'Hide empty Cells', 'avia_framework' )	=> '',
											__( 'Show empty Cells', 'avia_framework' )	=> 'avia_show_empty_cells'
										)
						)

				);

			$template = array(
							array(
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Table Styling', 'avia_framework' ),
								'content'		=> $c
							),
					);

			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_table' ), $template );

			$c = array(
						array(
							'name' 	=> __( 'Table Caption', 'avia_framework' ),
							'desc' 	=> __( 'Add a short caption to the table so visitors know what the data is about', 'avia_framework' ),
							'id' 	=> 'caption',
							'type' 	=> 'input',
							'std' 	=> '',
							'required' => array( 'purpose', 'equals', 'tabular' )
						),

				);

			$template = array(
							array(
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Caption', 'avia_framework' ),
								'content'		=> $c
							),
					);

			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_caption' ), $template );

			/**
			 * Advanced Tab
			 * ===========
			 */

			$c = array(
						array(
							'name' 	=> __( 'Responsive Styling', 'avia_framework' ),
							'desc' 	=> __( 'Select which table styling should be used if the screen is too small for the table.', 'avia_framework' ),
							'id' 	=> 'responsive_styling',
							'type' 	=> 'select',
							'std' 	=> 'avia_responsive_table',
							'required'	=> array( 'purpose', 'equals', 'tabular' ),
							'subtype'	=> array(
												__( 'Adjust table to screen size', 'avia_framework' )	=> 'avia_responsive_table',
												__( 'Make entire table scrollable', 'avia_framework' )	=> 'avia_scrollable_table'
											)
						),

				);

			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_table_responsive' ), $c );

		}

		/**
		 * Editor Element - this function defines the visual appearance of an element on the AviaBuilder Canvas
		 * Most common usage is to define some markup in the $params['innerHtml'] which is then inserted into the drag and drop container
		 * Less often used: $params['data'] to add data attributes, $params['class'] to modify the className
		 *
		 *
		 * @param array $params this array holds the default values for $content and $args.
		 * @return $params the return array usually holds an innerHtml key that holds item specific markup.
		 */
		function editor_element( $params )
		{
			$params = parent::editor_element( $params );
			return $params;
		}

		/**
		 * Editor Sub Element - this function defines the visual appearance of an element that is displayed within a modal window and on click opens its own modal window
		 * Works in the same way as Editor Element
		 * @param array $params this array holds the default values for $content and $args.
		 * @return $params the return array usually holds an innerHtml key that holds item specific markup.
		 */
		function editor_sub_element( $params )
		{
			$template = $this->update_template( 'label', __( 'Element', 'avia_framework' ) . ': {{label}}' );

			$params['content'] = null;
			$params['innerHtml']  = '';
			$params['innerHtml'] .= "<div class='avia_image_container' {$template}>" . __( 'Element', 'avia_framework' ) . ": {$params['args']['label']}</div>";
			$params['data'] = array( 'modal_class' => 'mediumscreen' );

			return $params;
		}

		/**
		 * Create custom stylings
		 *
		 * @since 4.8.4
		 * @param array $args
		 * @return array
		 */
		protected function get_element_styles( array $args )
		{
			$result = parent::get_element_styles( $args );

			extract( $result );

			$default = array(
							'purpose'				=> 'pricing',
							'caption'				=> '',
							'responsive_styling'	=> 'avia_responsive_table',
							'pricing_hidden_cells'	=> '',
							'pricing_table_design'	=> 'avia_pricing_default'
						);

			$default = $this->sync_sc_defaults_array( $default, 'no_modal_item', 'no_content' );

			$atts = shortcode_atts( $default, $atts, $this->config['shortcode'] );

			$this->screen_options = AviaHelper::av_mobile_sizes( $atts );

			$depth = 2;
			$atts['table_rows'] = ShortcodeHelper::shortcode2array( $content, $depth );

			$result['default'] = $default;
			$result['atts'] = $atts;
			$result['content'] = $content;
			$result['element_styling'] = $element_styling;

			switch( $atts['purpose'] )
			{
				case 'pricing':
					$this->get_element_styles_pricing( $result );
					break;
				default:
					$this->get_element_styles_data_table( $result );
					break;
			}


			$this->el_styling = $result['element_styling'];

			return $result;
		}

		/**
		 * Create custom stylings for items
		 *
		 * @since 4.8.4
		 * @param array $args
		 * @return array
		 */
		protected function get_element_styles_item( array $args )
		{
			$result = parent::get_element_styles_item( $args );

			/**
			 * Fixes a problem when 3-rd party plugins call nested shortcodes without executing main shortcode  (like YOAST in wpseo-filter-shortcodes)
			 */
			if( empty( $this->screen_options ) )
			{
				return $result;
			}

			/**
			 * !!!!!!!!!!!!!!!!!
			 *
			 * Currently not used as we have no styling for row elements at the moment.
			 * Ýou have to seperate av_av_row and av_cell shortcodes here as both are called when creating the header.
			 *
			 * This also needs a refactor of get_element_styles() and the main shortcode handler.
			 *
			 * @since 4.8.4
			 */

			return $result;
		}

		/**
		 * Create stylings for "Pricing Table"
		 *
		 * @since 4.8.4
		 * @param array $result
		 */
		protected function get_element_styles_pricing( array &$result )
		{
			extract( $result );

			$classes = array(
						'avia-table',
						$element_id,
						'main_color',
						'avia-pricing-table-container',
						$atts['pricing_hidden_cells'],
						$atts['pricing_table_design']
					);

			$element_styling->add_classes( 'container', $classes );

			$element_styling->add_classes_from_array( 'container', $meta, 'el_class' );



			$selectors = array(
						'container'				=> ".avia-table.{$element_id}"
					);

			$element_styling->add_selectors( $selectors );

			$result['element_styling'] = $element_styling;

			$this->el_styling = $element_styling;
		}

		/**
		 * Create stylings for "Tabular Data Table"
		 *
		 * @since 4.8.4
		 * @param array $result
		 */
		protected function get_element_styles_data_table( array &$result )
		{
			extract( $result );

			$classes = array(
						'avia-data-table-wrap',
						$element_id,
						$atts['responsive_styling'],
					);

			$element_styling->add_classes( 'container', $classes );


			$classes = array(
						'avia-table',
						'avia-data-table',
						$atts['pricing_table_design'],
					);

			$element_styling->add_classes( 'table', $classes );

			$element_styling->add_classes_from_array( 'table', $meta, 'el_class' );

			$selectors = array(
						'container'				=> ".avia-data-table-wrap.{$element_id}"
					);

			$element_styling->add_selectors( $selectors );

			$result['element_styling'] = $element_styling;

			$this->el_styling = $element_styling;
		}

		/**
		 * Frontend Shortcode Handler
		 *
		 * @param array $atts array of attributes
		 * @param string $content text within enclosing form of shortcode element
		 * @param string $shortcodename the shortcode found, when == callback name
		 * @return string $output returns the modified html string
		 */
		function shortcode_handler( $atts, $content = '', $shortcodename = '', $meta = '' )
		{
			$result = $this->get_element_styles( compact( array( 'atts', 'content', 'shortcodename', 'meta' ) ) );

			extract( $result );

			if( empty( $atts['table_rows'] ) )
			{
				return '';
			}


			avia_sc_table::$table_count ++;

			switch( $atts['purpose'] )
			{
				case 'pricing':
					$output = $this->pricing_table( $atts, $meta, $element_id );
					break;
				default:
					$output = $this->data_table( $atts, $meta, $element_id );
					break;
			}

			return $output;
		}


		/**
		 * resort the array so that its easier to do a liststlye output when using pricing tables
		 *
		 * @param array $table_rows
		 * @return array
		 */
		protected function list_sort_array( array &$table_rows )
		{
			$new = array();

			foreach( $table_rows as $rk => $row )
			{
				foreach( $row['content'] as $ck => $cell )
				{
					$new[$ck]['ul_style'] 	= $cell['attr']['col_style'];
					$new[$ck]['attr'][] 	= $row['attr'];
					$new[$ck]['content'][] 	= $cell;
				}
			}

			return $new;
		}

		/**
		 * Pricing table uses unordered lists to display the table structure
		 *
		 * @param array $atts
		 * @param array $meta
		 * @param string $element_id
		 * @return string
		 */
		protected function pricing_table( array &$atts, array $meta, $element_id )
		{
			$table_rows = $atts['table_rows'];

			extract( $this->screen_options );

			$sorted_rows = $this->list_sort_array( $table_rows );

			$markup = avia_markup_helper( array( 'context' => 'table', 'echo' => false, 'custom_markup' => $meta['custom_markup'] ) );

			$this->el_styling->add_classes( 'container', 'avia-table-' . avia_sc_table::$table_count );

			$style_tag = $this->el_styling->get_style_tag( $element_id );
			$container_class = $this->el_styling->get_class_string( 'container' );

			$output  = '';
			$output .= $style_tag;
			$output .= "<div {$meta['custom_el_id']} class='{$container_class} {$av_display_classes}' {$markup}>";

			$fallback_values = array();
			$empty_cells = false;

			foreach( $sorted_rows as $ul_k => $ul )
			{
				$output .= '<div class="pricing-table-wrap">';
				$output .=		"<ul class='pricing-table {$ul['ul_style']}'>";

				foreach( $ul['content'] as $key => $li )
				{
					$content = trim( do_shortcode( $li['content'] ) );

					if( empty( $content ) && $content !== '0' )
					{
						$ul['attr'][ $key ]['row_style'] .= ' empty-table-cell';
						$content = "{{content-{$key}}}";
						$empty_cells = true;
					}
					else if( empty( $fallback_values[ $key ] ) )
					{
						$fallback_values[ $key ] = $content;
					}

					if( strpos( $ul['attr'][ $key ]['row_style'], 'avia-pricing-row' ) !== false )
					{
						$content = preg_replace( '!(\$|€|¥|£|¢|¤|%|‰|&cent;|&curren;|&pound;|&yen;|&euro;)!' , '<span class="currency-symbol">$1</span>', $content );
					}


					$output .= "<li class='{$ul['attr'][$key]['row_style']}'>";
					$output .= $key == 0 ? "<div class='first-table-item'>{$content}</div>" : $content;
					$output .= $key == 0 ? "<span class='pricing-extra'></span>" :'';

					$output .= '</li>';
				}

				$output .=		'</ul>';
				$output .= '</div>';
			}

			if( $empty_cells )
			{
				foreach( $fallback_values as $key => $value )
				{
					$output = str_replace( "{{content-{$key}}}", "<span class='fallback-table-val'>{$value}</span>", $output );
				}
			}

			$output .= '</div>';
			return $output;
		}

		/**
		 * Data table uses the real table html tag to display its structure
		 *
		 * @param array $atts
		 * @param array $meta
		 * @param string $element_id
		 * @return string
		*/
		protected function data_table( array &$atts, array $meta, $element_id )
		{
			$table_rows = $atts['table_rows'];

			extract( $this->screen_options );

			$responsive_style = '';

			$markup = avia_markup_helper( array( 'context' => 'table', 'echo' => false, 'custom_markup' => $meta['custom_markup'] ) );

			$this->el_styling->add_classes( 'container', 'avia-table-' . avia_sc_table::$table_count );

			$style_tag = $this->el_styling->get_style_tag( $element_id );
			$container_class = $this->el_styling->get_class_string( 'container' );
			$table_class = $this->el_styling->get_class_string( 'table' );

			$output  = '';
			$output .= $style_tag;
			$output .= "<div class='{$container_class} {$av_display_classes}'>";
			$output .=		"<table {$meta['custom_el_id']} class='{$table_class}' {$markup}>";
			$output .=			$atts['caption'] ? "<caption>{$atts['caption']}</caption>" : '';
			$output .=			'<tbody>';

			$counter = 0;

			foreach( $table_rows as $rk => $row )
			{
				$responsive_style_nth_modifier = 1;

				if( empty( $row['attr'] ) )
				{
					$row['attr'] = array();
				}

				$row_attributes = array_merge( array( 'row_style' => '' ), $row['attr'] );

				$output .= "<tr class='{$row_attributes['row_style']}'>";

				foreach( $row['content'] as $key => $cell )
				{
					if( empty( $cell['attr'] ) )
					{
						$cell['attr'] = array();
					}

					$cell_attributes = array_merge( array( 'col_style' => '' ), $cell['attr'] );

					$tag = $row_attributes['row_style'] == 'avia-heading-row' ? 'th' : 'td';
					$tag = $cell_attributes['col_style'] == 'avia-desc-col' ? 'th' : $tag;

					if( $row_attributes['row_style'] == 'avia-heading-row' && $cell_attributes['col_style'] == 'avia-desc-col' )
					{
						//fixes issues like
						//https://kriesi.at/support/topic/display-of-a-table-displays-wron-headlines-on-mobile/.

						$responsive_style_nth_modifier = 0;
					}

					if( $rk == 0 && $tag == 'th' )
					{
						$responsive_style .= ".avia-table-" . self::$table_count . " td:nth-of-type(" . ( $counter + $responsive_style_nth_modifier ) . "):before { content: '" . strip_tags( html_entity_decode( $row['content'][ $counter ]['content'] ) ) . "'; } ";
						$counter ++;
					}

					$output .= "<{$tag} class='{$cell_attributes['col_style']}'>";
					$output .=		do_shortcode( $cell['content'] );
					$output .= "</{$tag}>";
				}
				$output .= '</tr>';
			}

			$output .=			'</tbody>';
			$output .=		'</table>';
			$output .= '</div>';

			if( $atts['responsive_styling'] == 'avia_responsive_table' && ! empty( $responsive_style ) )
			{
				$output .= "<style type='text/css'>{$responsive_style}</style>";
			}

			return $output;
		}

	}
}
