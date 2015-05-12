<?php

/* user box meta */
add_action( 'show_user_profile', 'action_personal_options_func' );
add_action( 'edit_user_profile', 'action_personal_options_func' );
function action_personal_options_func( $user ) {
	# New group at user profile
}
