<?php

namespace Easy_Backup\Libraries;

class Easy_Backup_ZipArchive extends \ZipArchive {

    public function addDir($location, $name) {
        $this->addEmptyDir($name);
        $this->addDirDo($location, $name);
    }

    private function addDirDo($location, $name) {
        $name .= '/';
        $location .= '/';
        $dir = opendir($location);
        while ($file = readdir($dir)) {
            if ($file == '.' || $file == '..' || $file == 'easy_backup') {
                continue;
            }

            $do = (filetype($location . $file) == 'dir') ? 'addDir' : 'addFile';
            $this->$do($location . $file, $name . $file);
        }
    }

}
