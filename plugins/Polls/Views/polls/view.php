<?php
$is_disabled = "";

if ($can_create_polls) {
    $is_disabled = "";
} else if ($vote_info->id) {
    $is_disabled = "disabled";
}
?>

<div class="modal-body clearfix">
    <div class="card">
        <div class="row">
            <div class="col-lg-4 order-lg-last">
                <div class="clearfix">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12 mb15 task-title-right d-none">
                                <strong><?php echo $model_info->title; ?></strong>
                            </div>

                            <div class="col-md-12 border-bottom pb-3">
                                <div class="row">
                                    <div class="col-md-6 text-center">
                                        <h5 class="m0"><?php echo $model_info->total_views; ?></h5>
                                        <div class="text-off"><span data-feather="eye" class="icon-14"></span> <?php echo app_lang('polls_views'); ?></div>
                                    </div>
                                    <div class="col-md-6 text-center">
                                        <h5 class="m0">0<?php echo $count_total_votes; ?></h5>
                                        <div class="text-off"><span data-feather="heart" class="icon-14"></span> <?php echo app_lang('polls_votes'); ?></div>
                                    </div>
                                </div>
                            </div>

                            <?php if ($can_create_polls) { ?>
                                <div class="col-md-12">
                                    <?php echo view("Polls\Views\polls\\vote_pie_chart"); ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 mb15">
                <div class="clearfix">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12 mb15 task-title-left">
                                <h5 class="m0"><?php echo $model_info->title; ?></h5>
                            </div>

                            <?php if ($model_info->description) { ?>
                                <div class="col-md-12 mb5">
                                    <?php echo nl2br(link_it($model_info->description)); ?>
                                </div>
                            <?php } ?>

                            <div class="col-md-12">
                                <span class="text-off">
                                    <?php echo app_lang("polls_created_by") . " " . $model_info->created_by_user; ?>
                                </span>

                                <?php if ($model_info->expire_at) { ?>
                                    <span class="text-off float-end">
                                        <?php echo app_lang("polls_will_expire_at") . " " . $model_info->expire_at; ?>
                                    </span>
                                <?php } ?>
                            </div>

                            <!--poll answer-->
                            <div>
                                <div class="col-md-12 mb15">
                                    <?php foreach ($poll_answers as $poll_answer) { ?>
                                        <div class="mt15">
                                            <?php
                                            $total_vote = $poll_answer->total_vote;

                                            if ($total_vote == 0) {
                                                $percentage = "";
                                            } else {
                                                $percentage = round($total_vote / $count_total_votes * 100);
                                            }
                                            ?>

                                            <div class="progress" style="height: 35px;">
                                                <div class="progress-bar bg-info text-default text-left overflow-visible vote-<?php echo $poll_answer->id; ?>" role="progressbar" data-percentage="<?php echo $percentage; ?>" style="width: 0%" aria-valuenow="<?php echo $percentage; ?>" aria-value-min="0" aria-valuemax="100">
                                                    <div class="form-check ms-2 poll_answer_id">
                                                        <?php
                                                        echo form_radio(array(
                                                            "id" => "poll-$poll_answer->id",
                                                            "name" => "poll_answer_id",
                                                            "style" => "margin-top: 3px",
                                                            "class" => "form-check-input poll_answer",
                                                            "$is_disabled" => "true",
                                                                ), $poll_answer->id, $poll_answer->vote_status ? $poll_answer->vote_status : "");
                                                        ?>
                                                        <label for="poll-<?php echo $poll_answer->id; ?>" class="font-14 mb0 ms-1"><?php echo $poll_answer->title; ?></label>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php if ($can_create_polls) { ?>
                                                <div class="vote-result-section hide">
                                                    <?php if ($poll_answer->total_vote) { ?>
                                                        <div class="vote-result mt5">
                                                            <?php echo view("Polls\Views\polls\\vote_result", array("vote_result" => $poll_answer)); ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                </div>

                                <?php
                                if ($login_user->id == $model_info->created_by || $login_user->is_admin) {
                                    echo view("Polls\Views\polls\\poll_answer_form");
                                }
                                ?>

                                <?php echo form_open(get_uri("polls/save_vote"), array("id" => "save_vote_form", "class" => "general-form", "role" => "form")); ?>
                                <input type="hidden" name="poll_id" value="<?php echo $model_info->id; ?>" />
                                <input type="hidden" id="poll_answer_id" name="poll_answer_id" value="" />

                                <div class="col-md-12 mb15">
                                    <?php if ($can_create_polls) { ?>
                                        <div class="show-results-button form-check float-start mt5">
                                            <input class="form-check-input" type="checkbox" id="show-pull-result-checkbox">
                                            <label class="show-results-button form-check-label uppercase text-off" for="show-pull-result-checkbox"><?php echo app_lang('polls_show_results'); ?></label>
                                        </div>
                                    <?php } ?>

                                    <div class="float-end">
                                        <?php echo modal_anchor(get_uri("polls/save_vote"), "<i data-feather='heart' class='icon-16'></i> " . app_lang('polls_vote'), array("class" => "btn btn-success float-start vote-button $is_disabled", "title" => app_lang('polls_vote'), "data-post-poll_id" => "$model_info->id", "data-post-poll_answer_id" => "", "data-modal-lg" => 1)); ?>
                                    </div>
                                </div>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>

    <?php
    if ($model_info->created_by == $login_user->id || $login_user->is_admin || $can_create_polls) {
        echo modal_anchor(get_uri("polls/modal_form/"), "<i data-feather='edit-2' class='icon-16'></i> " . app_lang('polls_edit_poll'), array("class" => "btn btn-default", "data-post-id" => $model_info->id, "title" => app_lang('polls_edit_poll')));

        if ($model_info->status == "inactive") {
            echo ajax_anchor(get_uri("polls/save_poll_status/$model_info->id/active"), "<i data-feather='check-circle' class='icon-16'></i> " . app_lang('polls_mark_as_active'), array("class" => "btn btn-success", "title" => app_lang('polls_mark_as_active'), "data-reload-on-success" => "1"));
        } else {
            echo ajax_anchor(get_uri("polls/save_poll_status/$model_info->id/inactive"), "<i data-feather='x-circle' class='icon-16'></i> " . app_lang('polls_mark_as_inactive'), array("class" => "btn btn-danger", "title" => app_lang('polls_mark_as_inactive'), "data-reload-on-success" => "1"));
        }
    }
    ?>
</div>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $("#save_vote_form").appForm({
            isModal: false,
            onSuccess: function (response) {
                console.log(response);
            }
        });

        $("#show-pull-result-checkbox").on('click', function () {
            if ($(this).prop("checked")) {
<?php foreach ($poll_answers as $poll_answer) { ?>
                    var pollAnsId = <?php echo $poll_answer->id; ?>;
                    var percentage = $(".vote-" + pollAnsId).data("percentage");
                    $(".vote-" + pollAnsId).css("width", percentage + "%");
<?php } ?>
                $(".vote-result-section").removeClass("hide");
            } else {
                $(".progress-bar").css("width", "0%");
                $(".vote-result-section").addClass("hide");
            }
        });

        $(".poll_answer_id").on('change', function () {
            var getPollAnsId = $('input[name="poll_answer_id"]:checked').val();
            $(".vote-button").attr("data-post-poll_answer_id", getPollAnsId);
        });

        $('.vote-button').addClass("disabled");

        $('.poll_answer').on('change', function () {
            $('.vote-button').removeClass("disabled");
        });

    });
</script>