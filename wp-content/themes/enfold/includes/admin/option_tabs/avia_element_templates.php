<?php
/**
 * Custom Elements Tab
 * ===================
 *
 * @since 4.8
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

global $avia_config, $avia_pages, $avia_elements;


/**
 * Initially we limit the most settings to default. Might change in future versions.
 *
 * @since 4.8
 */
$advanced_class = current_theme_supports( 'show_advanced_custom_element_options' ) ? '' : 'avia-forced-hide-option';


$desc  = __( 'Create your custom ALB elements from existing ALB elements with fixed styling and content. You can use these custom elements as a base for your elements when creating your pages.', 'avia_framework' ) . ' ';
$desc .= __( 'Changes to these custom element templates will be used in the elements based on these templates. Please check our <a href="https://kriesi.at/documentation/enfold/custom-element-templates/" target="_blank" rel="noopener noreferrer">documentation</a> for more details.', 'avia_framework' );
$desc .= '<br /><br /><strong class="av-text-notice">';
$desc .=	__( 'Attention when using caching plugins: Whenever you make changes to a custom element template please clear your server cache to show the changes.', 'avia_framework' );
$desc .= '</strong>';

/**
 * @used_by				avia_WPML					10
 * @since 4.8
 * @param string $desc
 * @param string $context
 * @return string
 */
$desc = apply_filters( 'avf_theme_options_heading_desc', $desc, 'alb_element_templates_header' );

$avia_elements[] = array(
			'slug'		=> 'avia_element_templates',
			'name'		=> __( 'Custom Elements (Custom Element Templates - CET)', 'avia_framework' ),
			'desc'		=> $desc,
			'id'		=> 'alb_element_templates_header',
			'type'		=> 'heading',
			'nodescription' => true
		);

$avia_elements[] = array(
			'slug'		=> 'avia_element_templates',
			'name'		=> __( 'Custom Elements Management', 'avia_framework' ),
			'desc'		=> __( 'Activate and select who is allowed to create and edit custom elements. See documentation if you need a more specific handling.', 'avia_framework' ),
			'id'		=> 'alb_element_templates',
			'type'		=> 'select',
			'std'		=> '',
			'no_first'	=> true,
			'global'	=> true,
			'subtype'	=> array(
								__( 'Disabled', 'avia_framework' )				=> '',
								__( 'Editors and Admins', 'avia_framework' )	=> 'all',
								__( 'Admins only', 'avia_framework' )			=> 'admins_only'
							)
		);

$avia_elements[] = array(
			'slug'		=> 'avia_element_templates',
			'id'		=> 'alb_element_templates_management_start',
			'type'		=> 'visual_group_start',
			'required'	=> array( 'alb_element_templates', '{contains_array}all;admins_only' ),
			'nodescription' => true
		);

if( current_theme_supports( 'show_advanced_custom_element_options' ) )
{
	$subtype = array(
					__( 'Hide locked options for all users', 'avia_framework' )		=> '',
					__( 'Hide locked options for non admins', 'avia_framework' )	=> 'hide_non_admin',
					__( 'Show locked options to all users', 'avia_framework' )		=> 'show_all',
				);
}
else
{
	$subtype = array(
					__( 'Hide locked options', 'avia_framework' )	=> '',
					__( 'Show locked options', 'avia_framework' )	=> 'show_all',
				);
}

$avia_elements[] = array(
			'slug'		=> 'avia_element_templates',
			'name'		=> __( 'Custom Elements Locked Options', 'avia_framework' ),
			'desc'		=> __( 'To reduce size of modal popup window locked options and their values are hidden when using element templates. Select if you want to add a checkbox to the customize tab to show the locked values when editing an element.', 'avia_framework' ),
			'id'		=> 'alb_locked_modal_options',
			'type'		=> 'select',
			'std'		=> '',
			'no_first'	=> true,
			'global'	=> true,
			'subtype'	=> $subtype
		);

$avia_elements[] = array(
			'slug'		=> 'avia_element_templates',
			'name'		=> __( 'Checkbox To Show Locked Options', 'avia_framework' ),
			'desc'		=> __( 'Select if you want to show the locked options by default when opening the modal popup to edit an element.', 'avia_framework' ),
			'id'		=> 'alb_show_locked_modal_options',
			'type'		=> 'select',
			'std'		=> '',
			'no_first'	=> true,
			'global'	=> true,
			'class'		=> $advanced_class,
			'required'	=> array( 'alb_locked_modal_options', '{contains_array}hide_non_admin;show_all' ),
			'subtype'	=> array(
								__( 'Unchecked for all users', 'avia_framework' )		=> '',
								__( 'Unchecked for non admins only', 'avia_framework' )	=> 'hide_non_admin',
								__( 'Checked for all users', 'avia_framework' )			=> 'show_all',
							)
		);

