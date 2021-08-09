<?php
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


global $avia_config, $post_loop_count;

$post_loop_count= 1;
$post_class 	= "post-entry-".avia_get_the_id();



// check if we got posts to display:
if (have_posts()) :

	while (have_posts()) : the_post();
?>

		<article class='post-entry post-entry-type-page <?php echo $post_class; ?>' <?php avia_markup_helper(array('context' => 'entry')); ?>>

			<div class="entry-content-wrapper clearfix">
				<?php
                echo '<header class="entry-content-header">';
				
				if( '1' != get_post_meta( get_the_ID(), '_avia_hide_featured_image', true ) )
				{
					$thumb = get_the_post_thumbnail( get_the_ID(), $avia_config['size'] );
					if( $thumb )
					{
						echo "<div class='page-thumb'>{$thumb}</div>";
					}
				}
				
                echo '</header>';

				//display the actual post content
				echo '<div class="entry-content" '.avia_markup_helper(array('context' => 'entry_content','echo'=>false)).'>';
				    the_content(__('Read more','avia_framework').'<span class="more-link-arrow"></span>');
				echo '</div>';

                echo '<footer class="entry-footer">';
			
				$avia_wp_link_pages_args = apply_filters('avf_wp_link_pages_args', array(
																						'before' =>'<nav class="pagination_split_post">'.__('Pages:','avia_framework'),
																	                    'after'  =>'</nav>',
																	                    'pagelink' => '<span>%</span>',
																	                    'separator'        => ' ',
																	                    ));

				wp_link_pages($avia_wp_link_pages_args);

				if(has_tag() && is_single())
				{
					echo '<span class="blog-tags minor-meta">';
					the_tags('<strong>'.__('Tags:','avia_framework').'</strong><span> ');
					echo '</span></span>';
				}
                echo '</footer>';

				?>
			</div>
			
			<?php do_action('ava_after_content', get_the_ID(), 'single-portfolio'); ?>
			
		</article><!--end post-entry-->


<?php
	$post_loop_count++;
	endwhile;
	else:
		
			$default_heading = 'h1';
			$args = array(
						'heading'		=> $default_heading,
						'extra_class'	=> ''
					);
			
			/**
			 * @since 4.5.5
			 * @return array
			 */
			$args = apply_filters( 'avf_customize_heading_settings', $args, 'loop_portfolio::nothing_found', array() );
			
			$heading = ! empty( $args['heading'] ) ? $args['heading'] : $default_heading;
			$css = ! empty( $args['extra_class'] ) ? $args['extra_class'] : '';
?>

    <article class="entry">
        <header class="entry-content-header">
            <?php echo "<{$heading} class='post-title entry-title {$css}'>" . __( 'Nothing Found', 'avia_framework' ) . "</{$heading}>"; ?>
        </header>

        <?php get_template_part('includes/error404'); ?>

        <footer class="entry-footer"></footer>
    </article>

<?php

	endif;
