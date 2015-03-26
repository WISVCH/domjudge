<?php
/**
 * Produce a total score. Call with parameter 'static' for
 * output suitable for static HTML pages.
 *
 * Part of the DOMjudge Programming Contest Jury System and licenced
 * under the GNU GPL. See README and COPYING for details.
 */
require('init.php');

/* First check if our ip address exists */
$ip_fp = fopen(ETCDIR . "/computers.csv", "a+");
$ip_contents = fread($ip_fp, filesize(ETCDIR . "/computers.csv"));
$ip = $_SERVER['REMOTE_ADDR'];

if(strstr($ip_contents, $ip) === FALSE) {
	if(isset($_POST['submit'])) {
		fwrite($ip_fp, $ip . "\t" . $_POST['comp_name']);
	}
	else {
		?>
		<form method="POST">
			Computer name: <input type="text" name="comp_name"/><br />
			<input type="submit" name="submit" value="Save"/>
		</form>
		<?
		exit();
	}
}


$title="Scoreboard";
// set auto refresh
$refresh="30;url=./";

// This reads and sets a cookie, so must be called before headers are sent.
$filter = initScorefilter();

$menu = true;
require(LIBWWWDIR . '/header.php');

$isstatic = @$_SERVER['argv'][1] == 'static' || isset($_REQUEST['static']);

if ( ! $isstatic ) {
	echo "<div id=\"menutopright\">\n";
	putClock();
	echo "</div>\n";
}

// call the general putScoreBoard function from scoreboard.php
putScoreBoard($cdata, null, $isstatic, $filter);

echo "<script type=\"text/javascript\">initFavouriteTeams();</script>";

require(LIBWWWDIR . '/footer.php');
