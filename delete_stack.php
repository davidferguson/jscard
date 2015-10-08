<?php

session_start();

$userID = $_SESSION['userid'];

if( ! isset( $_SESSION['username'] ) )
{
	header( "Location: error.php?error=2" );
	exit;
}

$stackid = $_GET['id'];

include('mysql_connect.php');

if( mysql_num_rows(mysql_query("SELECT * FROM `stacks` where `users_id` =$userID AND `id` =$stackid")) != 1 )
{
	echo "You do not have permission to delete this stack.";
}

mysql_query("DELETE FROM `stacks` WHERE `id` =$stackid");
mysql_query("DELETE FROM `cards` WHERE `stacks_id` =$stackid");
mysql_query("DELETE FROM `parts` WHERE `stacks_id` =$stackid");

$query = mysql_query("SELECT * FROM stacks WHERE id='$stackid'");
$numrowsStacks = mysql_num_rows($query);
$query = mysql_query("SELECT * FROM cards WHERE stacks_id='$stackid'");
$numrowsCards = mysql_num_rows($query);
$query = mysql_query("SELECT * FROM parts WHERE stacks_id='$stackid'");
$numrowsParts = mysql_num_rows($query);
if ( $numrowsStacks == 0 || $numrowsCards == 0 || $numrowsParts == 0 )
{
	header('Location: stack.php?stack=1');
}
else
{
	echo "There has been an error, and your stack was not deleted. Please try again.</br>If this error persists, please contect <a href='mailto:admin@jscard.org'>admin@jscard.org</a>.";
}
?>