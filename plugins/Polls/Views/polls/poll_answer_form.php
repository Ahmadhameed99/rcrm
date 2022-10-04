<?php echo form_open(get_uri("polls/save_poll_answers"), array("id" => "poll_answers_form", "class" => "general-form", "role" => "form")); ?>
<div class="col-md-12 mb15">
    <input type="hidden" name="poll_id" value="<?php echo $model_info->id; ?>" />
    <div class="poll-answers" id="poll-answers">

    </div>

    <div class="form-group">
        <div class="mt5 p0">
            <?php
            echo form_input(array(
                "id" => "poll-answer",
                "name" => "poll-answer",
                "class" => "form-control",
                "placeholder" => app_lang('add_item'),
                "data-rule-required" => true,
                "data-msg-required" => app_lang("field_required")
            ));
            ?>
        </div>
    </div>
    <div id="poll-answer-panel" class="mb15 p0 hide">
        <button type="submit" class="btn btn-primary mr10"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('add'); ?></button> 
        <button id="poll-answer-panel-close" type="button" class="btn btn-default"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('cancel'); ?></button>
    </div>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        //poll answers section

        //show poll answers
        $("#poll-answers").html(<?php echo ""; ?>);

        //show save & cancel button when the poll-answer-form is focused
        $("#poll-answer").on("focus", function () {
            $("#poll-answer-panel").removeClass("hide");
            $("#poll-answer-error").removeClass("hide");
        });

        $("#poll-answer-panel-close").on("click", function () {
            $("#poll-answer-panel").addClass("hide");
            $("#poll-answer-error").addClass("hide");
            $("#poll-answer").val("");
        });

        $("#poll_answers_form").appForm({
            isModal: false,
            onSuccess: function (response) {
                $("#poll-answer").val("");
                $("#poll-answer").focus();
                $("#poll-answers").append(response.data);
            }
        });

    });
</script>