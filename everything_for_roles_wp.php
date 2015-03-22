<?php 
add_action('init', 'custom_change_role_name');
/**
 * function custom_change_role_name
 * change name of available roles
 * @action init
 * @version 1.0 | WP 3.0 up
 */
function custom_change_role_name() {
	global $wp_roles;

	if ( ! isset( $wp_roles ) )
		$wp_roles = new WP_Roles();

	$roles = $wp_roles->get_names();
	
	foreach ($roles as $key => $role) {
		switch ($key) {
			case 'administrator':
				$wp_roles->roles[$key]['name'] = 'Quản trị viên';
				$wp_roles->role_names[$key] = 'Quản trị viên';
				break;
			case 'editor':
				$wp_roles->roles[$key]['name'] = 'Quản trị tin tức';
				$wp_roles->role_names[$key] = 'Quản trị tin tức';
				break;
			case 'author':
				$wp_roles->roles[$key]['name'] = 'Quản trị đơn hàng (QTDH)';
				$wp_roles->role_names[$key] = 'Quản trị đơn hàng (QTDH)';
				break;
			case 'contributor':
				$wp_roles->roles[$key]['name'] = 'Quản trị kho (QTK)';
				$wp_roles->role_names[$key] = 'Quản trị kho (QTK)';
				break;
			case 'subscriber':
				$wp_roles->roles[$key]['name'] = 'Thành viên';
				$wp_roles->role_names[$key] = 'Thành viên';
				break;
		}
	}
}

// add_action('admin_init', 'change_contributor_caps');
function change_contributor_caps() {
	$role = get_role('contributor');

	$role->add_cap('edit_others_posts');
}