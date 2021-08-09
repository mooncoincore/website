<?php

/**
 * 
 * Addon dashboard sidebar.
 */

 if( !isset($this->main_menu_slug) ):
    return false;
 endif;

 $cool_support_email = "https://coolplugins.net/contact-plugin-support/";
?>

 <div class="cool-body-right">
    <a href="https://coolplugins.net" target="_blank"><img src="<?php echo plugin_dir_url( $this->addon_file ) .'/assets/coolplugins-logo.png'; ?>"></a>
    <ul>
      <li>Cool Plugins develops best crypto plugins for WordPress.</li>
      <li>Our crypto plugins have <b>10000+</b> active installs.</li>
      <li>For any query or support, please contact plugin support team.
      <br><br>
      <a href="<?php echo $cool_support_email; ?>" target="_blank" class="button button-secondary">Premium Plugin Support</a>
      <br><br>
      </li>
      <li>We also provide <b>crypto plugins customization</b> services.
      <br><br>
      <a href="<?php echo $cool_support_email; ?>" target="_blank" class="button button-primary">Hire Developer</a>
      <br><br>
      </li>
   </ul>
    </div>

</div><!-- End of main container-->