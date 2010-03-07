<table class="form-table">
    <tr>
		<td>
			<p><em><?php _e('Available Variables', 'theme-my-login'); ?>: %blogname%, %siteurl%, %activateurl%, %user_login%, %user_email%, %user_ip%</em></p>
			<label for="theme_my_login_user_activation_title"><?php _e('Subject', 'theme-my-login'); ?></label><br />
			<input name="theme_my_login[email][user_activation][title]" type="text" id="theme_my_login_user_activation_title" value="<?php if ( isset($theme_my_login->options['email']['user_activation']['title']) ) echo $theme_my_login->options['email']['user_activation']['title']; ?>" class="full-text" /><br />
			<label for="theme_my_login_user_activation_message"><?php _e('Message', 'theme-my-login'); ?></label><br />
			<textarea name="theme_my_login[email][user_activation][message]" id="theme_my_login_user_activation_message" class="large-text" rows="10"><?php if ( isset($theme_my_login->options['email']['user_activation']['message']) ) echo $theme_my_login->options['email']['user_activation']['message']; ?></textarea><br />
		</td>
	</tr>
</table>