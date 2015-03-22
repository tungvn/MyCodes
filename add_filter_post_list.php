<?php 
/**
 * Wordpress Codes
 * Add filter to post (post_type) lists
 * Nov 2014
 * http://tungvn.info/
 * https://github.com/tungvn/wordpress-codes
 */

// add filter to admin posts list
add_action('restrict_manage_posts', 'admin_posts_filter_restrict_manage_posts');
/**
 * admin_posts_filter_restrict_manage_posts
 * @version wordpress 3.3 up
 * add a filter (selectbox, radio, checkbox or anythings you want)
 */
function admin_posts_filter_restrict_manage_posts() {
	$post_type = 'post';
	if(isset($_GET['post_type']))
		$post_type = $_GET['post_type'];

	if($post_type == '<your-post-type>') {
		// filter options bellow. ex. selectbox from taxonomy
		$terms = get_terms('series-post', array('hide_empty' => 0)); ?>
		<select name="filter_by_series">
			<option value="">Filter by Series</option><?php $current_s = (isset($_GET['filter_by_series'])) ? $_GET['filter_by_series']:'';
			foreach ($terms as $tkey => $term) {
			echo '<option value="'. $term->slug .'" '. (($current_s == $term->slug) ? 'selected=""':'') .'>'. $term->name .'</option>'. "\n";
		} ?>
		</select>
	<?php
	}
}

add_filter('parse_query', 'custom_post_filter');
/**
 * custom_post_filter
 * @version wordpress 4.0
 * @param $query (WP_Query Object)
 * add a query to wp_query when filter
 */
function custom_post_filter($query) {
	global $pagenow;
	$post_type = 'post';
	if(isset($_GET['post_type']))
		$post_type = $_GET['post_type'];

	if ('<your-post-type>' == $post_type && is_admin() && $pagenow == 'edit.php' && isset($_GET['filter_by_series']) && $_GET['filter_by_series'] != '') {
		$query->query_vars['series-post'] = $_GET['filter_by_series'];
	}
}

add_filter('manage_edit-post_columns', 'post_series_columns_head');
/**
 * post_series_columns_head
 * @version wordpress 4.0
 * @param $default array
 * add (a) column(s) to post lists
 * filter manage_edit-<post-type>_columns
 */
function post_series_columns_head($defaults) {
	$defaults = array_slice($defaults, 0, 4, true) + array('series-post' => 'Series') + array_slice($defaults, 4, count($defaults)-4, true);
	return $defaults;
}

add_action('manage_posts_custom_column' , 'post_series_custom_column', 10, 2);
/**
 * post_series_custom_column
 * @version wordpress 4.0
 * @param $column string
 * @param $post_id number
 * add content to post column
 * action manage_<post-type>_posts_custom_column
 */
function post_series_custom_column($column, $post_id){
	switch ($column) {
		case 'series-post':
			$s_term = wp_get_object_terms($post_id, array('series-post'));
			if(!is_wp_error($s_term) && !empty($s_term))
				echo '<a class="series-title" href="'. get_edit_term_link($s_term[0]->term_id, 'series-post', 'post') .'">'. get_term($s_term[0], 'series-post')->name .'</a>';
			else echo '(no-series)';
			break;
	}
}
