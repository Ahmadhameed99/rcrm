<div class="modal-body">
    <div class="p20">
        <div class="form-group">
            <div class="row">
                <label for="title" class=" col-md-12"><?php echo $model_info->title; ?></label>
            </div>
        </div>
        <div class="pb10 pt10">
            <strong><?php echo app_lang('polls_answers'); ?></strong>
        </div>
        <?php echo view("Polls\Views\polls\\poll_answer_form", array("view_type" => "")); ?>
    </div>
</div>

<div class="modal-footer">
    <div id="link-of-poll-view" class="hide">
        <?php echo modal_anchor(get_uri("polls/view"), "", array()); ?>
    </div>

    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button id="view-poll-button" type="button" class="btn btn-primary"><span data-feather="bar-chart-2" class="icon-16"></span> <?php echo app_lang('polls_view_poll'); ?></button>
</div>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $("#view-poll-button").on("click", function () {
            var $viewPollLink = $("#link-of-poll-view").find("a");
            $viewPollLink.attr("data-post-id", <?php echo $model_info->id; ?>);
            $viewPollLink.attr("data-title", "<?php echo app_lang('poll') . " #$model_info->id"; ?>");

            $viewPollLink.trigger("click");
        });
    });
</script>