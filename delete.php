<?php

include_once (dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR. "wp-config.php");
include_once (dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR."wp-includes/wp-db.php");
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR ."global.php");



delete_option("tic_css");
delete_option("tic_csserw");
delete_option("tic_content_len");
delete_option("tic_posts");
delete_option("tic_templrss");
delete_option("tic_templ");
delete_option("tic_speed");
delete_option("tic_pause_out");
delete_option("tic_pause");
delete_option("tic_content");
delete_option("tic_rssurl");
delete_option("tic_width");
delete_option("tic_height");
delete_option("tic_typ");
delete_option("tic_eigentxt");


$categories = $wpdb->get_results("SELECT * FROM $wpdb->terms ORDER BY name");
$kat_count = count($categories);
for($i=0;$i<$kat_count; $i++) {
  $kat_name = "tic_kat".$i;
  delete_option($kat_name);
}

echo "Database cleared."

?>