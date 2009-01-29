<?php

print "getproviders<br/>";

$output = $_REQUEST['output'];
if (empty($output)) die();

try {
	$dbh = new PDO('sqlite:file=providers.db');
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
} 
catch (PDOException $e) {
	print "Database connect error: " . $e->getMessage() . "<br/>";
	die();
}

# $action handling...
$action = $_REQUEST['action'];

bla bla  bla

if ($output == 'xml') {
	print "outputting in xml...<br/>";
}
else if ($output == 'json') {
	print "outputting in json...<br/>";
}

print_r ($_REQUEST);

?>
