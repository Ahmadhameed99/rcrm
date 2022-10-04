<?php

namespace Polls\Models;

class Poll_answers_model extends \App\Models\Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'poll_answers';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $poll_answers_table = $this->db->prefixTable('poll_answers');
        $Polls_table = $this->db->prefixTable('polls');
        $Votes_table = $this->db->prefixTable('poll_votes');
        $users_table = $this->db->prefixTable('users');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $poll_answers_table.id=$id";
        }

        $poll_id = get_array_value($options, "poll_id");
        if ($poll_id) {
            $where = " AND $poll_answers_table.poll_id=$poll_id";
        }

        $extra_select = "";
        $login_user_id = get_array_value($options, "login_user_id");
        if ($login_user_id) {
            $extra_select = ", (SELECT count($Votes_table.id) FROM $Votes_table WHERE $Votes_table.poll_answer_id=$poll_answers_table.id AND $Votes_table.created_by=$login_user_id AND $Votes_table.deleted=0) as vote_status";
        }

        $sql = "SELECT $poll_answers_table.*,
        (SELECT GROUP_CONCAT(' ', $users_table.first_name, ' ', $users_table.last_name) 
                    FROM $users_table
                    WHERE $users_table.deleted=0 AND $users_table.user_type!='lead' AND $users_table.id IN(SELECT $Votes_table.created_by FROM $Votes_table WHERE $Votes_table.deleted=0 AND $Votes_table.poll_answer_id=$poll_answers_table.id)) AS poll_voters,
        (SELECT COUNT($Votes_table.id) as total_vote FROM $Votes_table WHERE $Votes_table.poll_answer_id=$poll_answers_table.id AND $Votes_table.deleted=0) AS total_vote $extra_select
        FROM $poll_answers_table
        LEFT JOIN $Polls_table ON $Polls_table.id= $poll_answers_table.poll_id
        WHERE $poll_answers_table.deleted=0 $where";

        return $this->db->query($sql);
    }

}
