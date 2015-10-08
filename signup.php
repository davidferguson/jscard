<?php

// commented out to disable signup
echo "Signup Disabled";

/*
function validate_username($v_username)
{
	return eregi('[^a-z0-9_]', $v_username) ? FALSE : TRUE;
} 

function generateRandomString($length)
{
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@£$%^&*()-=_+[]{};:"\'\|<>,./?';
	$randomString = '';
	for ($i = 0; $i < $length; $i++)
	{
		$randomString .= $characters[rand(0, strlen($characters) - 1)];
	}
	return $randomString;
}

include('mysql_connect.php');

$dbresult = mysql_query( "select * from users where username='" . strtolower( $_POST["jssuname"] ) . "'" );

if( ! validate_username( $_POST["jssuname"] ) )
{
	header( "Location: signup.html?usernameisinvalid=1&email=" . $_POST["jssemail"] );
	exit;
}

if ( mysql_num_rows($dbresult) != 0 )
{
	header( "Location: signup.html?usernamealreadyexists=1&email=" . $_POST["jssemail"] );
	exit;
}

if ( $_POST["jsspword"] != $_POST["jsscpword"] )
{
	header( "Location: signup.html?passwordsdontmatch=1&uname=" . $_POST["jssuname"] . "&email=" . $_POST["jssemail"] );
	exit;
}

if ( $_POST["jssemail"] != $_POST["jsscemail"] )
{
	header( "Location: signup.html?emailsdontmatch=1&uname=" . $_POST["jssuname"] );
	exit;
}

include("encrypt.php");

$unencrypted = $_POST["jsspword"];

$encrypted = encrypt($unencrypted);

$randomString = generateRandomString(50);

mysql_query( "insert into users values( 0, '" . mysql_real_escape_string(strtolower( $_POST["jssuname"] )) . "', '" . mysql_real_escape_string($encrypted) . "', '" . mysql_real_escape_string( $_POST["jssemail"] ) . "', '" . mysql_real_escape_string($randomString) . "')" ) or die('There has been an error, any your account was not created. Please go back and try again.');

if (!mkdir(("users/" . mysql_real_escape_string(strtolower( $_POST["jssuname"] ))), 0777, true))
{
	mysql_query( "delete from users where username='" . mysql_real_escape_string(strtolower( $_POST["jssuname"] )) . "'" );
    die('Failed to create user directory...');
}
if (!mkdir(("users/" . mysql_real_escape_string(strtolower( $_POST["jssuname"] ))) . "/sounds", 0777, true))
{
	mysql_query( "delete from users where username='" . mysql_real_escape_string(strtolower( $_POST["jssuname"] )) . "'" );
    die('Failed to create user sounds directory...');
}
if (!mkdir(("users/" . mysql_real_escape_string(strtolower( $_POST["jssuname"] ))) . "/images", 0777, true))
{
	mysql_query( "delete from users where username='" . mysql_real_escape_string(strtolower( $_POST["jssuname"] )) . "'" );
    die('Failed to create user images directory...');
}

$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

$emailAddr = mysql_real_escape_string( $_POST["jssemail"] );
$emailSubject = "Welcome to jsCard";
$emailBody = "<html><h1>Welcome to jsCard</h1><p>Hello and Welcome to jsCard.</p><p>Now that you have registered with jsCard, you can log in to create and edit your own stacks.</p><p>To get started, simply follow the link below:</p><p><a href='htttp://jscard.org/'>http://jscard.org/</a></p></html>";

mail( $emailAddr, $emailSubject, $emailBody, $headers );

header( "Location: index.html?signedup=1" );
*/
?>