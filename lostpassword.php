<?php

function validate_username($v_username)
{
	return eregi('[^a-z0-9_]', $v_username) ? FALSE : TRUE;
}

if( ! validate_username( $_POST["jssuname"] ) )
{
	header( "Location: index.html" );
	exit;
}

include('mysql_connect.php');

$dbresult = mysql_query( "SELECT * FROM users WHERE username='" . strtolower( $_POST["jssuname"] ) . "'" );

if ( mysql_num_rows($dbresult) != 0 )
{
	$dbresult = mysql_fetch_array( $dbresult, MYSQL_ASSOC );
	
	mysql_query("UPDATE password FROM users WHERE username='" . strtolower( $_POST["jssuname"] ) . "'" );

	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	
	$emailAddr = $dbresult["email"];
	$emailSubject = "jsCard: Your Password";
	$emailBody = "<html><h1>BIG TEXT</h1></html>";

	mail( $emailAddr, $emailSubject, $emailBody );
//	header( "Location: index.html", $headers );
//	exit;
}

header( "Location: index.html" );

?>