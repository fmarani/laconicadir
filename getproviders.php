<?php

/**
 * Laconi.ca directory
 * Copyright (C) 2009 Federico Marani
 *
 * Released under Apache 2.0 license
 *
 * API access
 */

require_once 'support/db.php';

# $output contains the output method (xml,json) 
$output = $_REQUEST['output'];
if (empty($output)) die();

$dbh = dbconnect();

# $action handling... see rewriterules
$action = $_REQUEST['action'];

switch ($action) {
	case 'all':
		# GET FULL LIST OF THE PROVIDERS
		$providers = dbgetproviders($dbh);
		break;
	case 'search':
		# SEARCH FOR A SPECIFIC PROVIDER
		$q = '%'.$_REQUEST['q'].'%';
		$providers = dbgetproviders($dbh,"p.nickname LIKE ? OR pl.description LIKE ? OR pl.fullname LIKE ? ",array($q,$q,$q));
		break;
	case 'getid':
		# GET A PROVIDER BY ID
		$provider = dbgetproviders($dbh,"p.id = ? ",array($_REQUEST['provider_id']),True);
		break;
	case 'getnick':
		# GET A PROVIDER BY NICKNAME
		$provider = dbgetproviders($dbh,"p.nickname = ? ",array($_REQUEST['provider_nickname']),True);
		break;
}

if ($output == 'xml') {
	function xw_print_provider_element($xw,$provider) {
		# print a single <provider> element on xmlwriter
		$xw->startElement('provider');
		foreach($provider as $key => $value) {
			if ($key == 'description' or $key == 'fullname' or $key == 'categories') {
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
	else if (isset($provider)) {
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
