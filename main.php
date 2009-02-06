<?php

/**
 * Laconi.ca directory
 * Copyright (C) 2009 Federico Marani
 * 
 * Released under Apache 2.0 license
 *
 * Directory main interface
 */

require_once 'support/db.php';

$LANGUAGES = array(
	'en' => "English",
	'it' => "Italian",
	'es' => "Spanish",
	'fr' => "French"
	);

session_start();
if (!$_SESSION['logged_in']) {
	print "ACCESS DENIED";
	die();
}
if ($_REQUEST['logout']) {
	session_destroy();
	header("Location: index.php");
	die();
}

$dbh = dbconnect();

// action handling
if ($_REQUEST['submit']) {
	$st = $dbh->prepare("UPDATE providers SET nickname = ?, minilogo = ?, profilelogo = ?,
	streamlogo = ?, rooturl = ?, registrationurl = ?, apirooturl = ? WHERE id = ?");
	if($st->execute(array(
		$_REQUEST['nickname'],
		$_REQUEST['minilogo'],
		$_REQUEST['profilelogo'],
		$_REQUEST['streamlogo'],
		$_REQUEST['rooturl'],
		$_REQUEST['registrationurl'],
		$_REQUEST['apirooturl'],
		$_SESSION['id_provider']
	)))
		$msg = "Update successful";
	else
		$msg = "Update unsuccessful";
	$st->closeCursor();
}

// action language specific handling
if ($_REQUEST['submit_lang']) {
	$lang = $_REQUEST['lang'];
	$st = $dbh->prepare("UPDATE providers_langspecific SET fullname = ?, 
		description = ?, categories = ? WHERE language = ? AND id_provider = ?");
	$st->execute(array(
		$_REQUEST['fullname'][$lang],
		$_REQUEST['description'][$lang],
		$_REQUEST['categories'][$lang],
		$lang,
		$_SESSION['id_provider']
	));
	if ($st->rowCount() > 0) {
		$msg_lang = $LANGUAGES[$lang] . " translation updated";
	}
	else {
		$st = $dbh->prepare("INSERT providers_langspecific VALUES (?,?,?,?,?) ");
		$success = $st->execute(array(
			$_SESSION['id_provider'],
			$lang,
			$_REQUEST['fullname'][$lang],
			$_REQUEST['description'][$lang],
			$_REQUEST['categories'][$lang]
		));
		$st->closeCursor();
		$msg_lang = $LANGUAGES[$lang] . " translation created";
	}
}

// retrieve data for visualization
$provider = dbgetproviders($dbh,"p.id = ? ",array($_SESSION['id_provider']),True);
?>

<html>
 <head>
  <title>Laconi.ca Directory</title>
  <link rel="stylesheet" type="text/css" href="support/laconicadir.css"/>
  <script type="text/javascript" src="support/jquery.min.js"> </script>
  <script type="text/javascript" src="support/laconicadir.js"/> </script>
</head>
 <body>
<img src="http://theme.identi.ca/identica/logo.png"/>
<div class="title">Laconi.ca directory</div><br/>
<a class="logout" href="?logout=1">logout</a><br/>

<?=$msg?>
<form class="dbform providersdata" method="POST">
<ul>
<li><label for="nickname">Nickname</label>
<input id="nickname" name="nickname" value="<?=$provider['nickname']?>"/>
</li>
<li>
<label for="minilogo">Minilogo URL</label>
<input id="minilogo" name="minilogo" value="<?=$provider['minilogo']?>"/>
</li>
<li>
<label for="profilelogo">Profile logo URL</label>
<input id="profilelogo" name="profilelogo" value="<?=$provider['profilelogo']?>"/>
</li>
<li>
<label for="streamlogo">Stream logo URL</label>
<input id="streamlogo" name="streamlogo" value="<?=$provider['streamlogo']?>"/>
</li>
<li>
<label for="rooturl">Root URL</label>
<input id="rooturl" name="rooturl" value="<?=$provider['rooturl']?>"/>
</li>
<li>
<label for="registrationurl">Registration URL</label>
<input id="registrationurl" name="registrationurl" value="<?=$provider['registrationurl']?>"/>
</li>
<li>
<label for="apirooturl">API Root URL</label>
<input id="apirooturl" name="apirooturl" value="<?=$provider['apirooturl']?>"/>
</li>
<li>
<input type="submit" class="submitbutton" name="submit" value="Save"/>
</li></ul>
</form>

<?=$msg_lang?>

<div class="dbform langspecificdata">
<p>
<label for="langselect">Language</label>
<select id="langselect" name="langselect">
<option selected disabled>Select language</option>
<?php
foreach ($LANGUAGES as $lang=>$langext) {
?>
<option value="<?=$lang?>"><?=$langext?></option>
<?php
}
?>
</select>
</p>

<div id="langcells">
<?php
foreach(array_keys($LANGUAGES) as $lang) {
?>
<div class="langcell" id="<?=$lang?>">
<form method="POST">
<ul>
<input type="hidden" name="lang" value="<?=$lang?>"/>

<li>
<label for="fullname[<?=$lang?>]">Full name</label>
<input id="fullname[<?=$lang?>]" name="fullname[<?=$lang?>]" value="<?=$provider['fullname'][$lang]?>"/>
</li>

<li>
<label for="description[<?=$lang?>]">Description</label>
<textarea id="description[<?=$lang?>]" name="description[<?=$lang?>]"><?=$provider['description'][$lang]?></textarea>
</li>

<li>
<label for="categories[<?=$lang?>]">Categories (comma separated)</label>
<input id="categories[<?=$lang?>]" name="categories[<?=$lang?>]" value="<?=$provider['categories'][$lang]?>"/>
</li>

<li>
<input type="submit" class="submitbutton" name="submit_lang" value="Save this translation"/>
</li>

</ul>
</form>
</div>
<?php
}
?>
</div>

</div>

</body>
</html>
