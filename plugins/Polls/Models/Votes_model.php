<?php

namespace Polls\Models;

class Votes_model extends \App\Models\Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'poll_votes';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $Votes_table = $this->db->prefixTable('poll_votes');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $Votes_table.id=$id";
        }

        $poll_id = get_array_value($options, "poll_id");
        if ($poll_id) {
            $where .= " AND $Votes_table.poll_id=$poll_id";
        }

        $created_by = get_array_value($options, "created_by");
        if ($created_by) {
            $where .= " AND $Votes_table.created_by=$created_by";
        }

        $sql = "SELECT $Votes_table.*
        FROM $Votes_table
        WHERE $Votes_table.deleted=0 $where";

        return $this->db->query($sql);
    }

    function count_total_votes($poll_id) {
        $Votes_table = $this->db->prefixTable('poll_votes');
        $sql = "SELECT COUNT($Votes_table.id) AS total
        FROM $Votes_table
        WHERE $Votes_table.deleted=0 AND $Votes_table.poll_id=$poll_id";
        return $this->db->query($sql)->getRow()->total;
    }

}
