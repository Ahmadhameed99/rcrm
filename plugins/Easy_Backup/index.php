<?php

defined('PLUGINPATH') or exit('No direct script access allowed');

/*
  Plugin Name: Easy Backup 
  Description: Hassle-free backups for RISE CRM.
  Version: 1.0
  Requires at least: 2.8
  Author: ClassicCompiler
  Author URL: https://codecanyon.net/user/classiccompiler
 */

//add admin setting menu item
app_hooks()->add_filter('app_filter_admin_settings_menu', function($settings_menu) {
    $settings_menu["setup"][] = array("name" => "easy_backup", "url" => "easy_backup/settings");
    return $settings_menu;
});

//add setting link to the plugin setting
app_hooks()->add_filter('app_filter_action_links_of_Easy_Backup', function ($action_links_array) {
    $action_links_array = array(
        anchor(get_uri("easy_backup/settings"), app_lang("settings"))
    );

    return $action_links_array;
});

//install dependencies
register_installation_hook("Easy_Backup", function ($item_purchase_code) {
    include PLUGINPATH . "Easy_Backup/install/do_install.php";
});

//uninstallation: remove data from database
register_uninstallation_hook("Easy_Backup", function () {
    $dbprefix = get_db_prefix();
    $db = db_connect('default');

    $sql_query = "DELETE FROM `" . $dbprefix . "settings` WHERE `" . $dbprefix . "settings`.`setting_name`='easy_backup_item_purchase_code';";
    $db->query($sql_query);
});

//update plugin
use Easy_Backup\Controllers\Easy_Backup_Updates;

register_update_hook("Easy_Backup", function () {
    $update = new Easy_Backup_Updates();
    return $update->index();
});