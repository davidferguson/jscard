<?php

function clearLog()
{
	$file = 'output.html';
	file_put_contents($file, "");
}

function logThis ( $something )
{
	$file = 'output.html';
	$something = str_replace("\\n", "<br>", $something);
	$something = str_replace("\n", "<br>", $something);
	$current = file_get_contents($file);
	$current .= "<p>";
	$current .= $something;
	$current .= "</p>";
	//$current = $something;
	file_put_contents($file, $current);
}

//clearLog();

session_start();

if( ! isset( $_SESSION['username'] ) )
{
header("Location: error.php?error=2");
	exit;
}

include('mysql_connect.php');

$stackuserid = mysql_fetch_array( mysql_query( "select users_id from stacks where id=" . mysql_escape_string( $_GET['stack_id'] ) . " limit 1" ), MYSQL_ASSOC );
$stackuserid = $stackuserid['users_id'];

$currentuserid = mysql_fetch_array( mysql_query ( "select id from users where username='" . mysql_escape_string( $_SESSION['username'] ) . "' limit 1" ), MYSQL_ASSOC );
$currentuserid = $currentuserid['id'];

if ( $stackuserid != $currentuserid )
{
header("Location: error.php?error=1");
	exit;
}




// SAVE OBJECT

function hasObjects($string) { 

// Stip any string representations (which might contain object syntax) 
$string = preg_replace('/s:[0-9]+:".*"/Us','',$string); 

// Pull out the class named 
	preg_match_all('/O:[0-9]+:"(.*)"/U',$string,$matches,PREG_PATTERN_ORDER); 

	return count($matches[1]) > 0; 
} 

$objectType = $_GET['type'];
$objectStackID = $_GET['stack_id'];
$objectCardID = $_GET['card_id'];
$objectID = $_GET['id'];

if ( !hasObjects(urldecode(stripslashes($_GET['properties']))))
{

	$objectProperties = unserialize(base64_decode(stripslashes($_GET['properties'])));	

}
else
{
	echo "ERROR:OBJECTS";
	exit;
}

print_r($objectProperties);


