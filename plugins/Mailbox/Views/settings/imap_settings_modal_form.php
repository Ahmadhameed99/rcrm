<?php echo form_open(get_uri("mailbox_settings/save_imap_settings"), array("id" => "mailbox-settings-form", "class" => "general-form bg-white", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
        <div class="form-group">
            <div class="row">
                <label for="imap_encryption" class=" col-md-3">
                    <?php echo app_lang('encryption'); ?>
                    <span class="help" data-bs-toggle="tooltip" title="<?php echo app_lang('imap_encryption_help_message'); ?>"><i data-feather='help-circle' class="icon-16"></i></span>
                </label>
                <div class=" col-md-9">
                    <?php
                    $imap_encryptions = array(
                        "imap/ssl/validate-cert" => "imap/ssl/validate-cert",
                        "novalidate-cert" => "novalidate-cert",
                        "ssl/validate-cert" => "ssl/validate-cert",
                        "ssl/novalidate-cert" => "ssl/novalidate-cert",
                        "validate-cert" => "validate-cert",
                    );
                    echo form_dropdown(
                            "imap_encryption", $imap_encryptions, $model_info->imap_encryption, "class='select2 mini'"
                    );
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="imap_host" class=" col-md-3"><?php echo app_lang('imap_host'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "imap_host",
                        "name" => "imap_host",
                        "value" => $model_info->imap_host,
                        "class" => "form-control",
                        "placeholder" => app_lang('imap_host'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required")
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="imap_port" class=" col-md-3"><?php echo app_lang('imap_port'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "imap_port",
                        "name" => "imap_port",
                        "value" => $model_info->imap_port,
                        "class" => "form-control",
                        "placeholder" => app_lang('imap_port'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required")
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="imap_email" class=" col-md-3"><?php echo app_lang("username") . "/" . app_lang('email'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "imap_email",
                        "name" => "imap_email",
                        "value" => $model_info->imap_email,
                        "class" => "form-control",
                        "placeholder" => app_lang("username") . "/" . app_lang('email'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required")
                    ));
                    ?>
                    <span class="mt10 d-inline-block"><i data-feather='alert-triangle' class="icon-16 text-warning"></i> <?php echo app_lang("email_piping_help_message"); ?></span>     
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="imap_password" class=" col-md-3"><?php echo app_lang('password'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_password(array(
                        "id" => "imap_password",
                        "name" => "imap_password",
                        "class" => "form-control",
                        "value" => "",
                        "placeholder" => app_lang('password'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required")
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="status" class=" col-md-3"><?php echo app_lang('status'); ?></label>
                <div class=" col-md-9">
                    <?php if ($model_info->imap_authorized) { ?>
                        <span class="badge bg-success"><?php echo app_lang("authorized"); ?></span>
                    <?php } else { ?>
                        <span class="badge mailbox-badge-alert"><?php echo app_lang("unauthorized"); ?></span>
                    <?php } ?>

                    <?php if ($model_info->imap_failed_login_attempts) { ?>
                        <span class="ml5 badge mailbox-badge-alert"><?php echo $model_info->imap_failed_login_attempts . " " . app_lang("login_attempt_failed"); ?></span>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather='check-circle' class="icon-16"></span> <?php echo app_lang('save_and_authorize'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    "use strict";

    $("#mailbox-settings-form").appForm({
        onSuccess: function (result) {
            $("#mailbox-table").appTable({newData: result.data, dataId: result.id});
            appAlert.success(result.message, {duration: 10000});
        }
    });

    $("#mailbox-settings-form .select2").select2();
    $('[data-bs-toggle="tooltip"]').tooltip();
</script>