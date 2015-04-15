<?php 
/*
 * Wordpress save image from external link to jpg file and attach that to post
 * @version Wordpress 3.8 up
 */

function wp_save_image_from_external_link($link, $parent_post_id = 0){
	if(!$link) return false;

	$upload_dir = wp_upload_dir();
	$filename = wp_generate_password( 12, false ) .'.jpg';
	$image_dir = $upload_dir['path'] . '/' . $filename;
	$image_uri = $upload_dir['url'] .'/'. $filename;

	$image_contents = file_get_contents($link);
	$file_to_save = fopen($image_dir, 'w');
	fwrite($file_to_save, $image_contents);
	fclose($file_to_save);

	$file_type = wp_check_filetype( basename( $image_dir ), null );

	$attachment = array(
		'guid'           => $image_uri, 
		'post_mime_type' => $file_type['type'],
		'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $image_dir ) ),
		'post_content'   => '',
		'post_status'    => 'inherit'
	);

	$attach_id = wp_insert_attachment( $attachment, $image_dir, $parent_post_id );

	return $attach_id;
}
