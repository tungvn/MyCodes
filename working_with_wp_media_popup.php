<?php 
/*
 * Show, control and save data with media popup with js at wordpress backend
 * Require: jQuery - WP_Media
 */

// WP Media lib
add_action( 'admin_enqueue_scripts', 'admin_enqueue_scripts_styles' );
function admin_enqueue_scripts_styles(){
    wp_enqueue_media();
}

// Save metabox for post
add_action( 'save_post', 'custom_save_metabox_for_post' );
function custom_save_metabox_for_post( $post_id ){
    if( wp_is_post_revision( $post_id ) )
        return;

    if('post' == get_post_type( $post_id ) ){
        if( isset( $_POST['gallery'] ) )
            update_post_meta( $post_id, 'gallery', $_POST['gallery'] );
    }
}

?>

<!-- Save list of images id -->
<input id="gallery_input" type="hidden" name="gallery" value="">
<!-- The button to click, start choose -->
<p><button id="pick_images" class="button">Select Images</button></p>
<!-- Show list image, use wp_get_attachment_image_src -->
<ul id="display_gallery"></ul>

<script type="text/javascript">
jQuery(document).ready(function($) {
    /* 
    Example a button has id attribute "pick_images"
    When click the button, the wp media popup show. We choose images and press Select (like set featured image)
    */
    $('#pick_images').click(function(event) {
        event.preventDefault();

        // Init
        var file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Select Images',
            library: {},
            button: {text: 'Select'}, 
            multiple: true
        });

        // Pre select images
        file_frame.on('open', function() {
            var images = $( '#gallery_input' ).val();
            images = images.split( ',' );
            
            var selection = file_frame.state().get( 'selection' );
            $.each(images, function(index, el) {
                var attachment = wp.media.attachment( el );
                attachment.fetch();
                selection.add( attachment ? [ attachment ] : [] );
            });
        });

        // Select images
        file_frame.on('select', function() {
            var attachment_ids = [];
            attachment = file_frame.state().get( 'selection' ).toJSON();
            imgs_html = '';

            $.each( attachment, function(index, item){
                attachment_ids.push( item.id );
                imgs_html += '<li data-image-id="'+ item.id +'"><img src="'+ item.url +'" /><a class="del-img" href="javascript:;"><span class="dashicons dashicons-dismiss"></span></a></li>';
            });
            
            $( '#gallery_input' ).val( attachment_ids.join( ',' ) );
            $( '#display_gallery' ).html( imgs_html );
        });

        // Open media popup
        file_frame.open();
    });
});
</script>