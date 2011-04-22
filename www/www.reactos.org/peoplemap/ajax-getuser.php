<?php
/*
  PROJECT:    People Map of the ReactOS Website
  LICENSE:    GNU GPLv2 or any later version as published by the Free Software Foundation
  PURPOSE:    AJAX backend for the People Map for getting user information
  COPYRIGHT:  Copyright 2007-2008 Colin Finck <mail@colinfinck.de>
*/

	header("Content-type: text/xml");
	
	if(!isset($_GET["query"]) || !isset($_GET["subject"]))
		die("<error>Necessary information not specified!</error>");
	
	if(!$_GET["subject"] == "userid" && strlen($_GET["query"]) < 3)
		die("<error>Minimum query length is 3 characters!</error>");
	
	
	require_once("config.inc.php");
	
	try
	{
		$dbh = new PDO("mysql:host=$DB_HOST", $DB_USER, $DB_PASS);
	}
	catch(PDOException $e)
	{
		// Give no exact error message here, so no server internals are exposed
		die("<error>Could not establish the DB connection</error>");
	}
	
	// Build the query
	$query = "SELECT u.id, u.name, u.fullname, l.latitude, l.longitude " .
	         "FROM $DB_ROSCMS.roscms_accounts u " .
	         "JOIN $DB_PEOPLEMAP.user_locations l ON u.id = l.roscms_user_id ";
	
	switch($_GET["subject"])
	{
		case "username":
			$query .= "WHERE u.name LIKE :query";
			break;
		
		case "fullname":
			$query .= "WHERE u.fullname LIKE :query";
			break;
		
		case "group":
			$query .= "JOIN $DB_ROSCMS.roscms_rel_groups_accounts g ON u.id = g.user_id " .
			          "WHERE g.group_id = :query";
			break;
		
		case "userid":
			$query .= "WHERE u.id = :query";
			$_GET["query"] = (int)$_GET["query"];
			break;
		
		default:
			die("<error>Invalid subject!</error>");
	}
	
	$query .= " ORDER BY u.name ASC";
	
	switch($_GET["subject"])
	{
		case "username":
		case "fullname":
			$query .= " LIMIT 25";
			break;
		
		case "group":
			$query .= " LIMIT 500";
			break;
		
		case "userid":
			$query .= " LIMIT 1";
			break;
	}
	
	$stmt = $dbh->prepare($query);
	
	switch($_GET["subject"])
	{
		case "username":
		case "fullname":
			$stmt->bindValue(":query", $_GET["query"] . "%");
			break;
		
		case "group":
		case "userid":
			$stmt->bindParam(":query", $_GET["query"]);
			break;
	}
	
	$stmt->execute() or die("<error>Query failed</error>");
	
	echo "<userinformation>";

	while($row = $stmt->fetch(PDO::FETCH_NUM))
	{
		$row[2] = mb_encode_numericentity($row[2], array (0xff, 0xffff, 0, 0xffff), 'UTF-8');
		echo "<user>";
		printf("<id>%u</id>", $row[0]);
		printf("<username>%s</username>", $row[1]);
		printf("<fullname>%s</fullname>", htmlspecialchars($row[2]));
		printf("<latitude>%s</latitude>", $row[3]);
		printf("<longitude>%s</longitude>", $row[4]);
		echo "</user>";
	}
	
	echo "</userinformation>";
?>
