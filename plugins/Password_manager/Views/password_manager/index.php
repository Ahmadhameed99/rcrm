<div id="page-content" class="page-wrapper clearfix">
    <div class="card clearfix">
        <ul id="password-manager-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs scrollable-tabs border-top-radius title" role="tablist">
            <li class="title-tab"><h4 class="pl15 pt10 pr15"><?php echo app_lang("password_manager_team_passwords"); ?></h4></li>
            <li><a  role="presentation" href="<?php echo_uri("password_manager/general/"); ?>" data-bs-target="#general-tab"> <?php echo app_lang('general'); ?></a></li>
            <!-- <li><a  role="presentation" href=" <?php //echo_uri("password_manager/email/"); ?>" data-bs-target="#email-tab"> <?php // echo app_lang('email'); ?></a></li> -->
            <!-- <li><a  role="presentation" href="<?php //echo_uri("password_manager/credit_card/"); ?>" data-bs-target="#credit-card-tab"><?php //echo app_lang('password_manager_credit_card'); ?></a></li> -->
            <!-- <li><a  role="presentation" href="<?php //echo_uri("password_manager/bank_account/"); ?>" data-bs-target="#bank-account-tab"> <?php //echo app_lang('password_manager_bank_account'); ?></a></li> -->
            <li><a  role="presentation" href="<?php echo_uri("password_manager/software_license/"); ?>" data-bs-target="#software-license-tab"> <?php echo app_lang('password_manager_software_license'); ?></a></li>

            <div class="tab-title clearfix no-border">
                <div class="title-button-group">
                    <?php echo modal_anchor(get_uri("password_manager/general_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('password_manager_add_general'), array("id" => "add-button", "class" => "btn btn-default", "title" => app_lang('password_manager_add_general'))); ?>
                </div>
            </div>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade" id="general-tab"></div>
            <div role="tabpanel" class="tab-pane fade" id="email-tab"></div>
            <div role="tabpanel" class="tab-pane fade" id="credit-card-tab"></div>
            <div role="tabpanel" class="tab-pane fade" id="bank-account-tab"></div>
            <div role="tabpanel" class="tab-pane fade" id="software-license-tab"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        //change the add button attributes on changing tab panel
        var addButton = $("#add-button");
        $(".nav-tabs li").click(function () {
            var activeField = $(this).find("a").attr("data-bs-target");

            //task status
            if (activeField === "#general-tab") {
                addButton.attr("title", "<?php echo app_lang("password_manager_add_general"); ?>");
                addButton.attr("data-title", "<?php echo app_lang("password_manager_add_general"); ?>");
                addButton.attr("data-action-url", "<?php echo get_uri("password_manager/general_modal_form"); ?>");

                addButton.html("<?php echo "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('password_manager_add_general'); ?>");
                feather.replace();
            } else if (activeField === "#email-tab") {
                addButton.attr("title", "<?php echo app_lang("password_manager_add_email"); ?>");
                addButton.attr("data-title", "<?php echo app_lang("password_manager_add_email"); ?>");
                addButton.attr("data-action-url", "<?php echo get_uri("password_manager/email_modal_form"); ?>");

                addButton.html("<?php echo "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('password_manager_add_email'); ?>");
                feather.replace();
            } else if (activeField === "#credit-card-tab") {
                addButton.attr("title", "<?php echo app_lang("password_manager_add_credit_card"); ?>");
                addButton.attr("data-title", "<?php echo app_lang("password_manager_add_credit_card"); ?>");
                addButton.attr("data-action-url", "<?php echo get_uri("password_manager/credit_card_modal_form"); ?>");

                addButton.html("<?php echo "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('password_manager_add_credit_card'); ?>");
                feather.replace();
            } else if (activeField === "#bank-account-tab") {
                addButton.attr("title", "<?php echo app_lang("password_manager_add_bank_account"); ?>");
                addButton.attr("data-title", "<?php echo app_lang("password_manager_add_bank_account"); ?>");
                addButton.attr("data-action-url", "<?php echo get_uri("password_manager/bank_account_modal_form"); ?>");

                addButton.html("<?php echo "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('password_manager_add_bank_account'); ?>");
                feather.replace();
            } else if (activeField === "#software-license-tab") {
                addButton.attr("title", "<?php echo app_lang("password_manager_add_software_license"); ?>");
                addButton.attr("data-title", "<?php echo app_lang("password_manager_add_software_license"); ?>");
                addButton.attr("data-action-url", "<?php echo get_uri("password_manager/software_license_modal_form"); ?>");

                addButton.html("<?php echo "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('password_manager_add_software_license'); ?>");
                feather.replace();
            }
        });
    });
</script>