<?php

namespace Mailbox\Libraries;

class Mailbox_Imap {

    protected $Mailbox_settings_model;
    protected $Mailbox_emails_model;
    protected $Mailboxes_model;

    public function __construct() {
        $this->Mailbox_settings_model = new \Mailbox\Models\Mailbox_settings_model();
        $this->Mailbox_emails_model = new \Mailbox\Models\Mailbox_emails_model();
        $this->Mailboxes_model = new \Mailbox\Models\Mailboxes_model();

        //load EmailReplyParser resources
        require_once(PLUGINPATH . "Mailbox/ThirdParty/Imap/EmailReplyParser/vendor/autoload.php");

        //load ddeboer-imap resources
        require_once(PLUGINPATH . "Mailbox/ThirdParty/Imap/ddeboer-imap/vendor/autoload.php");

        //load mail-mime-parser resources
        require_once(PLUGINPATH . "Mailbox/ThirdParty/Imap/mail-mime-parser/vendor/autoload.php");
    }

    function authorize_imap_and_get_inbox($mailbox_id = 0, $is_cron = false) {
        if (!$mailbox_id) {
            return false;
        }

        $mailbox_info = $this->Mailboxes_model->get_one($mailbox_id);
        $server = new \Ddeboer\Imap\Server($mailbox_info->imap_host, $mailbox_info->imap_port, $mailbox_info->imap_encryption);

        //try to login 10 times and save the count on each load of cron job
        //after a success login, reset the count to 0
        try {
            $connection = $server->authenticate($mailbox_info->imap_email, decode_password($mailbox_info->imap_password, "imap_password"));

            $data = array(
                "imap_authorized" => 1, //the credentials is valid. store to settings that it's authorized
                "imap_failed_login_attempts" => 0, //reset failed login attempts count
            );

            $this->Mailboxes_model->ci_save($data, $mailbox_id);
            return $connection;
        } catch (\Exception $exc) {
            //the credentials is invalid, increase attempt count and store
            $attempts_count = $mailbox_info->imap_failed_login_attempts;
            if ($is_cron) {
                $attempts_count = $attempts_count ? ($attempts_count * 1 + 1) : 1;
                $data = array("imap_failed_login_attempts" => $attempts_count);
                $this->Mailboxes_model->ci_save($data, $mailbox_id);
            }

            //log error for every exception
            log_message('error', '[ERROR] {exception}', ['exception' => $exc]);

            if ($attempts_count === 10 || !$is_cron) {
                //flag it's unauthorized, only after 10 failed attempts
                $data = array("imap_authorized" => 0);
                $this->Mailboxes_model->ci_save($data, $mailbox_id);
            }

            return false;
        }
    }

    public function run_imap() {
        $Mailboxes_model = new \Mailbox\Models\Mailboxes_model();
        $options = array("authorized_imap_only" => true);
        $mailboxes = $Mailboxes_model->get_details($options)->getResult();

        foreach ($mailboxes as $mailbox) {
            $connection = $this->authorize_imap_and_get_inbox($mailbox->id, true);
            if (!$connection) {
                continue; //couldn't get connection of this email
            }

            $mailbox_connection = $connection->getMailbox('INBOX'); //get mails of inbox only
            $messages = $mailbox_connection->getMessages();

            foreach ($messages as $message) {
                //create emails for unread emails
                if (!$message->isSeen()) {
                    $this->_create_email_from_imap($mailbox->id, $message);

                    //mark the mail as read
                    $message->markAsSeen();
                }
            }
        }
    }

