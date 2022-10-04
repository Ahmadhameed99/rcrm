<?php

namespace Polls\Controllers;

class Polls extends \App\Controllers\Security_Controller {

    protected $Polls_model;
    protected $Poll_answers_model;
    protected $Votes_table;
    protected $Poll_settings_model;

    function __construct() {
        parent::__construct();
        $this->Polls_model = new \Polls\Models\Polls_model();
        $this->Poll_answers_model = new \Polls\Models\Poll_answers_model();
        $this->Votes_model = new \Polls\Models\Votes_model();
        $this->Poll_settings_model = new \Polls\Models\Poll_settings_model();
    }

    //check is user access poll
    private function can_access_poll() {
        //check settings if user is an admin or has permission to access poll
        if ($this->can_create_poll()) {
            return true;
        } else if ($this->can_only_view_poll()) {
            return true;
        } else {
            app_redirect("forbidden");
        }
    }

    //check is user access poll
    private function can_create_poll() {
        $poll_access_permission = get_poll_setting("access_all_members");

        $access_poll_specific_permission = unserialize(get_poll_setting("access_poll_specific"));
        if (!$access_poll_specific_permission) {
            $access_poll_specific_permission = array();
        }

        $poll_access_permission_specific = get_array_value($access_poll_specific_permission, "manage_polls_specific");
        $poll_access_specific = explode(',', $poll_access_permission_specific);

        //check settings if user is an admin or has permission to access poll
        if ($this->login_user->is_admin || $poll_access_permission || in_array($this->login_user->id, $poll_access_specific)) {
            return true;
        }
    }

    //check user can only view polls
    private function can_only_view_poll() {
        $poll_view_permission = get_poll_setting("view_all_members");

        $view_poll_specific_permission = unserialize(get_poll_setting("view_poll_specific"));

        if (!$view_poll_specific_permission) {
            $view_poll_specific_permission = array();
        }

        $poll_view_permission_specific = get_array_value($view_poll_specific_permission, "view_polls_specific");
        $poll_view_specific = explode(',', $poll_view_permission_specific);

        if ($poll_view_permission || in_array($this->login_user->id, $poll_view_specific)) {
            return true;
        }
    }

    //check is user edit poll
    private function can_edit_poll($poll_info) {
        //for add/edit polls, creator, admin and who has permission to access polls 
        if ($this->login_user->id !== $poll_info->created_by && !$this->login_user->is_admin && !$this->can_create_poll()) {
            app_redirect("forbidden");
        }
    }

    //load poll list view
    function index() {
        $this->can_access_poll();

        $view_data["can_create_polls"] = $this->can_create_poll();
        return $this->template->rander("Polls\Views\polls\index", $view_data);
    }

    /* load poll add/edit modal */

    function modal_form() {
        $id = $this->request->getPost('id');
        $view_data['model_info'] = $this->Polls_model->get_one($id);

        $this->can_edit_poll($view_data['model_info']);

        $view_data['poll_answers'] = $this->Poll_answers_model->get_details(array("poll_id" => $id, "login_user_id" => $this->login_user->id))->getResult();

        return $this->template->view('Polls\Views\polls\modal_form', $view_data);
    }

    /* add or edit a poll */

