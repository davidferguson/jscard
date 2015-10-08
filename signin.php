<?php

session_start();

include('mysql_connect.php');

include("encrypt.php");

$encrypted = encrypt($_POST["jscdp"]);

$dbresult = mysql_query( "select * from users where username='" . $_POST["jscdn"] . "' AND password='" . $encrypted . "'" );

if ( mysql_num_rows( $dbresult )== 0 )
{
	session_destroy();
	header( "Location: index.html?loginerror=1" );
	exit;
}

$dbresult = mysql_fetch_array( $dbresult, MYSQL_ASSOC );

$_SESSION['username'] = $dbresult["username"];
$_SESSION['userid'] = $dbresult["id"];

$browserAsString = $_SERVER['HTTP_USER_AGENT'];

if (strstr($browserAsString, " AppleWebKit/") && strstr($browserAsString, " Mobile/"))
{
	header( "Location:stack.php?stack=1&ioserror=yes" );
}
else
{
	header('Location:stack.php?stack=1');
}
?>