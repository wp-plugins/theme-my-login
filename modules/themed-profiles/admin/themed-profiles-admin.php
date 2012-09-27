<?php
/**
 * Holds Theme My Login Themed Profiles Admin class
 *
 * @package Theme_My_Login
 * @subpackage Theme_My_Login_Themed_Profiles
 * @since 6.2
 */

if ( ! class_exists( 'Theme_My_Login_Themed_Profiles_Admin' ) ) :
/**
 * Theme My Login Themed Profiles Admin class
 *
 * @since 6.2
 */
class Theme_My_Login_Themed_Profiles_Admin extends Theme_My_Login_Abstract {
	/**
	 * Holds options key
	 *
	 * @since 6.3
	 * @access protected
	 * @var string
	 */
	protected $options_key = 'theme_my_login_themed_profiles';

	/**
	 * Loads the module
	 *
	 * @since 6.2
	 * @access protected
	 */
	protected function load() {
		add_action( 'tml_activate_themed-profiles/themed-profiles.php',  array( &$this, 'activate' ) );
		add_action( 'tml_uninstall_themed-profiles/themed-profiles.php', array( &$this, 'uninstall' ) );

		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		add_action( 'admin_init', array( &$this, 'admin_init' ) );
	}

	/**
	 * Returns default options
	 *
	 * @since 6.3
	 * @access public
	 */
	public function default_options() {
		return Theme_My_Login_Themed_Profiles::default_options();
	}

	/**
	 * Activates the module
	 *
	 * Callback for "tml_activate_themed-profiles/themed-profiles.php" hook in method Theme_My_Login_Modules_Admin::activate_module()
	 *
	 * @see Theme_My_Login_Modules_Admin::activate_module()
	 * @since 6.0
	 * @access public
	 */
	public function activate() {
		$this->save_options();
	}

	/**
	 * Uninstalls the module
	 *
	 * Callback for "tml_uninstall_themed-profiles/themed-profiles.php" hook in method Theme_My_Login_Admin::uninstall()
	 *
	 * @see Theme_My_Login_Admin::uninstall()
	 * @since 6.3
	 * @access public
	 */
	public function uninstall() {
		delete_option( $this->options_key );
	}

	/**
	 * Adds "Themed Profiles" tab to Theme My Login menu
	 *
	 * Callback for "admin_menu" hook
	 *
	 * @since 6.3
	 * @access public
	 */
	public function admin_menu() {
		global $theme_my_login;

		add_submenu_page(
			'theme_my_login',
			__( 'Theme My Login Themed Profiles Settings', 'theme-my-login' ),
			__( 'Themed Profiles', 'theme-my-login' ),
			'manage_options',
			$this->options_key,
			array( &$this, 'settings_page' )
		);

		add_settings_section( 'general', null, '__return_false', $this->options_key );

		add_settings_field( 'themed_profiles', __( 'Themed Profiles',       'theme-my-login' ), array( &$this, 'settings_field_themed_profiles'       ), $this->options_key, 'general' );
		add_settings_field( 'restrict_admin',  __( 'Restrict Admin Access', 'theme-my-login' ), array( &$this, 'settings_field_restrict_admin_access' ), $this->options_key, 'general' );

		if ( $theme_my_login->is_module_loaded( 'custom-permalinks' ) ) {
			$custom_permalinks_admin =& $theme_my_login->get_module( 'custom-permalinks-admin' );
			add_settings_field( 'profile', __( 'Profile' ), array( &$custom_permalinks_admin, 'settings_field_permalink' ), 'theme_my_login_permalinks', 'general', array( 'action' => 'profile' ) );
		}
	}

	/**
	 * Registers options group
	 *
	 * Callback for "admin_init" hook
	 *
	 * @since 6.3
	 * @access public
	 */
	public function admin_init() {
		register_setting( $this->options_key, $this->options_key, array( &$this, 'save_settings' ) );
	}

	/**
	 * Renders settings page
	 *
	 * Callback for add_submenu_page()
	 *
	 * @since 6.3
	 * @access public
	 */
	public function settings_page() {
		Theme_My_Login_Admin::settings_page( array(
			'title'       => __( 'Theme My Login Themed Profiles Settings', 'theme-my-login' ),
			'options_key' => $this->options_key
		) );
	}

	/**
	 * Renders Themed Profiles settings field
	 *
	 * @since 6.3
	 * @access public
	 */
	public function settings_field_themed_profiles() {
		global $wp_roles;

		foreach ( $wp_roles->get_names() as $role => $role_name ) {
			if ( 'pending' == $role )
				continue;
			?>
            <input name="<?php echo $this->options_key; ?>[<?php echo $role; ?>][theme_profile]" type="checkbox" id="<?php echo $this->options_key; ?>_<?php echo $role; ?>_theme_profile" value="1"<?php checked( $this->get_option( array( $role, 'theme_profile' ) ) ); ?> />
            <label for="<?php echo $this->options_key; ?>_<?php echo $role; ?>_theme_profile"><?php echo $role_name; ?></label><br />
    		<?php 
    	}
	}

	/**
	 * Renders Restrict Admin Access settings field
	 *
	 * @since 6.3
	 * @access public
	 */
	public function settings_field_restrict_admin_access() {
		global $wp_roles;

		foreach ( $wp_roles->get_names() as $role => $role_name ) {
			if ( 'pending' == $role )
				continue;
			?>
			<input name="<?php echo $this->options_key; ?>[<?php echo $role; ?>][restrict_admin]" type="checkbox" id="<?php echo $this->options_key; ?>_<?php echo $role; ?>_restrict_admin" value="1"<?php checked( $this->get_option( array( $role, 'restrict_admin' ) ) ); ?><?php if ( 'administrator' == $role ) echo ' disabled="disabled"'; ?> />
			<label for="<?php echo $this->options_key; ?>_<?php echo $role; ?>_restrict_admin"><?php echo $role_name; ?></label><br />
			<?php
		}
	}

	/**
	 * Outputs HTML for "Permalinks" settings tab
	 *
	 * @since 6.2
	 * @access public
	 */
	public function settings_field_permalink( $args = '' ) {
		global $theme_my_login_custom_permalinks_admin;
		$theme_my_login_custom_permalinks_admin->settings_field_permalink( array(
			'action' => 'profile'
		) );
	}

	/**
	 * Sanitizes settings
	 *
	 * Callback for register_setting()
	 *
	 * @since 6.2
	 * @access public
	 *
	 * @param array $settings Settings passed in from filter
	 * @return array Sanitized settings
	 */
	public function save_settings( $settings ) {
		global $wp_roles;

		foreach( $wp_roles->get_names() as $role => $role_name ) {
			if ( 'pending' != $role ) {
				$settings[$role] = array(
					'theme_profile'  => isset( $settings[$role]['theme_profile']  ),
					'restrict_admin' => isset( $settings[$role]['restrict_admin'] )
				);
			}
		}
		return $settings;
	}
}
endif; // Class exists

