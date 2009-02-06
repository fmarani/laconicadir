<?php

/**
 * Laconi.ca directory
 * Copyright (C) 2009 Federico Marani
 *
 * Released under Apache 2.0 license
 *
 * DB support functions
 */

define(C_DBUSER,'laconicadir');
define(C_DBPASSWORD,'laconicadir');

/**
 * Connect to db
 * 
 * Wrapper for PDO connection
 *
 * @return object PDO handler
 *
 */
function dbconnect() {
	try {
		#$dbh = new PDO('sqlite:db/providers.db');
		$dbh = new PDO('mysql:host=localhost;dbname=laconicadir', C_DBUSER, C_DBPASSWORD);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
		return $dbh;
	} 
	catch (PDOException $e) {
		print "Database connect error: " . $e->getMessage() . "<br/>";
		die();
	}
}


/**
 * Get provider(s) list
 * 
 * Wrapper for provider data in db
 * 
 * @param object $dbh PDO connection handler
 * @param string $sqlconditions String appended in the WHERE clause
 * @param array $sqlvars values to subsitute in the WHERE clause via PDOStatement->execute
 * @param boolean $return_one Return only one value
 *
 * @return array providers list
 * 
 * $providers format as returned by the function:
 * $providers = array(
 *	'id' => id
 *	'nickname' => nickname
 *	'description' => array(
 *		'en' => 'english description'
 *		'it' => 'italian description'
 *		... )
 *	'fullname' => array(
 *		'en' => 'english fullname'
 *		'it' => 'italian fullname'
 *		... )
 *	'url' => ...
 * )
 */
function dbgetproviders($dbh,$sqlconditions=null,$sqlvars=null,$return_one=false) {
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

	$providers = array();
	while ($row = $st->fetch()) {
		if ($providers[$row['id']]) {
			$providers[$row['id']]['description'][$row['language']] = $row['description'];
			$providers[$row['id']]['fullname'][$row['language']] = $row['fullname'];
			$providers[$row['id']]['categories'][$row['language']] = $row['categories'];
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
				'registrationurl' => $row['registrationurl'],
				'license' => $row['license'],
				'description' => array(
					$row['language'] => $row['description']
				),
				'fullname' => array(
					$row['language'] => $row['fullname']
				),
				'categories' => array(
					$row['language'] => $row['categories']
				)
			);
			$providers[$row['id']] = $array;
		}
	}
	$st->closeCursor();
	$providers = array_values($providers);
	if ($return_one)
		return $providers[0];
	else
		return $providers;
}


?>
