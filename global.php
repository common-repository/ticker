<?php

//======================= Benutzer-Parameter ==================================
//------- in this section you may edit some Ticker admin parameters --------
$custom_textbox_height_min = "1.0em";      //Width and height parameter of "own text" (small)
$custom_textbox_width_min = "100px";
$custom_textbox_height = "100px";      //Width and height parameter of "own text" (expandet)
$custom_textbox_width = "300px";

$rssurl_textbox_height = 100;      //Width and height parameter of "Fedd URLs"
$rssurl_textbox_width = 500;

$templ_textbox_height = 100;      //Width and height parameter of "Ticker Template Box"
$templ_textbox_width = 300;
//------- end of edit section ----------------------------------------------




//======================== Variablen/Konstanten ================================
$version = get_bloginfo('version');

if($version<2.6) {
  $plugin_dir = str_replace( '\\', '/', dirname( __FILE__ ) );
  if( preg_match( '#(/'.PLUGINDIR.'.*)#i', $plugin_dir, $treffer ) )
    $plugin_dir = $treffer[1];
  else
    $plugin_dir = '/'.PLUGINDIR;

  $plugin_dir = get_bloginfo('url').$plugin_dir;
}
else {
  define('NGCOOKIMGLIST_URLPATH', WP_CONTENT_URL.'/plugins/'.plugin_basename( dirname(__FILE__)) );
  $plugin_dir = NGCOOKIMGLIST_URLPATH;
}

// Read in existing option value from database
$ticker_options = get_option( "tic_options" );

$holder_w = $ticker_options["tic_width"];
$holder_h = $ticker_options["tic_height"];
$max_content_len = $ticker_options["tic_contentlen"];
$max_records = $ticker_options["tic_postcount"];
$tic_typ = $ticker_options["tic_typ"];
$tic_template = $ticker_options["tic_templ"];
$tic_templaterss = $ticker_options["tic_templrss"];
$tic_pause = $ticker_options["tic_pause"];
$tic_speed = $ticker_options["tic_speed"];
$tic_content = $ticker_options["tic_content"];
$text_typ = $ticker_options["tic_content"];
$et_sort_wahl = $ticker_options["tic_sortwahl"];
$eigentext = get_eigentext ('str',true,$et_sort_wahl,'ASC','filtered');
$eigentext = str_replace("\r\n","<br />",$eigentext);
$eigentext = str_replace("\r","<br />",$eigentext);
$eigentext = str_replace("\n","<br />",$eigentext);

$rssurls = $ticker_options["tic_rssurl"];


//=============== Funktionen =========================================
function get_eigentext ($type,$delete,$order,$order_direction,$ergebnis){
  global $wpdb;

  if($order=="")
    $order = "Date_Input";
  if($order_direction=="")
    $order_direction = "ASC";

  $text_array = Array();
  $text_string = "";
  $datum_heute = date("Y-m-d",time());

  if($delete) {
    $wpdb->query("DELETE FROM " . $wpdb->prefix ."tic_timer WHERE Del_Flag=1 AND Date_Stop<='$datum_heute' AND Date_Stop!='0000-00-00'");
  }

  $k=0;
  if($ergebnis=="all")
    $befehl = "SELECT ID,Content,Date_Start,Date_Stop,Date_Input,Date_Edit,Del_Flag,Sort FROM ".$wpdb->prefix ."tic_timer WHERE Type='eigen' ORDER BY $order $order_direction";
  if($ergebnis=="filtered")
    $befehl = "SELECT ID,Content,Date_Start,Date_Stop,Date_Input,Date_Edit,Del_Flag,Sort FROM ".$wpdb->prefix ."tic_timer WHERE Type='eigen' AND Date_Start<='$datum_heute' AND Date_Stop>'$datum_heute' OR Date_Start='0000-00-00' ORDER BY $order $order_direction";

  $eigentexte = $wpdb->get_results($befehl);
  foreach ($eigentexte as $eigentext) {
    $text_array[$k]["ID"] = $eigentext->ID;
    $eigentext_stack = stripslashes($eigentext->Content);
    $eigentext_stack = str_replace("\r\n","",$eigentext_stack);
    $eigentext_stack = str_replace("\n\r","",$eigentext_stack);
    $eigentext_stack = str_replace("\r","",$eigentext_stack);
    $eigentext_stack = str_replace("\n","",$eigentext_stack);
    $text_array[$k]["Content"] = $eigentext_stack;
    $text_array[$k]["Date_Start"] = $eigentext->Date_Start;
    $text_array[$k]["Date_Stop"] = $eigentext->Date_Stop;
    $text_array[$k]["Date_Input"] = $eigentext->Date_Input;
    $text_array[$k]["Date_Edit"] = $eigentext->Date_Edit;
    $text_array[$k]["Del_Flag"] = $eigentext->Del_Flag;
    $text_array[$k]["Sort"] = $eigentext->Sort;
    $text_string .= $text_array[$k]["Content"]."[---]";
    $k++;
  }

  switch($type) {
    case "arr": return $text_array;
                  break;
    case "str": $text_string = substr($text_string,0,strlen($text_string)-5);
                return $text_string;
                   break;
    default: return $text_array;
                    break;
  } //switch


}


function date_en2de ($datum) {
  $dat_elements = explode("-",$datum);
  $dat_de = $dat_elements[2].".".$dat_elements[1].".".$dat_elements[0];
  return $dat_de;
}

?>