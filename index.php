<?php

/**
 * Laconi.ca directory
 * Copyright (C) 2009 Federico Marani
 * 
 * Released under Apache 2.0 license
 *
 * Login page
 */

require_once 'support/db.php';


if (isset($_REQUEST['nickname']) and isset($_REQUEST['password'])) {
	$dbh = dbconnect();
	if ($_REQUEST['new_instance'] == "new_instance") {
		// check if the instance already exists
		$st = $dbh->prepare("SELECT id FROM providers WHERE nickname = ? ");
		$st->execute(array($_REQUEST['nickname']));
		if ($st->rowCount() > 0) {
			$msg = "Another instance with the same nickname is already registered";
		}
		else {
			// insert new instance into the db
			$st = $dbh->prepare("INSERT INTO providers (nickname) VALUES (?) ");
			$st->execute(array($_REQUEST['nickname']));
			$idpro = $dbh->lastInsertId();
			$st = $dbh->prepare("INSERT INTO ui_users VALUES (?,sha1(?)) ");
			$st->execute(array($idpro,$_REQUEST['password']));
			$auth_ok = true;
		}
	}
	else {	
		// login to an existing instance: check
		$st = $dbh->prepare("SELECT id FROM providers as p JOIN ui_users as u ON p.id = u.id_provider
				WHERE p.nickname = ? AND u.sha1_password = sha1(?) ");
		$st->execute(array($_REQUEST['nickname'],$_REQUEST['password']));
		if ($row = $st->fetch()) {
			$auth_ok = true;
			$idpro = $row['id'];
		}
		else
			$msg = "Authentication failed";
	}
	if ($auth_ok) {
		session_start();
		$_SESSION['logged_in'] = true;
		$_SESSION['id_provider'] = $idpro;
		header("Location: main.php");
		exit();
	}
}


?>

<html>
 <head>
  <title>Laconi.ca directory authentication</title>
  <link rel="stylesheet" type="text/css" href="support/laconicadir.css"/>
 </head>
<body>
<div align="center">
<img src="http://theme.identi.ca/identica/logo.png"/>
<div class="title">Laconi.ca directory</div><br>

<form method=POST>

<p><div class="errormsg"><?=$msg?></div></p>

<ul>
<li>
<label for="nickname">Nickname</label>
<input type="text" id="nickname" name="nickname"/>
</li>
<li>
<label for="password">Password</label>
<input type="password" id="password" name="password"/>
</li>
<li>
<label for="new_instance">Register new instance</label>
<input type="checkbox" id="new_instance" name="new_instance" value="new_instance"/>
</li>
<li>
<input type="submit" value="Enter"/>
</li>
</ul>
</form>
(cookies must be enabled!)
</div>
</body>
</html>
