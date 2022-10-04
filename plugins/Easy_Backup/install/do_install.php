<?php

ini_set('max_execution_time', 300); //300 seconds 

$product = "Easy_Backup";

//check requirements
// include PLUGINPATH . "$product/install/verfiy_purchase_code.php";
// if (!$verification || $verification != "verified") {
//     echo json_encode(array("success" => false, "message" => "Please enter a valid purchase code."));
//     exit();
// }

//save purchase code
$Settings_model = model("App\Models\Settings_model");
$Settings_model->save_setting("easy_backup_item_purchase_code", $item_purchase_code);
