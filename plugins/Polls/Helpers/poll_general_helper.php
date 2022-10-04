<?php

use App\Controllers\App_Controller;

/**
 * get the defined config value by a key
 * @param string $key
 * @return config value
 */
if (!function_exists('get_poll_setting')) {

    function get_poll_setting($key = "") {
        $Poll_settings_model = new \Polls\Models\Poll_settings_model();
        return $Poll_settings_model->get_poll_setting($key);
    }

}

/**
 * count active polls
 * @return value
 */
if (!function_exists('polls_count_active_polls')) {

    function polls_count_active_polls() {
        $Polls_model = new \Polls\Models\Polls_model();

        $active_polls = $Polls_model->count_active_polls();
        return $active_polls;
    }

}