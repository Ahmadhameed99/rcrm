<?php

namespace Config;

$routes = Services::routes();

$polls_namespace = ['namespace' => 'Polls\Controllers'];

$routes->get('polls', 'Polls::index', $polls_namespace);
$routes->post('polls/(:any)', 'Polls::$1', $polls_namespace);
$routes->get('polls/(:any)', 'Polls::$1', $polls_namespace);

$routes->get('poll_settings', 'Poll_settings::index', $polls_namespace);
$routes->post('poll_settings/(:any)', 'Poll_settings::$1', $polls_namespace);
$routes->get('poll_settings/(:any)', 'Poll_settings::$1', $polls_namespace);


$routes->get('poll_updates', 'Poll_Updates::index', $polls_namespace);
$routes->get('poll_updates/(:any)', 'Poll_Updates::$1', $polls_namespace);
