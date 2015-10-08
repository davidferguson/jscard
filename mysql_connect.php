<?php
	
	$link = mysql_connect("hostname","username","password") or die("Unable to connect to database");
	mysql_select_db("databasename") or die("Unable to select database");
	
?>