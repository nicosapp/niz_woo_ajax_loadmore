<?php

if (!defined('ABSPATH')){
    exit("Do not access this file directly.");
}

class Nyz_Woo_Ajax_Dependency_Checker {
	private $_message;
	/**
	 * Define the plugins that our plugin requires to function.
	 * Array format: 'Plugin Name' => 'Path to main plugin file'
	 */
	const REQUIRED_PLUGINS = array(
		'WooCommerce' => 'woocommerce/woocommerce.php',
	);

	/**
	 * Check if all required plugins are active, otherwise throw an exception.
	 *
	 * @throws My_Plugin_Name_Missing_Dependencies_Exception
	 */
	public function check() {
		$missing_plugins = $this->get_missing_plugins_list();
		if ( ! empty( $missing_plugins ) ) {
			$this->$_message=$this->plugins_missing_message($missing_plugins);
			add_action( 'admin_notices', array( $this, 'display_admin_notice' ) );
			return false;
		}
		return true;
	}

	/**
	 * @return string[] Names of plugins that we require, but that are inactive.
	 */
	private function get_missing_plugins_list() {
		$missing_plugins = array();
		foreach ( self::REQUIRED_PLUGINS as $plugin_name => $main_file_path ) {
			if ( ! $this->is_plugin_active( $main_file_path ) ) {
				$missing_plugins[] = $plugin_name;
			}
		}
		return $missing_plugins;
	}

	/**
	 * @param string $main_file_path Path to main plugin file, as defined in self::REQUIRED_PLUGINS.
	 *
	 * @return bool
	 */
	private function is_plugin_active( $main_file_path ) {
		return in_array( $main_file_path, $this->get_active_plugins() );
	}

	/**
	 * @return string[] Returns an array of active plugins' main files.
	 */
	private function get_active_plugins() {
		return apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
	}

	private function plugins_missing_message($missing_plugin_names){
		// $plugin_data = get_plugin_data( __FILE__ );
		$plugin_name = NIZ_WOO_AJLOAD_PLUGIN_NAME;
		ob_start(); ?>
		<div class="error notice">
		    <p>
		        <strong>Error:</strong>
		        The <em><?php echo $plugin_name; ?></em> plugin won't execute
		        because the following required plugins are not active:
				<strong><?php echo esc_html( implode( ', ', $missing_plugin_names ) ) ?></strong>.
		        Please activate these plugins.
		    </p>
		</div><?php
		return ob_get_clean();
	}

	public function display_admin_notice(){
		echo $this->$_message;
	}

}