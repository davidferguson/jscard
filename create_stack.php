<?php

session_start();

if( ! isset( $_SESSION['username'] ) )
{
	header( "Location: error.php?error=2" );
	exit;
}

$name = $_GET['name'];
$id = $_SESSION['userid'];
$size = $_GET['size'];
$small = "small";
$medium = "medium";
$large = "large";


if ($size === $small)
{
	$dimension_one = "320";
	$dimension_two = "240";
}
else if ($size === $medium)
{
	$dimension_one = "640";
	$dimension_two = "480";
}
else if ($size === $large)
{
	$dimension_one = "960";
	$dimension_two = "720";
}

include('mysql_connect.php');

mysql_query( "insert into stacks values('', '$id', '$name', '$dimension_one', '$dimension_two', '', 0, '')" );

$query = mysql_query("SELECT * FROM stacks WHERE name='$name'");
$numrows = mysql_num_rows($query);
if ($numrows == 1)
{
	$row = mysql_fetch_assoc($query);
	$dbid = $row['id'];
	
	echo $dbid;
	
	mysql_query( "INSERT INTO cards (`id`, `card_id`, `stacks_id`, `card_order`, `name`, `image`, `script`) VALUES (NULL, '1', '$dbid', '1', 'Card 1', '', '');" );
	mysql_close();
	
	header ('Location:stack.php?stack=' . $dbid);
}
else
{
	echo "There has been an error, and your stack was not created. Please try again.</br>If this error persists, please contect <a href='mailto:admin@jscard.org'>admin@jscard.org</a>.";
}

?>