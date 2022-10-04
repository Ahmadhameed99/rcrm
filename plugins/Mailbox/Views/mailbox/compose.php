<?php echo form_open(get_uri("mailbox/send"), array("id" => "send-email-form", "class" => "general-form", "role" => "form")); ?>

<div id="send_email-dropzone" class="post-dropzone">
    <div class="clearfix <?php echo $email_info->id ? "card-body p20 b-a" : "modal-body"; ?>">
        <?php echo $email_info->id ? "" : "<div class='container-fluid'>"; ?>
        <input type="hidden" id="email_id" name="email_id" value="<?php echo $email_id; ?>" />
        <input type="hidden" id="mailbox_id" name="mailbox_id" value="<?php echo $mailbox_info->id; ?>" />
        <input type="hidden" id="id" name="id" value="<?php echo $email_info->status === "draft" ? $email_info->id : ""; ?>" />
        <input type="hidden" id="save_as_draft" name="save_as_draft" value="" />

        <div class="form-group">
            <div class="row">
                <label for="email_to" class=" col-md-2">TO</label>
                <div class="col-md-10">
                    <?php
                    echo form_input(array(
                        "id" => "email_to",
                        "name" => "email_to",
                        "value" => $email_info->to ? $email_info->to : (($email_info->created_by && $email_info->creator_email) ? $email_info->created_by : $email_info->creator_email),
                        "class" => "form-control",
                        "placeholder" => "TO",
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="email_cc" class=" col-md-2">CC</label>
                <div class="col-md-10">
                    <?php
                    echo form_input(array(
                        "id" => "email_cc",
                        "name" => "email_cc",
                        "value" => $email_info->cc,
                        "class" => "form-control",
                        "placeholder" => "CC"
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="email_bcc" class=" col-md-2">BCC</label>
                <div class="col-md-10">
                    <?php
                    echo form_input(array(
                        "id" => "email_bcc",
                        "name" => "email_bcc",
                        "value" => $email_info->bcc,
                        "class" => "form-control",
                        "placeholder" => "BCC"
                    ));
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="subject" class=" col-md-2"><?php echo app_lang("subject"); ?></label>
                <div class="col-md-10">
                    <?php
                    echo form_input(array(
                        "id" => "subject",
                        "name" => "subject",
                        "value" => $email_info->subject,
                        "class" => "form-control",
                        "placeholder" => app_lang("subject"),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class=" col-md-12">
                    <?php
                    $message = "";
                    if ($email_info->status === "draft") {
                        $message = $email_info->message;
                    } else if ($mailbox_info->signature) {
                        $message = $mailbox_info->signature;
                    }

                    echo form_textarea(array(
                        "id" => "message",
                        "name" => "message",
                        "value" => $message,
                        "class" => "form-control",
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-12">
                <?php
                echo view("includes/file_list", array("files" => $email_info->files));
                ?>
            </div>
        </div>

        <?php echo view("includes/dropzone_preview"); ?>
        <?php echo $email_info->id ? "" : "</div>"; ?>
    </div>


    <?php if ($email_info->id) { //doing this separately since style structure is different for card/modal footer and require lots of coditions ?>
        <div class="card-footer clearfix">
            <button class="btn btn-default upload-file-button float-start round me-auto mailbox-color-soft-white dz-clickable" type="button"><i data-feather="camera" class="icon-16"></i> <?php echo app_lang("add_attachment"); ?></button>
            <button type="submit" class="btn btn-primary float-end"><span data-feather="send" class="icon-16"></span> <?php echo app_lang('send'); ?></button>
            <button type="button" id="save_as_draft_btn" class="btn btn-info mr10 text-white float-end"><span data-feather="file" class="icon-16"></span> <?php echo app_lang('mailbox_save_as_draft'); ?></button>
        </div>
    <?php } else { ?>
        <div class="modal-footer">
            <div class="float-start me-auto">
                <button class="btn btn-default upload-file-button round" type="button"><i data-feather="camera" class="icon-16"></i> <?php echo app_lang("add_attachment"); ?></button>
            </div>

            <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
            <button type="button" id="save_as_draft_btn" class="btn btn-info text-white"><span data-feather="file" class="icon-16"></span> <?php echo app_lang('mailbox_save_as_draft'); ?></button>
            <button type="submit" class="btn btn-primary"><span data-feather="send" class="icon-16"></span> <?php echo app_lang('send'); ?></button>
        </div>
    <?php } ?>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        var uploadUrl = "<?php echo get_uri("mailbox/uploadFile"); ?>";
        var validationUri = "<?php echo get_uri("mailbox/validateEmailsFile"); ?>";
        attachDropzoneWithForm("#send_email-dropzone", uploadUrl, validationUri);

        var $sendEmailForm = $("#send-email-form"),
                $saveAsDraft = $("#save_as_draft");

        $("#message").summernote({
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['table', ['table']],
                ['insert', ['hr']],
                ['view', ['fullscreen', 'codeview']]
            ],
            disableDragAndDrop: true,
            callbacks: {
                onInit: function () {
<?php if (count($templates) && $templates) { ?>
                        var dom = '<div class="note-btn-group btn-group dropdown">' +
                                '<button type="button" class="spinning-btn dropdown-toggle note-btn btn btn-default btn-sm" data-bs-toggle="dropdown" aria-expanded="true" ><i data-feather="layout" class="icon-14"></i> <span class="note-icon-caret"></span></button>' +
                                '<ul class="note-dropdown-menu dropdown-menu note-check dropdown-line-height" role="menu">'
    <?php foreach ($templates as $template) { ?>
                            dom += "<li role='presentation' class='dropdown-item'><a href='#' data-id='<?php echo $template->id; ?>' class='insert-template-btn'><?php echo $template->title; ?></a></li>";
    <?php } ?>

                        dom += '</ul>';
                        dom += '</div>';

                        $(".note-editor").find(".note-toolbar").append(dom);
                        feather.replace();
<?php } ?>
                }
            }
        });

        $('#send-email-form .select2').select2();
        $('#email_cc, #email_to').select2({
            tags: <?php echo json_encode($users_dropdown); ?>
        });

        var isModal = true;
<?php if ($email_info->id) { ?>
            isModal = false;
<?php } ?>

        $sendEmailForm.appForm({
            isModal: isModal,
            showLoader: true,
            beforeAjaxSubmit: function (data) {
                var custom_message = encodeAjaxPostData(getWYSIWYGEditorHTML("#message"));
                $.each(data, function (index, obj) {
                    if (obj.name === "message") {
                        data[index]["value"] = custom_message;
                    }
                });
            },
            onSuccess: function (result) {
                if (result.success) {
<?php if ($email_info->id) { ?>
                        //viewing email
                        //reply/draft

                        if (result.email_view) {
                            //sent as reply
                            $(".mailbox-email-view").append(result.email_view);
                            loadReplyForm("<?php echo $email_id; ?>"); //load new reply
                        }

                        if (!result.email_view) {
                            //saved as draft
                            $("#ajaxModal").modal('hide');
                        }

                        $saveAsDraft.val("");
<?php } ?>

                    appAlert.success(result.message, {duration: 10000});
                } else {
                    appAlert.error(result.message);
                }
            }
        });

        //save as draft
        $("#save_as_draft_btn").on("click", function () {
            $saveAsDraft.val("1");
            $sendEmailForm.trigger("submit");
        });

        /* insert template section */

        var $inputField = $sendEmailForm.find("#message"), $lastFocused;

        function saveCursorPositionOfRichEditor() {
            $inputField.summernote('saveRange');
            $lastFocused = "rich-editor";
        }

        //store the cursor position
        $inputField.on("summernote.change", function (e) {
            saveCursorPositionOfRichEditor();
        });

        //it'll grab only cursor clicks
        $("body").on("click", ".note-editable", function () {
            saveCursorPositionOfRichEditor();
        });

        function insertTemplate(text) {
            if ($lastFocused === undefined) {
                return;
            }

            $inputField.summernote('restoreRange');
            $inputField.summernote('focus');
            $inputField.summernote('pasteHTML', text);
        }

        //insert email template
        $('body').on('click', '.insert-template-btn', function () {
            var $instance = $(this);
            $instance.closest("div").find("button").addClass("spinning");

            $.ajax({
                url: "<?php echo get_uri("mailbox/getTemplateContent"); ?>",
                data: {id: $instance.attr("data-id")},
                cache: false,
                type: 'POST',
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        if ($lastFocused === undefined) {
                            $inputField.summernote("code", response.template_content);
                        } else {
                            insertTemplate(response.template_content);
                        }

                        $instance.closest("div").find("button").removeClass("spinning");
                    }
                }
            });
        });

    });
</script>