<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "easy_backup";
            echo view("settings/tabs", $tab_view);
            ?>
        </div>
        <div class="col-sm-9 col-lg-10">

            <div class="card">
                <div class="card-header">
                    <h4><?php echo app_lang("easy_backup"); ?></h4>
                </div>
                <div class="card-body general-form dashed-row">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                <i data-feather='info' class="icon-16"></i> <?php echo app_lang("easy_backup_help_message"); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group clearfix">
                        <div class="row">
                            <label for="cron_job_link" class=" col-md-2"><?php echo app_lang('cron_job_link'); ?></label>
                            <div class=" col-md-10"><?php
                                echo get_uri("easy_backup");
                                ?></div>
                        </div>
                    </div>
                    <div class="form-group clearfix">
                        <div class="row">
                            <label  class=" col-md-2">cPanel Cron Job Command *</label>
                            <div class=" col-md-10">
                                <div>
                                    <?php echo "<pre>wget -q -O- " . get_uri("easy_backup") . "</pre>"; ?>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
                <div class="card-footer">
                    <?php echo anchor(get_uri("easy_backup/index/1"), "<span data-feather='download' class='icon-16'></span> " . app_lang('easy_backup_backup_and_download_now'), array("class" => "btn btn-secondary")); ?>
                </div>
            </div>
        </div>
    </div>
</div>
