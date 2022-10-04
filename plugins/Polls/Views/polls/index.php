<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix rounded">
            <h1><?php echo app_lang('polls'); ?></h1>
            <?php if ($can_create_polls) { ?>
                <div class="title-button-group">
                    <?php
                    echo modal_anchor(get_uri("polls/modal_form"), "<i data-feather='bar-chart-2' class='icon-16'></i> " . app_lang('polls_create_poll'), array("class" => "btn btn-default", "title" => app_lang('polls_create_poll'), "data-xs-modal" => 1));
                    ?>
                </div>
            <?php } ?>
        </div>
        <div class="table-responsive">
            <table id="poll-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $("#poll-table").appTable({
            source: '<?php echo_uri("polls/list_data") ?>',
            order: [[0, 'desc']],
            radioButtons: [{text: '<?php echo app_lang("active") ?>', name: "status", value: "active", isChecked: true}, {text: '<?php echo app_lang("inactive") ?>', name: "status", value: "inactive", isChecked: false}],
            columns: [
                {title: '<?php echo app_lang("title"); ?>'},
                {title: '<?php echo app_lang("created_by"); ?>', "class": "w200"},
                {title: '<?php echo app_lang("created_date"); ?>', "class": "w200"},
                {title: '<?php echo app_lang("polls_expire_at"); ?>', "class": "w150"},
                {title: '<?php echo app_lang("status"); ?>', "class": "w50"},
                {title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w150"}
            ],
            printColumns: [0, 2, 1, 3, 4],
            xlsColumns: [0, 2, 1, 3, 4]
        });
    });
</script>