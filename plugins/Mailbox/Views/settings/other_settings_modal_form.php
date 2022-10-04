<?php echo form_open(get_uri("mailbox_settings/save_other_settings"), array("id" => "mailbox-other-settings-form", "class" => "general-form bg-white", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
        <div class="form-group">
            <div class="row">
                <label for="permitted_users" class=" col-md-3"><?php echo app_lang('mailbox_who_can_access_this_mailbox'); ?> <span class="help" data-bs-toggle="tooltip" title="<?php echo app_lang('mailbox_users_help_message'); ?>"><i data-feather='help-circle' class="icon-16"></i></span></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "permitted_users",
                        "name" => "permitted_users",
                        "value" => $model_info->permitted_users,
                        "class" => "form-control",
                        "placeholder" => app_lang('team_members')
                    ));
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="signature" class=" col-md-3"><?php echo app_lang('signature'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_textarea(array(
                        "id" => "signature",
                        "name" => "signature",
                        "value" => $model_info->signature,
                        "class" => "form-control",
                    ));
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="send_bcc_to" class=" col-md-3"><?php echo app_lang('mailbox_send_bcc_to'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "send_bcc_to",
                        "name" => "send_bcc_to",
                        "value" => $model_info->send_bcc_to,
                        "class" => "form-control",
                        "placeholder" => app_lang("email")
                    ));
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather='check-circle' class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $("#mailbox-other-settings-form").appForm({
            beforeAjaxSubmit: function (data) {
                var signature = encodeAjaxPostData(getWYSIWYGEditorHTML("#signature"));
                $.each(data, function (index, obj) {
                    if (obj.name === "signature") {
                        data[index]["value"] = signature;
                    }
                });
            },
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});
            }
        });

        $("#permitted_users").select2({
            multiple: true,
            data: <?php echo ($members_dropdown); ?>
        });

        initWYSIWYGEditor("#signature", {height: 150});

        $("#mailbox-other-settings-form .select2").select2();
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>