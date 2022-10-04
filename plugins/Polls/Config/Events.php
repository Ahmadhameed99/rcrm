<?php

namespace Polls\Config;

use CodeIgniter\Events\Events;

Events::on('pre_system', function () {
    helper('poll_general');
});

Events::on('post_controller_constructor', function () {
    //run cron job for inactivate poll on expire date
    app_hooks()->add_action('app_hook_after_cron_run', function() {
        try {
            $Polls_model = new \Polls\Models\Polls_model();

            //inactivate expire polls
            $polls = $Polls_model->get_details(array(
                        "status" => "active", //don't find inactive polls
                        "expire_at" => get_today_date()
                    ))->getResult();

            foreach ($polls as $poll) {
                //make poll inactive
                $poll_data = array(
                    "status" => "inactive"
                );

                $Polls_model->ci_save($poll_data, $poll->id);
            }
        } catch (\Exception $e) {
            
        }
    });
});
