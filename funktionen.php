<?

//============= Daten für JavaScript aufbereiten =================================
function ticker_js() {
  global $plugin_dir,$wpdb,$max_content_len,$holder_w,$holder_h,$max_records,$tic_pause,$tic_template,$tic_speed,$tic_typ,$text_typ,$eigentext, $typ_val,$ticker_options;

  $scroller_items_array = Array();
  $scroller_items_str = "";

  $fader_items_array = Array();
  $fader_items_str = "";

  if($text_typ=="db") {
    if(trim($max_records)=="")
      $limit_str = "";
    else
      $limit_str = " LIMIT $max_records";

    $categories = $wpdb->get_results("SELECT * FROM $wpdb->terms ORDER BY name");
    $kat_count = count($categories);

    $kat_array = Array();
    $kat_str = "";
    for($i=0;$i<$kat_count; $i++) {
      $kat_name = "tic_kat".$i;
      if(trim($ticker_options[$kat_name]) != "")
        array_push($kat_array,$ticker_options[$kat_name]);
    }
    $kat_str = implode(",",$kat_array);



    if($kat_str!="") {
      $post_id_array = Array();
      $post_id_str = "";
      $kat_searchs = $wpdb->get_results("SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id IN ($kat_str)");
      foreach ($kat_searchs as $kat_search) {
        array_push($post_id_array,$kat_search->object_id);
      }
      $post_id_str = implode(",",$post_id_array);
      if($post_id_str != "")
        $fivesdrafts = $wpdb->get_results("SELECT ID, post_title, post_content, post_date, post_category, post_author  FROM $wpdb->posts WHERE post_status = 'publish' AND post_type='post' AND ID IN ($post_id_str) ORDER BY post_modified DESC".$limit_str);
      else
        $fivesdrafts = $wpdb->get_results("SELECT ID, post_title, post_content, post_date, post_category, post_author  FROM $wpdb->posts WHERE post_status = 'publish' AND post_type='xxx' ORDER BY post_modified DESC".$limit_str);


     //$fivesdrafts = $wpdb->get_results("SELECT ID, post_title, post_content, post_date, post_category, post_author  FROM $wpdb->posts WHERE post_status = 'publish' AND post_type='post' ORDER BY post_modified DESC".$limit_str);

    }
    else
      $fivesdrafts = $wpdb->get_results("SELECT ID, post_title, post_content, post_date, post_category, post_author  FROM $wpdb->posts WHERE post_status = 'publish' AND post_type='xxx' ORDER BY post_modified DESC".$limit_str);

    $fadercontentcount = 0;
    foreach ($fivesdrafts as $fivesdraft) {

      $kat_searchs = $wpdb->get_results("SELECT term_taxonomy_id FROM $wpdb->term_relationships WHERE object_id = ".$fivesdraft->ID);
      foreach ($kat_searchs as $kat_search) {
        $kat_id = $kat_search->term_taxonomy_id;
      }

      $kat_searchsm = $wpdb->get_results("SELECT name FROM $wpdb->terms WHERE term_id = $kat_id");
      foreach ($kat_searchsm as $kat_searchm) {
        $kat_name = $kat_searchm->name;
      }

      $autor_searchs = $wpdb->get_results("SELECT display_name FROM $wpdb->users WHERE ID = ".$fivesdraft->post_author);
      foreach ($autor_searchs as $autor_search) {
        $autor = $autor_search->display_name;
      }

      $text = $fivesdraft->post_content;

      $datum_zeit = explode(" ",$fivesdraft->post_date);
      $datum_elements = explode("-",$datum_zeit[0]);
      $datum_dt = $datum_elements[2].".".$datum_elements[1].".".$datum_elements[0];

      //$text = addslashes ( $text );
      $text = str_replace("<!--more-->"," ",$text);
      $text = str_replace("\n"," ",$text);
      $text = str_replace("\r"," ",$text);
      $text = strip_tags($text,"<b><i><u>");

      //----- parse template -----------------
      $tc_template = $tic_template;
      $tc_template = str_replace("\n"," ",$tc_template);
      $tc_template = str_replace("\r"," ",$tc_template);
      $tc_template = str_replace("%headline%",$fivesdraft->post_title,$tc_template);
      $tc_template = str_replace("%datum%",$datum_dt,$tc_template);
      $tc_template = str_replace("%zeit%",$datum_zeit[1],$tc_template);
      $tc_template = str_replace("%kategorie%",$kat_name,$tc_template);
      $tc_template = str_replace("%autor%",$autor,$tc_template);
      $tc_template = str_replace("%posting%",$text,$tc_template);
      $tc_template = trim($tc_template);

      if(trim($max_content_len) != "") {
        if($max_content_len<1)
          $max_content_len = 1;
        $last_blank = 0;
        if(strlen($tc_template)>$max_content_len) {
          for ($i=0; $i<$max_content_len; $i++) {
            if ($tc_template[$i] == " ")
              $last_blank = $i;
          }
          $tc_template = substr($tc_template,0,$last_blank)." ...";
        }
      }
      $tc_template = addslashes ( $tc_template );

      //$scroller_items .= " scroller1.addItem('<a href=\"".get_bloginfo('url')."/index.php?p=".$fivesdraft->ID."#more-".$fivesdraft->ID."\">$tc_template</a>');\n";
      array_push($scroller_items_array,"{'content': '<a href=\"".get_bloginfo('url')."/index.php?p=".$fivesdraft->ID."#more-".$fivesdraft->ID."\" target=\"_top\" alt=\"wordpress Ticker - Plugin powered by geschichtspassage.de\" title=\"wordpress Ticker - Plugin powered by geschichtspassage.de\">$tc_template</a>','pause_b': $tic_pause}");
      array_push($fader_items_array,"boxtxt[$fadercontentcount] = '<a href=\"".get_bloginfo('url')."/index.php?p=".$fivesdraft->ID."#more-".$fivesdraft->ID."\" target=\"_top\" alt=\"wordpress Ticker - Plugin powered by geschichtspassage.de\" title=\"wordpress Ticker - Plugin powered by geschichtspassage.de\">$tc_template</a>'");
      $fadercontentcount++;

    } //foreach

  } //if($text_typ=="db")
  else {
    $post_array = explode("[---]",$eigentext);

    for($i=0; $i<count($post_array); $i++) {
      $text = $post_array[$i];
      //$text = addslashes ( $text );
      $text = str_replace("\n","",$text);
      $text = str_replace("\r","",$text);
      //$text = strip_tags($text,"<b><i><u>");

      array_push($scroller_items_array,"{'content': '$text','pause_b': $tic_pause}");
      array_push($fader_items_array,"boxtxt[$i] = '$text'");

    } //for
  }


  $scroller_items_str = implode(",\n",$scroller_items_array);
  $fader_items_str = implode(";\n",$fader_items_array);

  if($tic_typ=="t")
    $settyp = "true";
  else
    $settyp = "false";

  $inhalt = "var LOOK = {'size': [$holder_w, $holder_h]},\n";
  if($tic_typ=="v") {
    $inhalt .= "BEHAVE = {'auto': true,'vertical': true, 'speed': $tic_speed},\n";
    $inhalt .= "ITEMS = [$scroller_items_str]";
  }
  else if($tic_typ=="h") {
    $inhalt .= "BEHAVE = {'auto': true,'vertical': false, 'speed': $tic_speed},\n";
    $inhalt .= "ITEMS = [$scroller_items_str]";
  }
  else
    $inhalt = $fader_items_str;

  return $inhalt;

}




