<?php

/**
 * API access to directory of laconi.ca istances
 * Copyright (C) 2009 Federico Marani
 *
 * Released under Apache 2.0 license
 *
 */

# DB HELPER
function dbgetproviders($sqlconditions=null,$sqlvars=null,$return_one=false) {
	try {
		$dbh = new PDO('sqlite:providers.db');
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	} 
	catch (PDOException $e) {
		print "Database connect error: " . $e->getMessage() . "<br/>";
		die();
	}

	$sql = 'SELECT * FROM providers as p JOIN providers_langspecific as pl ON p.id = pl.id_provider ';

	if ($_REQUEST['since'] or $sqlconditions) {
		$sql .= 'WHERE ';
		$put_AND = false;
		if ($sqlconditions) {
			$sql .= $sqlconditions;
			$put_AND = true;
		}
		if ($_REQUEST['since']) {
			if ($put_AND)
				$sql .= " AND ";
			array_push($sqlvars,$_REQUEST['since']);
			$sql .= " p.lastmodified > ? ";
			$put_AND = true;
		}
		if ($_REQUEST['hl']) {
			if ($put_AND)
				$sql .= " AND ";
			array_push($sqlvars,$_REQUEST['hl']);
			$sql .= " pl.language = ? ";
			$put_AND = true;
		}
	}

	$st = $dbh->prepare($sql);
	$st->execute($sqlvars);

	# $providers format as returned by the function:
	# $providers = array(
	#	'id' => id
	#	'nickname' => nickname
	#	'description' => array(
	#		'en' => 'english description'
	#		'it' => 'italian description'
	#		... )
	#	'fullname' => array(
	#		'en' => 'english fullname'
	#		'it' => 'italian fullname'
	#		... )
	#	'url' => ...
	# )
	$providers = array();
	while ($row = $st->fetch()) {
		if ($providers[$row['id']]) {
			$providers[$row['id']]['description'][$row['language']] = $row['description'];
			$providers[$row['id']]['fullname'][$row['language']] = $row['fullname'];
		}
		else {
			$array = array(
				'id' => $row['id'],
				'nickname' => $row['nickname'],
				'minilogo' => $row['minilogo'],
				'profilelogo' => $row['profilelogo'],
				'streamlogo' => $row['streamlogo'],
				'rooturl' => $row['rooturl'],
				'apirooturl' => $row['apirooturl'],
				'description' => array(
					$row['language'] => $row['description']
				),
				'fullname' => array(
					$row['language'] => $row['fullname']
				)
			);
			$providers[$row['id']] = $array;
		}
	}
	$providers = array_values($providers);
	if ($return_one)
		return $providers[0];
	else
		return $providers;
}


# $output contains the output method (xml,json) 
$output = $_REQUEST['output'];
if (empty($output)) die();


# $action handling... see rewriterules
$action = $_REQUEST['action'];

switch ($action) {
	case 'all':
		# GET FULL LIST OF THE PROVIDERS
		$providers = dbgetproviders();
		break;
	case 'search':
		# SEARCH FOR A SPECIFIC PROVIDER
		$q = '%'.$_REQUEST['q'].'%';
		$providers = dbgetproviders("p.nickname LIKE ? OR pl.description LIKE ? OR pl.fullname LIKE ? ",array($q,$q,$q));
		break;
	case 'getid':
		# GET A PROVIDER BY ID
		$provider = dbgetproviders("p.id == ? ",array($_REQUEST['provider_id']),True);
		break;
	case 'getnick':
		# GET A PROVIDER BY NICKNAME
		$provider = dbgetproviders("p.nickname == ? ",array($_REQUEST['provider_nickname']),True);
		break;
}

if ($output == 'xml') {
	function xw_print_provider_element($xw,$provider) {
		# print a single <provider> element on xmlwriter
		$xw->startElement('provider');
		foreach($provider as $key => $value) {
			if ($key == 'description' or $key == 'fullname') {
				# hanling languages
				foreach($value as $lang => $translated_text) {
					$xw->startElement($key);
					$xw->writeAttribute('xml:lang',$lang);
					$xw->text($translated_text);
					$xw->endElement();
				}	
			}
			else {
				# normal elements
				$xw->startElement($key);
				$xw->text($value);
				$xw->endElement();
			}
		}
		$xw->endElement();
	}

	$xw = new XMLWriter();
	$xw->openURI('php://output');
	$xw->setIndent(true); # help visualization
	$xw->startDocument('1.0', 'UTF-8');
	
	if (isset($providers)) {
		# WE HAVE TO OUTPUT AN ARRAY AND NOT A SINGLE ELEMENT (result of search and get_all)
		$xw->startElement('providers');
		foreach($providers as $provider) {
			xw_print_provider_element($xw,$provider);
		}
		$xw->endElement();
	}
	else {
		# ONLY ONE ELEMENT (result of get by id or nickname)
		xw_print_provider_element($xw,$provider);
	}

	$xw->endDocument();
	$xw->flush();
	
}
else if ($output == 'json') {
	# which format for json array??
	# for now, just output the language specific info as it is
	if (isset($providers)) {
		$jsonarray = $providers;
	}
	else {
		$jsonarray = $provider;
	}
	
	print(json_encode($jsonarray));
}


?>
