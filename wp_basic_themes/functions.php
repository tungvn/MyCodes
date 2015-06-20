<?php 
$_settings = array(
	'prefix' => ''
);

include_once( get_template_directory() . '/includes/init.php' );

// Adding Enqueue scripts 
add_action('wp_enqueue_scripts', 'enqueue_scripts_styles');
function enqueue_scripts_styles() {
	wp_enqueue_style('style', get_stylesheet_uri());
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js', array(), '1.9.1', false);
	wp_enqueue_script('addons', get_template_directory_uri() . '/scripts/addons.js', array(), '1.0', false);
	wp_enqueue_script('scripts', get_template_directory_uri() . '/scripts/main.js', array(), '1.0', false);

}

// Register nav menu
register_nav_menus(array(
	'main-menu' => 'Main Menu'
));

// Image size 
if(function_exists('add_theme_support')) {
	add_theme_support('post-thumbnails');

	// add_image_size('page_banner', 1100, 260, false); // for page banner
}

// Loads the theme's translated strings. 
// add_action('init', '_theme_textdomain');
function _theme_textdomain() {
	// load_theme_textdomain(, get_template_directory() . '/languages');
}

// Include custom posttype
// include_once(get_template_directory() . '/includes/agency.php');
