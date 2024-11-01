<?php

function update_tic_settings() {
  global $wpdb, $plugin_dir;

  $update = false;

  $ticker_options["tic_css"] = get_option( "tic_css" );
  $ticker_options["tic_csserw"] = get_option( "tic_csserw" );
  $ticker_options["tic_contentlen"] = get_option( "tic_content_len" );
  $ticker_options["tic_postcount"] = get_option( "tic_posts" );
  $ticker_options["tic_templrss"] = get_option( "tic_templrss" );
  $ticker_options["tic_templ"] = get_option( "tic_templ" );
  $ticker_options["tic_speed"] = get_option( "tic_speed" );
  $ticker_options["tic_pauseout"] = get_option( "tic_pause_out" );
  $ticker_options["tic_pause"] = get_option( "tic_pause" );
  $ticker_options["tic_content"] = get_option( "tic_content" );
  $ticker_options["tic_rssurl"] = get_option( "tic_rssurl" );
  $ticker_options["tic_width"] = get_option( "tic_width" );
  $ticker_options["tic_height"] = get_option( "tic_height" );
  $ticker_options["tic_typ"] = get_option( "tic_typ" );
  //$ticker_options["tic_eigentxt"] = get_option( "tic_eigentxt" );
  $eigentxt_array = explode("[---]",get_option( "tic_eigentxt" ));

  if(($ticker_options["tic_css"]!="") || ($ticker_options["tic_csserw"]!="") || ($ticker_options["tic_contentlen"]!="") || ($ticker_options["tic_postcount"]!="") || ($ticker_options["tic_templrss"]!="") || ($ticker_options["tic_templ"]!="") || ($ticker_options["tic_speed"]!="") || ($ticker_options["tic_pauseout"]!="") || ($ticker_options["tic_pause"]!="") || ($ticker_options["tic_content"]!="") || ($ticker_options["tic_rssurl"]!="") || ($ticker_options["tic_width"]!="") || ($ticker_options["tic_height"]!="") || ($ticker_options["tic_typ"]!="") || ($eigentxt_array[0]!=""))
    $update = true;

  $categories = $wpdb->get_results("SELECT * FROM $wpdb->terms ORDER BY name");
  $kat_count = count($categories);
  for($i=0;$i<$kat_count; $i++) {
    $kat_name = "tic_kat".$i;
    $tic_cat[$i] = get_option( $kat_name );
    //echo $kat_name.": ".$tic_cat[$i]."<br>";
    $ticker_options[$kat_name] = $tic_cat[$i];
    if(trim($tic_cat[$i])!="")
      $update = true;
  }

  foreach ($eigentxt_array as $eigentxt) {
    $heute = date("Y-m-d",time());
    $wpdb->query("INSERT INTO " . $wpdb->prefix ."tic_timer (Content,Type,Date_Input,Date_Edit) VALUES ('$eigentxt','eigen','".$heute."','".$heute."')");
  }

  if(update_option( "tic_options", $ticker_options )) {
    if($update == true)
      $msg = "<p><strong>Update done.</strong> <a href=\"".$plugin_dir ."/delete.php\" target=\"_blank\">Cleare database now</a></p>";
    else
      $msg = "<p><strong>Table installed.</strong></p>";
  }
  else
    $msg = "UPDATE ERROR.";
  return $msg;
}

?>