$avia_elements[] =	array(
			'slug'		=> 'avia_element_templates',
			'name'		=> __( 'Show advanced options', 'avia_framework' ),
			'desc'		=> __( 'Contains options to extend the functionality - recommended for advanced users.', 'avia_framework' ),
			'id'		=> 'custom_el_advanced_options',
			'type'		=> 'checkbox',
			'std'		=> false,
			'global'	=> true,
			'class'		=> $advanced_class,
			'required'	=> array( 'alb_element_templates', '{contains_array}all;admins_only' )
		);

$avia_elements[] = array(
			'slug'		=> 'avia_element_templates',
			'id'		=> 'alb_element_templates_advanced_start',
			'type'		=> 'visual_group_start',
			'required'	=> array( 'custom_el_advanced_options', 'custom_el_advanced_options' ),
			'nodescription' => true
		);

$avia_elements[] = array(
			'slug'		=> 'avia_element_templates',
			'name'		=> __( 'Custom Element Shortcode Buttons', 'avia_framework' ),
			'desc'		=> __( 'Select if you want to show all your custom element shortcode buttons in a single tab or grouped like the ALB shortcode buttons, if more than one tab contains elements.', 'avia_framework' ),
			'id'		=> 'custom_el_shortcode_buttons',
			'type'		=> 'select',
			'std'		=> '',
			'no_first'	=> true,
			'global'	=> true,
			'class'		=> $advanced_class,
			'subtype'	=> array(
								__( 'All buttons in a single tab', 'avia_framework' )	=> '',
								__( 'Group buttons similar to ALB', 'avia_framework' )	=> 'group',
							)
		);

$avia_elements[] = array(
			'slug'		=> 'avia_element_templates',
			'name'		=> __( 'Hierarchical Custom Elements', 'avia_framework' ),
			'desc'		=> __( 'This allows you to use your custom elements as a base for another custom element.', 'avia_framework' ),
			'id'		=> 'custom_el_hierarchical_templates',
			'type'		=> 'select',
			'std'		=> '',
			'no_first'	=> true,
			'global'	=> true,
			'class'		=> $advanced_class,
			'subtype'	=> array(
								__( 'Do not allow hierarchical custom elements', 'avia_framework' )		=> '',
								__( 'Allow use of hierarchical custom elements', 'avia_framework' )		=> 'hierarchical'
							)
		);

$avia_elements[] = array(
			'slug'		=> 'avia_element_templates',
			'name'		=> __( 'Custom Elements For Subitems', 'avia_framework' ),
			'desc'		=> __( 'Select if you want all subitems of an element to use the same subitem element template or allow to select it individually for each subitem. The second allows you to create and edit the subitem elements individually. Changing this option will have no effect on existing elements in a page. You must edit these elements and save the page.', 'avia_framework' ),
			'id'		=> 'custom_el_subitem_handling',
			'type'		=> 'select',
			'std'		=> '',
			'no_first'	=> true,
			'global'	=> true,
			'class'		=> $advanced_class,
			'subtype'	=> array(
								__( 'All subitems use the same custom element template', 'avia_framework' )		=> '',
								__( 'Individually select subitem custom element templates', 'avia_framework' )	=> 'individually',
								__( 'Do not allow to use subitem custom element templates', 'avia_framework' )	=> 'none'
							)
		);

$class = current_theme_supports( 'avia-custom-elements-cpt-screen' ) ? '' : 'hidden';

$desc  = __( 'Select if you want to have access to the custom elements via the default WP CPT Screens. Links are added below theme options page link in dashboard and in admin bar &quor;New&quor;. Visibility depends on capability to manage custom elements.', 'avia_framework' );
$desc .= '<br />';
$desc .= __( 'It is not recommended to create/edit a CET from the CPT screen because of limited support for some option settings, e.g. the option &quot;All subitems use the same custom element template&quot; is not supported.', 'avia_framework' );
$desc .= '<br /><strong>';
$desc .=	__( 'After activating this feature you must reload backend to load the necssary menus to work with the CPT screens.', 'avia_framework' );
$desc .= '</strong>';

$avia_elements[] = array(
			'slug'		=> 'avia_element_templates',
			'name'		=> __( 'Custom Post Type Screen (Developers Only)', 'avia_framework' ),
			'desc'		=> $desc,
			'id'		=> 'custom_el_cpt_screen',
			'type'		=> 'select',
			'class'		=> $class,
			'std'		=> '',
			'no_first'	=> true,
			'global'	=> true,
			'class'		=> $advanced_class,
			'subtype'	=> array(
								__( 'Do not allow access to the screens', 'avia_framework' )	=> '',
								__( 'Add links to open the CPT screens', 'avia_framework' )		=> 'allow_cpt_screen',
							)
		);


$avia_elements[] = array(
			'slug'		=> 'avia_element_templates',
			'id'		=> 'alb_element_templates_advanced_close',
			'type'		=> 'visual_group_end',
			'nodescription' => true
		);

$avia_elements[] = array(
			'slug'		=> 'avia_element_templates',
			'id'		=> 'alb_element_templates_management_close',
			'type'		=> 'visual_group_end',
			'nodescription' => true
		);

