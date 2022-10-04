<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "polls";
            echo view("settings/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">
            <?php echo form_open(get_uri("poll_settings/save_poll_setting"), array("id" => "polls-settings-form", "class" => "general-form dashed-row", "role" => "form")); ?>
            <div class="card">
                <div class="page-title clearfix">
                    <h4><?php echo app_lang("polls"); ?></h4>
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <div class="row">
                            <label for="can_access_poll" class="col-md-2 col-xs-8 col-sm-4"><?php echo app_lang('polls_who_can_manage_polls'); ?></label>
                            <div class="col-md-10 col-xs-4 col-sm-8">
                                <div>
                                    <?php
                                    echo form_checkbox("access_all_members", "1", get_poll_setting("access_all_members") ? true : false, "id='can_manage_all_members' class='manage_poll_section form-check-input'");
                                    ?>
                                    <label for="can_manage_all_members"><?php echo app_lang("polls_all_members"); ?></label>
                                </div>
                                <div class="form-group pb0 mb0 no-border access_poll_specific_section">
                                    <?php
                                    echo form_checkbox("access_poll_specific", "1", get_setting("access_poll_specific") ? true : false, "id='access_poll_specific' class='toggle_specific form-check-input'");
                                    ?>
                                    <label for="access_poll_specific"><?php echo app_lang("polls_specific_members"); ?></label>
                                    <div class="specific_dropdown">
                                        <input type="text" value="<?php echo $poll_access_permission_specific; ?>" name="manage_polls_specific" id="access_poll_specific_dropdown" class="w100p validate-hidden"  data-rule-required="true" data-msg-required="<?php echo app_lang('field_required'); ?>" placeholder="<?php echo app_lang('polls_choose_members'); ?>"  />    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group can_view_poll_section">
                        <div class="row">
                            <label class="col-md-2 col-xs-8 col-sm-4"><?php echo app_lang('polls_who_can_view_polls'); ?></label>
                            <div class="col-md-10 col-xs-4 col-sm-8">
                                <div>
                                    <?php
                                    echo form_checkbox("view_all_members", "1", get_poll_setting("view_all_members") ? true : false, "id='can_view_all_members' class='view_poll_section form-check-input'");
                                    ?>
                                    <label for="can_view_all_members"><?php echo app_lang("polls_all_members"); ?></label>
                                </div>
                                <div class="form-group pb0 mb0 no-border view_poll_specific_section">
                                    <?php
                                    echo form_checkbox("view_poll_specific", "1", get_setting("view_poll_specific") ? true : false, "id='view_poll_specific' class='view_poll_toggle_specific form-check-input'");
                                    ?>
                                    <label for="view_poll_specific"><?php echo app_lang("polls_specific_members"); ?></label>
                                    <div class="view_polls_specific_dropdown">
                                        <input type="text" value="<?php echo $poll_view_permission_specific; ?>" name="view_polls_specific" id="view_poll_specific_dropdown" class="w100p validate-hidden"  data-rule-required="true" data-msg-required="<?php echo app_lang('field_required'); ?>" placeholder="<?php echo app_lang('polls_choose_members'); ?>"  />    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $("#polls-settings-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});
            }
        });

        $("#access_poll_specific_dropdown, #view_poll_specific_dropdown").select2({
            multiple: true,
            data: <?php echo $team_members_dropdown; ?>
        });

        //manage polls section

        $(".toggle_specific").on("click", function () {
            toggle_specific_dropdown();
        });

        $("#can_manage_all_members").on("click", function () {
            if ($(this).is(":checked")) {
                $(".access_poll_specific_section").addClass("hide");
                $(".can_view_poll_section").addClass("hide");
            } else {
                $(".access_poll_specific_section").removeClass("hide");
                $(".can_view_poll_section").removeClass("hide");
            }
        });

        toggle_specific_dropdown();

        if ("<?php echo $poll_access_permission_specific; ?>") {
            $("#access_poll_specific").trigger("click");
        }

        function toggle_specific_dropdown() {
            $(".specific_dropdown").hide().find("input").removeClass("validate-hidden");

            if ($(".toggle_specific").is(":checked")) {
                var $dropdown = $(".toggle_specific").closest("div").find("div.specific_dropdown");
                $dropdown.show().find("input").addClass("validate-hidden");
            }
        }

        if ($("#can_manage_all_members").is(":checked")) {
            $(".access_poll_specific_section").addClass("hide");
            $(".can_view_poll_section").addClass("hide");
        }

        //view polls section

        $(".view_poll_toggle_specific").on("click", function () {
            view_poll_toggle_specific_dropdown();
        });

        $("#can_view_all_members").on("click", function () {
            if ($(this).is(":checked")) {
                $(".view_poll_specific_section").addClass("hide");
            } else {
                $(".view_poll_specific_section").removeClass("hide");
            }
        });

        view_poll_toggle_specific_dropdown();

        if ("<?php echo $poll_view_permission_specific; ?>") {
            $("#view_poll_specific").trigger("click");
        }

        function view_poll_toggle_specific_dropdown() {
            $(".view_polls_specific_dropdown").hide().find("input").removeClass("validate-hidden");

            if ($(".view_poll_toggle_specific").is(":checked")) {
                var $dropdown = $(".view_poll_toggle_specific").closest("div").find("div.view_polls_specific_dropdown");
                $dropdown.show().find("input").addClass("validate-hidden");
            }
        }

        if ($("#can_view_all_members").is(":checked")) {
            $(".view_poll_specific_section").addClass("hide");
        }

    });
</script>