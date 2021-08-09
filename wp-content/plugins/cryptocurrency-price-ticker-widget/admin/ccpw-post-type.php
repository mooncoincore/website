<?php

class CCPW_Posttype
{
    public function __construct()
    {
      add_action( 'init',  array( $this,'ccpwp_post_type') );	
      add_action( 'cmb2_admin_init', array( $this,'cmb2_ccpwp_metaboxes' ));
      add_action( 'add_meta_boxes', array( $this,'register_ccpwp_meta_box') );
      add_action( 'save_post', array( $this,'save_ccpwp_shortcode'),50, 3 );
      add_action( 'add_meta_boxes_ccpw',array($this,'ccpwp_add_meta_boxes'));	

      add_filter( 'manage_ccpw_posts_columns',array($this,'set_custom_edit_ccpw_columns'));
      add_action( 'manage_ccpw_posts_custom_column' ,array($this,'custom_ccpwp_column'), 10, 2 );
    }
   
/*
|--------------------------------------------------------------------------
| Create Widget generator Post Type
|--------------------------------------------------------------------------
*/
	function ccpwp_post_type() {

		$labels = array(
			'name'                  => _x( 'CryptoCurrency Price Widget', 'Post Type General Name', 'ccpw2' ),
			'singular_name'         => _x( 'CryptoCurrency Price Widget', 'Post Type Singular Name', 'ccpw2' ),
			'menu_name'             => __( 'Crypto Widget Pro', 'ccpw2' ),
			'name_admin_bar'        => __( 'Crypto Widget Pro', 'ccpw2' ),
			'archives'              => __( 'Item Archives', 'ccpw2' ),
			'attributes'            => __( 'Item Attributes', 'ccpw2' ),
			'parent_item_colon'     => __( 'Parent Item:', 'ccpw2' ),
			'all_items'             => __( 'All Widgets', 'ccpw2' ),
			'add_new_item'          => __( 'Add New Widget', 'ccpw2' ),
			'add_new'               => __( 'Add New', 'ccpw2' ),
			'new_item'              => __( 'New Item', 'ccpw2' ),
			'edit_item'             => __( 'Edit Item', 'ccpw2' ),
			'update_item'           => __( 'Update Item', 'ccpw2' ),
			'view_item'             => __( 'View Item', 'ccpw2' ),
			'view_items'            => __( 'View Items', 'ccpw2' ),
			'search_items'          => __( 'Search Item', 'ccpw2' ),
			'not_found'             => __( 'Not found', 'ccpw2' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'ccpw2' ),
			'featured_image'        => __( 'Featured Image', 'ccpw2' ),
			'set_featured_image'    => __( 'Set featured image', 'ccpw2' ),
			'remove_featured_image' => __( 'Remove featured image', 'ccpw2' ),
			'use_featured_image'    => __( 'Use as featured image', 'ccpw2' ),
			'insert_into_item'      => __( 'Insert into item', 'ccpw2' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'ccpw2' ),
			'items_list'            => __( 'Items list', 'ccpw2' ),
			'items_list_navigation' => __( 'Items list navigation', 'ccpw2' ),
			'filter_items_list'     => __( 'Filter items list', 'ccpw2' ),
		);
		$args = array(
			'label'                 => __( 'CryptoCurrency Price Widget', 'ccpw2' ),
			'description'           => __( 'Post Type Description', 'ccpw2' ),
			'labels'                => $labels,
			'supports'              => array( 'title' ),
			'taxonomies'            => array(''),
			'hierarchical'          => false,
			'public' => false,  // it's not public, it shouldn't have it's own permalink, and so on
			'show_ui'               => true,
			'show_in_nav_menus' => false,  // you shouldn't be able to add it to menus
			'menu_position'         => 20,
            'show_in_admin_bar'     => false,
            'show_in_menu'          => false,
			'can_export'            => true,
			'has_archive' => false,  // it shouldn't have archive page
			'rewrite' => false,  // it shouldn't have rewrite rules
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			 'menu_icon'           => CCPWP_URL.'/assets/ccpw-icon.png',
			'capability_type'       => 'page',
		);
		register_post_type( 'ccpw', $args );

    }

/*
|--------------------------------------------------------------------------
| Define the metabox and field configurations.
|--------------------------------------------------------------------------
*/
	function cmb2_ccpwp_metaboxes() {

		// Start with an underscore to hide fields from custom fields list
		$prefix = 'ccpw_';
		require_once CCPWP_PATH . '/admin/ccpw-settings.php';
    
	}
/*
|--------------------------------------------------------------------------
| Register meta boxes for shortcode
|--------------------------------------------------------------------------
*/
    public	function register_ccpwp_meta_box()
	{
	    add_meta_box( 'ccpw-shortcode', 'Cryptocurrency Widgets Shortcode',array($this,'p_shortcode_meta'), 'ccpw', 'side', 'high' );
    }
   
    /**
	 *  meta boxes callback
	 */
	public	function p_shortcode_meta()
    { 
        $id = get_the_ID();
        $dynamic_attr='';
        if( isset($_REQUEST['post']) ){
        echo '<p>Paste this shortcode in anywhere (page/post/sidebar)</p>'; 
        $element_type = get_post_meta( $id, 'pp_type', true );
        $dynamic_attr.="[ccpw id=\"{$id}\"";
        $dynamic_attr.=']';
        ?>
        <input onClick="this.select();" type="text"  class="regular-small" name="my_meta_box_text" id="my_meta_box_text" value="<?php echo htmlentities($dynamic_attr) ;?>" readonly/>
        <?php 
        }else{
            _e('Save this widget to generate the widget shortcode.','ccpwx');
        }
    }


