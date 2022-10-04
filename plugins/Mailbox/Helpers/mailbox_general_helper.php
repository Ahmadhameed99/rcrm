<?php

use App\Controllers\Security_Controller;

/**
 * link the css files 
 * 
 * @param array $array
 * @return print css links
 */
if (!function_exists('mailbox_load_css')) {

    function mailbox_load_css(array $array) {
        $version = get_setting("app_version");

        foreach ($array as $uri) {
            echo "<link rel='stylesheet' type='text/css' href='" . base_url($uri) . "?v=$version' />";
        }

        echo view('Mailbox\Views\includes\dark_theme_helper_js');
    }

}

/**
 * get the defined config value by a key
 * @param string $key
 * @return config value
 */
if (!function_exists('get_mailbox_setting')) {

    function get_mailbox_setting($key = "") {
        $config = new Mailbox\Config\Mailbox();

        $setting_value = get_array_value($config->app_settings_array, $key);
        if ($setting_value !== NULL) {
            return $setting_value;
        } else {
            return "";
        }
    }

}

if (!function_exists('prepare_recipients_data')) {

    function prepare_recipients_data($data) {
        if (!$data->recipients) {
            return "-";
        }

        $recipients_data = "";
        $Users_model = model("App\Models\Users_model");

        $recipients = explode(',', $data->recipients);
        foreach ($recipients as $recipient) {
            if (!$recipient) {
                continue;
            }

            $email_data = "";

            if (is_numeric($recipient)) {
                //user is a contact
                $contact_info = $Users_model->get_one($recipient);
                if ($contact_info->user_type === "client") {
                    $email_data = get_client_contact_profile_link($contact_info->id, $contact_info->first_name . " " . $contact_info->last_name, array("title" => $contact_info->email));
                } else if ($contact_info->user_type === "lead") {
                    $email_data = get_lead_contact_profile_link($contact_info->id, $contact_info->first_name . " " . $contact_info->last_name, array("title" => $contact_info->email));
                }
            } else {
                if ($data->creator_name && $data->creator_email) {
                    $email_data = $data->creator_name . " [" . $data->creator_email . "]";
                } else if ($data->creator_email) {
                    $email_data = $data->creator_email;
                } else {
                    $email_data = $recipient;
                }
            }

            if ($recipients_data) {
                $recipients_data .= ", ";
            }

            $recipients_data .= $email_data;
        }

        return $recipients_data;
    }

}

if (!function_exists('mailbox_count_unread_emails')) {

    function mailbox_count_unread_emails() {
        $mailbox_emails_model = new Mailbox\Models\Mailbox_emails_model();
        $allowed_mailboxes_ids = get_allowed_mailboxes_ids();
        return $mailbox_emails_model->count_unread_emails($allowed_mailboxes_ids);
    }

}

//prepare allowed mailbox ids
if (!function_exists('get_allowed_mailboxes_ids')) {

    function get_allowed_mailboxes_ids() {
        $instance = new Security_Controller();
        $options = array(
            "is_admin" => $instance->login_user->is_admin,
            "user_id" => $instance->login_user->id,
        );

        $Mailboxes_model = new \Mailbox\Models\Mailboxes_model();
        $allowed_mailboxes = $Mailboxes_model->get_details($options)->getResult();

        $allowed_mailboxes_ids = array();
        foreach ($allowed_mailboxes as $allowed_mailbox) {
            array_push($allowed_mailboxes_ids, $allowed_mailbox->id);
        }

        return $allowed_mailboxes_ids;
    }

}