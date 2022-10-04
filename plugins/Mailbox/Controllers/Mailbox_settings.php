<?php

namespace Mailbox\Controllers;

use App\Controllers\Security_Controller;
use Mailbox\Libraries\Mailbox_Imap;

class Mailbox_settings extends Security_Controller {

    protected $Mailbox_settings_model;
    protected $Mailboxes_model;

    function __construct() {
        parent::__construct();
        $this->access_only_admin_or_settings_admin();
        $this->Mailbox_settings_model = new \Mailbox\Models\Mailbox_settings_model();
        $this->Mailboxes_model = new \Mailbox\Models\Mailboxes_model();
    }

    function index() {
        return $this->template->rander("Mailbox\Views\settings\index");
    }

    function modal_form() {
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['model_info'] = $this->Mailboxes_model->get_one($this->request->getPost('id'));

        return $this->template->view('Mailbox\Views\settings\modal_form', $view_data);
    }

    function save() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "title" => "required",
        ));

        $id = $this->request->getPost('id');

        $data = array(
            "color" => $this->request->getPost('color'),
            "title" => $this->request->getPost('title')
        );

        $save_id = $this->Mailboxes_model->ci_save($data, $id);

        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    function list_data() {
        $list_data = $this->Mailboxes_model->get_details()->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _row_data($id) {
        $options = array("id" => $id);
        $data = $this->Mailboxes_model->get_details($options)->getRow();
        return $this->_make_row($data);
    }

    private function _make_row($data) {
        $options = modal_anchor(get_uri("mailbox_settings/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('mailbox_edit_mailbox'), "data-post-id" => $data->id));

        $options .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('mailbox_delete_mailbox'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("mailbox_settings/delete"), "data-action" => "delete"));

        $status = "<span class='mt0 badge mailbox-badge-alert'>" . app_lang("unauthorized") . "</span>";
        if ($data->imap_authorized) {
            $status = "<span class='mt0 badge bg-success'>" . app_lang("authorized") . "</span>";
        }

        return array(
            "<span style='background-color:" . $data->color . "' class='color-tag float-start'></span>" . $data->title,
            modal_anchor(get_uri("mailbox_settings/imap_settings_modal_form"), "<i data-feather='settings' class='icon-16'></i> " . "IMAP " . strtolower(app_lang("settings")), array("title" => "IMAP " . strtolower(app_lang("settings")), "data-post-id" => $data->id, "class" => "mailbox-mr30")) .
            modal_anchor(get_uri("mailbox_settings/other_settings_modal_form"), "<i data-feather='settings' class='icon-16'></i> " . app_lang("mailbox_other_settings"), array("title" => app_lang("mailbox_other_settings"), "data-post-id" => $data->id)),
            $status,
            $options
        );
    }

    function delete() {
        $this->validate_submitted_data(array(
            "id" => "numeric|required"
        ));

        $id = $this->request->getPost('id');
        if ($this->request->getPost('undo')) {
            if ($this->Mailboxes_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Mailboxes_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }

    function imap_settings_modal_form() {
        $this->validate_submitted_data(array(
            "id" => "numeric|required"
        ));

        $view_data['model_info'] = $this->Mailboxes_model->get_one($this->request->getPost('id'));

        return $this->template->view('Mailbox\Views\settings\imap_settings_modal_form', $view_data);
    }

    function save_imap_settings() {
        $this->validate_submitted_data(array(
            "id" => "numeric|required",
            "imap_encryption" => "required",
            "imap_host" => "required",
            "imap_port" => "required",
            "imap_email" => "required",
            "imap_password" => "required",
        ));

        $id = $this->request->getPost('id');
        $mailbox_info = $this->Mailboxes_model->get_one($id);

        $settings = array("imap_encryption", "imap_host", "imap_port", "imap_email", "imap_password");
        $data = array();

        foreach ($settings as $setting) {
            $value = $this->request->getPost($setting);

            //if user change credentials, flag as unauthorized
            if ($mailbox_info->imap_authorized && (($setting == "imap_password" && decode_password($mailbox_info->imap_password, "imap_password") != $value) || $mailbox_info->$setting != $value)) {
                $data["imap_authorized"] = 0;
            }

            if ($setting == "imap_password") {
                $value = encode_id($value, "imap_password");
            }

            if (is_null($value)) {
                $value = "";
            }

            $data[$setting] = $value;
        }

        //reset failed login attempts count after running from settings page
        $data["imap_failed_login_attempts"] = 0;

        $save_id = $this->Mailboxes_model->ci_save($data, $id);

        if ($save_id) {
            //authorize imap
            $imap = new Mailbox_Imap();
            if (!$imap->authorize_imap_and_get_inbox($id)) {
                echo json_encode(array("success" => false, 'message' => app_lang("imap_error_credentials_message")));
                exit();
            }

            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    function other_settings_modal_form() {
        $this->validate_submitted_data(array(
            "id" => "numeric|required"
        ));

        $view_data['model_info'] = $this->Mailboxes_model->get_one($this->request->getPost('id'));

        $team_members = $this->Users_model->get_all_where(array("deleted" => 0, "user_type" => "staff", "is_admin" => 0))->getResult();
        $members_dropdown = array();

        foreach ($team_members as $team_member) {
            $members_dropdown[] = array("id" => $team_member->id, "text" => $team_member->first_name . " " . $team_member->last_name);
        }

        $view_data['members_dropdown'] = json_encode($members_dropdown);

        return $this->template->view('Mailbox\Views\settings\other_settings_modal_form', $view_data);
    }

    function save_other_settings() {
        $this->validate_submitted_data(array(
            "id" => "numeric|required",
        ));

        $id = $this->request->getPost('id');
        $data = array(
            "permitted_users" => $this->request->getPost("permitted_users") ? $this->request->getPost("permitted_users") : "",
            "signature" => $this->request->getPost("signature") ? $this->request->getPost("signature") : "",
            "send_bcc_to" => $this->request->getPost("send_bcc_to") ? $this->request->getPost("send_bcc_to") : "",
        );

        $save_id = $this->Mailboxes_model->ci_save($data, $id);

        if ($save_id) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

}
