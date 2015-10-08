<?php

//get the stack info from GET or POST
$getstackid = $_POST['id'];
$getstackname = $_POST['name'];
$getstackdescription = $_POST['description'];


//start the session, and see if the user is logged in
session_start();
if( ! isset( $_SESSION['username'] ) )
{
	header( "Location: error.php?error=2" );
	exit;
}


include('mysql_connect.php');


$sql="SELECT * FROM stacks where id = $getstackid";
$result=mysql_query($sql);
$stackinfo=mysql_fetch_array($result);

//check if the user owns the stack they are trying to share
if ($stackinfo['users_id'] != $_SESSION['userid']){
echo $stackinfo['users_id'];
echo $_SESSION['userid'];
header( "Location: error.php?error=1");
}

//now see if the stack is already public. if this is the case, then we want to make it unpublic
if ($stackinfo['public'] == 1){
$result = mysql_query("UPDATE stacks SET public='0' WHERE id='" . $getstackid . "'") 
or die(mysql_error()); 

echo "unpublic"; 
}


//now see if the stack is not public. if this is the case, then we want to make it public. We then want to update the name and description, in case the user change it
if ($stackinfo['public'] == 0){
$result = mysql_query("UPDATE stacks SET public='1' WHERE id='" . $getstackid . "'") 
or die(mysql_error());

$result = mysql_query("UPDATE stacks SET name='" . $getstackname . "' WHERE id='" . $getstackid . "'") 
or die(mysql_error());

$result = mysql_query("UPDATE stacks SET public_description='" . $getstackdescription . "' WHERE id='" . $getstackid . "'") 
or die(mysql_error());


echo "public";

}


?>