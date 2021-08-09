<?php
/**
 *
 * This page serve as dashboard template
 *
 */
// do not render this page if its found outside of main class
if( !isset($this->main_menu_slug) ){
  return false;
}
$is_active = false;
$classes = 'plugin-block';
$is_installed = false;
$button = null;
$available_version = null;
$update_available = false;
$update_stats = '';
$pro_already_installed = false;

// Let's see if a pro version is already installed
if( isset( $this->disable_plugins[ $plugin_slug ] ) ){
    $pro_version = $this->disable_plugins[ $plugin_slug ];
    if( file_exists(WP_PLUGIN_DIR .'/' . $pro_version['pro'] ) ){
        $pro_already_installed = true;
        $classes .= ' plugin-not-required';
    }
}

if (file_exists(WP_PLUGIN_DIR . '/' . $plugin_slug)) {

    $is_installed = true;
    $plguin_file = null;
    $installed_plugins = get_plugins();//get_option('active_plugins', false);
    $is_active = false;
    $classes .= ' installed-plugin';

    foreach ($installed_plugins as $plugin=>$data) {
      $thisPlugin = substr($plugin,0,strpos($plugin,'/'));
      if ( strcasecmp($thisPlugin, $plugin_slug) == 0 ) {

          if( isset($plugin_version) && version_compare( $plugin_version, $data['Version'] ) >0 ){
            $available_version = $plugin_version ;
            $plugin_version =  $data['Version'];
            $update_stats = '<span class="plugin-update-available">Update Available: v '.$available_version.'</span>';
          }

          if( is_plugin_active($plugin) ){
            $is_active = true;
            $classes .= ' active-plugin';
            break;
          }else{
            $plguin_file = $plugin;
            $classes .= ' inactive-plugin';
          }

        }
    }
    if( $is_active ){
        $button = '<button class="button button-disabled">Active</button>';
    }else{
        $wp_nonce = wp_create_nonce( 'cool-plugins-activate-' . $plugin_slug );
        $button .= '<button class="button activate-now cool-plugins-addon plugin-activator" data-plugin-tag="'.$tag.'" data-plugin-id="'.$plguin_file.'" data-action-nonce="'.$wp_nonce.'" data-action-name="cool-plugins-activate-'.$plugin_slug.'">Activate</button>';
    }
} else {
    $wp_nonce = wp_create_nonce('cool-plugins-download-' . $plugin_slug );
    $classes .= ' available-plugin';
    if( $plugin_url !=null ){
      $button = '<button class="button install-now cool-plugins-addon plugin-downloader" data-plugin-tag="'.$tag.'" data-url="' . $plugin_url . '" data-action-nonce="' . $wp_nonce . '" data-action-name="cool-plugins-download-'.$plugin_slug.'">Install</button>';
    }elseif( isset($plugin_pro_url) ){
      $button = '<a class="button install-now cool-plugins-addon pro-plugin-downloader" href="'.$plugin_pro_url.'" target="_new">Buy Pro</a>';
    }
}

// Remove install / activate button if pro version is already installed
if( $pro_already_installed === true ){
  $pro_ver = $this->disable_plugins[ $plugin_slug ] ;
  $button = '<button class="button button-disabled" title="This plugin is no more required as you already have '.$pro_ver['pro'].'">Pro Installed</button>';
}

    // All php condition formation is over here
?>



<div class="<?php echo $classes; ?>">
  <div class="plugin-block-inner">

    <div class="plugin-logo">
    <img src="<?php echo $plugin_logo; ?>" width="250px" />
    </div>

    <div class="plugin-info">
      <h4 class="plugin-title"> <?php echo $plugin_name; ?></h4>
      <div class="plugin-desc"><?php echo $plugin_desc; ?></div>
      <div class="plugin-stats">
      <?php echo $button ; ?> 
      <?php if( isset($plugin_version) && !empty($plugin_version)) : ?>
        <div class="plugin-version">v <?php echo $plugin_version; ?></div>
        <?php echo $update_stats; ?>
      <?php endif; ?>
      </div>
    </div>

  </div>
</div>
