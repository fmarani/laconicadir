<?php

# DB CONNECT HELPER
function dbconnect() {
	try {
		$dbh = new PDO('sqlite:file=providers.db');
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
		return $dbh;
	} 
	catch (PDOException $e) {
		print "Database connect error: " . $e->getMessage() . "<br/>";
		die();
	}
}


# $output contains the output method (xml,json) 
$output = $_REQUEST['output'];
if (empty($output)) die();


# $action handling...
$action = $_REQUEST['action'];

switch ($action) {
	case 'all':
		# GET FULL LIST OF THE PROVIDERS
		$dbh = dbconnect();
		$st = $dbh->prepare('SELECT * FROM providers as p JOIN providers_langspecific as pl ON p.id = pl.id_provider');
		$st->execute();
		
		$providers = array();
		while ($row = $st->fetch()) {
			# ID,URL,REGURL,LANG,DESC,FULLNAME
			# STESSOID,STESSOURL,STESSOREGURL,LANG2,DESC2,FULLNAME2
			if ($providers[$row['id']]) {
				$providers[$row['id']]['description'][$row['lang']] = $row['description'];
			}
			else {
				$array = array(
					'id' => $row['id'],
					'nickname' => $row['nickname'],
					'description' => array(
						$row['lang'] => $row['description']
					)
				);
				$providers[$row['id']] = $array;
			}
		}
		# FIXME: handling $_REQUEST['since']
		print_r($providers);
		break;
	case 'search':
		# SEARCH FOR A SPECIFIC PROVIDER
		query($_REQUEST['q']);
		break;
	case 'getid':
		# GET A PROVIDER BY ID
		query($_REQUEST['provider_id']);
		break;
	case 'getnick':
		# GET A PROVIDER BY NICKNAME
		query($_REQUEST['provider_nickname']);
		break;
}

if ($output == 'xml') {
	print "outputting in xml...<br/>";
}
else if ($output == 'json') {
	print "outputting in json...<br/>";
}


?>
