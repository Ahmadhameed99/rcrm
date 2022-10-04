<?php $today = get_today_date(); ?>
<?php echo form_open(get_uri("polls/save"), array("id" => "poll-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />

        <div class="form-group">
            <div class="row">
                <label for="title" class="col-md-3"><?php echo app_lang('title'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "title",
                        "name" => "title",
                        "value" => $model_info->title,
                        "class" => "form-control",
                        "placeholder" => app_lang('polls_what_do_you_want_to_ask'),
                        "autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="description" class=" col-md-3"><?php echo app_lang('description'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_textarea(array(
                        "id" => "description",
                        "name" => "description",
                        "value" => "$model_info->description",
                        "class" => "form-control",
                        "placeholder" => app_lang('description'),
                        "data-rich-text-editor" => true
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="poll_expire_at" class=" col-md-3"><?php echo app_lang('polls_expire_at'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "poll_expire_at",
                        "name" => "poll_expire_at",
                        "value" => $model_info->expire_at,
                        "class" => "form-control",
                        "placeholder" => app_lang('polls_expire_at'),
                        "autocomplete" => "off",
                        "data-rule-greaterThanOrEqual" => "$today",
                        "data-msg-greaterThanOrEqual" => app_lang('polls_expire_date_must_be_equal_or_greater_than_today')
                    ));
                    ?>
                </div>
            </div>
        </div>
        <?php if ($model_info->id) { ?>
            <div class="form-group">
                <div class="row">
                    <label for="poll_answer" class="col-md-3"><?php echo app_lang('options'); ?></label>
                    <div class="col-md-9">
                        <?php
                        foreach ($poll_answers as $poll_answer) {
                            ?>
                            <?php $delete = ajax_anchor(get_uri("polls/delete_poll_answer/$poll_answer->id"), "<div class='float-end'><i data-feather='x' class='icon-16'></i></div>", array("class" => "delete-checklist-item text-default", "title" => app_lang("polls_delete_poll_answer"), "data-fade-out-on-success" => "#poll-answer-$poll_answer->id")); ?>
                            <div id='poll-answer-<?php echo $poll_answer->id; ?>' class='b-a rounded p-2 mb5 ps-3 pe-3'>
                                <?php echo $poll_answer->title . $delete; ?>
                            </div>
                            <?php
                        };
                        ?>

                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<div class="modal-footer">
    <div id="link-of-options-view" class="hide">
        <?php echo modal_anchor(get_uri("polls/add_answer"), "", array()); ?>
    </div>

    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('cancel'); ?></button>
    <?php if ($model_info->id) { ?>
        <button type="submit" class="btn btn-primary poll-create-button"><span data-feather="bar-chart-2" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
    <?php } else { ?>
        <button id="save-and-add-option-button" type="button" class="btn btn-primary poll-create-button"><span data-feather="chevrons-right" class="icon-16"></span> <?php echo app_lang('polls_save_and_add_options'); ?></button>
    <?php } ?>
</div>

<?php echo form_close(); ?>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        setTimeout(function () {
            $("#title").focus();
        }, 200);

        setDatePicker("#poll_expire_at");

<?php if (!$model_info->id) { ?>
            //disable poll create button until type anything in title
            $('.poll-create-button').prop('disabled', true);

            $('#poll-form').on("keyup", function () {
                if ($("#title").val() !== '') {
                    $('.poll-create-button').prop('disabled', false);
                } else {
                    $('.poll-create-button').prop('disabled', true);
                }
            });
<?php } ?>

        //reopened add option modal when save poll
        window.showAddNewModal = false;

        $("#save-and-add-option-button").on("click", function () {
            window.showAddNewModal = true;
            $(this).trigger("submit");
        });

        var optionViewText = "<?php echo app_lang('polls_add_options') ?>";

        window.pollForm = $("#poll-form").appForm({
            closeModalOnSuccess: false,
            onSuccess: function (result) {
                if (window.showAddNewModal) {
                    var $optionViewLink = $("#link-of-options-view").find("a");
                    var $saveAndAddOptionBtn = $("#save-and-add-option-button");
                    $optionViewLink.attr("data-action-url", "<?php echo get_uri("polls/add_answer"); ?>");
                    $optionViewLink.attr("data-title", optionViewText);
                    $optionViewLink.attr("data-post-id", result.id);

                    $saveAndAddOptionBtn.remove();

                    $optionViewLink.trigger("click");
                } else {
                    window.pollForm.closeModal();
                }
                if (result.success) {
                    $("#poll-table").appTable({newData: result.data, dataId: result.id});
                }
            }
        });

    });
</script>