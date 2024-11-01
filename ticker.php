<?php
/*
Plugin Name: Ticker
Plugin URI: http://www.stegasoft.de/
Description: Posts, RSS oder eigenen Text als Scrolltext, Newsticker oder mit Fade-Effekt zeigen. Scroll-JavaScript-Codes basiert auf TigraScroller (http://www.softcomplex.com/products/tigra_scroller/). TigraScroller ist zum Entwicklungszeitpunkt dieses Plugins Freeware (Stand 07/2008). Die RSS-Funktion basiert auf dem Script von Sebastian Gollus (http://www.web-spirit.de/startseite/7/RSS-Feed-auslesen-mit-PHP). Bitte das jeweilige Copyright beachten.
Version: 2.1
Author: Stephan Gaertner
Author URI: http://www.stegasoft.de
*/




//============= INCLUDES ==========================================================
include_once (dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR. "wp-config.php");
include_once (dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR."wp-includes/wp-db.php");
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR ."global.php");


//============= Code für Admin-Kopf erzeugen ============================
function js2adminhead() {
  global $plugin_dir;
  $jscript_licence = "";

  $jscript_includes = "<script src=\"$plugin_dir/js/calendar3.js\" type=\"text/javascript\"></script>\n";

  echo $jscript_includes;
}
add_action('admin_head', 'js2adminhead');


//============= Code für Template-Kopf erzeugen ============================
function js2head() {
  global $plugin_dir;
  $jscript_licence = "/*\n".
                     "Ticker: v2.0\n".
                     "JavaScript copyright of TICKER-PLUGIN:\n".
                     "Title: Tigra Scroller\n".
                     "Description: See the demo at url\n".
                     "URL: http://www.softcomplex.com/products/tigra_scroller/\n".
                     "Version: 1.5\n".
                     "Date: 07-09-2003 (mm-dd-yyyy)\n".
                     "Note: Permission given to use this script in ANY kind of applications if\n".
                     "header lines are left unchanged.\n*/\n";

  $jscript_includes = "<script src=\"$plugin_dir/ts_files/scroll.js\" type=\"text/javascript\">\n$jscript_licence</script>\n";
  echo $jscript_includes;
}
add_action('wp_head', 'js2head');




//============= Funktion für Scriptaufruf im Template ========================
function show_ticker() {
  //**** Please note: leave that part of the code (copyright) unchanged. For more informations visit www.stegasoft.de
  $html_code = "<script language='JavaScript' type='text/javascript'>Tscroll_init (0);</script> <span style='font-size:7pt;'>Ticker powered by <a href='http://www.stegasoft.de' target='_blank'>SteGaSoft</a></span>";
  //*****************************************************
  echo $html_code;
}



//============= Plugin - Button einbauen =====================================
add_action('admin_menu', 'ticker_page');
function ticker_page() {
    add_submenu_page('plugins.php', __('Ticker'), __('Ticker'), 10, 'tickeradmin', 'mt_options_page');
}



//============= Timer-Tabelle erstellen =====================================
function install() {
  global $wpdb;

  $install_query = "CREATE TABLE " . $wpdb->prefix ."tic_timer (ID bigint(20) unsigned NOT NULL auto_increment, Content text NOT NULL, Date_Start DATE DEFAULT '0000-00-00' NOT NULL, Date_Stop DATE DEFAULT '0000-00-00' NOT NULL , Date_Input DATE DEFAULT '0000-00-00' NOT NULL, Date_Edit DATE DEFAULT '0000-00-00' NOT NULL, Type VARCHAR(10) NOT NULL, Del_Flag INT(1) NOT NULL , Sort bigint(20) unsigned NOT NULL, PRIMARY KEY  (ID))";

  // only create, if the table does not exists
  include_once (ABSPATH."/wp-admin/upgrade-functions.php");
  maybe_create_table($wpdb->prefix . "tic_timer", $install_query);
}

//============= Tabellen/Optionen loeschen ===================================
$ticker_options = get_option( "tic_options" );
if($ticker_options["tic_deinstall"] == "yes")
  register_deactivation_hook(__FILE__, 'deinstall');
function deinstall() {
  global $wpdb;
  delete_option('tic_options');
  $wpdb->query("DROP TABLE " . $wpdb->prefix ."tic_timer");
  $wpdb->query("OPTIMIZE TABLE $wpdb->options");
}


