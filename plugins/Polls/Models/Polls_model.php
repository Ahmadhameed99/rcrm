<?php

namespace Polls\Models;

class Polls_model extends \App\Models\Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'polls';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $Polls_table = $this->db->prefixTable('polls');
        $users_table = $this->db->prefixTable('users');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $Polls_table.id=$id";
        }

        $status = get_array_value($options, "status");
        if ($status) {
            $where = " AND $Polls_table.status='$status'";
        }

        $expire_at = get_array_value($options, "expire_at");
        if ($expire_at) {
            $where = " AND $Polls_table.expire_at>=$expire_at";
        }

        $sql = "SELECT $Polls_table.*, CONCAT($users_table.first_name, ' ',$users_table.last_name) AS created_by_user, $users_table.image as created_by_avater
        FROM $Polls_table
        LEFT JOIN $users_table ON $users_table.id= $Polls_table.created_by
        WHERE $Polls_table.deleted=0 $where";

        return $this->db->query($sql);
    }

    function increas_poll_view($id) {
        $Polls_table = $this->db->prefixTable('polls');

        $sql = "UPDATE $Polls_table
        SET total_views = total_views+1 
        WHERE $Polls_table.id=$id";

        return $this->db->query($sql);
    }

    function count_active_polls() {
        $Polls_table = $this->db->prefixTable('polls');

        $sql = "SELECT COUNT($Polls_table.id) AS total
        FROM $Polls_table
        WHERE $Polls_table.deleted=0 AND $Polls_table.status='active'";
        return $this->db->query($sql)->getRow()->total;
    }

}
