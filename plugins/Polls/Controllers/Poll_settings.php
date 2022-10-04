<?php

namespace Polls\Controllers;

class Poll_settings extends \App\Controllers\Security_Controller {

    protected $Poll_settings_model;

    function __construct() {
        parent::__construct();
        $this->Poll_settings_model = new \Polls\Models\Poll_settings_model();
    }

    //poll settings view
    function index() {
        $poll_permission = unserialize(get_poll_setting("access_poll_specific"));

        if (!$poll_permission) {
            $poll_permission = array();
        }

        $view_data["poll_access_permission_specific"] = get_array_value($poll_permission, "manage_polls_specific");
        $view_data["poll_view_permission_specific"] = get_array_value($poll_permission, "view_polls_specific");

        $view_data["team_members_dropdown"] = $this->get_team_members_dropdown();

        return $this->template->rander("Polls\Views\settings\index", $view_data);
    }

    //save poll settings
    function save_poll_setting() {
        $settings = array("access_all_members", "access_poll_specific", "view_all_members", "view_poll_specific");

        foreach ($settings as $setting) {
            $value = $this->request->getPost($setting);
            if (!$value) {
                $value = "";
            }

            if ($setting == "access_poll_specific" || $setting == "view_poll_specific") {
                $poll_access_permission = $this->request->getPost("access_poll_specific");
                $poll_view_permission = $this->request->getPost("view_poll_specific");

                $poll_access_permission_specific = "";
                $poll_view_permission_specific = "";
                if ($poll_access_permission) {
                    $poll_access_permission_specific = $this->request->getPost("manage_polls_specific");
                }
                if ($poll_view_permission) {
                    $poll_view_permission_specific = $this->request->getPost("view_polls_specific");
                }

                $poll_permission = array(
                    "manage_polls_specific" => $poll_access_permission_specific,
                    "view_polls_specific" => $poll_view_permission_specific
                );

                $value = array("permissions" => serialize($poll_permission));
            }

            $this->Poll_settings_model->save_poll_setting($setting, $value);
        }
        echo json_encode(array("success" => true, 'message' => app_lang('settings_updated')));
    }

}

/* End of file poll_settings.php */
/* Location: ./plugins/polls/controllers/poll_settings.php */