    function save() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "title" => "required"
        ));

        $this->can_create_poll();

        $id = $this->request->getPost('id');
        $expire_at = $this->request->getPost('poll_expire_at');

        $data = array(
            "title" => $this->request->getPost('title'),
            "description" => $this->request->getPost('description')
        );

        if ($expire_at) {
            $data["expire_at"] = $expire_at;
        }

        if ($id) {
            //saving existing poll
            $this->Polls_model->get_one($id);
        } else {
            $data['created_by'] = $this->login_user->id;
            $data['created_at'] = get_current_utc_time();
        }

        $save_id = $this->Polls_model->ci_save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* delete a poll */

    function delete() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $this->can_create_poll();

        $id = $this->request->getPost('id');

        if ($this->Polls_model->delete($id)) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

    /* list of polls, prepared for datatable  */

    function list_data() {
        $status = $this->request->getPost("status");
        $options = array("status" => $status);

        $list_data = $this->Polls_model->get_details($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of poll list  table */

    function _row_data($id) {
        $data = $this->Polls_model->get_details(array("id" => $id))->getRow();
        return $this->_make_row($data);
    }

    /* prepare a row of poll list table */

    function _make_row($data) {
        $title = modal_anchor(get_uri("polls/view/" . $data->id), $data->title, array("title" => app_lang('poll') . " #$data->id", "data-modal-title" => app_lang('poll') . " #$data->id", "data-modal-lg" => 1, "data-post-id" => $data->id));

        $image_url = get_avatar($data->created_by_avater);
        $created_by_user = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span> $data->created_by_user";
        $created_by = get_team_member_profile_link($data->created_by, $created_by_user);


        $expire_at = "-";
        if ($data->expire_at && is_date_exists($data->expire_at)) {
            $expire_at = format_to_date($data->expire_at, false);
            if (get_my_local_time("Y-m-d") > $data->expire_at) {
                $expire_at = "<span class='text-danger'>" . $expire_at . "</span> ";
            } else if (get_my_local_time("Y-m-d") == $data->expire_at && $data->status != "inactive") {
                $expire_at = "<span class='text-warning'>" . $expire_at . "</span> ";
            }
        }

        $poll_status_class = "bg-danger";
        if ($data->status == "active") {
            $poll_status_class = "bg-success";
        }

        $poll_status = "<span class='badge $poll_status_class large'>" . app_lang($data->status) . "</span> ";

        $actions = modal_anchor(get_uri("polls/view/" . $data->id), "<i data-feather='cloud-lightning' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('poll') . " #$data->id", "data-modal-title" => app_lang('poll') . " #$data->id", "data-modal-lg" => 1, "data-post-id" => $data->id));
        if ($data->created_by == $this->login_user->id || $this->login_user->is_admin || $this->can_create_poll()) {
            $actions = modal_anchor(get_uri("polls/view/" . $data->id), "<i data-feather='cloud-lightning' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('poll') . " #$data->id", "data-modal-title" => app_lang('poll') . " #$data->id", "data-modal-lg" => 1, "data-post-id" => $data->id))
                    . modal_anchor(get_uri("polls/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('polls_edit_poll'), "data-post-id" => $data->id))
                    . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('polls_delete_poll'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("polls/delete"), "data-action" => "delete-confirmation"));
        }

        return array(
            $title,
            $created_by,
            format_to_relative_time($data->created_at),
            $expire_at,
            $poll_status,
            $actions
        );
    }

    /* load poll details view */

    function view() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        $view_data = $this->_make_view_data($id);

        return $this->template->view('Polls\Views\polls\view', $view_data);
    }

    /* load add poll answer modal */

    function add_answer() {
        $id = $this->request->getPost('id');

        $view_data['model_info'] = $this->Polls_model->get_one($id);
        return $this->template->view('Polls\Views\polls\add_answer', $view_data);
    }

    /* add a poll answer */

    function save_poll_answers() {
        $poll_id = $this->request->getPost('poll_id');

        $this->validate_submitted_data(array(
            "poll_id" => "required|numeric"
        ));

        $data = array(
            "poll_id" => $poll_id,
            "title" => $this->request->getPost("poll-answer")
        );

        $save_id = $this->Poll_answers_model->ci_save($data);

        if ($save_id) {
            $item_info = $this->Poll_answers_model->get_one($save_id);
            echo json_encode(array("success" => true, "data" => $this->_make_poll_answer_row($item_info), 'id' => $save_id));
        } else {
            echo json_encode(array("success" => false));
        }
    }

    /* prepare a item of poll answer in poll view */

    function _make_poll_answer_row($data = array()) {
        $title = "<span class='font-13'>" . $data->title . "</span>";

        $poll_answer = "<div class='progress mb15' style='height: 35px;'>
                            <div class='progress-bar bg-info text-default text-left overflow-visible vote-$data->id' role='progressbar' data-percentage='' style='width: 0%' aria-valuenow='' aria-value-min='0' aria-valuemax='100'>
                                <div class='form-check ms-2 poll_answer_id'>
                                    <input type='radio' name='poll_answer_id' value='$data->id' id='poll-$data->id' style='margin-top: 3px' class='form-check-input poll_answer' disabled='true'>
                                    <label for='poll-$data->id' class='font-14 mb0 ms-1'>$title</label>
                                </div>
                            </div>
                        </div>";

        return $poll_answer;
    }

    /* delete a poll answer */

    function delete_poll_answer($id) {
        if ($this->Poll_answers_model->delete($id)) {
            echo json_encode(array("success" => true));
        } else {
            echo json_encode(array("success" => false));
        }
    }

    /* add a vote */

    function save_vote() {
        $poll_id = $this->request->getPost("poll_id");
        $poll_answer_id = $this->request->getPost("poll_answer_id");

        $this->validate_submitted_data(array(
            "poll_answer_id" => "required|numeric"
        ));

        $vote_status = $this->Votes_model->get_details(array("poll_id" => $poll_id, "created_by" => $this->login_user->id))->getRow();

        if (!$this->can_create_poll() && $vote_status) {
            echo json_encode(array("success" => false, 'message' => app_lang('polls_second_time_vote_error')));
            return false;
        }

        $data = array(
            "poll_answer_id" => $poll_answer_id,
        );

        if ($vote_status) {
            //saving existing vote
            $this->Votes_model->ci_save($data, $vote_status->id);
        } else {
            $data['poll_answer_id'] = $poll_answer_id;
            $data['poll_id'] = $poll_id;
            $data['created_by'] = $this->login_user->id;
            $data['created_at'] = get_current_utc_time();

            $this->Votes_model->ci_save($data);
        }

        $view_data = $this->_make_view_data($poll_id);

        return $this->template->view('Polls\Views\polls\view', $view_data);
    }

    /* prepared view data for poll view */

    private function _make_view_data($id) {
        if ($id) {
            $this->Polls_model->increas_poll_view($id);

            $view_data['model_info'] = $this->Polls_model->get_details(array("id" => $id))->getRow();
            $view_data['poll_answers'] = $this->Poll_answers_model->get_details(array("poll_id" => $id, "login_user_id" => $this->login_user->id))->getResult();
            $view_data['vote_info'] = $this->Votes_model->get_details(array("poll_id" => $id, "created_by" => $this->login_user->id))->getRow();
            $view_data['count_total_votes'] = $this->Votes_model->count_total_votes($id);

            $view_data["can_create_polls"] = $this->can_create_poll();
            $view_data["can_access_poll"] = $this->can_access_poll();

            return $view_data;
        } else {
            show_404();
        }
    }

    /* save poll status */

    function save_poll_status($poll_id = 0, $status = "") {
        validate_numeric_value($poll_id);
        if ($poll_id) {
            $data = array(
                "status" => $status
            );

            $save_id = $this->Polls_model->ci_save($data, $poll_id);
            if ($save_id) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        }
    }

}

/* End of file polls.php */
/* Location: ./plugins/polls/controllers/polls.php */