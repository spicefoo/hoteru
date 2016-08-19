<?php

global $wptigermenus;
$wptigermenus = array('vtiger_db_fields' => __('Lead Form Fields'), 'widget_fields' => __('Contact Form Fields'), 'capture_wp_users' => __('Sync WP Users'), 'plugin_settings' => __('Settings'), 'Captcha_settings ' => __('Captcha'), 'vts_tiger_listShortcodes' => __('List Shortcodes'));

function vtst_topnavmenu() {
	global $wptigermenus;
	$class = "";
	$top_nav_menu = "<div class='nav-pills-div '>";
	$top_nav_menu .= '<ul class="nav nav-pills">';
	$top_nav_menu .= '       <ul class="nav nav-tabs top-nav">';
	if (is_array($wptigermenus)) {
		foreach ($wptigermenus as $links => $text) {
			if (!isset($_REQUEST['action']) && ($links == "plugin_settings")) {
				$class = 'button button-default';
			} elseif (isset($_REQUEST['action']) && ($_REQUEST['action'] == $links)) {
				$class = "button button-default";
			} else {
				$class = "button button-primary";
			}

			$top_nav_menu .= '<li > <a href="?page=vtlc&action=' . $links . '" class = "saio_nav_smartbot"><button class="' . $class . '" type="button">' . $text . '</button></a> </li>';
			$class = "";
		}
	}
	$top_nav_menu .= '        </ul>
                        </ul>';
	$top_nav_menu .= '</div>';
	return $top_nav_menu;
}

function vtst_getActionVtsTiger() {
	if (isset($_REQUEST['action'])) {
		$action = $_REQUEST['action'];
	} else {
		$action = 'plugin_settings';
	}
	return $action;
}

function vtst_displaySettings() {
	echo "<h3>Please save the settings first</h3>";
}
