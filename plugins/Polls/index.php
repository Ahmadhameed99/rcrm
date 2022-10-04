<?php

//Prevent direct access
defined('PLUGINPATH') or exit('No direct script access allowed');

/*
  Plugin Name: Polls
  Description: Polls module for RISE CRM.
  Version: 1.0
  Requires at least: 2.8
  Author: SketchCode
  Author URL: https://codecanyon.net/user/sketchcode
 */

use App\Controllers\Security_Controller;

app_hooks()->add_filter('app_filter_staff_left_menu', function($sidebar_menu) {
    $poll_access_permission = get_poll_setting("access_all_members");
    $poll_view_permission = get_poll_setting("view_all_members");

    $access_poll_specific_permission = unserialize(get_poll_setting("access_poll_specific"));
    $view_poll_specific_permission = unserialize(get_poll_setting("view_poll_specific"));

    if (!$access_poll_specific_permission) {
        $access_poll_specific_permission = array();
    }
    if (!$view_poll_specific_permission) {
        $view_poll_specific_permission = array();
    }

    $poll_access_permission_specific = get_array_value($access_poll_specific_permission, "manage_polls_specific");
    $poll_access_specific = explode(',', $poll_access_permission_specific);

    $poll_view_permission_specific = get_array_value($view_poll_specific_permission, "view_polls_specific");
    $poll_view_specific = explode(',', $poll_view_permission_specific);

    $instance = new Security_Controller();
    if ($instance->login_user->is_admin || $poll_access_permission || $poll_view_permission || in_array($instance->login_user->id, $poll_access_specific) || in_array($instance->login_user->id, $poll_view_specific)) {
        $sidebar_menu["polls"] = array(
            "name" => "polls",
            "url" => "polls",
            "class" => "bar-chart-2",
            "position" => 6,
            "badge" => polls_count_active_polls(),
            "badge_class" => "bg-primary"
        );
    }

    return $sidebar_menu;
});

app_hooks()->add_filter('app_filter_admin_settings_menu', function($settings_menu) {
    $settings_menu["setup"][] = array("name" => "polls", "url" => "poll_settings");
    return $settings_menu;
});

//installation: install dependencies
register_installation_hook("Polls", function ($item_purchase_code) {
    include PLUGINPATH . "Polls/install/do_install.php";
});


//uninstallation: remove data from database
register_uninstallation_hook("Polls", function () {
    $dbprefix = get_db_prefix();
    $db = db_connect('default');

    $sql_query = "DROP TABLE `" . $dbprefix . "polls`;";
    $db->query($sql_query);

    $sql_query = "DROP TABLE `" . $dbprefix . "poll_answers`;";
    $db->query($sql_query);

    $sql_query = "DROP TABLE `" . $dbprefix . "poll_settings`;";
    $db->query($sql_query);

    $sql_query = "DROP TABLE `" . $dbprefix . "poll_votes`;";
    $db->query($sql_query);

    $sql_query = "DELETE FROM `" . $dbprefix . "settings` WHERE `" . $dbprefix . "settings`.`setting_name`='polls_item_purchase_code';";
    $db->query($sql_query);
});

//update plugin
use Polls\Controllers\Poll_Updates;

register_update_hook("Polls", function () {
    $update = new Poll_Updates();
    return $update->index();
});
