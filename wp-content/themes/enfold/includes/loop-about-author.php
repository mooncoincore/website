<?php
	if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly
	
	
	global $avia_config;

	$author_id = get_query_var( 'author' );
	
	if( empty( $author_id ) ) 
	{
		$author_id = get_the_author_meta( 'ID' );
	}
	
	$context = 'loop-about-author.php';
	$description_ori = get_the_author_meta( 'description', $author_id );
	
	/**
	 * Filter author data
	 * 
	 * @param string
	 * @param int $author_id
	 * @param string $context		added with 4.7.5.1
	 * @return string
	 */
	$name = apply_filters( 'avf_author_name', get_the_author_meta( 'display_name', $author_id ), $author_id, $context );
	$email = apply_filters( 'avf_author_email', get_the_author_meta( 'email', $author_id ), $author_id, $context );
	$description = apply_filters( 'avf_author_description', $description_ori, $author_id, $context );
	
	$gravatar_alt = esc_html( $name );
	$gravatar = get_avatar( $email, '81', '', $gravatar_alt );
	$name = "<span class='author-box-name' " . avia_markup_helper( array( 'context' => 'author_name', 'echo' => false ) ) . '>'. $name . '</span>';
	$heading = __( 'About', 'avia_framework' ) . ' ' . $name;
	
	$default_heading = 'h3';
	$args = array(
				'heading'		=> $default_heading,
				'extra_class'	=> ''
			);

	/**
	 * @since 4.5.5
	 * @return array
	 */
	$args = apply_filters( 'avf_customize_heading_settings', $args, 'loop_default_author', array() );

	$heading1 = ! empty( $args['heading'] ) ? $args['heading'] : $default_heading;
	$css = ! empty( $args['extra_class'] ) ? $args['extra_class'] : '';
	
	if( empty( $description ) )
	{
		$cnt_posts = count_user_posts( $author_id );
		
		$description  = __( 'This author has not written his bio yet.', 'avia_framework' );
		
		if( $cnt_posts > 0 )
		{
			$description .= '<br />' . sprintf( __( 'But we are proud to say that %s contributed %s entries already.', 'avia_framework' ), $name, $cnt_posts );
		}
		
		if( current_user_can( 'edit_users' ) || get_current_user_id() == $author_id )
		{
	    	$description .= "<br /><a href='" . admin_url( 'profile.php?user_id=' . $author_id ) . "'>" . __( 'Edit the profile description here.', 'avia_framework' ) . '</a>';
		}
	}
	
	/**
	 * Filter final output of author description or skip completly
	 * 
	 * @since 4.7.5.1
	 * @param string $description
	 * @param string $description_ori
	 * @param int $author_id
	 * @return string|false					false to skip output completely
	 */
	$description = apply_filters( 'avf_author_description_loop_about', $description, $description_ori, $author_id );

	if( false === $description )
	{
		return;
	}

	echo '<section class="author-box" ' . avia_markup_helper( array( 'context' => 'author', 'echo' => false ) ) . '>';
	echo	"<span class='post-author-format-type blog-meta'><span class='rounded-container'>{$gravatar}</span></span>";
	echo	"<div class='author_description '>";
	echo		"<{$heading1} class='author-title {$css}'>{$heading}</{$heading1}>";
	echo		"<div class='author_description_text'" . avia_markup_helper( array( 'context' => 'description', 'echo' => false ) ) . '>' . wpautop( $description ) . '</div>';
	echo		'<span class="author-extra-border"></span>';
	echo	'</div>';
	echo '</section>';