function ticker_rss() {
   global $rssurls, $max_records, $tic_pause, $tic_typ, $tic_speed, $holder_w, $holder_h;
   $url_array = explode("\n",$rssurls);
   $inhalte_array = Array();
   $inhalte_str = "";
   $start = 0;
   for($i=0; $i<count($url_array); $i++) {
      $url_elements = explode(",",$url_array[$i]);
      if(trim($url_elements[1])=="")
        $encode = "auto";
      else
        $encode = trim($url_elements[1]);
      //$ergebnis = getRssfeed($url_array[$i], "", "no", $max_records, 3, $start);
      $ergebnis = getRssfeed(trim($url_elements[0]), "", $encode, $max_records, 3, $start);

      array_push($inhalte_array,$ergebnis[0]);
      $start = $ergebnis[1];
   }

  if($tic_typ=="v"){
    $inhalte_str = implode(",\n",$inhalte_array);
    $inhalte .= "var LOOK = {'size': [$holder_w, $holder_h]},\n";
    $inhalte .= "BEHAVE = {'auto': true,'vertical': true, 'speed': $tic_speed},\n";
    $inhalte .= "ITEMS = [$inhalte_str]";
  }
  else if($tic_typ=="h") {
    $inhalte_str = implode(",\n",$inhalte_array);
    $inhalte .= "var LOOK = {'size': [$holder_w, $holder_h]},\n";
    $inhalte .= "BEHAVE = {'auto': true,'vertical': false, 'speed': $tic_speed},\n";
    $inhalte .= "ITEMS = [$inhalte_str]";
  }
  else {
    $inhalte = implode(";\n",$inhalte_array);
  }

  return $inhalte;
}



