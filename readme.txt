=== Ticker ===
Contributors: Stephan Gaertner
Donate link: http://www.stegasoft.de
Tags: posts, ticker, textfading, textscroll, newsticker
Requires at least: 2.3
Tested up to: 2.5
Stable tag: 2.1


== Description ==
Mit "Ticker" koennen Sie ganz einfach Ihre Artikel, eigenen Text oder RSS-Feeds als gescrollten oder gefadeten Text an beliebiger
Stelle Ihres Blogs anzeigen lassen. Es koennen dazu eine Reihe von Einstellungen vorgenommen werden.
Z.B.:
Scroll- bzw. Fade-Geschwindigkeit, max. Anzahl anzuzeigender Posts, max. Anzahl Zeichen, nur Posts aus bestimmten Kategorien, Layout per CSS anpassen, Titel, Autor, Zeit, Artikel koennen ueber Variablen angezeigt oder ausgeblendet werden u.v.m.


== Copyright ==
Wordpress - Plugin "Ticker"
Ver. 1.2 (07/2008)
(c) 2007-2008 by SteGaSoft, Stephan Gärtner
Www: http://www.stegasoft.de
eMail: gaertner@stegasoft.de

Das Plugin darf kostenlos genutzt werden. Es besteht
jedoch kein Anspruche auf Funktionalitaet. Auch wird
jegliche Haftung bei Problemen ausgeschlossen.
Die Nutzung geschieht auf eigene Verantwortung.
Der Copyrighthinweis muss auf der Seite sichtbar 
am Ticker erhalten bleiben. Weitere Infos finden 
Sie unter www.stegasoft.de.

Copyright fuer das genutzte JavaScript:
Title: Tigra Scroller
Version: 1.5
Date: 07-03-2003 (mm-dd-yyyy)
Www: http://www.softcomplex.com/products/tigra_scroller/
Der JavaScript-Code ist zum Zeitpunkt der Entwicklung dieser
Plugin-Version Freeware. Bitte beachten Sie das Copyright
von Tigra Scroller.

Copyright fuer die RSS-Funktion:
URL: http://www.web-spirit.de/startseite/7/RSS-Feed-auslesen-mit-PHP
Beschreibung: RSS Feed auslesen mit PHP
Autor: Sebastian Gollus
Internet: http://www.web-spirit.de


== Historie ==
Version 1.0
  Erste Version fuer Wordpress bis V2.2x

Version 1.1
  Angepasste Version fuer Wordpress ab V2.3x verfuegbar.
  Administration des Plugins nur noch mit Admin-Rechten moeglich.

Version 1.2 (nur wp ab version 2.3x)
  Erweiterung des Plugins mit Fade-Effekt.

Version 1.3 (nur wp ab version 2.3x)
 - Mehrsprachigkeit moeglich. Die Sprachdatei (PHP) muss als Dateinamen die
   WPLANG-Definition haben (s. wp-config.php), also z.B. "de_DE.php".
   Diese in den Ordner "lang" kopieren.
 - Ein paar Parameter fuer das Admin-Layout koennen in "global.php"
   eingestellt werden.
 - RSS-Feeds koennen als Tickertext genutzt werden

Version 1.3.1 (nur wp ab version 2.3x)
  Encoding bei RSS kann eingestellt werden, um evtl. Probleme mit Sonderzeichen
  zu beheben.

Version 2.0 (nur wp ab version 2.3x)
 - Parameter-Speicherung wurde komplett ueberarbeitet. Dadurch wird die Wordpress-
   Datenbank weniger belastet.
 - Eigentext-Verwaltung wurde komplett ueberarbeitet. Texte werden in separater Tabelle
   gespeichert. Es kann ein Start- und End-Datum angegeben werden. Dadurch laesst sich
   ganz einfach die Anzeigezeitraum des Textes, z.B. Termine, anpassen. Termine koennen schon
   im voraus eingegeben aber erst zu einem spaeteren Zeitpunkt angezeigt werden.
 - Update-Funktion wurde eingebaut. Somit koennen die Daten einer aelteren Ticker-Version
   in die neue uebernommen werden.
 - Eine Deinstallations-Option wurde eingebaut.

Version 2.1 (nur wp ab version 2.3x)
 - Start- /Stop-Datum-Eingabe bei eigenen Text mit Kalender-Funktion
 - Problem mit Sprachwahl behoben


== Installation ==
Laden Sie den Ordner "ticker" einfach in das Pluginverzeichnis
von Wordpress hoch. Lassen Sie ggf. die Dateien einer aelteren Version ueberschreiben

Aendern Sie die Zugriffs-/Schreibrechte folgender Dateien auf 777 (chmod):
 - ts_files/scroll.js
 - ts_files/scroll0.css

Loggen Sie sich dann als Admin unter
Wordpress ein. Unter dem Menuepunkt "Plugins" koennen Sie Ticker
nun aktivieren. Sie finden dort auch den Untermenuepunkt "Ticker".
Durch Klick auf diesen Link gelangen Sie zur Administration des
Plugins.

Falls Sie vorher eine aeltere Version von Ticker genutzt haben, erscheint
oben die Meldung "Update done. Cleare database now".
Durch Klick auf "Cleare database now" wird die Wordpress-Datenbank von
den alten, nicht mehr benoetigten Eintraegen bereinigt (empfohlen).

Falls Sie zum ersten Mal Ticker nutzen, erscheint die Meldung "Table installed.".

Tragen Sie an der Stelle in Ihrem Template, an der der Ticker
erscheinen soll (z.B in sidebar.php), noch folgenden Funktionsaufruf
ein: <?php show_ticker(); ?>
Wenn Sie das Plugin deaktivieren, entfernen Sie bitte diesen Funktionsaufruf
wieder.





