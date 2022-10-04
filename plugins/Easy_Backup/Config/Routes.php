<?php

namespace Config;

$routes = Services::routes();

$routes->get('easy_backup', 'Easy_Backup::index', ['namespace' => 'Easy_Backup\Controllers']);
$routes->get('easy_backup/(:any)', 'Easy_Backup::$1', ['namespace' => 'Easy_Backup\Controllers']);
$routes->add('easy_backup/(:any)', 'Easy_Backup::$1', ['namespace' => 'Easy_Backup\Controllers']);

$routes->get('easy_backup_updates', 'Easy_Backup_Updates::index', ['namespace' => 'Easy_Backup\Controllers']);
$routes->get('easy_backup_updates/(:any)', 'Easy_Backup_Updates::$1', ['namespace' => 'Easy_Backup\Controllers']);