function getRssfeed($rssfeed, $cssclass="", $encode="auto", $anzahl=10, $mode=0, $startnum=0) {
        global $tic_pause, $tic_typ, $tic_speed, $tic_templaterss, $max_content_len;

         //$tic_typ = "v";


        // $encode e[".*"; "no"; "auto"]

        // $mode e[0; 1; 2; 3]:
        // 0 = nur Titel und Link der Items weden ausgegeben
        // 1 = Titel und Link zum Channel werden ausgegeben
        // 2 = Titel, Link und Beschreibung der Items werden ausgegeben
        // 3 = 1 & 2

  $blog_titel = "";

  $scroller_items_array = Array();
  $scroller_items_str = "";

  $fader_items_array = Array();
  $fader_items_str = "";



        // Zugriff auf den RSS Feed
        $data = @file($rssfeed);
        $data = implode ("", $data);
        preg_match_all("/<item.*>(.+)<\/item>/Uism", $data, $items);

        // Encodierung
        if($encode == "auto")
        {
                preg_match("/<?xml.*encoding=\"(.+)\".*?>/Uism", $data, $encodingarray);
                $encoding = $encodingarray[1];
        }
        else
        {$encoding = $encode;}

        //echo "<!-- RSS Feed Script von Sebastian Gollus: http://www.web-spirit.de/startseite/7/RSS-Feed-auslesen-mit-PHP -->\n";
        //echo "<div class=\"rssfeed_".$cssclass."\">\n";

        // Titel und Link zum Channel
        if($mode == 1 || $mode == 3)
        {
                $data = preg_replace("/<item>(.+)<\/item>/Uism", '', $data);
                preg_match("/<title>(.+)<\/title>/Uism", $data, $channeltitle);
                preg_match("/<link>(.+)<\/link>/Uism", $data, $channellink);
                //echo "<h1><a href=\"".$channellink[1]."\" title=\"";
                $blog_titel = "<a href=\"".$channellink[1]."\" target=\"_blank\" class=\"rss_blogtitel\" title=\"";
                if($encode != "no") {
                  //echo htmlentities($channeltitle[1],ENT_QUOTES,$encoding);
                  $blog_titel .= htmlentities($channeltitle[1],ENT_QUOTES,$encoding);
                }
                else {
                  //echo $channeltitle[1];
                  $blog_titel .= $channeltitle[1];
                }
                //echo "\">";
                $blog_titel .= "\">";
                if($encode != "no") {
                  //echo htmlentities($channeltitle[1],ENT_QUOTES,$encoding);
                  $blog_titel .= htmlentities($channeltitle[1],ENT_QUOTES,$encoding);
                }
                else {
                  //echo $channeltitle[1];
                  $blog_titel .= $channeltitle[1];
                }
                //echo "</a></h1>\n";
                $blog_titel .= "</a>";
        }

        // Titel, Link und Beschreibung der Items
        $fadercontentcount = $startnum;
        foreach ($items[1] as $item) {
                $zeile_link = "";
                $zeile_describ = "";

                preg_match("/<title>(.+)<\/title>/Uism", $item, $title);
                preg_match("/<link>(.+)<\/link>/Uism", $item, $link);
                preg_match("/<description>(.*)<\/description>/Uism", $item, $description);

                $title = preg_replace('/<!\[CDATA\[(.+)\]\]>/Uism', '$1', $title);
                $description = preg_replace('/<!\[CDATA\[(.+)\]\]>/Uism', '$1', $description);

                //echo "<p class=\"link\">\n";
                //echo "<a href=\"".$link[1]."\" title=\"";
                $zeile_link = "<a href=\"".$link[1]."\" target=\"_blank\" class=\"rss_posttitel\" title=\"";
                if($encode != "no") {
                  //echo htmlentities($title[1],ENT_QUOTES,$encoding);
                  $zeile_link .= htmlentities($title[1],ENT_QUOTES,$encoding);
                }
                else {
                  //echo $title[1];
                  $zeile_link .= $title[1];
                }
                //echo "\">";
                $zeile_link .= "\">";
                if($encode != "no") {
                  //echo htmlentities($title[1],ENT_QUOTES,$encoding)."</a>\n";
                  $zeile_link .= htmlentities($title[1],ENT_QUOTES,$encoding)."</a>";
                }
                else {
                  //echo $title[1]."</a>\n";
                  $zeile_link .= $title[1]."</a>";
                }
                //echo "</p>\n";
                if($mode == 2 || $mode == 3 && ($description[1]!="" && $description[1]!=" ")) {
                  //echo "<p class=\"description\">\n";
                  $zeile_describ = "<span class=\"rss_description\">";
                  if($encode != "no") {
                    //echo htmlentities($description[1],ENT_QUOTES,$encoding)."\n";
                    $zeile_describ .= htmlentities($description[1],ENT_QUOTES,$encoding);
                  }
                  else {
                    //echo $description[1];
                    $zeile_describ .= $description[1];
                  }
                  //echo "</p>\n";
                  $zeile_describ .= "</span>";
                }

                $zeile_link = str_replace("\n\r","",$zeile_link);
                $zeile_link = str_replace("\n","",$zeile_link);
                $zeile_link = str_replace("\r","",$zeile_link);

                $zeile_describ = str_replace("\n\r","",$zeile_describ);
                $zeile_describ = str_replace("\n","",$zeile_describ);
                $zeile_describ = str_replace("\r","",$zeile_describ);
                $zeile_describ = str_replace("<!--more-->"," ",$zeile_describ);
                $zeile_describ = strip_tags($zeile_describ,"<b><i><u>");

                /*
                if($mode == 2 || $mode == 3 && ($description[1]!="" && $description[1]!=" ")) {
                  $zeile = "<b>$blog_titel</b></b><br />$zeile_link<br />$zeile_describ";
                }
                else
                  $zeile = "<b>$blog_titel</b><br />$zeile_link";
                */


      $text = $fivesdraft->post_content;


      //----- parse template -----------------
      $tc_template = $tic_templaterss;
      $tc_template = str_replace("\n","",$tc_template);
      $tc_template = str_replace("\r","",$tc_template);
      $tc_template = str_replace("%blogtitel%",$blog_titel,$tc_template);
      $tc_template = str_replace("%posttitel%",$zeile_link,$tc_template);

      if(trim($max_content_len) != "") {
        if($max_content_len<1)
          $max_content_len = 1;
        $last_blank = 0;
        if(strlen($zeile_describ)>$max_content_len) {
          for ($i=0; $i<$max_content_len; $i++) {
            if ($zeile_describ[$i] == " ")
              $last_blank = $i;
          }
          $zeile_describ = substr($zeile_describ,0,$last_blank)." ...";
        }
      }

      $tc_template = str_replace("%posting%",$zeile_describ,$tc_template);
      $tc_template = trim($tc_template);

      $tc_template = addslashes ( $tc_template );



                array_push($scroller_items_array,"{'content': '$tc_template','pause_b': $tic_pause}");
                array_push($fader_items_array,"boxtxt[$fadercontentcount] = '$tc_template'");
                $fadercontentcount++;
                if ($anzahl-- <= 1) break;
        }
        //echo "</div>\n\n";

        $scroller_items_str = implode(",\n",$scroller_items_array);
        $fader_items_str = implode(";\n",$fader_items_array);

  if($tic_typ=="t")
    $settyp = "true";
  else
    $settyp = "false";

  if(($tic_typ=="v") or ($tic_typ=="h")){
    $inhalt = $scroller_items_str;
  }
  else
    $inhalt = $fader_items_str;

  $rueckgabe[0] = $inhalt;
  $rueckgabe[1] = $fadercontentcount;

  return $rueckgabe;


}

?>