== Administration ==
Allgemeines
  Deinstallieren:
     Wenn Sie dieses Feld markieren, werden alle zu Ticker gehoerenden Tabellen und Optionen
     nach Deaktivierung des Plugins aus der Wordpress-Datenbank geloescht (empfohlen).


  Typ: Hier geben Sie an, ob der Text horizontal oder vertikal scrollen soll.
       Die dritte Moeglichkeit ist ein Fade-In/Out - Effekt

  Inhalt: Text aus Datenbank holen (Posts), eigenen Text anzeigen lassen  oder RSS abonnieren

  Max. Ticker-Zeichen:
      Hier koennen Sie festlegen, wieviel Zeichen der
      Ticker-Text maximal haben darf. Bleibt das
      Feld leer, wird der ganze Text, abhaengig von
      den Template-Vorgaben (s. unten), angezeigt.
  Max. Anzahl Posts:
      Hier koennen Sie angeben, wieviele Eintraege angezeigt
      werden sollen. Diese werden absteigend nach Datum sortiert.
      Bleibt das Feld leer, werden alle Posts gezeigt.

  Nur Posts aus Kategorie:
      Hier koennen Sie die Posts nach ihrer Kategoriezugehoerigkeit einschraenken. Es
      werden nur Posts der ausgewaehlten Kategorie(n) angezeigt. Ist nichts aktiviert,
      wird auch nichts angezeigt!

  Feed URLs:
      Tragen Sie hier die komplette RSS-Feed-URL ein, z.B. http://www.geschichtspassage.de/wordpress/wp-rss.php
      Sie koennen mehrere URLs angeben, die Sie mit [Return] oder [Enter] trennen.
      Beachten Sie aber, dass das Script langsamer arbeitet, je mehr Feeds Sie nutzen.
      Falls es Probleme mit Umlauten oder Sonderzeichen gibt, koennen Sie direkt nach der URL mit
      Komma getrennt entweder "no" (kein Encoding) oder "auto" angeben. 
      Z.B so:
      http://www.geschichtspassage.de/wordpress/wp-rss.php,no
      http://www.tagesschau.de/xml/rss2,auto


Layout
  Breite:   Breite des Scroll-Textfeldes in Pixel.
  Hoehe:  Hoehe des Scroll-Textfeldes in Pixel.

  CSS Basis-Layout:
      Hier koennen Sie die Eigenschaften der vordefinierten CSS-Klasse "ItemBody"
      anpassen, z.B. Schriftgroesse etc. Bitte beachten Sie, dass diese Angaben keinen
      Einfluss auf HTML-Tags haben, die im Scrolltext vorkommen. Diese koennen Sie
      im Feld "CSS erweitertes Layout" angeben.

  CSS erweitertes Layout:
      Hier koennen Sie zusaetzliche CSS-Definitionen fuer ggf. im Scrolltext vorkommende
      HTML-Tags angeben, z.B. a, a:hover, a:visited etc. Dort bestimmen Sie auch,
      welche Eigenschaften die vordefinierte CSS-Klasse "Back" haben soll.
      Tragen Sie dann z.B. folgenden Code ein:
      .Back{background: #FFFFFF;}
      Der Hintergrund des Scrollfeldes wird in diesem Beispiel weiss eingefaerbt.

  Template:
      Hier koennen Sie den Inhalt des Scroll-Textfeldes beeinflussen. Es stehen drei/fuenf Platzhalter
      zur Verfuegung. Das Aussehen kann durch Nutzung von HTML-Tags veraendert werden.
      Beachten Sie bitte, dass der Wert unter "Max. Ticker-Zeichen" den Text evtl. so kuerzt, dass
      Angaben fehlen koennen.

Geschwindigkeit
  Pause:
    Scrolling: Wartezeit bis zum naechsten Scrollen in Sekunden
    Fading   : Anzeigedauer des Textes

  Pause (faded out):
    Fading   : Pause, nach der neuer Text wieder eingeblendet wird

  Scrollspeed:
    Scrolling: Anzahl Pixels pro 40 Millisekunden, um die gescrollt wird
    Fading   : Fade-In/Out-Geschwindigkeit


eigener Text:
 - Klicken Sie auf "neuer Datensatz" um einen neuen eigenen Text anzulegen.
   Durch Doppelklick auf das Eingabefeld unter Inhalt wird dieses Vergroessert.
   Die Masse koennen in der Datei global.php angepasst werden.
 - Tippen Sie den gewuenschten Text ein.
 - Aendern Sie ggf. das Start und/oder das End-Datum. 
   Wichtig: Das Format muss JJJJ-MM-DD sein, also 2008-10-06 fuer den 06.10.2008.
   Das Start-Datum gibt an, ab wann der Text gezeigt werden soll.
   Das End-Datum gibt an, ab wann der Text nicht mehr gezeigt werden soll.
   
   Ist das Feld in der Spalte "Loeschen" markiert, wird der entsprechende Datensatz
   automatisch zu diesem zeitpunkt geloescht.
   Bitte beachten Sie: die Loeschung haengt direkt vom eingegebenen Enddatum ab! Vorher 
   wird der Datensatz nicht geloescht.

   In der Spalte "Sortierung" koennen Sie die Anzeige-Reihenfolge der Texte bestimmen. 
   Geben Sie dazu einfach einen Zahlenwert fuer die Position ein.
   Diese Einstellung kommt aber nur zum tragen, wenn Sie im Auswahlfeld im Tabellenkopf den
   Eintrag "Sortierung" auswaehlen.



Vergessen Sie zum Schluss das Speichern nicht!


Viel Spass mit dem Plugin wuenscht
SteGaSoft, Stephan Gaertner