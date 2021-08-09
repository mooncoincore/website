<?php
/**
 * Single Event Template
 * A single event. This displays the event title, description, meta, and
 * optionally, the Google map for the event.
 * 
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/single-event.php
 *
 * @package TribeEventsCalendar
 *
 * Fallback template only if plugin Tribe__Tickets_Plus is active. Function of this plugin is broken with our overridden version with mobile support 
 * @since 4.2.7
 */

if ( ! defined( 'ABSPATH' ) ) { die('-1'); }

$event_id = get_the_ID();

?>

<div id="tribe-events-content" class="tribe-events-single">

	<p class="tribe-events-back"><a href="<?php echo tribe_get_events_link() ?>"> <?php _e( '&laquo; All Events', 'avia_framework' ) ?></a></p>

	<!-- Notices -->
	<?php 
		if( function_exists( 'tribe_the_notices' ) )
		{
			tribe_the_notices();
		}
		else
		{
			tribe_events_the_notices();
		}
		?>


	<?php while ( have_posts() ) :  the_post(); 
	
			$default_heading = 'h2';
			$args = array(
						'heading'		=> $default_heading,
						'extra_class'	=> ''
					);
			
			/**
			 * @since 4.5.5
			 * @return array
			 */
			$args = apply_filters( 'avf_customize_heading_settings', $args, 'single-event-no-mobile', array() );
			
			$heading = ! empty( $args['heading'] ) ? $args['heading'] : $default_heading;
			$css = ! empty( $args['extra_class'] ) ? $args['extra_class'] : '';
	
	?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<!-- Event featured image, but exclude link -->
						
			<div class='av-single-event-content'>
				
				<?php the_title( "<{$heading} class='tribe-events-single-event-title summary entry-title {$css}'>", "</{$heading}>" ); ?>
	
				<div class="tribe-events-schedule updated published tribe-clearfix">
					<?php echo tribe_events_event_schedule_details( $event_id, '<h3>', '</h3>' ); ?>
					<?php  if ( tribe_get_cost() ) :  ?>
						<span class="tribe-events-divider">-</span>
						<span class="tribe-events-cost"><?php echo tribe_get_cost( null, true ) ?></span>
					<?php endif; ?>
				</div>
				
				<!-- Event content -->
				<?php do_action( 'tribe_events_single_event_before_the_content' ) ?>
				<div class="tribe-events-single-event-description tribe-events-content entry-content description">
					<?php echo tribe_event_featured_image( $event_id, 'entry_with_sidebar', false ); ?>
					<?php the_content(); ?>
				</div><!-- .tribe-events-single-event-description -->
				
				<?php do_action( 'tribe_events_single_event_after_the_content' ) ?>
	
				<?php 
					if( get_post_type() == Tribe__Events__Main::POSTTYPE && tribe_get_option( 'showComments', false ) ) 
					{
						comments_template();
					}
				?>
			
			</div> <!-- av-single-event-content -->
			
			<div class='av-single-event-meta-bar'>
				
					<div class='av-single-event-meta-bar-inner'>
					
					<!-- Event meta -->
					<?php do_action( 'tribe_events_single_event_before_the_meta' ) ?>
						<?php
						/**
						 * The tribe_events_single_event_meta() function has been deprecated and has been
						 * left in place only to help customers with existing meta factory customizations
						 * to transition: if you are one of those users, please review the new meta templates
						 * and make the switch!
						 * 
						 * Function was removed in version 3.11 on 22.7.2015
						 * 
						 * To allow a more logical order of content on mobile/desktop we add 2 copies of the meta data 
						 * and show/hide with CSS
						 */
						if ( ! apply_filters( 'tribe_events_single_event_meta_legacy_mode', false ) )
						{	
							tribe_get_template_part( 'modules/meta' );
						}
						else 
						{
							echo tribe_events_single_event_meta();
						}
						?>
					<?php do_action( 'tribe_events_single_event_after_the_meta' ) ?>
				
				</div>
			</div>
			
			
			</div> <!-- #post-x -->
		
	<?php endwhile; ?>

	<!-- Event footer -->
    <div id="tribe-events-footer">
		<!-- Navigation -->
		<!-- Navigation -->
		<h3 class="tribe-events-visuallyhidden"><?php _e( 'Event Navigation', 'avia_framework' ) ?></h3>
		<ul class="tribe-events-sub-nav">
			<li class="tribe-events-nav-previous"><?php tribe_the_prev_event_link( '<span>&laquo;</span> %title%' ) ?></li>
			<li class="tribe-events-nav-next"><?php tribe_the_next_event_link( '%title% <span>&raquo;</span>' ) ?></li>
		</ul><!-- .tribe-events-sub-nav -->
	</div><!-- #tribe-events-footer -->

</div><!-- #tribe-events-content -->