    /**
     * Save shortcode when a post is saved.
     *
     * @param int $post_id The post ID.
     * @param post $post The post object.
     * @param bool $update Whether this is an existing post being updated or not.
     */
    function save_ccpwp_shortcode( $post_id, $post, $update ) {
        // Autosave, do nothing
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
                    return;
            // AJAX? Not used here
            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) 
                    return;
            // Check user permissions
            if ( ! current_user_can( 'edit_post', $post_id ) )
                    return;
            // Return if it's a post revision
            if ( false !== wp_is_post_revision( $post_id ) )
                    return;
        /*
        * In production code, $slug should be set only once in the plugin,
        * preferably as a class property, rather than in each function that needs it.
        */
        $post_type = get_post_type($post_id);

        // If this isn't a 'ccpw' post, don't update it.
        if ( "ccpw" != $post_type ) return;
            // - Update the post's metadata.
            
            if(isset($_POST['type']) && $_POST['type']=="rss-feed"){
                
                if(isset($_POST['rss_style']) && $_POST['rss_style']=="ticker-rss")
                {
                if(isset($_POST['rss_ticker_position'])&& in_array($_POST['rss_ticker_position'],array('rss-header','rss-footer'))){
                update_option('ccpw-news-p-id',$post_id);
            // update_option('ccpw-shortcode',"[ccpw id=".$post_id."]");
                }
            }	

            }else if(isset($_POST['type']) && $_POST['type']=="ticker"){

                if(isset($_POST['ticker_position'])&& in_array($_POST['ticker_position'],array('header','footer'))){
                update_option('ccpw-p-id',$post_id);
            // update_option('ccpw-shortcode',"[ccpw id=".$post_id."]");
                }
            }
            
             ccpwp_update_ua_coins_on_save_post( $post_id );

            delete_transient( 'ccpw-coins' ); // Site Transient
            delete_transient( 'ccpw-new-feed-data');
        }

/*
|--------------------------------------------------------------------------
| Register meta boxes for Feedback
|--------------------------------------------------------------------------
*/
    function ccpwp_add_meta_boxes( $post){
        add_meta_box(
            'ccpw-feedback-section',
            __( 'Hopefully you are Happy with our plugin','ccpw2'),
            array($this,'ccpwp_right_section'),
            'ccpw',
            'side',
            'low'
        );
    }

    /**
    *  admin notice for plugin review callback
    */
    function ccpwp_right_section($post, $callback){
    global $post;
    $pro_add='';
    $pro_add .=
    __('May I ask you to give it a 5-star rating on Codecanyon?','ccpw2').'<strong><a target="_blank" href="https://codecanyon.net/item/cryptocurrency-price-ticker-widget-pro-wordpress-plugin/reviews/21269050"></a></strong><br/>'.
        __('This will help to spread its popularity and to make this plugin a better one ','ccpw2').
    '<strong><a target="_blank" href="https://codecanyon.net/item/cryptocurrency-price-ticker-widget-pro-wordpress-plugin/reviews/21269050"></a></strong>on Codecanyon<br/>
    <a target="_blank" href="https://codecanyon.net/downloads#item-21269050"><img src="https://res.cloudinary.com/cooltimeline/image/upload/v1504097450/stars5_gtc1rg.png"></a>
        <div><a href="http://cryptowidgetpro.coolplugins.net" class="button button-primary" target="_blank">View Demos</a> &nbsp; <a href="https://codecanyon.net/downloads#item-21269050" class="button button-secondary" target="_blank">Submit Review</a></div><br/><div>
        </div>';
    echo $pro_add ;

    }

 /*
|--------------------------------------------------------------------------
| Handle All Widget Columns
|--------------------------------------------------------------------------
*/   
    function set_custom_edit_ccpw_columns($columns) {
        $columns['type'] = __( 'Widget Type', 'ccpw2' );
         $columns['shortcode'] = __( 'Shortcode', 'ccpw2' );
        return $columns;
     }
 
     /**
      * CCPW custom post type all shortcode types
      */
     function custom_ccpwp_column( $column, $post_id ) {
         switch ( $column ) {
             case 'type' :
                  $type=get_post_meta( $post_id , 'type' , true );
              switch ( $type ){
                 case "binance-live-widget":
                      _e('Binance Live Widget','ccpw2');
                 break;
                 case "ticker":
                      _e('Ticker','ccpw2');
                 break;
                 case "price-card":
                      _e('Price Card','ccpw2');
                 break;
                 case "price-label":
                      _e('Price Label','ccpw2'); 
                 break;
                 case "chart":
                      _e('Chart','ccpw2');
                 break;
                 case "price-block":
                      _e('Price Block','ccpw2');
                 break;
                 case "slider-widget":
                      _e('Slider Widget','ccpw2');
                 break;
                 case "multi-currency-tab":
                      _e('Multi Currency Tabs','ccpw2');
                 break;
                 case "calculator":
                      _e('Calculator','ccpw2');
                 break;
                 case "changelly-widget":
                      _e('Changelly Exchange Widget','ccpw2');
                 break;
                 case "rss-feed":
                      _e('News Feed','ccpw2');
                 break;
                 case "table-widget":
                     _e('Advanced Table','ccpw2');
                 break;
                 case "accordion-block":
                     _e('Accordion Widget','ccpw2');
                 break;
                 case "technical-analysis":
                     _e('Technical Analysis','ccpw2');
                 break;
          /* 		case "quick-stats":
                     _e('Quick Stats','ccpw2');
                 break; */
                 default:
                      _e('List Widget','ccpw2');
              }
                break;
            case 'shortcode' :
                    echo "<input type='text' style='text-align:center;' value='[ccpw id=&#34;".$post_id."&#34;]' onClick='this.select();' readonly/>";
         }
     }    


    
}