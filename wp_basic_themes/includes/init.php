<?php 

// Admin Login
add_filter( 'login_headerurl', 'loginpage_custom_link' );
function loginpage_custom_link() {
    return home_url('/');
}

add_filter( 'login_headertitle', 'change_title_on_logo' );
function change_title_on_logo() {
    return get_option('blogname');
}

add_action( 'login_enqueue_scripts', 'change_wp_admin_logo' );
function change_wp_admin_logo() { ?>
<style type="text/css">
body.login div#login{padding: 3% 0px 0px;}
body.login div#login h1 a{background: url("<?php echo get_template_directory_uri() .'/images/logo.png'; ?>") no-repeat center center / 100% 100%; width: 230px; height: 44px;}
</style>
<?php }

// Remove AdminBar 
add_filter('show_admin_bar', '__return_false');

remove_action('wp_head', 'wp_generator'); 
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');
add_action( 'wp_before_admin_bar_render', 'remove_edit_comments_admin_bar' );
function remove_edit_comments_admin_bar() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu( 'comments' );
}

// Remove comments page
add_action( 'admin_menu', 'remove_admin_comments_page' );
function remove_admin_comments_page(){
    remove_menu_page( 'edit-comments.php' );
}

// Removes comments from post and pages
add_action('init', 'remove_comment_support', 100);
function remove_comment_support() {
    remove_post_type_support( 'post', 'comments' );
    remove_post_type_support( 'page', 'comments' );
    remove_post_type_support( 'attachment', 'comments' );
}

/* Functions */
function wp_get_image_src($id, $size = 'full'){
    if($id > 0){
        $imageInfo = wp_get_attachment_image_src($id, $size);
        return $imageInfo[0];
    }
    return false;
}
