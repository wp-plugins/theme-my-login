<?php

function jkf_tml_user_mod_load_users_page() {
	global $theme_my_login;
	
    if ( 'admin' == $theme_my_login->options['moderation']['type'] ) {
	    add_action('delete_user', 'jkf_tml_user_mod_deny_user');
        add_filter('user_row_actions', 'jkf_tml_user_mod_user_row_actions', 10, 2);
        if ( isset($_GET['action']) && 'approve' == $_GET['action'] ) {
            check_admin_referer('approve-user');

            $user = isset($_GET['user']) ? $_GET['user'] : '';
            if ( !$user )
                wp_die(__('You can&#8217;t edit that user.', 'theme-my-login'));

            if ( !current_user_can('edit_user', $user) )
                wp_die(__('You can&#8217;t edit that user.', 'theme-my-login'));

            include_once( TML_MODULE_DIR. '/user-moderation/includes/functions.php' );

            $newpass = ( jkf_tml_is_module_active('custom-passwords/custom-passwords.php') ) ? 0 : 1;
            if ( ! jkf_tml_user_mod_approve_new_user($user, $newpass) )
                wp_die(__('You can&#8217;t edit that user.', 'theme-my-login'));

            add_action('admin_notices', create_function('', "echo '<div id=\"message\" class=\"updated fade\"><p>' . __('User approved.', 'theme-my-login') . '</p></div>';"));
        }
    }
}

function jkf_tml_user_mod_user_row_actions($actions, $user_object) {
    $current_user = wp_get_current_user();
    $user_role = reset($user_object->roles);
    if ( $current_user->ID != $user_object->ID ) {
        if ( 'pending' == $user_role ) {
            $approve['approve-user'] = '<a href="' . add_query_arg( 'wp_http_referer', urlencode( esc_url( stripslashes( $_SERVER['REQUEST_URI'] ) ) ), wp_nonce_url("users.php?action=approve&amp;user=$user_object->ID", 'approve-user') ) . '">Approve</a>';
            $actions = array_merge($approve, $actions);
        }
    }
    return $actions;
}

function jkf_tml_user_mod_deny_user($user_id) {
    $user = new WP_User($user_id);
    $user_role = reset($user->roles);
    if ( 'pending' != $user_role )
        return;

    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    // we want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
    
    $message = sprintf(__('You have been denied access to %s', 'theme-my-login'), $blogname);
    $title = sprintf(__('[%s] Registration Denied', 'theme-my-login'), $blogname);
    
    $title = apply_filters('user_denial_title', $title);
    $message = apply_filters('user_denial_message', $message, $user_id);

    if ( $message && !wp_mail($user->user_email, $title, $message) )
          die('<p>' . __('The e-mail could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function...') . '</p>');
}

function jkf_tml_user_mod_admin_menu() {
	global $theme_my_login;
	
    jkf_tml_add_menu_page(__('Moderation', 'theme-my-login'), __FILE__, 'jkf_tml_user_mod_admin_page');
	
	if ( in_array('custom-email/custom-email.php', $theme_my_login->options['active_modules']) ) {
		$parent = plugin_basename(TML_MODULE_DIR . '/custom-email/admin/options.php');
		jkf_tml_add_submenu_page($parent, __('User Approval', 'theme-my-login'), TML_MODULE_DIR . '/user-moderation/admin/options-user-approval-email.php');
		jkf_tml_add_submenu_page($parent, __('User Denial', 'theme-my-login'), TML_MODULE_DIR . '/user-moderation/admin/options-user-denial-email.php');
	}	
}

function jkf_tml_user_mod_admin_page() {
	global $theme_my_login;
    ?>
<table class="form-table">
	<tr valign="top">
		<th scope="row"><?php _e('User Moderation', 'theme-my-login'); ?></th>
		<td>
			<input name="theme_my_login[moderation][type]" type="radio" id="theme_my_login_moderation_type_none" value="none" <?php if ( 'none' == $theme_my_login->options['moderation']['type'] ) { echo 'checked="checked"'; } ?> />
			<label for="theme_my_login_moderation_type_none"><?php _e('None', 'theme-my-login'); ?></label>
			<br />
			<input name="theme_my_login[moderation][type]" type="radio" id="theme_my_login_moderation_type_email" value="email" <?php if ( 'email' == $theme_my_login->options['moderation']['type'] ) { echo 'checked="checked"'; } ?> />
			<label for="theme_my_login_moderation_type_email"><?php _e('E-mail Confirmation', 'theme-my-login'); ?></label>
			<br />
			<input name="theme_my_login[moderation][type]" type="radio" id="theme_my_login_moderation_type_admin" value="admin" <?php if ( 'admin' == $theme_my_login->options['moderation']['type'] ) { echo 'checked="checked"'; } ?> />
			<label for="theme_my_login_moderation_type_admin"><?php _e('Admin Approval', 'theme-my-login'); ?></label>
		</td>
	</tr>
</table>
<?php
}

function jkf_tml_user_mod_save_settings($settings) {
	return $settings;
}

?>