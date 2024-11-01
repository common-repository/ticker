// Title: Tigra Scroller
// Description: See the demo at url
// URL: http://www.softcomplex.com/products/tigra_scroller/
// Version: 1.5
// Date: 07-03-2003 (mm-dd-yyyy)
// Note: Permission given to use this script in ANY kind of applications if
//       header lines are left unchanged.

var Tscroll_path_to_files = 'http://192.168.0.3/wordpress25/wp-content/plugins/ticker/ts_files/'
function Tscroll_init (id) {
  document.write ('<iframe id="Tscr' + id + '" scrolling=no frameborder=no src="' + Tscroll_path_to_files + 'scroll.php?id=' + id + '" width="1" height="1"></iframe>');
}
