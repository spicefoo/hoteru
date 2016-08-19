<?php

class VTSWPVT {

	/*
	 * Function to initialize this plugin
	 */
	public static function init() {
		add_action('admin_enqueue_scripts', 'vtst_LoadVtsTigerScript');
		add_action('admin_menu', 'vtst_Vtstigermenu');
		add_action('user_register', 'vtst_vts_tiger_capture_registering_users');
		add_action('after_plugin_row_VTLC-tiger/vtlc.php', array('VTSWPVT', 'plugin_row'));
		add_action('plugin_action_links_VTLC-tiger/vtlc.php', array('VTSWPVT', 'plugin_settings_link'), 10, 2);
	}

	/*
	 * Function to get the settings link
	 * @$links string URL for the link
	 * @$file string filename for the link
	 * @return string html links
	 */
	public static function plugin_settings_link($links, $file) {

		array_unshift($links, '<a href="' . admin_url("admin.php") . '?page=vtlc">' . __('Settings', 'vtlc') . '</a>');

		return $links;
	}

	/*
	 * Function to get the plugin row
	 * @$plugin_name as string
	 */
	public static function plugin_row($plugin_name) {
		echo '</tr><tr class="plugin-update-tr"><td colspan="3" class="plugin-update"><div class="update-message"> Now get 25% discount for purchasing pro version using the coupon "<b>OFF25WPTIGER</b>" <a href="http://mastersoftwaretechnologies.com//wp-vtiger-pro.html">Purchase pro version now!</a></div></td>';
	}
}