//============= Seite für Plugin-Administration aufbauen ====================
function mt_options_page() {
  global $wpdb,$kat_count,$opt_kat,$plugin_dir,$custom_textbox_height,$custom_textbox_width,$rssurl_textbox_height,$rssurl_textbox_width,$templ_textbox_width,$templ_textbox_height,$custom_textbox_height_min,$custom_textbox_width_min;

  if (defined('WPLANG')) {
    $lang = WPLANG;
  }
  if (empty($lang)) {
    $lang = 'de_DE';
  }

  if(!@include_once "lang/".$lang.".php")
    include_once "lang/en_EN.php";


  $hidden_field_name = "tic_submit_hidden";



  $opt_typ = "tic_typ";
  $data_field_name_typ = "tictyp";
  $opt_content = "tic_content";
  $data_field_name_content = "ticcontent";
  $opt_eigentxt = "tic_eigentxt";
  $data_field_name_eigentxt = "ticeigentxt";
  $opt_rssurl = "tic_rssurl";
  $data_field_name_rssurl = "ticrssurl";
  $opt_name = "tic_content_len";
  $data_field_name = "content_len";
  $opt_width = "tic_width";
  $data_field_name_width = "ticwidth";
  $opt_height = "tic_height";
  $data_field_name_height = "ticheight";
  $opt_posts = "tic_posts";
  $data_field_name_posts = "ticposts";
  $opt_pause = "tic_pause";
  $data_field_name_pause = "ticpause";
  $opt_pause_out = "tic_pause_out";
  $data_field_name_pause_out = "ticpauseout";
  $opt_speed = "tic_speed";
  $data_field_name_speed = "ticspeed";
  $opt_templ = "tic_templ";
  $data_field_name_templ = "tictempl";
  $opt_templrss = "tic_templrss";
  $data_field_name_templrss = "tictemplrss";
  $opt_css = "tic_css";
  $data_field_name_css = "ticcss";
  $opt_csserw = "tic_csserw";
  $data_field_name_csserw = "ticsserw";


  $categories = $wpdb->get_results("SELECT * FROM $wpdb->terms ORDER BY name");
  $kat_count = count($categories);
  for($i=0;$i<$kat_count; $i++) {
    $opt_kat[$i] = "tic_kat".$i;
    $data_field_name_kat[$i] = "tickat".$i;
  }


  // Read in existing option value from database
  $ticker_options = get_option( "tic_options" );

  if($ticker_options=="") {
    install();
    require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR ."update.php");
    $msg = update_tic_settings();
    echo "<div class=\"updated\">$msg</div>";
    $ticker_options = get_option( "tic_options" );
  }

  $et_sort_wahl = $ticker_options["tic_sortwahl"];
  $deinstall_val = $ticker_options["tic_deinstall"];
  $typ_val = $ticker_options["tic_typ"];
  $content_val = $ticker_options["tic_content"];
  $eigentxt_val = get_eigentext ('str',true,$et_sort_wahl,'ASC','all');
  $rssurl_val = $ticker_options["tic_rssurl"];
  $opt_val = $ticker_options["tic_contentlen"];
  $width_val = $ticker_options["tic_width"];
  $height_val = $ticker_options["tic_height"];
  $posts_val = $ticker_options["tic_postcount"];
  $pause_val = $ticker_options["tic_pause"];
  $pause_out_val = $ticker_options["tic_pauseout"];
  $speed_val = $ticker_options["tic_speed"];
  $templ_val = $ticker_options["tic_templ"];
  $templrss_val = $ticker_options["tic_templrss"];
  $css_val = $ticker_options["tic_css"];
  $csserw_val = $ticker_options["tic_csserw"];

  for($i=0;$i<$kat_count; $i++) {
    //$kat_val[$i] = get_option( $opt_kat[$i] );
    $katstack = "tic_kat".$i;
    $kat_val[$i] = $ticker_options[$katstack];
  }


  //Eigentext auslesen
  $eigentext_arr = get_eigentext ('arr',true,$et_sort_wahl,'ASC','all');




  // See if the user has posted us some information
  // If they did, this hidden field will be set to 'Y'
  if( $_POST[ $hidden_field_name ] == "Y" ) {

    // Read their posted value
    $deinstall_val = $_POST[ 'ticdeinstall' ];
    $typ_val = $_POST[ $data_field_name_typ ];
    $content_val = $_POST[ $data_field_name_content ];
    if($content_val=="eigen") {
      $eigentxt_val = $_POST[ $data_field_name_eigentxt ];
      if(trim($eigentxt_val)=="")
        $eigentxt_val = $ticker_options["tic_eigentxt"];
    }
    if($content_val=="rss") {
      $rssurl_val = $_POST[ $data_field_name_rssurl ];
      if(trim($rssurl_val)=="")
        $rssurl_val = $ticker_options["tic_rssurl"];
      $rssurl_val = str_replace("\n\r","\n",$rssurl_val);
      $rssurl_val = str_replace("\r\n","\n",$rssurl_val);
      $rssurl_val = str_replace("\r","\n",$rssurl_val);
      $rssurl_val = str_replace(chr(11),"\n",$rssurl_val);
    }
    $opt_val = $_POST[ $data_field_name ];
    $width_val = $_POST[ $data_field_name_width ];
    $height_val = $_POST[ $data_field_name_height ];
    $posts_val = $_POST[ $data_field_name_posts ];
    $pause_val = $_POST[ $data_field_name_pause ];
    $pause_out_val = $_POST[ $data_field_name_pause_out ];
    $speed_val = $_POST[ $data_field_name_speed ];
    $templ_val = $_POST[ $data_field_name_templ ];
    $templrss_val = $_POST[ $data_field_name_templrss ];
    $css_val = $_POST[ $data_field_name_css ];
    $csserw_val = $_POST[ $data_field_name_csserw ];
    for($i=0;$i<$kat_count; $i++) {
      $kat_val[$i] = $_POST[ $data_field_name_kat[$i] ];
    }
    $eigentext_ids = $_POST['et_ids'];
    $eigentext_content = $_POST['et_content'];
    $eigentext_start = $_POST['et_start'];
    $eigentext_stop = $_POST['et_stop'];
    $eigentext_del = $_POST['et_del'];
    $eigentext_sort = $_POST['et_sort'];

    $et_neu_content = $_POST['et_neu_content'];
    $et_neu_start = $_POST['et_neu_start'];
    $et_neu_stop = $_POST['et_neu_stop'];
    $et_neu_del = $_POST['et_neu_del'];
    $et_neu_sort = $_POST['et_neu_sort'];
    $et_sort_wahl = $_POST['et_sort_wahl'];

    for($i=0;$i<count($et_neu_sort);$i++) {
      if(trim($et_neu_content[$i])!="") {
        $et_content_stack = addslashes($et_neu_content[$i]);
        $et_del_stack = $et_neu_del[$i];
        if($et_del_stack=="")
          $et_del_stack=0;
        $befehl = "INSERT INTO ".$wpdb->prefix ."tic_timer (Content,Date_Start,Date_Stop,Del_Flag,Sort,Type,Date_Input,Date_Edit) VALUES ('$et_content_stack','".$et_neu_start[$i]."','".$et_neu_stop[$i]."',".$et_del_stack.",".$et_neu_sort[$i].",'eigen','".date("Y-m-d",time())."','".date("Y-m-d",time())."')";
        $wpdb->query($befehl);
        //echo $befehl."<br />";
        $eigentext_arr = get_eigentext ('arr',true,$et_sort_wahl,'ASC','all');
      }
    }

    // Save the posted value in the database
    $ticker_options["tic_deinstall"] = $deinstall_val;
    $ticker_options["tic_typ"] = $typ_val;
    $ticker_options["tic_content"] = $content_val;
    if($content_val=="eigen")
      $ticker_options["tic_eigentxt"] = $eigentxt_val;
    if($content_val=="rss")
      $ticker_options["tic_rssurl"] = $rssurl_val;

    $ticker_options["tic_contentlen"] = $opt_val;
    $ticker_options["tic_width"] = $width_val;
    $ticker_options["tic_height"] = $height_val;
    $ticker_options["tic_postcount"] = $posts_val;
    $ticker_options["tic_pause"] = $pause_val;
    $ticker_options["tic_pauseout"] = $pause_out_val;
    $ticker_options["tic_speed"] = $speed_val;
    $ticker_options["tic_templ"] = $templ_val;
    $ticker_options["tic_templrss"] = $templrss_val;
    $ticker_options["tic_css"] = $css_val;
    $ticker_options["tic_csserw"] = $csserw_val;
    if(($content_val=="eigen") AND ($_POST['et_sort_wahl']!=""))
      $ticker_options["tic_sortwahl"] = $et_sort_wahl;
    else
      $et_sort_wahl = $ticker_options["tic_sortwahl"];

    if($content_val=="db") {
      for($i=0;$i<$kat_count; $i++) {
        //update_option( $opt_kat[$i], $kat_val[$i] );
        $katstack = "tic_kat".$i;
        $ticker_options[$katstack] = $kat_val[$i];
      }
    }

    update_option( "tic_options", $ticker_options );

    //---- update eigentext ------------------
    if($eigentext_ids!="") {
      $eigentext_array = explode(",",$eigentext_ids);
      for($i=0;$i<count($eigentext_array);$i++) {
        $et_content = addslashes($eigentext_content[$eigentext_array[$i]]);
        $et_start = $eigentext_start[$eigentext_array[$i]];
        $et_stop =  $eigentext_stop[$eigentext_array[$i]];
        $et_del = $eigentext_del[$eigentext_array[$i]];
        if($et_del=="")
          $et_del=0;
        $et_sort = $eigentext_sort[$eigentext_array[$i]];
        if($et_sort=="")
          $et_sort=0;
        $befehl = "UPDATE ".$wpdb->prefix ."tic_timer SET Content='$et_content',Date_Start='$et_start',Date_Stop='$et_stop',Del_Flag=$et_del,Sort=$et_sort,Date_Edit=".date("Y-m-d",time())." WHERE ID=".$eigentext_array[$i];
        $wpdb->query($befehl);
      }
      $eigentext_arr = get_eigentext ('arr',true,$et_sort_wahl,'ASC','all');
    }



    //------------ Daten in scroll.js schreiben ----------------------------
    $datei = dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR."wp-content/plugins/ticker/ts_files/scroll.js";
    if($typ_val=="f") {

    $inhalt = "// Title: Tigra Scroller\n".
              "// Description: See the demo at url\n".
              "// URL: http://www.softcomplex.com/products/tigra_scroller/\n".
              "// Version: 1.5\n".
              "// Date: 07-03-2003 (mm-dd-yyyy)\n".
              "// Note: Permission given to use this script in ANY kind of applications if\n".
              "//       header lines are left unchanged.\n\n".
              "var Tscroll_path_to_files = '$plugin_dir/ts_files/'\n".
              "function Tscroll_init (id) {\n".
              "  document.write ('<iframe id=\"Tscr' + id + '\" name=\"framebox\" onMouseOver=\"framebox.vstop=true\" onMouseOut=\"framebox.vstop=false\" scrolling=no frameborder=no src=\"' + Tscroll_path_to_files + 'fade.php?id='+id+'&pause_show=$pause_val&pause_hide=$pause_out_val&delta=$speed_val\" width=\"$width_val\" height=\"$height_val\"></iframe>');\n".
              "}\n";

    }
    else {

    $inhalt = "// Title: Tigra Scroller\n".
              "// Description: See the demo at url\n".
              "// URL: http://www.softcomplex.com/products/tigra_scroller/\n".
              "// Version: 1.5\n".
              "// Date: 07-03-2003 (mm-dd-yyyy)\n".
              "// Note: Permission given to use this script in ANY kind of applications if\n".
              "//       header lines are left unchanged.\n\n".
              "var Tscroll_path_to_files = '$plugin_dir/ts_files/'\n".
              "function Tscroll_init (id) {\n".
              "  document.write ('<iframe id=\"Tscr' + id + '\" scrolling=no frameborder=no src=\"' + Tscroll_path_to_files + 'scroll.php?id=' + id + '\" width=\"1\" height=\"1\"></iframe>');\n".
              "}\n";
    }
    $fp = fopen($datei,"w");
    fwrite($fp,$inhalt);
    fclose($fp);

    //------------ Daten in scroll0.css schreiben -------------------------------
    $datei = dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR."wp-content/plugins/ticker/ts_files/scroll0.css";
    $inhalt = ".ItemBody {\n".$css_val."\n}\n".
              $csserw_val;
    $fp = fopen($datei,"w");
    fwrite($fp,$inhalt);
    fclose($fp);

    // Put an options updated message on the screen

    ?>
    <div class="updated"><p><strong><?php echo $istgespeichert_w; ?></strong></p></div>
    <?php

  } //bei Formularversand

    // define default values
    if($typ_val=="") {
      $typ_val="v";
      $ticker_options["tic_typ"] = $typ_val;
    }
    if($content_val=="") {
      $content_val="db";
      $ticker_options["tic_content"] = $content_val;
    }
    if(trim($rssurl_val)=="") {
      $rssurl_val = "http://www.geschichtspassage.de/wordpress/wp-rss.php";
      $ticker_options["tic_rssurl"] = $rssurl_val;
    }
    if($width_val=="") {
      $width_val=150;
      $ticker_options["tic_width"] = $width_val;
    }
    if($height_val=="") {
      $height_val=50;
      $ticker_options["tic_height"] = $height_val;
    }
    if($pause_val=="") {
      $pause_val=2;
      $ticker_options["tic_pause"] = $pause_val;
    }
    if($pause_out_val=="") {
      $pause_out_val=0.8;
      $ticker_options["tic_pauseout"] = $pause_out_val;
    }
    if($speed_val=="") {
      $speed_val=3;
      $ticker_options["tic_speed"] = $speed_val;
    }
    if($templ_val=="") {
      $templ_val="<b>%headline%</b><br />\n%posting%";
      $ticker_options["tic_templ"] = $templ_val;
    }
    if($templrss_val=="") {
      $templrss_val="<b>%blogtitel%</b><br />\n%posttitel%";
      $ticker_options["tic_templrss"] = $templrss_val;
    }
    if($css_val=="") {
      $css_val="font-size:8pt;\nfont-family:Arial;\npadding:5px;";
      $ticker_options["tic_css"] = $css_val;
    }
    if($csserw_val=="") {
      $csserw_val=".Back{\nbackground:transparent;\n}\na{\ntext-decoration:none;\ncolor:#000000;\n}\na:visited{\ntext-decoration:none;\ncolor:#000000;\n}\n";
      $ticker_options["tic_csserw"] = $csserw_val;
    }

    update_option( tic_options, $ticker_options );


  if($deinstall_val=="yes")
    $deinstall_check = " checked";
  else
    $deinstall_check = "";

  if($typ_val=="h"){
    $typ_check_h=" checked";
    $typ_check_v="";
    $typ_check_f="";
  }
  else if($typ_val=="v"){
    $typ_check_h="";
    $typ_check_v=" checked";
    $typ_check_f="";
  }
  else {
    $typ_check_h="";
    $typ_check_v="";
    $typ_check_f=" checked";
  }

  if($content_val=="db"){
    $content_check_db=" checked";
    $content_check_eigen="";
    $content_check_rss="";
  }
  else if($content_val=="eigen"){
    $content_check_db="";
    $content_check_eigen=" checked";
    $content_check_rss="";
  }
  else {
    $content_check_db="";
    $content_check_eigen="";
    $content_check_rss=" checked";
  }

  $feldoption = "";
  $feldoption_array = Array("Content","Date_Start","Date_Stop","Del_Flag","Sort");
  for($i=0;$i<count($felder_w);$i++) {
    if($et_sort_wahl == $feldoption_array[$i])
      $feldoption .= "<option value=\"".$feldoption_array[$i]."\" selected>".$felder_w[$i]."</option>\n";
    else
      $feldoption .= "<option value=\"".$feldoption_array[$i]."\">".$felder_w[$i]."</option>\n";
  }


  // Now display the options editing screen

  echo "<div class=\"wrap\">";

  // header

  echo "<h2>" . __( "Ticker Administration", "tic_trans_domain" ) . " <span style=\"margin-left:0px;font-size:10pt;\">(V2.1)</span><br /><span style=\"margin-left:0px;font-size:12pt;\"><b>$head_hinweis:</b></span> <span style=\"font-size:10pt;\"> <script language=\"JavaScript\" type=\"text/javascript\" src=\"http://www.stegasoft.de/php/ticker-info.php?lang=$lang&v=21\"></script><br />&nbsp;</span></h2>";

  // options form

  ?>

  <form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
  <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y" />

  <table border="0" cellpadding="3" cellspacing="0">
   <tr><td colspan="3"><b><?php echo $allgemeines; ?></b></td></tr>
   <tr><td><?php echo $deinstall_w; ?>:</td><td><input type="checkbox" name="ticdeinstall" value="yes"<?php echo $deinstall_check; ?> /></td><td><?php echo $deinstall_hinweis_w; ?></td></tr>
  </table>
  <br />
  <table border="0" cellpadding="3" cellspacing="0">
   <tr>
    <td><?php echo $typ_w; ?>:</td>
    <td>
      <input type="radio" name="<?php echo $data_field_name_typ; ?>" value="v"<?php echo $typ_check_v; ?> /><?php echo $vertikal_w; ?>
      <input type="radio" name="<?php echo $data_field_name_typ; ?>" value="h"<?php echo $typ_check_h; ?> style="margin-left:10px;" /><?php echo $horizontal_w; ?>
      <input type="radio" name="<?php echo $data_field_name_typ; ?>" value="f"<?php echo $typ_check_f; ?> style="margin-left:10px;" /><?php echo $fading_w; ?>
    </td>
    <td>
      <span style="margin-left:30px;"><?php echo $typ_hinweis_w; ?></span>
    </td>
   </tr>
   <tr>
    <td><?php echo $text_w; ?>:</td>
    <td>
      <input type="radio" name="<?php echo $data_field_name_content; ?>" value="db"<?php echo $content_check_db; ?> /><?php echo $aus_db_w; ?>
      <input type="radio" name="<?php echo $data_field_name_content; ?>" value="eigen"<?php echo $content_check_eigen; ?> style="margin-left:10px;" /><?php echo $eigener_w; ?>
      <input type="radio" name="<?php echo $data_field_name_content; ?>" value="rss"<?php echo $content_check_rss; ?> style="margin-left:10px;" /><?php echo $rss_w; ?>
    </td>
    <td>
      <span style="margin-left:30px;"><?php echo $text_hinweis_w; ?></span>
    </td>
   </tr>
  </table>

  <?php if($content_val=="db") { ?>

  <table border="0" cellpadding="3" cellspacing="0">
   <tr>
    <td><?php echo $max_zeichen_w; ?></td>
    <td><input type="text" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" size="2" /> <?php echo $max_zeichen_hinweis_w; ?></td>
   </tr>
   <tr>
    <td><?php echo $max_posts_w; ?></td>
    <td><input type="text" name="<?php echo $data_field_name_posts; ?>" value="<?php echo $posts_val; ?>" size="2" /> <?php echo $max_posts_hinweis_w; ?></td>
   </tr>
  </table>

  <table border="0" cellpadding="3" cellspacing="0">
  <tr><td><?php echo $kat_wahl_w; ?></td></tr>
   <tr>
    <td>
    <?php
      $i=0;
      foreach ($categories as $category) {
        if(trim($kat_val[$i]) != "")
          echo "<input type='checkbox' name='".$data_field_name_kat[$i]."' value='".$category->term_id."' checked /><span style='margin-right:20px;'>".$category->name."</span>\n";
        else
          echo "<input type='checkbox' name='".$data_field_name_kat[$i]."' value='".$category->term_id."' /><span style='margin-right:20px;'>".$category->name."</span>\n";

        $i++;
      }
    ?>
    </td>
   </tr>
  </table>
  <input type="hidden" name="<?php echo $data_field_name_templrss; ?>" value="<?php echo $templrss_val; ?>" />
  <?php } else if($content_val=="eigen") { ?>
  <br />
  <table border="0" cellpadding="3" cellspacing="0">
   <tr><td><b><?php echo $eigener_w; ?></b> <?php echo $eigentext_hinweis_w; ?></td></tr>
   </table>

  <table border="1" id="RB_table" cellpadding="3" cellspacing="0">
  <tr><td colspan="5" align="left"><a href='javascript:create_recordsets()'><?php echo $neu_rec_w; ?></a></td></tr>
  <tr><td align="left"><?php echo $felder_w[0]; ?></td><td align="center"><?php echo $felder_w[1]; ?></td><td align="center"><?php echo $felder_w[2]; ?></td><td align="center"><?php echo $felder_w[3]; ?></td><td align="center"><?php echo $felder_w[4]; ?></td></tr>
  </table>
  <br />


  <table border="1" cellpadding="3" cellspacing="0">
  <tr><td colspan="5" align="left"><?php echo $bearbeiten_w; ?> <select name="et_sort_wahl" size="1"><?php echo $feldoption; ?></select></td></tr>
  <tr><td align="left"><?php echo $felder_w[0]; ?></td><td align="center"><?php echo $felder_w[1]; ?></td><td align="center"><?php echo $felder_w[2]; ?></td><td align="center"><?php echo $felder_w[3]; ?></td><td align="center"><?php echo $felder_w[4]; ?></td></tr>

  <?php
  $id_array = Array();
  $kal_image = "<img src='$plugin_dir/js/img/cal.png' border='0'>";

  for($i=0;$i<count($eigentext_arr);$i++) {
    echo "<tr><td><textarea name='et_content[".$eigentext_arr[$i]["ID"]."]' style='width:".$custom_textbox_width_min.";height:".$custom_textbox_height_min."; border:solid 1px; overflow:auto;' ondblclick='bigger(this)' title='$dblclick_hinweis_gr'>".$eigentext_arr[$i]["Content"]."</textarea></td>".
             "<td><input type='text' name='et_start[".$eigentext_arr[$i]["ID"]."]' value='".$eigentext_arr[$i]["Date_Start"]."' size='12' /> <a href=\"javascript:init_cal('et_start[".$eigentext_arr[$i]["ID"]."]')\">$kal_image</a></td>".
             "<td><input type='text' name='et_stop[".$eigentext_arr[$i]["ID"]."]' value='".$eigentext_arr[$i]["Date_Stop"]."' size='12' /> <a href=\"javascript:init_cal('et_stop[".$eigentext_arr[$i]["ID"]."]')\">$kal_image</a></td>";
    if($eigentext_arr[$i]["Del_Flag"]==0)
      echo "<td><input type='checkbox' name='et_del[".$eigentext_arr[$i]["ID"]."]' value='1' /></td>";
    else
      echo "<td><input type='checkbox' name='et_del[".$eigentext_arr[$i]["ID"]."]' value='1' checked /></td>";

    echo "<td><input type='text' name='et_sort[".$eigentext_arr[$i]["ID"]."]' value='".$eigentext_arr[$i]["Sort"]."' size='3' /></td></tr>\n";

    $id_array[$i] = $eigentext_arr[$i]["ID"];
  }
  $id_string = implode (",",$id_array);
  ?>

  </table>

  <script language="JavaScript" type="text/javascript">
   var path = "<?php echo "$plugin_dir/js/"; ?>";
   var lang = "<?php echo $lang; ?>";

  function init_cal(objekt) {
    //var cal9 = new calendar3(document.forms['form1'].elements['et_start[3]']);
    var cal9 = new calendar3(document.forms['form1'].elements[objekt]);
    cal9.year_scroll = true;
    cal9.time_comp = false;
    cal9.popup();
  }


  function bigger(element) {
    if(element.style.width!="<?php echo $custom_textbox_width ?>") {
      element.style.width = "<?php echo $custom_textbox_width ?>";
      element.style.height = "<?php echo $custom_textbox_height ?>";
      element.title = "<?php echo $dblclick_hinweis_kl; ?>";
    }
    else {
      element.style.width = "<?php echo $custom_textbox_width_min ?>";
      element.style.height = "<?php echo $custom_textbox_height_min ?>";
      element.title = "<?php echo $dblclick_hinweis_gr; ?>";
    }
  }

  var rec_id = 0;
  function create_recordsets(){
    var f_tr = document.createElement("tr");
    var f_td1 = document.createElement("td");
    var f_td2 = document.createElement("td");
    var f_td3 = document.createElement("td");
    var f_td4 = document.createElement("td");
    var f_td5 = document.createElement("td");

    var f_content = document.createElement("textarea");
    var f_start = document.createElement("input");
    var f_stop = document.createElement("input");
    var f_del = document.createElement("input");
    var f_sort = document.createElement("input");
    var f_img = document.createElement("img");
    var f_img1 = document.createElement("img");

    f_img.src = "<?php echo "$plugin_dir/js/img/cal.png"; ?>";
    f_img1.src = "<?php echo "$plugin_dir/js/img/cal.png"; ?>";

    f_content.style.borderWidth="1px";
    f_content.style.borderColor="#000000";
    f_content.style.borderStyle="solid";
    f_content.style.width="<?php echo $custom_textbox_width_min ?>";
    f_content.style.height="<?php echo $custom_textbox_height_min ?>";
    f_content.name = "et_neu_content["+rec_id+"]";
    f_content.title = "<?php echo $dblclick_hinweis_gr; ?>";
    f_content.ondblclick = function(e) {bigger(this) };

    f_start.type = "text";
    f_start.style.borderWidth="1px";
    f_start.style.borderColor="#000000";
    f_start.style.borderStyle="solid";
    f_start.style.width="90px";
    f_start.style.height="20px";
    f_start.style.marginRight="5px";
    f_start.name = "et_neu_start["+rec_id+"]";
    f_start.value = "<?php echo date("Y-m-d",time()); ?>";
    f_img.onclick = function(e) {init_cal(f_start.name) };

    f_stop.type = "text";
    f_stop.style.borderWidth="1px";
    f_stop.style.borderColor="#000000";
    f_stop.style.borderStyle="solid";
    f_stop.style.width="90px";
    f_stop.style.height="20px";
    f_stop.style.marginRight="5px";
    f_stop.name = "et_neu_stop["+rec_id+"]";
    f_stop.value = "<?php echo date("Y-m-d",time()+86400); ?>";
    f_img1.onclick = function(e) {init_cal(f_stop.name) };

    f_del.type = "checkbox";
    f_del.value = "1";
    f_del.name = "et_neu_del["+rec_id+"]";

    f_sort.type = "text";
    f_sort.style.borderWidth="1px";
    f_sort.style.borderColor="#000000";
    f_sort.style.borderStyle="solid";
    f_sort.style.width="30px";
    f_sort.style.height="20px";
    f_sort.name = "et_neu_sort["+rec_id+"]";
    f_sort.value = "0";

    f_td1.appendChild(f_content);
    f_td2.appendChild(f_start);
    f_td2.appendChild(f_img);
    f_td3.appendChild(f_stop);
    f_td3.appendChild(f_img1);
    f_td4.appendChild(f_del);
    f_td5.appendChild(f_sort);

    f_tr.appendChild(f_td1);
    f_tr.appendChild(f_td2);
    f_tr.appendChild(f_td3);
    f_tr.appendChild(f_td4);
    f_tr.appendChild(f_td5);

    document.getElementById("RB_table").appendChild(f_tr);

    rec_id++;
  }
  </script>



  <input type="hidden" name="et_ids" value="<?php echo $id_string; ?>" />
  <input type="hidden" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" />
  <input type="hidden" name="<?php echo $data_field_name_posts; ?>" value="<?php echo $posts_val; ?>" />
  <input type="hidden" name="<?php echo $data_field_name_templ; ?>" value="<?php echo $templ_val; ?>" />
  <input type="hidden" name="<?php echo $data_field_name_templrss; ?>" value="<?php echo $templrss_val; ?>" />
    <?php
      $i=0;
      foreach ($categories as $category) {
        if(trim($kat_val[$i]) != "")
          echo "<input type='hidden' name='".$data_field_name_kat[$i]."' value='".$category->term_id."' />\n";
        $i++;
      }
    ?>

  <?php } else if($content_val=="rss") { ?>

  <table border="0" cellpadding="3" cellspacing="0">
   <tr>
    <td><?php echo $max_zeichen_w; ?></td>
    <td><input type="text" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" size="2" /> <?php echo $max_zeichen_hinweis_w; ?></td>
   </tr>
   <tr>
    <td><?php echo $max_posts_w; ?></td>
    <td><input type="text" name="<?php echo $data_field_name_posts; ?>" value="<?php echo $posts_val; ?>" size="2" /> <?php echo $max_posts_hinweis_rss_w; ?></td>
   </tr>
  </table>

  <table border="0" cellpadding="3" cellspacing="0">
   <tr><td><?php echo "$rss_url_w ($rssurl_hinweis_w)"; ?></td></tr>
   <tr>
    <td valign="top">
    <textarea name="<?php echo $data_field_name_rssurl; ?>" style="float:left; margin-right:10px; width:<?php echo $rssurl_textbox_width; ?>px; height:<?php echo $rssurl_textbox_height; ?>px;"><?php echo stripslashes($rssurl_val); ?></textarea>
   </tr>
  </table>
  <input type="hidden" name="<?php echo $data_field_name_templ; ?>" value="<?php echo $templ_val; ?>" />
    <?php
      $i=0;
      foreach ($categories as $category) {
        if(trim($kat_val[$i]) != "")
          echo "<input type='hidden' name='".$data_field_name_kat[$i]."' value='".$category->term_id."' />\n";
        $i++;
      }
    ?>

  <?php } ?>



  <table border="0" cellpadding="3" cellspacing="0" style="margin-top:30px;">
   <tr><td colspan="4"><b><?php echo $layout_w; ?></b></td></tr>
   <tr>
    <td><?php echo $breite_w; ?></td>
    <td><input type="text" name="<?php echo $data_field_name_width; ?>" value="<?php echo $width_val; ?>" size="2" /> px</td>
    <td><span style="margin-left:20px;"><?php echo $hoehe_w; ?></span></td>
    <td><input type="text" name="<?php echo $data_field_name_height; ?>" value="<?php echo $height_val; ?>" size="2" /> px</td>
   </tr>
  </table>

  <table border="0" cellpadding="3" cellspacing="0" style="margin-top:0px;">
   <tr><td align="left"><?php echo $css_basis_w; ?></td><td align="left"><?php echo $css_erweitert_w; ?></td></tr>
   <tr>
    <td valign="top">
    <textarea name="<?php echo $data_field_name_css; ?>" cols="24" rows="3" style="float:left; margin-right:10px;"><?php echo $css_val; ?></textarea>
    <?php echo $css_basis_hinweis_w; ?>
    </td>
    <td valign="top">
    <textarea name="<?php echo $data_field_name_csserw; ?>" cols="24" rows="3" style="float:left; margin-right:10px;"><?php echo $csserw_val; ?></textarea>
    <?php echo $css_erweitert_hinweis_w; ?>
    </td>
   </tr>
  </table>

  <?php if($content_val=="db") { ?>
  <table border="0" cellpadding="3" cellspacing="0" style="margin-top:10px;">
   <tr><td valign="top"><?php echo $template_w; ?></td></tr>
   <tr>
    <td>
    <textarea name="<?php echo $data_field_name_templ; ?>" style="width:<?php echo $templ_textbox_width; ?>px;height:<?php echo $templ_textbox_height; ?>px;"><?php echo $templ_val; ?></textarea>
    <td colspan="2" valign="top">
    <?php echo $template_hinweis_w; ?>
    </td>
   </tr>
  </table>
  <?php } ?>

  <?php if($content_val=="rss") { ?>
  <table border="0" cellpadding="3" cellspacing="0" style="margin-top:10px;">
   <tr><td valign="top"><?php echo $template_w; ?></td></tr>
   <tr>
    <td>
    <textarea name="<?php echo $data_field_name_templrss; ?>" style="width:<?php echo $templ_textbox_width; ?>px;height:<?php echo $templ_textbox_height; ?>px;"><?php echo $templrss_val; ?></textarea>
    <td colspan="2" valign="top">
    <?php echo $template_rss_hinweis_w; ?>
    </td>
   </tr>
  </table>
  <?php } ?>


  <table border="0" cellpadding="3" cellspacing="0" style="margin-top:30px;">
   <?php if($typ_val=="f") { ?>
   <tr><td colspan="6"><b><?php echo $geschwindigkeit; ?></b></td></tr>
   <tr>
    <td><?php echo $pause_fadein_w; ?></td>
    <td><input type="text" name="<?php echo $data_field_name_pause; ?>" value="<?php echo $pause_val; ?>" size="3" /> <?php echo $sek_w; ?></td>

    <td><?php echo $pause_fadeout_w; ?></td>
    <td><input type="text" name="<?php echo $data_field_name_pause_out; ?>" value="<?php echo $pause_out_val; ?>" size="3" /> <?php echo $sek_w; ?></td>

    <td><span style="margin-left:50px;"><?php echo $speed_w; ?></span></td>
    <td><input type="text" name="<?php echo $data_field_name_speed; ?>" value="<?php echo $speed_val; ?>" size="2" /> <?php echo $speed_hinweis_fade_w; ?></td>

   </tr>
   <?php } else { ?>
   <tr><td colspan="4"><b><?php echo $geschwindigkeit; ?></b> <input type="hidden" name="<?php echo $data_field_name_pause_out; ?>" value="<?php echo $pause_out_val; ?>" /></td></tr>
   <tr>
    <td><?php echo $pause_w; ?></td>
    <td><input type="text" name="<?php echo $data_field_name_pause; ?>" value="<?php echo $pause_val; ?>" size="3" /></td>
    <td><span style="margin-left:50px;"><?php echo $speed_w; ?></span></td>
    <td><input type="text" name="<?php echo $data_field_name_speed; ?>" value="<?php echo $speed_val; ?>" size="2" /> <?php echo $speed_hinweis_scroll_w; ?></td>
   </tr>
   <?php } ?>
  </table>


  <hr />

  <p class="submit">
  <input type="submit" name="Submit" value="<?php echo $speichern_w; ?>" />
  </p>

  </form>
  <br />
  <?php echo $fußnote_w; ?>

  </div>

  <?
}
?>