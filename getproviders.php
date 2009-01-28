<?php

print "getproviders<br/>";

$output = $_REQUEST['output'];
if (!empty($output)) {
	if ($output == 'xml') {
		print "outputting in xml...<br/>";
	}
	else if ($output == 'json') {
		print "outputting in json...<br/>";
	}
}

print_r ($_REQUEST);

?>