    private function _create_email_from_imap($mailbox_id, $message_info = "") {
        if ($message_info) {
            $email = $message_info->getFrom()->getAddress();
            $creator_name = $message_info->getFrom()->getName();
            $subject = $message_info->getSubject();
            $now = get_current_utc_time();

            //check if there has any client containing this email address
            //if so, go through with the client id
            $contact_info = $this->Mailbox_emails_model->get_user_of_email($email)->getRow();
            $contact_id = isset($contact_info->id) ? $contact_info->id : 0;

            //check if the email is exists on the app
            //if not, that will be considered as a new email
            //but for this case, it's a replying email. we've to parse the message
            $email_id = $this->_get_email_id_from_subject($subject, $email, $contact_id, $mailbox_id);

            $email_data = array(
                "subject" => $subject,
                "message" => $this->get_email_message($message_info, $email_id),
                "created_by" => $contact_id,
                "created_at" => $now,
                "last_activity_at" => $now,
                "creator_name" => $creator_name ? $creator_name : "",
                "creator_email" => $email,
                "email_id" => $email_id,
                "mailbox_id" => $mailbox_id
            );

            $email_data = clean_data($email_data);

            $files_data = $this->_prepare_attachment_data_of_mail($message_info);
            $email_data["files"] = serialize($files_data);

            $this->Mailbox_emails_model->ci_save($email_data);

            if ($email_id) {
                //save last activity to the parent email
                $email_data = array(
                    "last_activity_at" => $now
                );

                $this->Mailbox_emails_model->ci_save($email_data, $email_id);
            }
        }
    }

    private function _prepare_replying_message($message = "") {
        try {
            $reply_parser = new \EmailReplyParser\EmailReplyParser();
            return $reply_parser->parseReply($message);
        } catch (\Exception $ex) {
            log_message('error', '[ERROR] {exception}', ['exception' => $ex]);
            return "";
        }
    }

    //save emails comment
    private function get_email_message($message_info, $email_id) {
        $description = $message_info->getBodyText();
        if ($email_id) {
            $description = $this->_prepare_replying_message($description);
        }

        if ($description) {
            return $description;
        }

        //parse email content if the predefined method returns empty
        $encoding_type = $message_info->getEncoding();
        $raw_content = $message_info->getRawMessage();

        //parse with another library
        try {
            $mail_mime_parser = \ZBateson\MailMimeParser\Message::from($raw_content);
            $description = $mail_mime_parser->getHtmlContent();

            //get content inside body tag only if it exists
            if ($description) {
                preg_match("/<body[^>]*>(.*?)<\/body>/is", $description, $body_matches);
                $description = isset($body_matches[1]) ? $body_matches[1] : $description;
            }
        } catch (\Exception $ex) {
            log_message('error', '[ERROR] {exception}', ['exception' => $ex]);
        }

        if ($description) {
            return $description;
        }

        //get content after X-Yandex-Forward: random strings (32) + new lines
        $description = substr($raw_content, strpos($raw_content, "X-Yandex-Forward") + 52);

        //parse for different encoding types
        if ($encoding_type == "7bit") {
            $description = quoted_printable_decode($description);
        } else if ($encoding_type == "base64") {
            $description = imap_base64($description);
        } else if ($encoding_type == "quoted-printable") {
            $description = imap_qprint($description);
        }

        return $description;
    }

    //get email id
    private function _get_email_id_from_subject($subject = "", $email = "", $contact_id = 0, $mailbox_id = 0) {
        if (!($subject && $email)) {
            return 0;
        }

        //find 'Re: '
        $reply_text = "Re: ";
        if (substr($subject, 0, strlen($reply_text)) !== $reply_text) {
            return 0;
        }

        //it's a replying email
        $main_subject = str_replace($reply_text, "", $subject);
        $email_info = $this->Mailbox_emails_model->get_email_with_subject($main_subject, $email, $contact_id, $mailbox_id)->getRow();

        return isset($email_info->id) ? $email_info->id : 0;
    }

    //download attached files to local
    private function _prepare_attachment_data_of_mail($message_info = "") {
        if ($message_info) {
            $files_data = array();
            $attachments = $message_info->getAttachments();

            foreach ($attachments as $attachment) {
                //move files to the directory
                $file_data = move_temp_file($attachment->getFilename(), get_mailbox_setting("mailbox_email_file_path"), "mailbox", NULL, "", $attachment->getDecodedContent());

                array_push($files_data, $file_data);
            }

            return $files_data;
        }
    }

}
