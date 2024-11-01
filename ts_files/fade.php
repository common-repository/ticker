<?
include_once (dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR. "wp-config.php");
require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR ."global.php");
require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR ."funktionen.php");
include_once (dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR."wp-includes/wp-db.php");
$id = $_GET['id'];

$box_id = "Tscr".$id;
?>
<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
<title>Geschichtspassage.de</title>
<script language="JavaScript" type="text/javascript">
<!--

//###############################################
//#  Script: SteGaSoft, Stephan Gaertner, 2008  #
//#  Dieser Kopf muss bei jeder Änderung er-    #
//#  halten bleiben.                            #
//#  Modifikationen bitte mit Angabe und er-    #
//#  erlaeuterung an folgende Adresse mitteilen #
//#          gaertner@stegasoft.de              #
//#  Updates oder Erweiterungen werden unter    #
//#  folgenden Adressen mitgeteilt:             #
//#  www.stegasoft.de         und/oder          #
//#  www.geschichtspassage.de                   #
//###############################################

var n_id = <? echo $id; ?>;
 document.write("<link rel='stylesheet' href='scroll" + n_id + ".css'>")

var boxtxt = new Array();
var neutext;
var fade_direction = "in";
var textcount=0;
var fadetimer;
var vstop=false;
var timer_started = true;
var akt_pause=0;


<? if($tic_content=="rss") echo ticker_rss(); else echo ticker_js(); ?>


var pause_show = <? echo ($_GET['pause_show']*1000); ?>;
var pause_hide = <? echo ($_GET['pause_hide']*1000); ?>;
var delta = <? echo $_GET['delta']; ?>;

IE  = document.all &&!window.opera;
GK  = window.sidebar;
AOB = [];
x   = 0;


function stop() {
  window.clearTimeout(fadetimer);
  timer_started=false;
}

function start() {
  timer_started=true;
  akt_pause = 100;
  init_fader(document.getElementById("<?php echo $box_id; ?>"),delta,10,0,100,1);

}

function zeige_text() {
 if(textcount<boxtxt.length) {
    neutext = boxtxt[textcount];
    document.getElementById("<?php echo $box_id; ?>").innerHTML = neutext;
    textcount++;
  }
  else {
    textcount=0
    neutext = boxtxt[textcount];
    document.getElementById("<?php echo $box_id; ?>").innerHTML = neutext;
    textcount++;
  }

}



function Fader() {
  this.timeOut=false;
  this.fade=function(y) {
    clearTimeout(this.timeOut);
    this.value=Number(eval('this.object.'+this.attr))+(this.delta*this.evt[y]);
    if(this.value>=this.min && this.value<=this.max) {
      eval('this.object.'+this.attr+'='+this.value)
      this.timeOut=setTimeout('AOB['+this.index+'].fade('+y+')',this.rate);
    }
  }
}


function init_fader(obj,delta,rate,min,max,dir){
  if(akt_pause==0)
    akt_pause=pause_show;

  if(IE || GK) {
    AOB.push(new Fader(x));
    OB        = AOB[x];
    OB.index  = x;
    OB.object = obj;
    if(IE) {
      OB.attr = 'filters.alpha.opacity';
      OB.faktor = 1;
    }
    else {
      OB.attr   = 'style.MozOpacity';
      OB.faktor = 100;
    }
    OB.delta = delta/OB.faktor;
    OB.rate  = rate;
    OB.min   = min/OB.faktor;
    OB.max   = max/OB.faktor;
    OB.evt=[-1,1];if(dir)OB.evt.reverse();
    fadetimer = window.setTimeout('dofade('+x+')', akt_pause);
    akt_pause = pause_show;
    OB.fade(0);x++;
  }
}


function dofade(x) {
  if(fade_direction == "in") {
    fade_direction = "out";
    AOB[x].fade(1);
    akt_pause = pause_hide;
  }
  else if(fade_direction == "out") {
    fade_direction = "in";
    AOB[x].fade(0);
    zeige_text();
    akt_pause = pause_show;
  }
  fadetimer = window.setTimeout('dofade('+x+')', akt_pause);
}


window.setInterval('check_status()', 100);


function check_status() {
  if((vstop==false) && (timer_started==false)) {
    start();
  }
  else if ((vstop==true) && (timer_started==true)) {
    stop();
  }
}


-->
</script>

</head>

<body style="margin:0">

<div style="position:absolute; left:0px; top:0px;filter:alpha(opacity=40);-moz-opacity:0.4;" name="edition" id="<?php echo $box_id; ?>" class="ItemBody">Starttext</div>
<script language="JavaScript" type="text/javascript">zeige_text(); init_fader(document.getElementById("<?php echo $box_id; ?>"),delta,10,0,100,1)</script>

</body>
</html>