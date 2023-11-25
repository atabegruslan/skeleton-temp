<?php

function my_register_styles()
{
	// Bootstrap
	wp_register_style( 'bs3_style', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css' );

	// Carousel
	wp_register_style( 'bxslider_style', 'https://cdnjs.cloudflare.com/ajax/libs/bxslider/4.2.15/jquery.bxslider.min.css' );

	wp_register_style( 'front_style', get_template_directory_uri() . '/style.css' );
}
add_action('init', 'my_register_styles');

function my_enqueue_styles()
{
	//if ( is_front_page() )
	//{
		wp_enqueue_style( 'front_style' );
		wp_enqueue_style( 'bs3_style' ); // Bootstrap
		wp_enqueue_style( 'bxslider_style' ); // Carousel
	//}
}
add_action( 'wp_enqueue_scripts', 'my_enqueue_styles' );

function my_register_scripts()
{
	wp_register_script( 'front_script', get_template_directory_uri() . '/js/script.js' );

	// jQuery
	wp_register_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js' );

	// Bootstrap
	wp_register_script( 'bs3_script', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js' );

	// Carousel
	wp_register_script( 'bxslider_script', 'https://cdnjs.cloudflare.com/ajax/libs/bxslider/4.2.15/jquery.bxslider.min.js' );
}
add_action('init', 'my_register_scripts');

function my_enqueue_scripts()
{
	wp_enqueue_script( 'jquery' ); // jQuery
	wp_enqueue_script( 'bs3_script' ); // Bootstrap
	wp_enqueue_script( 'bxslider_script' ); // Carousel
	//if ( is_front_page() )
	//{
		wp_enqueue_script( 'front_script' );
	//}
}
add_action( 'wp_enqueue_scripts', 'my_enqueue_scripts' );

add_theme_support( 'menus' );

register_nav_menus(
	array(
		'uacmenu' => __('uac_menu_links')
	)
);

add_theme_support( 'post-thumbnails' );
add_theme_support( 'category-thumbnails' );
add_theme_support( 'html5' );

remove_filter( 'the_content', 'wpautop' );
remove_filter( 'the_excerpt', 'wpautop' );

//Add gallery size
add_image_size('thumbnail-gallery','135','87',true);

// hide top admin bar when admin login
show_admin_bar( false );
