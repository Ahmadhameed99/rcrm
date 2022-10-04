<?php

namespace Easy_Backup\Controllers;

use App\Controllers\Security_Controller;
use Easy_Backup\Libraries\Easy_Backup_ZipArchive;
use App\Libraries\Google;

class Easy_Backup extends Security_Controller {

    function __construct() {
        parent::__construct(false);
    }

    function index($download_now = false) {
        if ($download_now) {
            $this->access_only_admin_or_settings_admin();
        }

        //execute maximum 300 seconds 
        ini_set('max_execution_time', 300);

        //create unique and secure file name
        $now = date("d-m-Y--h-i-s");
        $backup_full_file_name = get_setting('app_title') . "--" . get_setting("app_version") . "--" . $now . "--" . substr(md5(rand()), 0, 5);
        $backup_full_file_name = preg_replace('/\s+/', '-', $backup_full_file_name);
        $backup_full_file_name = str_replace("’", "-", $backup_full_file_name);
        $backup_full_file_name = str_replace("'", "-", $backup_full_file_name);
        $backup_full_file_name = str_replace("(", "-", $backup_full_file_name);
        $backup_full_file_name = str_replace(")", "-", $backup_full_file_name);

        $backup_zip_file_name = "$backup_full_file_name.zip";
        $backup_sql_file_name = "SQL--$backup_full_file_name.sql";

        //backup to local first
        $target_path = ROOTPATH . "easy_backup/";

        //check destination directory. if not found try to create a new one
        if (!is_dir($target_path)) {
            if (!mkdir($target_path, 0777, true)) {
                die('Failed to create file folders.');
            }
            //create a index.html file inside the folder
            copy(getcwd() . "/" . get_setting("system_file_path") . "index.html", $target_path . "index.html");
        }

        //Backup zip of whole folder
        $zip_file_name = "$target_path/$backup_zip_file_name";
        $zip = new Easy_Backup_ZipArchive();
        $res = $zip->open($zip_file_name, \ZipArchive::CREATE);
        if ($res === TRUE) {
            $zip->addDir(ROOTPATH, basename(ROOTPATH));

            //Backup whole database to the same zip
            $db = db_connect('default');
            $sql_backup = $this->get_database_sql($db->hostname, $db->username, $db->password, $db->database);
            $zip->addFromString($backup_sql_file_name, $sql_backup);

            $zip->close();
        }

        //check if the zip is created
        if (!file_exists($zip_file_name)) {
            show_404();
        }

        //if google drive enabled, upload to drive and delete local file
        if (get_setting("enable_google_drive_api_to_upload_file") && get_setting("google_drive_authorized")) {
            $google = new Google();
            $file_data = $google->upload_file($zip_file_name, $backup_zip_file_name, "easy_backup");
            unlink($zip_file_name);

            if ($download_now) {
                //download now
                $file_id = get_array_value($file_data, "file_id");
                $drive_file_data = $google->download_file($file_id);
                return $this->response->download($backup_zip_file_name, $drive_file_data);
            }
        }

        if ($download_now) {
            //download now from server's local directory
            //note: the existing method which is used to download file in RISE, isn't working for big files here
            app_redirect(base_url() . "/easy_backup/$backup_zip_file_name", true);
        }

        return true;
    }

    function settings() {
        $this->access_only_admin_or_settings_admin();
        return $this->template->rander("Easy_Backup\Views\settings\index");
    }

    private function get_database_sql($host, $user, $pass, $name) {
        $mysqli = new \mysqli($host, $user, $pass, $name);
        $mysqli->select_db($name);
        $mysqli->query("SET NAMES 'utf8'");

        $queryTables = $mysqli->query('SHOW TABLES');
        while ($row = $queryTables->fetch_row()) {
            $target_tables[] = $row[0];
        }
        foreach ($target_tables as $table) {
            $result = $mysqli->query('SELECT * FROM ' . $table);
            $fields_amount = $result->field_count;
            $rows_num = $mysqli->affected_rows;
            $res = $mysqli->query('SHOW CREATE TABLE ' . $table);
            $TableMLine = $res->fetch_row();
            $content = (!isset($content) ? 'SET NAMES utf8;' . "\n" . 'SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";' : $content) . "\n\n" . $TableMLine[1] . ";\n\n";

            for ($i = 0, $st_counter = 0; $i < $fields_amount; $i++, $st_counter = 0) {
                while ($row = $result->fetch_row()) { //when started (and every after 100 command cycle):
                    if ($st_counter % 100 == 0 || $st_counter == 0) {
                        $content .= "\nINSERT INTO " . $table . " VALUES";
                    }
                    $content .= "\n(";
                    for ($j = 0; $j < $fields_amount; $j++) {
                        $row[$j] = str_replace("\n", "\\n", addslashes($row[$j]));
                        if (isset($row[$j])) {
                            $content .= '"' . $row[$j] . '"';
                        } else {
                            $content .= '""';
                        }
                        if ($j < ($fields_amount - 1)) {
                            $content .= ',';
                        }
                    }
                    $content .= ")";
                    //every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler
                    if ((($st_counter + 1) % 100 == 0 && $st_counter != 0) || $st_counter + 1 == $rows_num) {
                        $content .= ";";
                    } else {
                        $content .= ",";
                    }
                    $st_counter = $st_counter + 1;
                }
            } $content .= "\n\n\n";
        }

        return $content;
    }

}