if ( $objectType == "object" )
{

	$dbresult = mysql_query("select id from parts where stacks_id=" . mysql_escape_string( $objectStackID ) . " and cards_id=" . mysql_escape_string( $objectCardID ) . " and part_id=" . mysql_escape_string( $objectID ) );

	if ( mysql_num_rows($dbresult) == 0 )
	{
			mysql_query( "insert into parts values( 0, " . mysql_escape_string( $objectID ) . ", " . $objectStackID . ", " . $objectCardID . ", '" .
																				mysql_escape_string( "" ) . "', '" .
																				mysql_escape_string( $objectProperties['name'] ) . "', '" .
																				mysql_escape_string( $objectProperties['partorder'] ) . "', '" .
																				mysql_escape_string( $objectProperties['top'] ) . "', '" .
																				mysql_escape_string( $objectProperties['left'] ) . "', '" .
																				mysql_escape_string( $objectProperties['width'] ) . "', '" .
																				mysql_escape_string( $objectProperties['height'] ) . "', '" .
																				mysql_escape_string( $objectProperties['stype'] ) . "', '" .
																				mysql_escape_string( $objectProperties['value'] ) . "', '" .
																				mysql_escape_string( $objectProperties['script'] ) . "', '" .
																				mysql_escape_string( $objectProperties['visible'] ) . "', '" .
																				mysql_escape_string( $objectProperties['enabled'] ) . "', '" .
																				mysql_escape_string( $objectProperties['style'] ) . "', '" .
																				mysql_escape_string( $objectProperties['family'] ) . "', '" .
																				mysql_escape_string( $objectProperties['locktext'] ) . "', '" .
																				mysql_escape_string( $objectProperties['hilite'] ) . "', '" .
																				mysql_escape_string( $objectProperties['autohilite'] ) . "', '" .
																				mysql_escape_string( $objectProperties['dontwrap'] ) . "', '" .
																				mysql_escape_string( $objectProperties['autoselect'] ) . "', '" .
																				mysql_escape_string( $objectProperties['multiplelines'] ) . "', '" .
																				mysql_escape_string( $objectProperties['showname'] ) . "' )" );
															
	}
	else
	{
		mysql_query( "UPDATE parts SET name='" . mysql_escape_string( $objectProperties['name'] ) .
											 		"', `part_order`='" . mysql_escape_string( $objectProperties['partorder'] ) .
											 		"', `top`='" . mysql_escape_string( $objectProperties['top'] ) .
											 		"', `left`='" . mysql_escape_string( $objectProperties['left'] ) .
											 		"', `width`='" . mysql_escape_string( $objectProperties['width'] ) .
											 		"', `height`='" . mysql_escape_string( $objectProperties['height'] ) .
											 		"', `value`='" . mysql_escape_string( $objectProperties['value'] ) .
													"', `script`='" . mysql_escape_string( $objectProperties['script'] ) .
													"', `visible`='" . mysql_escape_string( $objectProperties['visible'] ) .
											 		"', `enabled`='" . mysql_escape_string( $objectProperties['enabled'] ) .
													"', `style`='" . mysql_escape_string( $objectProperties['style'] ) .
													"', `family`='" . mysql_escape_string( $objectProperties['family'] ) .
											 		"', `locktext`='" . mysql_escape_string( $objectProperties['locktext'] ) .
											 		"', `hilite`='" . mysql_escape_string( $objectProperties['hilite'] ) .
											 		"', `autohilite`='" . mysql_escape_string( $objectProperties['autohilite'] ) .
													"', `dontwrap`='" . mysql_escape_string( $objectProperties['dontwrap'] ) .
											 		"', `autoselect`='" . mysql_escape_string( $objectProperties['autoselect'] ) .
													"', `multiplelines`='" . mysql_escape_string( $objectProperties['multiplelines'] ) .
													"', `showname`='" . mysql_escape_string( $objectProperties['showname'] ) .
											 		"' WHERE stacks_id=" . mysql_escape_string( $objectStackID ) .
											 		" AND cards_id=" . mysql_escape_string( $objectCardID ) .
										 			" AND part_id=" . mysql_escape_string( $objectID ) );
	}															
}
else if ( $objectType == "card" )
{
	
	$dbresult = mysql_query( "select id from cards where stacks_id=" . mysql_escape_string( $objectStackID ) . " and card_id=" . mysql_escape_string( $objectCardID ) );
	
	if ( mysql_num_rows($dbresult) == 0 )
	{
		mysql_query( "insert into cards values( 0, '" . mysql_escape_string( $objectCardID ) . "', '" .
																			 mysql_escape_string( $objectStackID ) . "', '" .
																			 mysql_escape_string( $objectProperties['cardorder'] ) . "', '" .
																			 mysql_escape_string( $objectProperties['name'] ) . "', '" .
																			 "', '" .
																			 mysql_escape_string( $objectProperties['script'] ) . " )" ); // Add support for image above
	}
	else
	{
		$result = mysql_query( "update cards set `card_order`='" . mysql_escape_string( $objectProperties['cardorder'] ) . "', " .
														"`name`='" . mysql_escape_string( $objectProperties['name'] ) . "', " .
														"`script`='" . mysql_escape_string( $objectProperties['script'] ) . "' " .
														"where stacks_id=" . mysql_escape_string( $objectStackID ) . " and card_id=" . mysql_escape_string( $objectCardID ) );
	
	}
	
}
else if ( $objectType == "deleteparts" )
{

	$dbresult = mysql_query( "select id, part_id from parts where stacks_id=" . $objectStackID . " and cards_id=" . $objectCardID );
	
	while( $line = mysql_fetch_array( $dbresult, MYSQL_ASSOC ) )
	{
		if( ! in_array( $line['part_id'], $objectProperties ) )
		{
			mysql_query( "delete from parts where id=" . $line['id'] );
		}
	}

}
else if ( $objectType == "deletecards" )
{
	$dbresult = mysql_query( "select id, card_id from cards where stacks_id=" . $objectStackID );
	
	while( $line = mysql_fetch_array( $dbresult, MYSQL_ASSOC ) )
	{
		if( ! in_array( $line['card_id'], $objectProperties ) )
		{
			mysql_query( "delete from cards where id=" . $line['id'] );
			mysql_query( "delete from parts where cards_id=" . $line['card_id'] . " and stacks_id=" . $objectStackID );
		}
	}

}

?>