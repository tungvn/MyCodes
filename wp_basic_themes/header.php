<?php $metaProperty = array(
	'app_id' => '',
	'type' => 'article',
	'title' => get_bloginfo('name'),
	'url' => home_url() . $_SERVER['REQUEST_URI'],
	'image' => get_template_directory_uri() . '/screenshot.png',
	'description' => get_bloginfo('description'),
	'author' => 'Time Universal',
);

if( is_tax() || is_category() || is_tag() ) {
	$metaProperty['title'] = single_term_title( '', false ) .' | '. get_bloginfo('name');
	$metaProperty['description'] = term_description() ? term_description() : $metaProperty['description'];
}

if( is_search() ) {
	$metaProperty['title'] = 'Search: '. get_query_var('s') .' | '. get_bloginfo('name');
	$metaProperty['description'] = 'Search result for "'. get_query_var('s') .'" from '. get_bloginfo('name');
}

if( is_author() ) {
	$authorID = get_query_var('author');
	$authorData = get_userdata( $authorID );
	$metaProperty['title'] = $authorData->display_name .' @ '. get_bloginfo('name');
	$metaProperty['description'] = $authorData->descriptionn ? $authorData->description : $metaProperty['description'];
}

if( is_single() || is_page() ) {
	$imageSource = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
	$postDescription = $post->post_excerpt ? $post->post_excerpt : wp_trim_words( $post->post_content );
	$metaProperty['title'] = $post->post_title;
	$metaProperty['description'] = strip_tags( $postDescription );
	$metaProperty['image'] = $imageSource ? $imageSource[0] : $metaProperty['image'];
	$metaProperty['author'] = get_the_author_meta( 'display_name', $post->post_author );
}

if( is_paged() ) {
	$metaProperty['title'] .= ' | Page '. get_query_var('paged');
	$metaProperty['description'] .= ' | Page '. get_query_var('paged');
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php language_attributes(); ?>" xml:lang="<?php language_attributes(); ?>">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

	<meta name="author" content="<?php echo str_replace('"', '', $metaProperty['author']);?>" />
	<meta name="description" content="<?php echo str_replace('"', '', $metaProperty['description']);?>" />
	<meta property="fb:app_id" content="<?php echo $metaProperty['app_id'];?>" />
	<meta property="og:type" content='<?php echo $metaProperty['type'];?>' />
	<meta property="og:title" content="<?php echo str_replace('"', '', $metaProperty['title']);?>" />
	<meta property="og:url" content="<?php echo $metaProperty['url'];?>" />
	<meta property="og:image" content="<?php echo $metaProperty['image'];?>" />
	<meta property="og:description" content="<?php echo str_replace('"', '', $metaProperty['description']);?>" />

	<title><?php echo $metaProperty['title'];?></title>
	<link type="image/x-icon" rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/images/favicon.png">

	<?php wp_head();?>
</head>
<body <?php body_class(); ?>>
