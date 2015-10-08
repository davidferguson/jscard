<?php
/*

Copyright (c) 2006 Tyler J. Vano

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without
restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following
conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT N
OT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.

*/

session_start();

if ($_GET['stack'] == "")
{
	header( "Location: error.php?error=4" );
	exit;
}

if( ! isset( $_GET['stack'] ) )
{
	header( "Location: error.php?error=4" );
	exit;
}

$getstackid = $_GET['stack'];

include( 'compiler.php' );

include('mysql_connect.php');

$query = mysql_query("SELECT * FROM stacks WHERE id='$getstackid'");
$numrows = mysql_num_rows($query);
if ($numrows == 0)
{
	header( "Location: error.php?error=3" );
	exit;
}

$sql="SELECT * FROM stacks where id = $getstackid";
$result=mysql_query($sql);
$stackinfo=mysql_fetch_array($result);
if ($stackinfo['users_id'] != $_SESSION['userid'])
{
	if ($stackinfo['public'] != "1")
	{
		if (! isset($_SESSION['userid']))
		{
			header( "Location: error.php?error=2" );
			exit;
		}
		header( "Location: error.php?error=1" );
		exit;
	}
}

$getuserid = $_SESSION['username'];
$getusersid = $_SESSION['userid'];

$stack = mysql_fetch_array( mysql_query( "select * from stacks where id=" . $_GET['stack'] . " limit 1" ), MYSQL_ASSOC );

$current_user = mysql_fetch_array( mysql_query( "select * from users where username='" . $_SESSION['username'] . "' limit 1" ), MYSQL_ASSOC );

$cards = mysql_query( "select * from cards where stacks_id=" . $_GET['stack'] . " order by card_order" );

if( ! isset( $_SESSION['username'] ) )
{
	if ($stack['public'] != "1")
	{
		header( "Location: index.html" );
		exit;
	}
}

$tools_image = "images/tools.gif";
if ($stack['public'] == "1")
{
	$options_image = "images/options_unshare.png";
}
if ($stack['public'] == "0")
{
	$options_image = "images/options.png";
}

if ($stack['public'] == "1" && $getusersid != $stack['users_id'])
{
	$options_image = "images/options_disabled.png";
	$tools_image = "images/tools_disabled.png";
}

?>
<html>
	<head>
		<link rel="shortcut icon" href="logo.ico" />
		<title><?php echo $stack['name']; ?> :: jsCard</title>
		<style type="text/css">
			body
			{
				background-color: #607AC5;
				margin:0px;
			}
			
			#header
			{
				width: 100%;
				height: 45px;
				background-color: #C5CEE8;
			}
			
			#loginbtn
			{
				background-color: #607AC5;
				height: 100%;
				width: 100px;
				font-family: ''MS Trebuchet'',Verdana,Arial,sans-serif;"
				color: white;
			}
			#loginbtn:hover {
				background-color: #90A2D6;
			}
			#loginbtn:active {
				background-color: #4D629E;
			}
			
			#stack_container_shadow
			{
				position: absolute;
				top: 50%;
				left: 50%;
				margin-top: -<?php echo round($stack['height']/2)+15; ?>px;
				margin-left: -<?php echo round($stack['width']/2)-5; ?>px;
				width: <?php echo $stack['width']; ?>px;
				height: <?php echo $stack['height']; ?>px;
				background-color: #405BA7;
			}
			
			.stack_container
			{
				position: absolute;
				top: 0px;
				left: 0px;
				width: <?php echo $stack['width']; ?>px;
				height: <?php echo $stack['height']; ?>px;
				background-color: #fff;
				overflow: hidden;
			}
			
			.stack
			{
				position: absolute;
				top: 50%;
				left: 50%;
				margin-top: -<?php echo round($stack['height']/2)+20; ?>px;
				margin-left: -<?php echo round($stack['width']/2); ?>px;
				width: <?php echo $stack['width']; ?>px;
				height: <?php echo $stack['height']; ?>px;
				background-color: #fff;
			}
			
			.background
			{
				position: absolute;
				top: 0px;
				left: 0px;
				width: <?php echo $stack['width']; ?>px;
				height: <?php echo $stack['height']; ?>px;
			}
			
			.script_editor
			{
				display: block;
				z-index: 1000;
				background-color: #fff;
			}
			
			#msgbox
			{
				position: absolute;
				top: 50%;
				left: 50%;
				margin-top: <?php if( round($stack['height']) < 300 ) { echo (0 - (round($stack['height']/2)+20)) + 284; } else { echo round($stack['height']/2); } ?>px;
				margin-left: -<?php if( round($stack['width']) < 500 ) { echo round($stack['width']/2) + round(500-$stack['width'])/2; } else { echo round($stack['width']/2); } ?>px;
				width: <?php if( ($stack['width'] + 5) < 500 ) { echo "500"; } else { echo ($stack['width'] + 5); } ?>px;
				height: 87px;
			}
			
			.msgbox_selector
			{
				position: absolute;
				height: 23px;
				width: 85px;
				top:50%;
				left:50%;
				margin-left: -<?php if( round($stack['width']) < 500 ) { echo round($stack['width']/2) + (round(500-$stack['width'])/2)-13; } else { echo (round($stack['width']/2))-13; } ?>px;
				margin-top: <?php if( round($stack['height']) < 300 ) { echo (0 - (round($stack['height']/2)+20)) + 284; } else { echo round($stack['height']/2); } ?>px;
			}
			
			.card_manager_selector
			{
				position: absolute;
				height: 23px;
				width: 92px;
				top:50%;
				left:50%;
				margin-left: -<?php if( round($stack['width']) < 500 ) { echo round($stack['width']/2) + (round(500-$stack['width'])/2)-125; } else { echo (round($stack['width']/2))-125; } ?>px;
				margin-top: <?php if( round($stack['height']) < 300 ) { echo (0 - (round($stack['height']/2)+20)) + 284; } else { echo round($stack['height']/2); } ?>px;
			}
				
			#tools
			{
				position: absolute;
				top: 50%;
				left: 50%;
				margin-top: -<?php echo round( $stack['height']/2 )+20; ?>px;
				margin-left: -<?php echo round( $stack['width']/2) + 113; ?>px;
				width: 93px;
				height: 226px;
				background-image: url( '<?php echo $tools_image; ?>' );
				-webkit-user-select: none; /* webkit (safari, chrome) browsers */
				-moz-user-select: none; /* mozilla browsers */
				-khtml-user-select: none; /* webkit (konqueror) browsers */
				-ms-user-select: none; /* IE10+ */
			}

			.unselectable
			{
				-webkit-user-select: none; /* webkit (safari, chrome) browsers */
				-moz-user-select: none; /* mozilla browsers */
				-khtml-user-select: none; /* webkit (konqueror) browsers */
				-ms-user-select: none; /* IE10+ */
				user-select: none;
			}
			
			div.toolcell
			{
				border: 1px solid #e3e3e3;
			}
			div.toolcell:hover
			{
				border: 1px solid #b4b4b4;
			}
			div.toolcell:active
			{
				border: 1px solid black;
			}
			.toolcell_selected
			{
				border: 1px solid #607AC5;
			}
			
			#options
			{
				position: absolute;
				top: 50%;
				left: 49%;
				margin-top: -<?php echo round( $stack['height']/2 )+20; ?>px;
				margin-left: <?php echo round( $stack['width']/2) + 50; ?>px;
				width: 93px;
				height: 274px;
				background-image: url( '<?php echo $options_image; ?>' );
				-webkit-user-select: none; /* webkit (safari, chrome) browsers */
				-moz-user-select: none; /* mozilla browsers */
				-khtml-user-select: none; /* webkit (konqueror) browsers */
				-ms-user-select: none; /* IE10+ */
			}
			
			div.optioncell
			{
				border: 1px solid #e3e3e3;
			}
			div.optioncell:hover
			{
				border: 1px solid #b4b4b4;
			}
			
			.cardmanagercontent
			{
				width:100%;
				display:none;
				position: absolute;
				top: 32px;
				left: 12px;
				width: <?php if( ($stack['width'] + 5) < 500 ) { echo 500-22; } else { echo ($stack['width'] - 22); } ?>px;
				border: none;
			}
			
			.next-triangle
			{
				width: 0;
				height: 0;
				border-style: solid;
				border-width: 10px 0 10px 17.3px;
				border-color: transparent transparent transparent #ffffff;
				position: absolute;
				top: 50%;
				left: 50%;
				margin-top: -10px;
				margin-left: -6px;
			}
			
			.prev-triangle
			{
				width: 0;
				height: 0;
				border-style: solid;
				border-width: 10px 17.3px 10px 0;
				border-color: transparent #ffffff transparent transparent;
				position: absolute;
				top: 50%;
				left: 50%;
				margin-top: -10px;
				margin-left: -11px;
			}
			
			.card-slideshow {
				border-radius: 50%;
				width: 40px;
				height: 40px;
				position: relative;
				background-color: #607AC5;
				float:right;
				margin-right:5px;
			}
			.card-slideshow:hover {
				background-color: #90A2D6;
			}
			.card-slideshow:active {
				background-color: #4D629E;
			}
			
			.add-card {
				border-radius: 50%;
				width: 40px;
				height: 40px;
				position: relative;
				background-color: #33AD5C;
				float:left;
				margin-right:5px;
				text-align: center;
			}
			.add-card:hover {
				background-color: #5CBD7D;
			}
			.add-card:active {
				background-color: #298C4B;
			}
			
			.remove-card {
				border-radius: 50%;
				width: 40px;
				height: 40px;
				position: relative;
				background-color: #FF3333;
				float:left;
				margin-right:5px;
				text-align: center;
			}
			.remove-card:hover {
				background-color: #FF5C5C;
			}
			.remove-card:active {
				background-color: #CC2929;
			}
			
			.plus-icon
			{
				color: white;
				position: absolute;
				top: 50%;
				left: 50%;
				margin-top: -15px;
				margin-left: -9px;
				font-size:30px;
				user-select: none;
				-webkit-touch-callout: none;
				-webkit-user-select: none;
				-khtml-user-select: none;
				-moz-user-select: none;
				-ms-user-select: none;
				cursor:default;
			}
			
			.remove-icon
			{
				color: white;
				position: absolute;
				top: 50%;
				left: 50%;
				margin-top: -20px;
				margin-left: -7px;
				font-size:40px;
				user-select: none;
				-webkit-touch-callout: none;
				-webkit-user-select: none;
				-khtml-user-select: none;
				-moz-user-select: none;
				-ms-user-select: none;
				cursor:default;
			}
			
			.card-manager-info
			{
				position: absolute;
				left: 50%;
				text-align: center;
				width: 150px;
				margin-left: -75px;
			}
			
			.card-properties {
				border-radius: 50%;
				width: 40px;
				height: 40px;
				position: relative;
				background-color: #607AC5;
				float:left;
				margin-left:20px;
			}
			.card-properties:hover {
				background-color: #90A2D6;
			}
			.card-properties:active {
				background-color: #4D629E;
			}
			
			.stack-properties {
				border-radius: 50%;
				width: 40px;
				height: 40px;
				position: relative;
				background-color: #607AC5;
				float:right;
				margin-right:20px;
			}
			.stack-properties:hover {
				background-color: #90A2D6;
			}
			.stack-properties:active {
				background-color: #4D629E;
			}
			
			.preloadImages
			{
				display: none;
				background-image: url('images/cardmanager_05.gif'), url('images/options.png'), url('images/options_unshare.png');
			}
			
			textarea {
				resize: none;
			}
			
			<?php
				if ($stack['public'] == 1 && $stack['users_id'] != $getusersid)
				{
					echo "#msgbox {display:none;}";
					if ( ! isset( $_SESSION['username'] ) )
					{
						echo "
							#options {display:none;}
							#tools {display:none;}
						";
					}
				}
			?>
			
			@media print
			{
				* {
					visibility: hidden;
				}
				
				.stack_container {
					visibility: visible
				}
			}
		</style>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<link href="themes/default.css" rel="stylesheet" type="text/css"></link>
		<link href="themes/alphacube.css" rel="stylesheet" type="text/css"></link> 
		<link href="css/element-styles.css" rel="stylesheet" type="text/css"></link>
		
		<script type="text/javascript" src="javascripts/prototype.js">Unable to load Prototype Library...</script>
		<script type="text/javascript" src="javascripts/effects.js">Unable to load Prototype Library...</script>
		<script type="text/javascript" src="javascripts/window.js">Unable to load Window Library...</script>
		<script type="text/javascript" src="js/constants.js">Unable to load constants...</script>
		<script type="text/javascript" src="js/chunkex.js">Unable to load Chunk Expression Library...</script>
		<script type="text/javascript" src="js/browserdetect.js">Unable to load Browser Detect Library...</script>
		<script type="text/javascript" src="js/base64.js">Unable to load Base64 Codec Library...</script>
		<script type="text/javascript" src="js/operators.js">Unable to load Operator Library...</script>
		<script type="text/javascript" src="js/eventhandlers.js">Unable to load Event Handler Library...</script>
		<script type="text/javascript" src="js/properties.js">Unable to load Property Helper Library...</script>
		<script type="text/javascript" src="js/dragdrop.js">Unable to load Drag and Drop Library...</script>		
		<script type="text/javascript" src="js/editmode.js">Unable to load Edit Mode Library...</script>
		<script type="text/javascript" src="js/soundmanager.js">Unable to load Sound Library...</script>
		<script type="text/javascript" src="js/functions.js">Unable to load Function Library...</script>
		
		<script type="text/javascript">
// Catch All Errors
window.onerror = function (errorMsg, url, lineNumber, column, errorObj)
{
	if( (( ! errorObj.indexOf("Cannot read property") > -1) && ( ( ! errorObj.indexOf("of null") > -1) || ( ! errorObj.indexOf("of undefined") > -1) )) )
	{
		alert("Object does not exist");
	}
	alert(1);
}

userlevel = 0;
dragInterval = "";
focusElement = "";
dragElement = "";
hasAboutWindow = false;
hasScriptEditor = false;
hasContentsEditor = false;
dontAsk = false;
saveInterval = "";
var stackShareStatus;
var shiftDown;
var msgbox = 1;
var cmdDown = false;
var clickLocation;

function stackClicked()
{
	clickLocation = mouseloc();
}

function getKey()
{
    keynum = event.which || event.keyCode;
	
	if( keynum == 8 )
	{
		keychar = "backspace";
	}
	else if( keynum == 9 )
	{
		keychar = "tab";
	}
	else if( keynum == 13 )
	{
		keychar = "enter";
	}
	else if( keynum == 27 )
	{
		keychar = "escape";
	}
	else if( keynum == 46 )
	{
		keychar = "delete";
	}
	else if( keynum == 37 )
	{
		keychar = "left";
	}
	else if( keynum == 38 )
	{
		keychar = "up";
	}
	else if( keynum == 39 )
	{
		keychar = "right";
	}
	else if( keynum == 40 )
	{
		keychar = "down";
	}
	else if( keynum > 46 )
	{
		keychar = String.fromCharCode(keynum);
	}
    return keychar;
}

function isInt(value)
{
	return !isNaN(value) && (function(x)
	{
		return (x | 0) === x;
	})(parseFloat(value))
}

function getButtonNameOrNumber( nameOrNumber )
{
	if( isInt(nameOrNumber) )
	{
		var buttonList = eval( 'card_' + getCurrentCardID() + '_buttonList' );
		var buttonID = buttonList[nameOrNumber-1];
		return document.getElementById( 'obj' + buttonID );
	}
	else
	{
		return (document.getElementsByName( "btn_" + stackID + "_" + cardID + "_" + nameOrNumber )[0]);
	}
}

function getFieldNameOrNumber( nameOrNumber )
{
	if( isInt(nameOrNumber) )
	{
		var fieldList = eval( 'card_' + getCurrentCardID() + '_fieldList' );
		var fieldID = fieldList[nameOrNumber-1];
		return document.getElementById( 'obj' + fieldID );
	}
	else
	{
		return (document.getElementsByName( "fld_" + stackID + "_" + cardID + "_" + nameOrNumber )[0]);
	}
}

function getBlockNameOrNumber( nameOrNumber )
{
	if( isInt(nameOrNumber) )
	{
		var blockList = eval( 'card_' + getCurrentCardID() + '_blockList' );
		var blockID = blockList[nameOrNumber-1];
		return document.getElementById( 'obj' + blockID );
	}
	else
	{
		return (document.getElementsByName( "blk_" + stackID + "_" + cardID + "_" + nameOrNumber )[0]);
	}
}

function getCardNameOrNumber( nameOrNumber )
{
	if( isInt(nameOrNumber) )
	{
		return document.getElementById( 'card_' + cardList[nameOrNumber-1] );
	}
	else
	{
		return (document.getElementsByName( "card_" + nameOrNumber )[0]);
	}
}

// Code for getting whether the mouse is down or up
var mouseDown = 0;
function bodyMouseDown()
{ 
	mouseDown = 1;
}

function bodyMouseUp()
{
	clearInterval(dragInterval);
	mouseDown = 0;
}


function updateUserImages()
{
	// This function is used to update the list of all the images the user has in their directory
	http.open('get', 'getFileListing.php?username=<?php echo $_SESSION['username']; ?>&foldername=images', false);
    http.send( null );
    
	var userImages = http.responseText;
	document.getElementById("card_editor_images").innerHTML = userImages;
	return true;
}

function uploadImage()
{
	var fileSelect = document.getElementById('imageUploader');
	var files = fileSelect.files;
	var formData = new FormData();
	for (var i = 0; i < files.length; i++)
	{
		var file = files[i];
		// Check the file type.
		if (!file.type.match('image.*'))
		{
			continue;
		}
		// Add the file to the request.
		formData.append('photos[]', file, file.name);
	}
	var xhr = new XMLHttpRequest();
	xhr.open('POST', 'uploadImage.php?uploadDir=<?php echo $_SESSION['username']; ?>/images/', true);
	xhr.onload = function ()
	{
		if (xhr.status === 200)
		{
			// File(s) uploaded.
			if(xhr.responseText.indexOf("upload-success") > -1)
			{
				alert("The image has been uploaded.");
				updateUserImages();
			}
			else if(xhr.responseText.indexOf("server-error") > -1)
			{
				alert("There has been a server error, and your file was not uploaded. Please try again.");
			}
			else if(xhr.responseText.indexOf("file-exists") > -1)
			{
				alert("There is already a file with that name. Rename your file and try again.");
			}
			else if(xhr.responseText.indexOf("too-large") > -1)
			{
				alert("The image is too large. Please resize your image and try again.");
			}
			uploadButton.innerHTML = 'UploadComplete';
		}
		else
		{
			alert('An error occurred!');
		}
	};
	xhr.send(formData);
}

function switchToMsgBox()
{
	// This function is used to switch from the card manager to the msg box
	
	if( msgbox == 0 )
	{
		// If the msgbox is not currently showing, so the card manager is
		var cardManager = document.getElementById('cardmanagercontent');
		var msgBox = document.getElementById('msgboxcontent');
		var imageBox = document.getElementById('msgboximage');
		
		cardManager.style.display = "none";
		imageBox.style.backgroundImage = "url(images/messagebox_05.gif)";
		msgBox.style.display = "block";
		
		msgbox = 1;
	}
}

function switchToCardManager()
{
	// This function is used to switch from the card manager to the msg box
	
	if( msgbox == 1 )
	{
		// If the msgbox is currently showing
		var cardManager = document.getElementById('cardmanagercontent');
		var msgBox = document.getElementById('msgboxcontent');
		var imageBox = document.getElementById('msgboximage');
		
		msgBox.style.display = "none";
		imageBox.style.backgroundImage = "url(images/cardmanager_05.gif)";
		cardManager.style.display = "block";
		
		updateCardManager();
		
		msgbox = 0;
	}
}


function cardCount()
{
	// This function is used to count the number of cards in the stack
	return cardList.length;
}

function getCurrentCardID()
{
	// This fuction is used to find the number of the current card
	for( j = 0; j < cardList.length; j++ )
	{
		var cardIDNumber = parseInt(cardList[j]);
		var cardId = "card_" + cardIDNumber;
		var whatCard = document.getElementById(cardId);
		var cardVisible = whatCard.style.display;
		if( cardVisible != "none" )
		{
			return cardIDNumber;
			break;
		}
	}
}

function getCurrentCardNumber()
{
	// This fuction is used to find the number of the current card
	for( j = 0; j < cardList.length; j++ )
	{
		var cardIDNumber = parseInt(cardList[j]);
		var cardId = "card_" + cardIDNumber;
		var whatCard = document.getElementById(cardId);
		var cardVisible = whatCard.style.display;
		if( cardVisible != "none" )
		{
			return j+1;
			break;
		}
	}
}


function cardNumberFromID(cardId)
{
	// This fuction is used to find the number of the current card
	for( j = 0; j < cardList.length; j++ )
	{
		var cardIDNumber = parseInt(cardList[j]);
		var currentCardId = "card_" + cardIDNumber;
		if( cardId == currentCardId )
		{
			return j+1;
			break;
		}
	}
}

function elementCount( elementType )
{
	// This function is used to count elements on the current card
	var elementCounter = 0;
	var cardNumber = Number(getCurrentCardNumber()) - 1;
	
	if( elementType == "button" || elementType == "buttons" )
	{
		var buttonzlist = eval( "card_" + cardList[cardNumber] + "_buttonZList" );
		for( i = 0; i < buttonzlist.length; i++ )
		{
			elementCounter++;
		}
	}
	else if( elementType == "field" || elementType == "fields" )
	{
		var fldzlist = eval( "card_" + cardList[cardNumber] + "_fieldZList" );
		for( i = 0; i < fldzlist.length; i++ )
		{
			elementCounter++;
		}
	}
	else if( elementType == "block" || elementType == "blocks" )
	{
		var blkzlist = eval( "card_" + cardList[cardNumber] + "_blockZList" );
		for( i = 0; i < blkzlist.length; i++ )
		{
			elementCounter++;
		}
	}
	
	return elementCounter;
	elementCounter = 0;
}

function bodyKeyDown()
{
	// This function is fired when the user presses any key on their keyboard
	if( window.event )
	{
		keynum = event.keyCode;
	}
	else
	{
		keynum = event.which;
	}
	
	if( keynum == 16 )
	{
		shiftDown = true;
	}	
	else if( keynum == 244 || keynum == 17 || keynum == 91 || keynum == 93 )
	{
		cmdDown = true;
	}
	
	if( userLevel != 0 )
	{
		if(focusElement=='')
		{
			if ( keynum == 8 || keynum == 46 )
			{
				deleteElement( dragElement );
				event.preventDefault();
				event.stopPropagation();
				return false;
			}
			if( dragElement )
			{
				if( keynum == 37 )
				{
					e = window.event;
					if( e.shiftKey )
					{
						dragElement.style.left = parseInt(dragElement.style.left, 10) - 10;
					}
					else
					{
						dragElement.style.left = parseInt(dragElement.style.left, 10) - 1;
					}
				}
				if( keynum == 38 )
				{
					e = window.event;
					if( e.shiftKey )
					{
						dragElement.style.top = parseInt(dragElement.style.top, 10) - 10;
					}
					else
					{
						dragElement.style.top = parseInt(dragElement.style.top, 10) - 1;
					}
				}
				if( keynum == 39 )
				{
					e = window.event;
					if( e.shiftKey )
					{
						dragElement.style.left = parseInt(dragElement.style.left, 10) + 10;
					}
					else
					{
						dragElement.style.left = parseInt(dragElement.style.left, 10) + 1;
					}
				}
				if( keynum == 40 )
				{
					e = window.event;
					if( e.shiftKey )
					{
						dragElement.style.top = parseInt(dragElement.style.top, 10) + 10;
					}
					else
					{
						dragElement.style.top = parseInt(dragElement.style.top, 10) + 1;
					}
				}
			}
		}
	}
	else
	{
		key = String.fromCharCode(keynum)
	}
}

function bodyKeyUp()
{
	if(focusElement=='')
	{
		if( window.event )
		{
			keynum = event.keyCode;
		}
		else
		{
			keynum = event.which;
		}
		
		if( keynum == 16 )
		{
			shiftDown = false;
		}
		else if( keynum == 244 || keynum == 17 || keynum == 91 || keynum == 93 )
		{
			cmdDown = false;
		}
	}
}

function hpop__getItemDel()
{
	return cxl_item_delimiter;
}

function hpop__setItemDel( newItemDel )
{
	cxl_item_delimiter = newItemDel;
}

function convertNumStyle( styleNumber, whatElement )
{
	// This function is used to convert the style number found in the database and lists into
	// the word style, such as "roundrect" or "opaque"
	
	// First we need to see if we are working out button or field styles
	var elementType = hpop__getType( whatElement );
	
	if( elementType == "button" )
	{
		switch( styleNumber )
		{
			case 0:
				return "transparent";
				break;
			case 1:
				return "opaque";
				break;
			case 2:
				return "rectangle";
				break;
			case 3:
				return "shadow";
				break;
			case 4:
				return "popup";
				break;
			case 5:
				return "roundrect";
				break;
			case 6:
				return "oval";
				break;
			case 7:
				return "standard";
				break;
			case 8:
				return "default";
				break;
			case 9:
				return "checkbox";
				break;
			case 10:
				return "radiobutton";
				break;
		}
	}
	else if( elementType == "field" )
	{
		switch( styleNumber )
		{
			case 0:
				return "transparent";
				break;
			case 1:
				return "opaque";
				break;
			case 2:
				return "rectangle";
				break;
			case 3:
				return "shadow";
				break;
			case 4:
				return "scrolling";
				break;
		}
	}
	else
	{
		alert("jsCard Error: Only buttons and fields can have styles");
	}
}

function convertWordStyle( styleWord, whatElement )
{
	// This function is used to convert the style word found such as "roundrect" or "oval"
	// into the numbers that are found in the lists and the database
	
	// First we need to see if we are working out button or field styles
	var elementType = hpop__getType( whatElement );
	
	if( elementType == "button" )
	{
		switch( styleWord )
		{
			case "transparent":
				return 0;
				break;
			case "opaque":
				return 1;
				break;
			case "rectangle":
				return 2;
				break;
			case "shadow":
				return 3;
				break;
			case "popup":
				return 4;
				break;
			case "roundrect":
				return 5;
				break;
			case "oval":
				return 6;
				break;
			case "standard":
				return 7;
				break;
			case "default":
				return 8;
				break;
			case "checkbox":
				return 9;
				break;
			case "radiobutton":
				return 10;
				break;
		}
	}
	else if( elementType == "field" )
	{
		switch( styleWord )
		{
			case "transparent":
				return 0;
				break;
			case "opaque":
				return 1;
				break;
			case "rectangle":
				return 2;
				break;
			case "shadow":
				return 3;
				break;
			case "scrolling":
				return 4;
				break;
		}
	}
	else
	{
		alert("jsCard Error: Only buttons and fields can have styles");
	}
}

function convertBool( whatString )
{
	if( whatString == true )
	{
		return 1;
	}
	else
	{
		return 0;
	}
}

function hpop__getID( whatElement )
{
	return whatElement.getAttribute('id');
}

function selectmsgbox()
{
	document.getElementById('msgboxcontent').select();
	return true;
}

function hpop__getName( whatElement )
{
	return whatElement.getAttribute('name');
}

function hpop__getShortName( whatElement )
{
	var fullName = hpop__getName( whatElement );
	var index = fullName.indexOf('_');
	if( index == -1 )
	{
		return fullName;
	}
	else
	{
		index = fullName.indexOf('_', index + 1);
		index = fullName.indexOf('_', index + 1);
		var shortName = fullName.slice(index + 1);
		return shortName;
	}
}

<?php
if ($stack['public'] == "0")
{
	echo "var stackShareStatus = '0';";
}

if ($stack['public'] == "1")
{
	echo "var stackShareStatus = '1';";
}
?>



function newStack()
{
		var BEwin = new Window(Application.getNewId(), {className: "alphacube", title: "Create Stack", showEffect:Effect.Appear, hideEffect: Effect.Fade, showEffectOptions: { duration: 0.25 }, hideEffectOptions: { duration: 0.25 }, width:500, height:175});
		BEwin.setContent('newstack' );
		BEwin.setDestroyOnClose();
		BEwin.setZIndex(1000);
		BEwin.showCenter(true);
}


function createStack()
{
	var newstackname = document.getElementById("create_stack_name").value;
	var e = document.getElementById("ddlViewBy");
	var strUser = e.options[e.selectedIndex].value;
	
	window.location = "create_stack.php?name="+newstackname+"&size="+strUser;
}


function openStack()
{
	var BEwin = new Window(Application.getNewId(), {className: "alphacube", title: "Open Stack", showEffect:Effect.Appear, hideEffect: Effect.Fade, showEffectOptions: { duration: 0.25 }, hideEffectOptions: { duration: 0.25 }, width:500, height:175});
	BEwin.setContent('openstack' );
	BEwin.setDestroyOnClose();
	BEwin.setZIndex(1000);
	BEwin.showCenter(true);

}


function openSelectedStack()
{
	// This function is used to actually open the stack once the user has selected the stack they want to open, and clicked
	// OK on the popup window.
	
	// First we need to find out the ID of the stack selected
	var e = document.getElementById("openstack_name");
	var openStackID = e.options[e.selectedIndex].value;
	
	// Now we can navigate to stack.php?stack= and the stack ID that we just found.
	window.location = "stack.php?stack="+openStackID;
}


function shareStack()
{
	// This is the fuction used when the user actually clicks on the share stack icon in the tools menu
	
	// First we need to find out whether the stack is already public, or if it is not public
	if (stackShareStatus == "0")
	{
		// If the stack is not public, then we can show the window where they can enter info about the public stack
		var BEwin = new Window(Application.getNewId(), {className: "alphacube", title: "Share Stack", showEffect:Effect.Appear, hideEffect: Effect.Fade, showEffectOptions: { duration: 0.25 }, hideEffectOptions: { duration: 0.25 }, width:600, height:200});
		BEwin.setContent('sharestack' );
		BEwin.setDestroyOnClose();
		BEwin.setZIndex(1000);
		BEwin.showCenter(true);
	}
	
	if (stackShareStatus == "1")
	{
		// If it already is public, then go and make it unpublic
		shareStackSend()
	}
}



function shareStackSend()
{
	// This function is used when the user un-shares a stack, and when a user clicks OK in the share stack window, to actually send the information to PHP
	// to actually share or un-share the stack. As we do not want to refresh the page, we need to send this information using an AJAX request.
	
    // Create our AJAC object
    var hr = new XMLHttpRequest();
    // These are the variables going to be sent to share_stack.php
    var url = "share_stack.php";
    var id = "<?php echo $stack['id']; ?>"; // We can use a little bit of PHP code here, to make life easier!
    var name = document.getElementById("sharestack_name").value;
    var description = document.getElementById("sharestack_description").value;
    var vars = "id="+id+"&name="+name+"&description="+description;
    hr.open("POST", url, true);
    // Set content type header information for sending url encoded variables in the request
    hr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	
    // This part is used to update the picture for the share stack icon, to the opposite of what is was before
    hr.onreadystatechange = function()
	{
	    if(hr.readyState == 4 && hr.status == 200)
		{
			// When the PHP file has finished processing, find out whether we made a stack public, or user only
		    var return_data = hr.responseText;
			if (return_data==="unpublic")
			{
				// If we unshared a stack, then change the image so we can share it, and also update the variable
				document.getElementById('options').style.background = 'url( images/options.png )';
				stackShareStatus = "0";
			}
			if (return_data==="public")
			{
				// If we shared a stack, change the image so we can unshared it, and update the variable.
				document.getElementById('options').style.background = 'url( images/options_unshare.png )';
				stackShareStatus = "1";
			}
	    }
    }
    // Send the data to PHP now... and wait for response to update the status div
    hr.send(vars); // Actually execute the request
}



function confirmDeleteStack()
{
	// This function is used to make sure the user really wants to delete the stack
	
	// Show an alert dialog to the user, asking if they want to delete the stack
	var result=confirm("Are you sure you want to delete stack \"<?php echo $stack[name]; ?>\"?");
	if (result==true)
	{
		// If they clicked OK, then go ahead and delete the stack
		deleteStack()
	}
}


function deleteStack()
{
	// This is the function that actually deletes the stack.
	
	// The easiest way to delete the stack is to redirect to delete_stack.php with a GET variable of the stacks id
	window.location = "delete_stack.php?id=<?php echo $stack['id']; ?>";
}



function doSave()
{
	document.getElementById('saveindicator').style.display = "block";
	setTimeout( "saveStack();", 500 );
}

var Application =  {
  lastId: 0,

  getNewId: function() {
    Application.lastId++;
    return "window_id_" + Application.lastId;
  }
 }

function js_array_to_php_array (a)
{
    var a_php = "";
    var total = 0;
    for (var key in a)
    {
        ++ total;
        a_php = a_php + "s:" +
                String(key).length + ":\"" + String(key) + "\";s:" +
                String(a[key]).length + ":\"" + String(a[key]) + "\";";
    }
    a_php = "a:" + total + ":{" + a_php + "}";
    return a_php;
}

function savePart(part_id)
{
	var objectprops = new Object();
	var selectedObject = document.getElementById('obj'+part_id);
	//determine_part_order
	
	var i,j;
	var located = false;
	var objtype = "";
	var objposition = 0;
	var objlist;
	var objcard = 0;
	var objzlist;
	
	for( i = 0; i < cardList.length; i++ )
	{
		objlist = eval( "card_" + cardList[i] + "_buttonList" );
		objzlist = eval( "card_" + cardList[i] + "_buttonZList" );
		for( j = 0; j < objlist.length; j++ )
		{
			if( selectedObject.getAttribute('id') == ( "obj" + objlist[j] ) )
			{
				// Found it!
				objtype="button";
				objposition = j;
				objcard = i;
				located = true;
				
			}
		}
		
		if ( ! located )
		{
		
			objlist = eval( "card_" + cardList[i] + "_fieldList" );
			objzlist = eval( "card_" + cardList[i] + "_fieldZList" );
			for( j = 0; j < objlist.length; j++ )
			{
				if( selectedObject.getAttribute('id') == ( "obj" + objlist[j] ) )
				{
					// Found it!
					objtype="field";
					objposition = j;
					objcard = i;
					located = true;
				}
			}
		
		}
		
		if ( ! located )
		{
		
			objlist = eval( "card_" + cardList[i] + "_blockList" );
			objzlist = eval( "card_" + cardList[i] + "_blockZList" );
			for( j = 0; j < objlist.length; j++ )
			{
				if( selectedObject.getAttribute('id') == ( "obj" + objlist[j] ) )
				{
					// Found it!
					objtype="block";
					objposition = j;
					objcard = i;
					located = true;
				}
			}
		
		}

	}
	
	objectprops["name"] = selectedObject.getAttribute('name').substr( ( selectedObject.getAttribute('name').split( '_' )[1].length + selectedObject.getAttribute('name').split('_')[2].length ) + 6 );
	objectprops["partorder"] = objposition;
	objectprops["top"] = hpop__filterProperty( selectedObject.style.top );
	objectprops["left"] = hpop__filterProperty( selectedObject.style.left );
	objectprops["width"] = hpop__filterProperty( selectedObject.style.width );
	objectprops["height"] = hpop__filterProperty( selectedObject.style.height );
	objectprops["visible"] = convertBool( hpop__getVisible( selectedObject ) );
	objectprops["enabled"] = convertBool( hpop__getEnabled( selectedObject ) );
	
	if( objtype == "button" )
	{
		objectprops["value"] = "";
		objectprops["stype"] = 0;
		objectprops["style"] = convertWordStyle( hpop__getStyle( selectedObject ), selectedObject );
		objectprops["family"] = hpop__getFamily( selectedObject );
		objectprops["locktext"] = 0;
		objectprops["hilite"] = convertBool( hpop__getHilite( selectedObject ) );
		objectprops["autohilite"] = convertBool( hpop__getAutoHilite( selectedObject ) );
		objectprops["dontwrap"] = 0;
		objectprops["autoselect"] = 0;
		objectprops["multiplelines"] = 0;
		objectprops["showname"] = convertBool( hpop__getShowName( selectedObject ) );
	}
	else if ( objtype == "field" )
	{
		objectprops["value"] = hpop__getContents( selectedObject );
		objectprops["stype"] = 1;
		objectprops["style"] = convertWordStyle( hpop__getStyle( selectedObject ), selectedObject );
		objectprops["family"] = 0;
		objectprops["locktext"] = convertBool( hpop__getLockText( selectedObject ) );
		objectprops["hilite"] = 0;
		objectprops["autohilite"] = 0;
		objectprops["dontwrap"] = convertBool( hpop__getDontWrap( selectedObject ) );
		objectprops["autoselect"] = convertBool( hpop__getAutoSelect( selectedObject ) );
		objectprops["multiplelines"] = convertBool( hpop__getMultipleLines( selectedObject ) );
		objectprops["showname"] = 0;
	}
	else if ( objtype == "block" )
	{
		objectprops["value"] = hpop__getContents( selectedObject );
		objectprops["stype"] = 2;
		objectprops["style"] = 0;
		objectprops["family"] = 0;
		objectprops["locktext"] = 0;
		objectprops["hilite"] = 0;
		objectprops["autohilite"] = 0;
		objectprops["dontwrap"] = 0;
		objectprops["autoselect"] = 0;
		objectprops["multiplelines"] = 0;
		objectprops["showname"] = 0;
	}
	objectprops["script"] = eval( "part_" + stackID + "_" + cardList[objcard] + "_" + part_id + "_scriptx" );

	//alert("Name: " + objectprops['name'] + ", visible: " + objectprops['visible'] + ", enabled: " + objectprops['enabled'] + ", family: " + objectprops['family'] + ", style: " + objectprops['style']);
	
	objectprops = js_array_to_php_array( objectprops );
	objectprops = encode64(objectprops);
	
	//alert('saveobject.php?id=' + part_id + '&card_id=' + cardList[objcard] + '&stack_id=' + stackID + '&type=object&properties=' + objectprops);
	
    http.open('get', 'saveobject.php?id=' + part_id + '&card_id=' + cardList[objcard] + '&stack_id=' + stackID + '&type=object&properties=' + objectprops, false );
    http.send( null );
}

function js_normal_array_to_php( a )
{
	var s = "a:" + a.length + ":{";
	for( var i = 0; i < a.length; i++ )
	{
		s = s + "i:" + i + ";i:" + a[i] + ";";
	}
	return s + "}";
}

function saveCard( card_id )
{
	var selectedCard = document.getElementById( 'card_' + card_id );
	var cardName = selectedCard.getAttribute('name').substr(5);
	var whichCard = 0;
	var cardprops = new Object();
	// Find card's position in the list:
	
	for( var i = 0; i < cardList.length; i++ )
	{
		if( cardList[i] == card_id )
		{
			whichCard = i;
		}
	}
			
	cardprops["cardorder"] = whichCard;
	cardprops["name"] = cardName;
	cardprops["script"] = eval("card_" + stackID + "_" + card_id + "_scriptx");
	
	cardprops = js_array_to_php_array( cardprops );
	cardprops = encode64(cardprops);
	
    http.open('get', 'saveobject.php?id=0&card_id=' + card_id + '&stack_id=' + stackID + '&type=card&properties=' + cardprops, false );
    http.send( null );
	
    var deleteExceptions = new Array();
    
	var cardButtonList = eval( "card_" + card_id + "_buttonList" );
	
	for( var j = 0; j < cardButtonList.length; j++ )
	{
		savePart(cardButtonList[j]);
		deleteExceptions.push( cardButtonList[j] );
	}
    
	var cardFieldList = eval( "card_" + card_id + "_fieldList" );
	
	for( var k = 0; k < cardFieldList.length; k++ )
	{
		savePart(cardFieldList[k]);
		deleteExceptions.push( cardFieldList[k] );
	}
    
	var cardBlockList = eval( "card_" + card_id + "_blockList" );
	
	for( var l = 0; l < cardBlockList.length; l++ )
	{
		savePart(cardBlockList[l]);
		deleteExceptions.push( cardBlockList[l] );
	}
	
	deleteExceptions = js_normal_array_to_php( deleteExceptions );
	deleteExceptions = encode64( deleteExceptions );
	
	http.open('get', 'saveobject.php?id=0&card_id=' + card_id + '&stack_id=' + stackID + '&type=deleteparts&properties=' + deleteExceptions, false );
	http.send( null );
	
}

function saveStack()
{
	var cardExceptions = new Array();

	for ( var i = 0; i < cardList.length; i++ )
	{
		saveCard(cardList[i]);
		cardExceptions.push( cardList[i] );
	}
	
	cardExceptions = js_normal_array_to_php( cardExceptions );
	cardExceptions = encode64(cardExceptions );
	
	
	http.open('get', 'saveobject.php?id=0&card_id=0&stack_id=' + stackID + '&type=deletecards&properties=' + cardExceptions, false );
	http.send( null );
	
	
	document.getElementById('saveindicator').style.display="none";
}

function showscripteditor()
{
	document.getElementById('scripteditor').style.display='block';
}

function mouseCoords(ev)
{
	if(ev.pageX || ev.pageY){
		return {x:ev.pageX, y:ev.pageY};
	}
	return {
		x:ev.clientX + document.body.scrollLeft - document.body.clientLeft,
		y:ev.clientY + document.body.scrollTop  - document.body.clientTop
	}
}

function mouseMove(ev)
{
	ev = ev || window.event;
	var mousePos = mouseCoords(ev);

	if (parseInt(navigator.appVersion)>3)
	{
		if (navigator.appName=="Netscape")
		{
			winW = window.innerWidth;
			winH = window.innerHeight;
		}
		if (navigator.appName.indexOf("Microsoft")!=-1)
		{
			winW = document.body.offsetWidth;
			winH = document.body.offsetHeight;
		}
	}

	winW = winW / 2;
	winH = winH / 2;
	
	winW = winW - ( <?php echo $stack['width']; ?> / 2 );
	winH = winH - ( <?php echo $stack['height']; ?> / 2 ) - 20;

	mousex = mousePos.x - winW;
	mousey = mousePos.y - winH;
	
		return false;
}


function replaceAll(text, strA, strB)
{
    while ( text.indexOf(strA) != -1)
    {
        text = text.replace(strA,strB);
    }
    return text;
}

function createRequestObject()
{
    var ro;
    var browser = navigator.appName;
    if(browser == "Microsoft Internet Explorer")
	{
        ro = new ActiveXObject("Microsoft.XMLHTTP");
    }
	else
	{
        ro = new XMLHttpRequest();
    }
    return ro;
}

var http = createRequestObject();

function inlinecompiler(script)
{
	console.log(encode64( script ));
    http.open('get', 'inlinecompiler.php?script=' + encode64( script ), false);
    http.send( null );
    
    return replaceAll( http.responseText, "\\\"", "\"" );
}


function chartonum ( x )
{
	return x.charCodeAt(0);
}

function numtochar ( x )
{
	return String.fromCharCode( x );	
}

function length( x )
{
	return x.length;
}

function average()
{
	var i;
	var j = 0;
	for( i = 0; i < arguments.length; i++ )
	{
		j += arguments[i];
	}
	return j/arguments.length;
}



//topID = 0;
currentCardPtr = 0;
userLevel = 0;
changeStyles = 0;
<?php

$cardListCounter = 0;

echo "var stackID = " . $_GET['stack'] . ";\n";
?>


function getElementsByClassName(strTagName, strClassName)
{
	var arrElements = (strTagName == "*" && document.all)? document.all : document.getElementsByTagName(strTagName);
	var arrReturnElements = new Array();
	strClassName = strClassName.replace(/\-/g, "\\-");
	var oRegExp = new RegExp("(^|\\s)" + strClassName + "(\\s|$)");
	var oElement;
	for(var i=0; i<arrElements.length; i++)
	{
		oElement = arrElements[i];
		if(oRegExp.test(oElement.className))
		{
			arrReturnElements.push(oElement);
		}
	}
	return (arrReturnElements)
}
	
function changeCardToID( card_id )
{
	var changedCardID = false;
	var x = getElementsByClassName( "div", "stack_container" );
	for( var i = 0; i < x.length; i ++ )
	{
		if ( x[i].getAttribute("id") == card_id )
		{
			x[i].style.display = "block";
			updateCardPtr( card_id );
			changedCardID = true;
		}
		else
		{
			if( changedCardID = true )
			{
				x[i].style.display = "none";
			}
		}
	}
}

function changeCardToNumber( card_number )
{
	var indexNumber = card_number - 1;
	var x = getElementsByClassName( "div", "stack_container" );
	for( var i = 0; i < x.length; i ++ )
	{
		if ( i == indexNumber )
		{
			x[i].style.display = "block";
			updateCardPtr( x[i].getAttribute("id") );
		}
		else
		{
			x[i].style.display = "none";
		}
	}
}

function changeCardToName( card_name )
{
	var x = getElementsByClassName( "div", "stack_container" );
	for( var i = 0; i < x.length; i ++ )
	{
		if ( x[i].getAttribute("name").toLowerCase() == card_name.toLowerCase() )
		{
			x[i].style.display = "block";
			updateCardPtr( x[i].getAttribute("id") );
		}
		else
		{
			x[i].style.display = "none";
		}
	}
}

function updateCardPtr( newCardID )
{
	updateCardManager();
	for( var i = 0; i < cardList.length; i++ )
	{
		if ( newCardID == "card_" + cardList[i] )
		{
			currentCardPtr = i;
			cardID = cardList[currentCardPtr];
		}
	}
}

function updateCardManager()
{
	var theCardID;
	var cardElement;
	var cardDisplay;
	var cardNumber;
	var cardName;
	for( var i = 0; i < cardList.length; i++ )
	{
		theCardID = "card_" + cardList[i];
		cardElement = document.getElementById(theCardID);
		cardDisplay = cardElement.style.display;
		if( cardDisplay != "none" )
		{
			// We have found the current card
			cardNumber = i + 1;
			cardName = cardElement.getAttribute("name");
			break;
		}
	}
	var cardManagerInfo = document.getElementById("card-manager-info");
	var maxCards = cardList.length;
	
	var info = "<b>" + cardName.substring(5) + "</b><br>Card <b>" + cardNumber + "</b> of <b>" + maxCards + "</b>";
	cardManagerInfo.innerHTML = info;
}

function changeNextCard( )
{

	if ( currentCardPtr == cardList.length - 1 )
	{
		currentCardPtr = 0;
	}
	else
	{
		currentCardPtr++;
	}
		
	changeCardToID( "card_" + cardList[currentCardPtr] );
}

function changePrevCard( )
{
	if( currentCardPtr == 0 )
	{
		currentCardPtr = cardList.length -1;
	}
	else
	{
		currentCardPtr--;
	}
	changeCardToID( "card_" + cardList[currentCardPtr] );
}

			
function commitButtonEditor()
{
	var theButton = document.getElementById(document.getElementById('BEsourceObjectID').value);
	hpop__setName(theButton, document.getElementById('button_editor_name').value);
	hpop__setStyle( theButton, document.getElementById('button_editor_style').value );
	var theButton = document.getElementById(document.getElementById('BEsourceObjectID').value);
	console.log( hpop__getStyle( theButton ) );
	hpop__setEnabled( theButton, document.getElementById('button_editor_enabled').checked );
	hpop__setVisible( theButton, document.getElementById('button_editor_visible').checked );
	hpop__setHilite( theButton, document.getElementById('button_editor_hilite').checked );
	hpop__setAutoHilite( theButton, document.getElementById('button_editor_autohilite').checked );
	hpop__setShowName( theButton, document.getElementById('button_editor_showname').checked );
}


function commitFieldEditor( whatElement )
{
	var theField = document.getElementById(document.getElementById('FieldBEsourceObjectID').value);
	hpop__setName(theField, document.getElementById('field_editor_name').value);
	hpop__setStyle( theField, document.getElementById('field_editor_style').value );
	hpop__setEnabled( theField, document.getElementById('field_editor_enabled').checked );
	hpop__setVisible( theField, document.getElementById('field_editor_visible').checked );
	hpop__setLockText( theField, document.getElementById('field_editor_locktext').checked );
	hpop__setAutoSelect( theField, document.getElementById('field_editor_autoselect').checked );
	hpop__setDontWrap( theField, document.getElementById('field_editor_dontwrap').checked );
}

function commitBlockEditor(whatElement)
{
	var theBlock = document.getElementById(document.getElementById('BlockBEsourceObjectID').value);
	hpop__setName(theBlock, document.getElementById('block_editor_name').value);
	hpop__setEnabled( theBlock, document.getElementById('block_editor_enabled').checked );
	hpop__setVisible( theBlock, document.getElementById('block_editor_visible').checked );
}
	
function commitCardEditor()
{
	var theCard = document.getElementById("card_" + (document.getElementById('card_editor_id').innerHTML));
	var e = document.getElementById("card_editor_images");
	var selectedImage = e.options[e.selectedIndex].innerHTML;
	var filePath = "users/<?php echo $_SESSION['username']; ?>/images/" + selectedImage;
	hpop__setImage(theCard, filePath);
}

function openButtonEditor( whatElement, whatCard )
{

	document.getElementById( 'BEsourceObjectID' ).value = whatElement.getAttribute('id');
	document.getElementById( 'BEsourceCardID' ).value = whatCard;
	var sourceObject = whatElement.getAttribute('id').substr(3);

	var bename = whatElement.getAttribute( 'name' );
	document.getElementById( 'button_editor_name' ).value = bename.substr( ( bename.split( '_' )[1].length + bename.split('_')[2].length ) + 6 );


	var sourceID = whatElement.getAttribute('id').substr(3);
	
	document.getElementById('button_editor_id').innerHTML = sourceID;
	
	var buttonList = eval( 'card_' + whatCard + '_buttonList' );
	
	for( var i = 0; i < buttonList.length; i++ )
	{
		if ( buttonList[i] == sourceID )
		{
			document.getElementById('button_editor_order').innerHTML = i+1;
			break;
		}
	}
	
	document.getElementById('button_editor_enabled').checked = hpop__getEnabled( whatElement );
	document.getElementById('button_editor_visible').checked = hpop__getVisible( whatElement );
	document.getElementById('button_editor_hilite').checked = hpop__getHilite( whatElement );
	document.getElementById('button_editor_autohilite').checked = hpop__getAutoHilite( whatElement );
	document.getElementById("button_editor_style").value = hpop__getStyle( whatElement );
	document.getElementById('button_editor_showname').checked = hpop__getShowName( whatElement );

	var BEwin = new Window(Application.getNewId(), {className: "alphacube", title: "Button Info...", showEffect:Effect.Appear, hideEffect: Effect.Fade, showEffectOptions: { duration: 0.25 }, hideEffectOptions: { duration: 0.25 }, width:550, height:175});
	BEwin.setContent('buttoneditor' );
	BEwin.setDestroyOnClose();
	BEwin.setZIndex(1000);
	BEwin.showCenter(true); 
	myBEObserver =
	{
		onClose: function(eventName, nwin)
		{
			if (nwin == BEwin)
			{
				hasScriptEditor = false;
				//commitBEEditor();
				if ( ! dontAsk )
				{
					if ( confirm( "Save changes?" ) )
					{
						commitButtonEditor();
					}
				}
				dontAsk = false;
				Windows.removeObserver(this);
			}
		}
	}
	Windows.addObserver(myBEObserver);
	document.getElementById('button_editor_name').focus();
}


function openFieldEditor( whatElement, whatCard )
{
		document.getElementById( 'FieldBEsourceObjectID' ).value = whatElement.getAttribute('id');
		document.getElementById( 'FieldBEsourceCardID' ).value = whatCard;
		document.getElementById( 'FieldFullName' ).value = whatElement.getAttribute('name');
		var sourceObject = whatElement.getAttribute('id').substr(3);

		var bename = whatElement.getAttribute( 'name' );
		document.getElementById( 'field_editor_name' ).value = bename.substr( ( bename.split( '_' )[1].length + bename.split('_')[2].length ) + 6 );
		var sourceID = whatElement.getAttribute('id').substr(3);
		document.getElementById('field_editor_id').innerHTML = sourceID;
		var fieldList = eval( 'card_' + whatCard + '_fieldList' );
		
		for( var i = 0; i < fieldList.length; i++ )
		{
			if ( fieldList[i] == sourceID )
			{
				document.getElementById('field_editor_order').innerHTML = i+1;
				break;
			}
		}
		
		document.getElementById('field_editor_enabled').checked = hpop__getEnabled( whatElement );
		document.getElementById('field_editor_visible').checked = hpop__getVisible( whatElement );
		document.getElementById('field_editor_locktext').checked = hpop__getLockText( whatElement );
		document.getElementById("field_editor_style").value = hpop__getStyle( whatElement );
		document.getElementById('field_editor_autoselect').checked = hpop__getAutoSelect( whatElement );
		document.getElementById('field_editor_dontwrap').checked = hpop__getDontWrap( whatElement );
			
		var BEwin = new Window(Application.getNewId(), {className: "alphacube", title: "Field Info...", showEffect:Effect.Appear, hideEffect: Effect.Fade, showEffectOptions: { duration: 0.25 }, hideEffectOptions: { duration: 0.25 }, width:500, height:175});
		BEwin.setContent('fieldeditor' );
		BEwin.setDestroyOnClose();
		BEwin.setZIndex(1000);
		BEwin.showCenter(true); 
		
		myBEObserver =
			{
				onClose: function(eventName, nwin)
									{
										if (nwin == BEwin)
										{
											hasScriptEditor = false;
											//commitBEEditor();
											if ( ! dontAsk )
											{
												if ( confirm( "Save changes?" ) )
												{
													commitFieldEditor();
												}
											}
											dontAsk = false;
											Windows.removeObserver(this);
										}
								}
			}
		Windows.addObserver(myBEObserver);
		document.getElementById('field_editor_name').focus();
}


function openBlockEditor( whatElement, whatCard )
{
		document.getElementById( 'BlockBEsourceObjectID' ).value = whatElement.getAttribute('id');
		document.getElementById( 'BlockBEsourceCardID' ).value = whatCard;
		document.getElementById( 'BlockFullName' ).value = whatElement.getAttribute('name');
		var sourceObject = whatElement.getAttribute('id').substr(3);
		
		var bename = whatElement.getAttribute( 'name' );
		document.getElementById( 'block_editor_name' ).value = bename.substr( ( bename.split( '_' )[1].length + bename.split('_')[2].length ) + 6 );
		var sourceID = whatElement.getAttribute('id').substr(3);
		document.getElementById('block_editor_id').innerHTML = sourceID;
		
		var blockList = eval( 'card_' + whatCard + '_blockList' );
		for( var i = 0; i < blockList.length; i++ )
		{
			if ( blockList[i] == sourceID )
			{
				document.getElementById('block_editor_order').innerHTML = i+1;
				break;
			}
		}
		
		document.getElementById('block_editor_enabled').checked = hpop__getEnabled( whatElement );
		document.getElementById('block_editor_visible').checked = hpop__getVisible( whatElement );

		var BEwin = new Window(Application.getNewId(), {className: "alphacube", title: "Block Info...", showEffect:Effect.Appear, hideEffect: Effect.Fade, showEffectOptions: { duration: 0.25 }, hideEffectOptions: { duration: 0.25 }, width:500, height:175});
		BEwin.setContent('blockeditor' );
		BEwin.setDestroyOnClose();
		BEwin.setZIndex(1000);
		BEwin.showCenter(true); 
		myBEObserver =
			{
				onClose: function(eventName, nwin)
									{
										if (nwin == BEwin)
										{
											hasScriptEditor = false;
											hasContentsEditor = false;
											//commitBEEditor();
											if ( ! dontAsk )
											{
												if ( confirm( "Save changes?" ) )
												{
													commitBlockEditor();
												}
											}
											dontAsk = false;
											Windows.removeObserver(this);
										}
								}
			}
		Windows.addObserver(myBEObserver);
		document.getElementById('block_editor_name').focus();
}
	
function openCardEditor( whatCard )
{
	updateUserImages();
	var whatElement = whatCard;
	
	document.getElementById( 'card_editor_name' ).value = whatElement.getAttribute( 'name' ).substring(5);
	document.getElementById('card_editor_id').innerHTML = whatElement.getAttribute('id').substr(5);
	document.getElementById('card_editor_order').innerHTML = cardNumberFromID(whatElement.getAttribute('id'));
	document.getElementById("card_editor_images").value = hpop__getImage( whatCard );
			
	var CEwin = new Window(Application.getNewId(), {className: "alphacube", title: "Card Info...", showEffect:Effect.Appear, hideEffect: Effect.Fade, showEffectOptions: { duration: 0.25 }, hideEffectOptions: { duration: 0.25 }, width:492, height:167});
	CEwin.setContent('cardeditor');
	CEwin.setDestroyOnClose();
	CEwin.setZIndex(1000);
	CEwin.showCenter(true); 
		
	myCEObserver =
	{
		onClose: function(eventName, nwin)
		{
			if (nwin == CEwin)
			{
				hasScriptEditor = false;
				if ( ! dontAsk )
				{
					if ( confirm( "Save changes?" ) )
					{
						commitCardEditor();
					}
				}
				dontAsk = false;
				Windows.removeObserver(this);
			}
		}
	}
	Windows.addObserver(myCEObserver);
	document.getElementById('card_editor_name').focus();
}


function openScriptEditor( whatElement, whatCard )
{
	document.getElementById( 'sourceObjectID' ).value = whatElement.getAttribute('id');
	document.getElementById( 'sourceCardID' ).value = whatCard;
	var sourceObject = whatElement.getAttribute('id').substr(3);
	
	var elementName = whatElement.getAttribute( 'name' );
	
	var i = elementName.indexOf('_');
	i = elementName.indexOf('_', i + 1);
	i = elementName.indexOf('_', i + 1);
	
	var objectname = elementName.substring(i+1);

	document.getElementById('scriptHeader').innerHTML = "Editing script for Object ID " + sourceObject + " (\"" + objectname + "\")";
	
	document.getElementById('scriptField').value = eval( 'part_' + stackID + '_' + whatCard + '_' + sourceObject + '_scriptx' );
	
	if( ! hasScriptEditor )
	{
		hasScriptEditor = true;
		existing_script = eval( 'part_' + stackID + '_' + whatCard + '_' + sourceObject + '_scriptx' );
		var scriptwin = new Window(Application.getNewId(), {className: "alphacube", title: "Script Editor", showEffect:Effect.Appear, hideEffect: Effect.Fade, showEffectOptions: { duration: 0.25 }, hideEffectOptions: { duration: 0.25 }, width:500, height:600});
		scriptwin.setContent('scripteditor' );
		scriptwin.setDestroyOnClose();
		scriptwin.setZIndex(1000);
		scriptwin.showCenter(true); 
		myScriptObserver =
			{
				onClose: function(eventName, nwin)
									{
										if (nwin == scriptwin)
										{
											hasScriptEditor = false;
											commitScriptEditor();
											Windows.removeObserver(this);
										}
								}
			}
		Windows.addObserver(myScriptObserver);
	}

}
	
function openCardScriptEditor( cardID )
{
	var whatCard = document.getElementById(cardID);
	document.getElementById( 'cardSourceCardID' ).value = cardID.substring(5);
	
	var cardName = whatCard.getAttribute( 'name' ).substring(5);

	document.getElementById('cardScriptHeader').innerHTML = "Editing script for Card ID " + cardID.substring(5) + " (\"" + cardName + "\")";
	
	document.getElementById('cardScriptField').value = eval( 'card_' + stackID + '_' + cardID.substring(5) + '_scriptx' );
	
	if( ! hasScriptEditor )
	{
		hasScriptEditor = true;
		existing_script = eval( 'card_' + stackID + '_' + cardID.substring(5) + '_scriptx' );
		var scriptwin = new Window(Application.getNewId(), {className: "alphacube", title: "Script Editor", showEffect:Effect.Appear, hideEffect: Effect.Fade, showEffectOptions: { duration: 0.25 }, hideEffectOptions: { duration: 0.25 }, width:500, height:600});
		scriptwin.setContent('cardscripteditor' );
		scriptwin.setDestroyOnClose();
		scriptwin.setZIndex(1000);
		scriptwin.showCenter(true); 
		myScriptObserver =
			{
				onClose: function(eventName, nwin)
									{
										if (nwin == scriptwin)
										{
											hasScriptEditor = false;
											commitCardScriptEditor();
											Windows.removeObserver(this);
										}
								}
			}
		Windows.addObserver(myScriptObserver);
	}

}


function openContentsEditor( whatElement, whatCard )
{
	document.getElementById( 'contentsObjectID' ).value = whatElement.getAttribute('id');
	document.getElementById( 'contentsCardID' ).value = whatCard;
	var sourceObject = whatElement.getAttribute('id').substr(3);
	
	objectName = hpop__getShortName( whatElement );
	document.getElementById('contentsHeader').innerHTML = "Editing Contents for Object ID " + sourceObject + " (\"" + objectName + "\")";
	document.getElementById('contentsField').value = hpop__getContents( whatElement );
	
	if( ! hasContentsEditor )
	{
		hasContentsEditor = true;
		existing_contents = hpop__getContents( whatElement );
		var contentswin = new Window(Application.getNewId(), {className: "alphacube", title: "Contents Editor", showEffect:Effect.Appear, hideEffect: Effect.Fade, showEffectOptions: { duration: 0.25 }, hideEffectOptions: { duration: 0.25 }, width:500, height:600});
		contentswin.setContent('contentseditor' );
		contentswin.setDestroyOnClose();
		contentswin.setZIndex(1000);
		contentswin.showCenter(true); 
		myContentsObserver =
			{
				onClose: function(eventName, nwin)
									{
										if (nwin == contentswin)
										{
											hasContentsEditor = false;
											commitContentsEditor();
											Windows.removeObserver(this);
										}
								}
			}
		Windows.addObserver(myContentsObserver);
	}

}


function cancelScriptEditor( )
{
	//document.getElementById('scripteditor').style.display= 'none';
	Windows.closeAll();
}

function createCard ( cardName )
{
	
	var newCard = document.createElement('div');
	var maxid = 0;
	var currentcardloc;
	for ( var i = 0; i < cardList.length; i++ )
	{
		if ( cardList[i] > maxid )
		{
			maxid = cardList[i];
		}
	}
	maxid = (maxid*1)+ 1;
	cardList.splice( cardID, 0, maxid );
	
	newCard.setAttribute( 'id', 'card_' + maxid );
	newCard.setAttribute( 'name', 'card_' + cardName );
	newCard.setAttribute( 'class', 'stack_container' );
	newCard.style.display = "none";
	newCard.style.innerHTML = "<br/>";
	
	eval( "card_" + maxid + "_buttonList = new Array();" );
	eval( "card_" + maxid + "_fieldList = new Array();" );
	eval( "card_" + maxid + "_blockList = new Array();" );
	eval( "card_" + maxid + "_buttonZList = new Array();" );
	eval( "card_" + maxid + "_fieldZList = new Array();" );
	eval( "card_" + maxid + "_blockZList = new Array();" );
	
	var stackDiv = document.getElementById('stack');
	
	stackDiv.appendChild( newCard );
	changeCardToID("card_" + maxid);
}

function deleteCardID( delCardID )
{
	if( cardList.length == 1 )
	{
		alert("There is only one card left. You can not delete it.");
		return false;
	}
	var currentCardNumber = getCurrentCardNumber();
	var currentCardID = getCurrentCardID();
	delCardID = "card_" + delCardID;
	var theCard = document.getElementById( delCardID );
	var theCardID = delCardID.substr( 5 );
	
	if( theCard == null || theCard == "" || theCard == false )
	{
		alert("The card of ID " + theCardID + " does not exist.");
		return false;
	}
	
	theCard.parentNode.removeChild(theCard);
	
	for ( var i = 0; i < cardList.length; i++ )
	{
		if ( cardList[i] == theCardID )
		{
			cardList.splice(i,1);
			if ( theCardID == currentCardID )
			{
				// If the user is deleting the current card, then we need to change the card
				if( cardList.length >= currentCardNumber )
				{
					changeCardToNumber(currentCardNumber);
					updateCardManager();
				}
				else
				{
					//If the card that we were on was the last card then we need to go the previous card
					changeCardToNumber(currentCardNumber - 1);	
					updateCardManager();
				}
			}
		}
	}
}

function deleteCardNumber( delCardNumber )
{
	if( cardList.length == 1 )
	{
		alert("There is only one card left. You can not delete it.");
		return false;
	}
	var currentCardNumber = getCurrentCardNumber();
	var cardIndex = delCardNumber - 1;
	var cardID = "card_" + cardList[cardIndex];
	var thisCard = document.getElementById( cardID );
	
	if( thisCard == null || thisCard == "" || thisCard == false )
	{
		alert("Card " + delCardNumber + " does not exist.");
		return false;
	}
	
	thisCard.parentNode.removeChild(thisCard);
	
	cardList.splice(cardIndex,1);
	if ( delCardNumber == currentCardNumber )
	{
		// If the user is deleting the current card, then we need to change the card
		if( cardList.length >= currentCardNumber )
		{
			changeCardToNumber(currentCardNumber);
			updateCardManager();
		}
		else
		{
			//If the card that we were on was the last card then we need to go the previous card
			changeCardToNumber(currentCardNumber - 1);	
			updateCardManager();
		}
	}
	return true;
}

function deleteCardName( delCardName )
{
	if( cardList.length == 1 )
	{
		alert("There is only one card left. You can not delete it.");
		return false;
	}
	var currentCardID = getCurrentCardID();
	var currentCardNumber = getCurrentCardNumber();
	var thisCard = document.getElementsByName( 'card_' + delCardName )[0];
	var thisCardId = thisCard.getAttribute('id').substr( 5 );
	
	if( thisCard == null || thisCard == "" || thisCard == false )
	{
		alert("Card '" + delCardName + "' does not exist.");
		return false;
	}
	
	thisCard.parentNode.removeChild(thisCard);

	
	for ( var i = 0; i < cardList.length; i++ )
	{
		if ( cardList[i] == thisCardId )
		{
			cardList.splice(i,1);
			if ( thisCardId == currentCardID )
			{
				// If the user is deleting the current card, then we need to change the card
				if( cardList.length >= currentCardNumber )
				{
					changeCardToNumber(currentCardNumber);
					updateCardManager();
				}
				else
				{
					//If the card that we were on was the last card then we need to go the previous card
					changeCardToNumber(currentCardNumber - 1);	
					updateCardManager();
				}
			}
		}
	}
}

function commitScriptEditor()
{
	if ( document.getElementById('scriptField').value != existing_script )
	{
		var x = confirm( "Do you want to save your changes?" );
		if ( x )
		{
			var sourceObject = document.getElementById('sourceObjectID').value.substr(3);
			var sourceCardID = document.getElementById('sourceCardID').value;
	
			var sourceScript = document.getElementById('scriptField').value;
			var compiledScript = inlinecompiler(sourceScript);

			eval( "part_" + stackID + "_" + sourceCardID + "_" + sourceObject + "_scriptx = sourceScript;" );	
			eval( "part_" + stackID + "_" + sourceCardID + "_" + sourceObject + "_script = compiledScript;" );
		}
	}
	//document.getElementById('scripteditor').style.display= 'none';
	//Windows.closeAll();
}

function commitCardScriptEditor()
{
	if ( document.getElementById('cardScriptField').value != existing_script )
	{
		var x = confirm( "Do you want to save your changes?" );
		if ( x )
		{
			var sourceCardID = document.getElementById('cardSourceCardID').value;
	
			var sourceScript = document.getElementById('cardScriptField').value;
			var compiledScript = inlinecompiler(sourceScript);

			eval( "card_" + stackID + "_" + sourceCardID + "_scriptx = sourceScript;" );	
			eval( "card_" + stackID + "_" + sourceCardID + "_script = compiledScript;" );
		}
	}
	//document.getElementById('scripteditor').style.display= 'none';
	//Windows.closeAll();
}

function commitContentsEditor()
{
	if ( document.getElementById('contentsField').value != existing_contents )
	{
		var whatElement;
		var contents;
		var x = confirm( "Do you want to save your changes?" );
		if ( x )
		{
			whatElement = document.getElementById( document.getElementById('contentsObjectID').value );
			contents = document.getElementById('contentsField').value;
			hpop__setBlockValue( whatElement, contents, "into" );
		}
	}
}

function changeeditmode( neweditmode )
{
	var whatElement;
	var elementID;
	dragElement = "";
	olduserLevel = userLevel;
	userLevel = neweditmode;
	var i;
	var j;
			
	changeStyles = 1;
	// Clean up - and make the style of all elements the "edit" style
	for( j = 0; j < cardList.length; j++ )
	{
		var buttonzlist = eval( "card_" + cardList[j] + "_buttonZList" );
		var buttonlist = eval( "card_" + cardList[j] + "_buttonList" );
		for( i = 0; i < buttonzlist.length; i++ )
		{
			whatElement = document.getElementById( "obj" + buttonlist[i] );
			whatElement.style.zIndex = buttonzlist[i];
			whatElement.style.border = "";
			
			// Change the button back to it's previous style, enabled, visible, hilite and autohilite
			hpop__setStyle( whatElement, hpop__getStyle( whatElement ) );
			
			// Now that we have changed the style, we have to recapture the element, as whatElement now no longer exists
			elementID = hpop__getID( whatElement );
			whatElement = document.getElementById( elementID );
			
			hpop__setEnabled( whatElement, hpop__getEnabled( whatElement ) );
			hpop__setVisible( whatElement, hpop__getVisible( whatElement ) );
			hpop__setHilite( whatElement, hpop__getHilite( whatElement ) );
			hpop__setAutoHilite( whatElement, hpop__getAutoHilite( whatElement ) );
		}
		var fldzlist = eval( "card_" + cardList[j] + "_fieldZList" );
		var fldlist = eval( "card_" + cardList[j] + "_fieldList" );
		for( i = 0; i < fldzlist.length; i++ )
		{
			document.getElementById( "obj" + fldlist[i] ).style.zIndex = fldzlist[i];
			document.getElementById( "obj" + fldlist[i] ).style.border = '';
			whatElement = document.getElementById( "obj" + fldlist[i] );
			
			hpop__setStyle( whatElement, hpop__getStyle( whatElement ) );
			
			// Now that we have changed the style, we have to recapture the element, as whatElement now no longer exists
			elementID = hpop__getID( whatElement );
			whatElement = document.getElementById( elementID );
			
			hpop__setEnabled( whatElement, hpop__getEnabled( whatElement ) );
			hpop__setVisible( whatElement, hpop__getVisible( whatElement ) );
			hpop__setLockText( whatElement, hpop__getLockText( whatElement ) );
		}
		var blkzlist = eval( "card_" + cardList[j] + "_blockZList" );
		var blklist = eval( "card_" + cardList[j] + "_blockList" );
		for( i = 0; i < blkzlist.length; i++ )
		{
			document.getElementById( "obj" + blklist[i] ).style.zIndex = blkzlist[i];
			document.getElementById( "obj" + blklist[i] ).style.border = '';
			whatElement = document.getElementById( "obj" + blklist[i] );
			
			// Reset the contents of the div to what they should be
			elementID = hpop__getID( document.getElementById( "obj" + blklist[i] ) );
			elementIndex = blockIDList.indexOf( elementID );
			
			document.getElementById( "obj" + blklist[i] ).innerHTML = blockContentsList[elementIndex];
			document.getElementById( "obj" + blklist[i] ).className = "";
			
			hpop__setEnabled( whatElement, hpop__getEnabled( whatElement ) );
			hpop__setVisible( whatElement, hpop__getVisible( whatElement ) );
		}
	}
	document.getElementById( 'toolcell0' ).className = 'toolcell';
	document.getElementById( 'toolcell1' ).className = 'toolcell';
	document.getElementById( 'toolcell2' ).className = 'toolcell';
	document.getElementById( 'toolcell3' ).className = 'toolcell';
	changeStyles = 0;
	switch ( neweditmode )
	{			
		case 1:
			document.getElementById( 'toolcell1' ).className = 'toolcell_selected';
			for( j = 0; j < cardList.length; j++ )
			{
				var buttonlist = eval( "card_" + cardList[j] + "_buttonList" );
				for( i = 0; i < buttonlist.length; i++ )
				{
					document.getElementById( "obj" + buttonlist[i] ).style.zIndex = ( ( document.getElementById( "obj" + buttonlist[i] ).style.zIndex ) * 1 ) + 50;
					document.getElementById( "obj" + buttonlist[i] ).style.visibility = "visible";
					document.getElementById( "obj" + buttonlist[i] ).className = "btn_editing";
					if( hpop__getStyle( document.getElementById( "obj" + buttonlist[i] ) ) == "checkbox" || hpop__getStyle( document.getElementById( "obj" + buttonlist[i] ) ) == "radiobutton" )
					{
						document.getElementById( "obj" + buttonlist[i] ).firstChild.firstChild.firstChild.firstChild.disabled = true;
					}
					else
					{
						document.getElementById( "obj" + buttonlist[i] ).disabled = false;
					}
				}
			}
			break;
			 
		case 2:
			document.getElementById( 'toolcell2' ).className = 'toolcell_selected';
			for( j = 0; j < cardList.length; j++ )
			{
				var fldlist = eval( "card_" + cardList[j] + "_fieldList" );
				for( i = 0; i < fldlist.length; i++ )
				{
					document.getElementById( "obj" + fldlist[i] ).style.zIndex = ( ( document.getElementById( "obj" + fldlist[i] ).style.zIndex ) * 1 ) + 50;
					//document.getElementById( "obj" + fldlist[i] ).style.border = normalEditBorder;
					document.getElementById( "obj" + fldlist[i] ).disabled = false;
					document.getElementById( "obj" + fldlist[i] ).style.visibility = "visible";
					document.getElementById( "obj" + fldlist[i] ).readOnly = true;
				}
			}
			break;
			
		case 3:
			document.getElementById( 'toolcell3' ).className = 'toolcell_selected';
			for( j = 0; j < cardList.length; j++ )
			{
				var blklist = eval( "card_" + cardList[j] + "_blockList" );
				for( i = 0; i < blklist.length; i++ )
				{
					document.getElementById( "obj" + blklist[i] ).style.zIndex = ( ( document.getElementById( "obj" + blklist[i] ).style.zIndex ) * 1 ) + 50;
					document.getElementById( "obj" + blklist[i] ).style.visibility = "visible";
					document.getElementById( "obj" + blklist[i] ).disabled = false;
					document.getElementById( "obj" + blklist[i] ).className = "blk_editing";
					document.getElementById( "obj" + blklist[i] ).style = "";
					document.getElementById( "obj" + blklist[i] ).innerHTML = "";
				}
			}
			break;
			
			default:
			document.getElementById( 'toolcell0' ).className = 'toolcell_selected';
			break;
	}
}

function deleteElement( whatElement )
{
	// OK, we need to remove the element from both its list array, and its z-index array
	// and then remove the actual dom element.
	
	// First, we need to figure out where the hell this goes:
	
	var i,j;
	var deleted = false;
	var objlist;
	var objzlist;
	
	for( i = 0; i < cardList.length; i++ )
	{
		objlist = eval( "card_" + cardList[i] + "_buttonList" );
		objzlist = eval( "card_" + cardList[i] + "_buttonZList" );
		for( j = 0; j < objlist.length; j++ )
		{
			if( whatElement.getAttribute('id') == ( "obj" + objlist[j] ) )
			{
				// Found it!
				objlist.splice( j, 1 );
				objzlist.splice( j, 1 );
				deleted = true;
			}
		}
		
		if ( ! deleted )
		{
		
			objlist = eval( "card_" + cardList[i] + "_fieldList" );
			objzlist = eval( "card_" + cardList[i] + "_fieldZList" );
			for( j = 0; j < objlist.length; j++ )
			{
				if( whatElement.getAttribute('id') == ( "obj" + objlist[j] ) )
				{
					// Found it!
					objlist.splice( j, 1 );
					objzlist.splice( j, 1 );
					deleted = true;
				}
			}
		
		}
		
		if ( ! deleted )
		{
		
			objlist = eval( "card_" + cardList[i] + "_blockList" );
			objzlist = eval( "card_" + cardList[i] + "_blockZList" );
			for( j = 0; j < objlist.length; j++ )
			{
				if( whatElement.getAttribute('id') == ( "obj" + objlist[j] ) )
				{
					// Found it!
					objlist.splice( j, 1 );
					objzlist.splice( j, 1 );
					deleted = true;
				}
			}
		
		}

	}
	
	whatElement.parentNode.removeChild( whatElement );
	
	dragElement = "";	
}


function hpop__setCheckable( whatElement, newCheckable )
{
	if ( newCheckable == true || hpop__binaryEq( newCheckable, "true" ) )
	{
		// Convert Element to a <select> element with size = 30000
		
		// Copy all useful attributes about the existing element
		var newElementStyle = whatElement.getAttribute('style');
		var newElementID = whatElement.getAttribute('id');
		var newElementName = whatElement.name;
		var newElementZIndex = whatElement.zIndex;
		var newElementMouseUp = whatElement.getAttribute('onmouseup');
		var newElementMouseDown = whatElement.getAttribute('onmousedown');
		var newElementMouseOver = whatElement.getAttribute('onmouseover');
		var newElementMouseOut = whatElement.getAttribute('onmouseout');
		var newElementMouseMove = whatElement.getAttribute('onmousemove');
		var newElementDoubleClick = whatElement.getAttribute('ondblclick');
		
		// Copy the card that the existing element was on
		var newElementParent = whatElement.parentNode;
		
		// Create a new <table> element
		var newTableElement = document.createElement( "table" );
		
		// Here we need to create a new <tbody> element, then add it to the table element, as the PHP code auto-creates one
		var newTableBody = document.createElement( "tbody" );
		newTableElement.appendChild( newTableBody );
		
		// Set the new elements attributes to the same at the old element
		newTableElement.setAttribute('style', newElementStyle );
		newTableElement.setAttribute('id', newElementID );
		newTableElement.border = 0;
		newTableElement.setAttribute('name', newElementName );
		newTableElement.setAttribute('zindex', newElementZIndex );
		newTableElement.setAttribute( "onmouseup", newElementMouseUp );
		newTableElement.setAttribute( "onmousedown", newElementMouseDown );
		newTableElement.setAttribute( "onmouseover", newElementMouseOver );
		newTableElement.setAttribute( "onmouseout", newElementMouseOut );
		newTableElement.setAttribute( "onmousemove", newElementMouseMove );
		newTableElement.setAttribute( "ondblclick", newElementDoubleClick );

		// Create a new row , and add it to the tbody
		var newTableElementRow = document.createElement( "tr" );
		newTableBody.appendChild( newTableElementRow );
		
		// Create a new column, and set some attributes
		var newTableElementCell1 = document.createElement( "td" );
		newTableElementCell1.setAttribute("valign", "middle" );
		newTableElementCell1.width = 1;
		
		// Add the column to the row we just created
		newTableElementRow.appendChild( newTableElementCell1 );
		
		// Create another column, and add some attributes
		var newTableElementCell2 = document.createElement( "td" );
		newTableElementCell2.setAttribute("valign", "middle" );
		newTableElementCell2.style.fontFamily = "'Lucida Grande', Verdana, Arial, sans-serif;";
		newTableElementCell2.style.fontSize = "13px";
		
		// Create a label for that column, and add some attributes
		var newTableElementCell2Label = document.createElement( "label" );
		newTableElementCell2Label.setAttribute('for', whatElement.getAttribute('id') + "_child" );
		newTableElementCell2Label.innerHTML = whatElement.innerHTML;
		
		// Add the label to the column
		newTableElementCell2.appendChild( newTableElementCell2Label );
	
		// Add the column to the row
		newTableElementRow.appendChild( newTableElementCell2 );
		
		// Delete the old element
		whatElement.parentNode.removeChild( whatElement );
		
		// Create a new checkbox
		var newElement = document.createElement( "input" );
		newElement.setAttribute('type', 'checkbox' );
		newElement.setAttribute('id', whatElement.getAttribute('id') + "_child" );
		
		//The elements below are not required, as we already included them in the entire table
		/*newElement.setAttribute('onmouseup', newElementMouseUp );
		newElement.setAttribute('onmousedown', newElementMouseDown );
		newElement.setAttribute('onmouseover', newElementMouseOver );
		newElement.setAttribute('onmouseout', newElementMouseOut );
		newElement.setAttribute('onmousemove', newElementMouseMove );*/
		// Add the checkbox to the first column
		newTableElementCell1.appendChild( newElement );
		
		// Add the <table> to the card that it was on
		newElementParent.appendChild( newTableElement );
		
	}
	else
	{
		if ( hpop__getCheckable( whatElement ) )
		{
			// Convert Element to a <textarea> element
			
			// Copy all useful attributes about the existing element
			var newElementStyle = whatElement.getAttribute('style');
			var newElementID = whatElement.getAttribute('id');
			var newElementName = whatElement.getAttribute('name');
			var newElementZIndex = whatElement.zIndex;
			var newElementMouseUp = whatElement.getAttribute('onmouseup');
			var newElementMouseDown = whatElement.getAttribute('onmousedown');
			var newElementMouseOver = whatElement.getAttribute('onmouseover');
			var newElementMouseOut = whatElement.getAttribute('onmouseout');
			var newElementMouseMove = whatElement.getAttribute('onmousemove');
			var newElementDoubleClick = whatElement.getAttribute('ondblclick');
			
			var newElementParent = whatElement.parentNode;
						
			// Delete the old element
			whatElement.parentNode.removeChild( whatElement );
			
			// Fashion a new tag in its likeness
			var newElement = document.createElement( "button" );
			newElement.setAttribute('style', newElementStyle );
			newElement.setAttribute('id', newElementID );
			newElement.setAttribute('name', newElementName );
			newElement.innerHTML = newElementName.substr( ( newElementName.split( '_' )[1].length + newElementName.split('_')[2].length ) + 6 );
			newElement.setAttribute('zindex', newElementZIndex );
			newElement.setAttribute('onmouseup', newElementMouseUp );
			newElement.setAttribute('onmousedown', newElementMouseDown );
			newElement.setAttribute('onmouseover', newElementMouseOver );
			newElement.setAttribute('onmouseout', newElementMouseOut );
			newElement.setAttribute('onmousemove', newElementMouseMove );
			newElement.setAttribute('ondblclick', newElementDoubleClick );
			
			newElementParent.appendChild( newElement );

		}
	}
}



function hpop__setRound( whatElement )
{
	// First we need to see if the element is a button or not
	if(hpop__getType( whatElement ) == "button")
	{
		// We now need to check to see if the button is themed like a round rect
		if( hpop__getStyle( whatElement ) == "checkbox" || hpop__getStyle( whatElement ) == "radiobutton" )
		{
			// Copy all useful attributes about the existing element
			var newElementStyle = whatElement.getAttribute('style');
			var newElementID = whatElement.getAttribute('id');
			var newElementName = whatElement.getAttribute('name');
			var newElementZIndex = whatElement.zIndex;
			var newElementMouseUp = whatElement.getAttribute('onmouseup');
			var newElementMouseDown = whatElement.getAttribute('onmousedown');
			var newElementMouseOver = whatElement.getAttribute('onmouseover');
			var newElementMouseOut = whatElement.getAttribute('onmouseout');
			var newElementMouseMove = whatElement.getAttribute('onmousemove');
			var newElementDoubleClick = whatElement.getAttribute('ondblclick');
			
			var newElementParent = whatElement.parentNode;
						
			// Delete the old element
			whatElement.parentNode.removeChild( whatElement );
			
			// Fashion a new tag in its likeness
			var newElement = document.createElement( "button" );
			newElement.setAttribute('style', newElementStyle );
			newElement.setAttribute('id', newElementID );
			newElement.setAttribute('name', newElementName );
			newElement.innerHTML = newElementName.substr( ( newElementName.split( '_' )[1].length + newElementName.split('_')[2].length ) + 6 );
			newElement.setAttribute('zindex', newElementZIndex );
			newElement.setAttribute('onmouseup', newElementMouseUp );
			newElement.setAttribute('onmousedown', newElementMouseDown );
			newElement.setAttribute('onmouseover', newElementMouseOver );
			newElement.setAttribute('onmouseout', newElementMouseOut );
			newElement.setAttribute('onmousemove', newElementMouseMove );
			newElement.setAttribute('ondblclick', newElementDoubleClick );
			
			newElementParent.appendChild( newElement );

		}
	}
}


function hpop__setRadio( whatElement )
{
	// First we need to see if the element is a button or not
	if( hpop__getType( whatElement ) == "button" )
	{
		// We now need to check to see if the button is themed like a round rect
		var buttonStyle = hpop__getStyle( whatElement );
		if( buttonStyle == "checkbox" )
		{
			// If the button is a checkbox, then we only need to change a few properties on order for it
			// to become a radio button.
			whatElement.firstChild.firstChild.firstChild.firstChild.setAttribute('type', 'radio' );
			whatElement.firstChild.firstChild.firstChild.firstChild.setAttribute('name', hpop__getFamily( whatElement ) );
		}
		else if( buttonStyle == "popup" )
		{
			alert("jsCard Error: Changing from style 'popup' to 'radiobutton' is not supported.");
		}
		else if( buttonStyle == "radiobutton" )
		{
			if( whatElement.tagName == "BUTTON" )
			{
				actuallySetRadio( whatElement );
				// Do nothing here, as if we actually do something, then we will accidentally add another radiobutton!
			}
		}
		else
		{
			// If the button is themed like a round rectangle, then run the following code, which ONLY works if the button is a round rect

			// Convert Element to a <select> element with size = 30000
			actuallySetRadio( whatElement );
		}
	}
}


function actuallySetRadio( whatElement )
{
			var family = hpop__getFamily( whatElement );
			
			// Copy all useful attributes about the existing element
			var newElementStyle = whatElement.getAttribute('style');
			var newElementID = whatElement.getAttribute('id');
			var newElementName = whatElement.name;
			var newElementZIndex = whatElement.zIndex;
			var newElementMouseUp = whatElement.getAttribute('onmouseup');
			var newElementMouseDown = whatElement.getAttribute('onmousedown');
			var newElementMouseOver = whatElement.getAttribute('onmouseover');
			var newElementMouseOut = whatElement.getAttribute('onmouseout');
			var newElementMouseMove = whatElement.getAttribute('onmousemove');
			var newElementDoubleClick = whatElement.getAttribute('ondblclick');
			
			// Copy the card that the existing element was on
			var newElementParent = whatElement.parentNode;
			
			// Create a new <table> element
			var newTableElement = document.createElement( "table" );
			
			// Here we need to create a new <tbody> element, then add it to the table element, as the PHP code auto-creates one
			var newTableBody = document.createElement( "tbody" );
			newTableElement.appendChild( newTableBody );
			
			// Set the new elements attributes to the same at the old element
			newTableElement.setAttribute('style', newElementStyle );
			newTableElement.setAttribute('id', newElementID );
			newTableElement.border = 0;
			newTableElement.setAttribute('name', newElementName );
			newTableElement.setAttribute('zindex', newElementZIndex );
			newTableElement.setAttribute( "onmouseup", newElementMouseUp );
			newTableElement.setAttribute( "onmousedown", newElementMouseDown );
			newTableElement.setAttribute( "onmouseover", newElementMouseOver );
			newTableElement.setAttribute( "onmouseout", newElementMouseOut );
			newTableElement.setAttribute( "onmousemove", newElementMouseMove );
			newTableElement.setAttribute( "ondblclick", newElementDoubleClick );

			// Create a new row , and add it to the tbody
			var newTableElementRow = document.createElement( "tr" );
			newTableBody.appendChild( newTableElementRow );
			
			// Create a new column, and set some attributes
			var newTableElementCell1 = document.createElement( "td" );
			newTableElementCell1.setAttribute("valign", "middle" );
			newTableElementCell1.width = 1;
			
			// Add the column to the row we just created
			newTableElementRow.appendChild( newTableElementCell1 );
			
			// Create another column, and add some attributes
			var newTableElementCell2 = document.createElement( "td" );
			newTableElementCell2.setAttribute("valign", "middle" );
			newTableElementCell2.style.fontFamily = "'Lucida Grande', Verdana, Arial, sans-serif;";
			newTableElementCell2.style.fontSize = "13px";
			
			// Create a label for that column, and add some attributes
			var newTableElementCell2Label = document.createElement( "label" );
			newTableElementCell2Label.setAttribute('for', whatElement.getAttribute('id') + "_child" );
			newTableElementCell2Label.innerHTML = whatElement.innerHTML;
			
			// Add the label to the column
			newTableElementCell2.appendChild( newTableElementCell2Label );
			
			// Add the column to the row
			newTableElementRow.appendChild( newTableElementCell2 );
			
			// Delete the old element
			whatElement.parentNode.removeChild( whatElement );
			
			// Create a new checkbox
			var newElement = document.createElement( "input" );
			newElement.setAttribute('name', family );
			newElement.setAttribute('type', 'radio' );
			newElement.setAttribute('id', whatElement.getAttribute('id') + "_child" );
			
			//The elements below are not required, as we already included them in the entire table
			/*newElement.setAttribute('onmouseup', newElementMouseUp );
			newElement.setAttribute('onmousedown', newElementMouseDown );
			newElement.setAttribute('onmouseover', newElementMouseOver );
			newElement.setAttribute('onmouseout', newElementMouseOut );
			newElement.setAttribute('onmousemove', newElementMouseMove );*/

			// Add the checkbox to the first column
			newTableElementCell1.appendChild( newElement );
			
			// Add the <table> to the card that it was on
			newElementParent.appendChild( newTableElement );
}

function hpop__setCheck( whatElement )
{
	// First we need to see if the element is a button or not
	if( hpop__getType( whatElement ) == "button" )
	{
		// We now need to check to see if the button is themed like a round rect
		var buttonStyle = hpop__getStyle( whatElement );
		if( buttonStyle == "radiobutton" )
		{
			// If the button is a checkbox, then we only need to change a few properties on order for it
			// to become a radio button.
			whatElement.firstChild.firstChild.firstChild.firstChild.setAttribute('type', 'checkbox' );
			whatElement.firstChild.firstChild.firstChild.firstChild.setAttribute('name', hpop__getFamily( whatElement ) );
		}
		else if( buttonStyle == "popup" )
		{
			alert("jsCard Error: Changing from style 'popup' to 'radiobutton' is not supported.");
		}
		else if( buttonStyle == "checkbox" )
		{
			if( whatElement.tagName == "BUTTON" )
			{
				actuallySetCheck( whatElement );
				// Do nothing here, as if we actually do something, then we will accidentally add another radiobutton!
			}
		}
		else
		{
			// If the button is themed like a round rectangle, then run the following code, which ONLY works if the button is a round rect

			// Convert Element to a <select> element with size = 30000
			actuallySetCheck( whatElement );
		}
	}
}

function actuallySetCheck( whatElement )
{
	var family = hpop__getFamily( whatElement );
	
	// Copy all useful attributes about the existing element
	var newElementStyle = whatElement.getAttribute('style');
	var newElementID = whatElement.getAttribute('id');
	var newElementName = whatElement.name;
	var newElementZIndex = whatElement.zIndex;
	var newElementMouseUp = whatElement.getAttribute('onmouseup');
	var newElementMouseDown = whatElement.getAttribute('onmousedown');
	var newElementMouseOver = whatElement.getAttribute('onmouseover');
	var newElementMouseOut = whatElement.getAttribute('onmouseout');
	var newElementMouseMove = whatElement.getAttribute('onmousemove');
	var newElementDoubleClick = whatElement.getAttribute('ondblclick');
	
	// Copy the card that the existing element was on
	var newElementParent = whatElement.parentNode;
	
	// Create a new <table> element
	var newTableElement = document.createElement( "table" );
	
	// Here we need to create a new <tbody> element, then add it to the table element, as the PHP code auto-creates one
	var newTableBody = document.createElement( "tbody" );
	newTableElement.appendChild( newTableBody );
	
	// Set the new elements attributes to the same at the old element
	newTableElement.setAttribute('style', newElementStyle );
	newTableElement.setAttribute('id', newElementID );
	newTableElement.border = 0;
	newTableElement.setAttribute('name', newElementName );
	newTableElement.setAttribute('zindex', newElementZIndex );
	newTableElement.setAttribute( "onmouseup", newElementMouseUp );
	newTableElement.setAttribute( "onmousedown", newElementMouseDown );
	newTableElement.setAttribute( "onmouseover", newElementMouseOver );
	newTableElement.setAttribute( "onmouseout", newElementMouseOut );
	newTableElement.setAttribute( "onmousemove", newElementMouseMove );
	newTableElement.setAttribute( "ondblclick", newElementDoubleClick );
	
	// Create a new row , and add it to the tbody
	var newTableElementRow = document.createElement( "tr" );
	newTableBody.appendChild( newTableElementRow );
	
	// Create a new column, and set some attributes
	var newTableElementCell1 = document.createElement( "td" );
	newTableElementCell1.setAttribute("valign", "middle" );
	newTableElementCell1.width = 1;
	
	// Add the column to the row we just created
	newTableElementRow.appendChild( newTableElementCell1 );
	
	// Create another column, and add some attributes
	var newTableElementCell2 = document.createElement( "td" );
	newTableElementCell2.setAttribute("valign", "middle" );
	newTableElementCell2.style.fontFamily = "'Lucida Grande', Verdana, Arial, sans-serif;";
	newTableElementCell2.style.fontSize = "13px";
	
	// Create a label for that column, and add some attributes
	var newTableElementCell2Label = document.createElement( "label" );
	newTableElementCell2Label.setAttribute('for', whatElement.getAttribute('id') + "_child" );
	newTableElementCell2Label.innerHTML = whatElement.innerHTML;
	
	// Add the label to the column
	newTableElementCell2.appendChild( newTableElementCell2Label );
	
	// Add the column to the row
	newTableElementRow.appendChild( newTableElementCell2 );
	
	// Delete the old element
	whatElement.parentNode.removeChild( whatElement );
	
	// Create a new checkbox
	var newElement = document.createElement( "input" );
	newElement.setAttribute('name', family );
	newElement.setAttribute('type', 'checkbox' );
	newElement.setAttribute('id', whatElement.getAttribute('id') + "_child" );
	
	//The elements below are not required, as we already included them in the entire table
	/*newElement.setAttribute('onmouseup', newElementMouseUp );
	newElement.setAttribute('onmousedown', newElementMouseDown );
	newElement.setAttribute('onmouseover', newElementMouseOver );
	newElement.setAttribute('onmouseout', newElementMouseOut );
	newElement.setAttribute('onmousemove', newElementMouseMove );*/
	
	// Add the checkbox to the first column
	newTableElementCell1.appendChild( newElement );
	
	// Add the <table> to the card that it was on
	newElementParent.appendChild( newTableElement );
}


function hpop__getCheckable( whatElement )
{
	// We need to find out if the element is a button or not
	if ( hpop__getType(whatElement) == 'button')
	//If the element is a button
	{
		if ( hpop__getStyle(whatElement) == 'checkbox' )
		// If the element is a checkbox
		{
			return true;
		}
		else ( hpop__getStyle(whatElement) == 'roundrect' )
		// If the element is a round rect button
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function hpop__getSaveStyle( whatElement )
{
	if(hpop__getStyle( whatElement ) == "roundrect")
	{
		return "0";
	}
	else if(hpop__getStyle( whatElement ) == "checkbox")
	{
		return "1";
	}
	else if(hpop__getStyle( whatElement ) == "radiobutton")
	{
		return "2";
	}
}

function hpop__sort( sortType, sortChunk, sortValue, sortExpr )
{
	var currentElement, testElement1, testElement2;
	var numElements = cxl_count( sortValue, new Array( sortChunk ) );
	var doSwap = false;
	var didSwap = false;
	var i = 0;
	do
	{
		didSwap = doSwap = false;

		for ( i = 1; i < numElements; i++ )
		{
			doSwap = false;
			currentElement = cxl_get( sortValue, new Array( sortChunk, i ) );
			testElement1 = eval( sortExpr );
			currentElement = cxl_get( sortValue, new Array( sortChunk, i + 1 ) );
			testElement2 = eval( sortExpr );
			
			if ( sortType == 'ascending' )
			{
				if ( ( testElement1 + "" ) > ( testElement2 + "" ) )
				{
					didSwap = doSwap = true;
				}
			}
			else if ( sortType == 'descending' )
			{
				if ( ( testElement1 + "" ) < ( testElement2 + "" ) )
				{
					didSwap = doSwap = true;
				}
			}
			else if ( sortType == 'numeric' )
			{
				if ( ( testElement1 * 1 ) > ( testElement2 * 1 ) )
				{
					didSwap = doSwap = true;
				}
			}
			
			if ( doSwap )
			{
				sortValue = cxl_into( sortValue, new Array( sortChunk, i + 1 ), cxl_get( sortValue, new Array( sortChunk, i ) ) );
				sortValue = cxl_into( sortValue, new Array( sortChunk, i ), currentElement );
			}
			
		}
	
	} while ( didSwap );
	
	return sortValue;
}


var buttonEventStub = "function mouseup(){;} function mousedown(){;} function mouseenter(){;} function mouseleave(){;} function mousewithin(){;}\n";

//Backgrounds
// First create the array to hold the different backgrounds
var backgrounds = new Array();
// Then create an array in index 0 that will hold the object data for background 1 (index 0)
backgrounds[0] = new Array();

<?php
	// Echo out the stack script
	echo "var stack_" . $stack['id'] . "_script = \"" . compile( str_replace( "\r\n", "\n", $stack['script'] ), true ) . "\";\n";
	echo "var stack_" . $stack['id'] . "_scriptx = \"" . str_replace( "\r", "\\r", str_replace( "\n", "\\n", str_replace( "\"", "\\\"", $stack['script'] ) ) ) . "\";\n";

	//echo "var arithmetic_support = \"/* Helper Functions */\\n\\nfunction hpop__binaryAdd ( x, y )\\n{\\n\treturn ( x * 1 ) + ( y * 1 );\\n}\\n\\nfunction hpop__binarySubtract( x, y )\\n{\\n\treturn x - y;\\n}\\n\\nfunction hpop__binaryMultiply( x, y )\\n{\\n\treturn x * y;\\n}\\n\\nfunction hpop__binaryDivide( x, y )\\n{\\n\treturn x / y;\\n}\\n\\nfunction hpop__binaryExp( x, y )\\n{\\n\treturn pow( x, y );\\n}\\n\\nfunction hpop__binaryConcat( x, y )\\n{\\n\treturn x + \\\"\\\" + y;\\n}\\n\\nfunction hpop__binaryConcat2( x, y )\\n{\\n\treturn x + \\\" \\\" + y;\\n}\";\n\n";

	while( $card = mysql_fetch_array( $cards, MYSQL_ASSOC ) )
	{
		// Echo out the card scripts
		echo "var card_" . $card['stacks_id'] . "_" . $card['card_id'] . "_script = \"" . compile( str_replace( "\r\n", "\n", $card['script'] ), true ) . "\";\n";
		echo "var card_" . $card['stacks_id'] . "_" . $card['card_id'] . "_scriptx = \"" . str_replace( "\r", "\\r", str_replace( "\n", "\\n", str_replace( "\"", "\\\"", $card['script'] ) ) ) . "\";\n";

		$parts = mysql_query( "select * from parts where cards_id=" . $card['card_id'] . " AND stacks_id=" . $card['stacks_id'] . " order by part_order" );

		while( $part = mysql_fetch_array( $parts, MYSQL_ASSOC ) )
		{
			// Echo out the part scripts
			echo "var part_" . $part['stacks_id'] . "_" . $part['cards_id'] . "_" . $part['part_id'] . "_script = \"" . compile( str_replace( "\r\n", "\n", $part['script'] ), true ) . "\";\n";
			echo "var part_" . $part['stacks_id'] . "_" . $part['cards_id'] . "_" . $part['part_id'] . "_scriptx = \"" . str_replace( "\r", "\\r", str_replace( "\n", "\\n", str_replace( "\"", "\\\"", $part['script'] ) ) ) . "\";\n";
		}

	}

	mysql_data_seek( $cards, 0 );
?>
</script>
</head>
<body onmousemove="javascript:mouseMove(event);"; onkeydown="bodyKeyDown()" onKeyUp="bodyKeyUp()" onkeypress="if( focusElement == '' ){javascript:if( window.event ) { keynum = event.keyCode } else { keynum = event.which } if ( keynum == 8 ) { event.preventDefault();event.stopPropagation();return false; }}" onmousedown="bodyMouseDown()" onmouseup="bodyMouseUp()">
	<div id="header">
		<div id="loginbtn" style="float: right;" class="unselectable">LOGIN</div>
	</div>
	<!--<div id="content">-->
	
	<div>
		<br/><!-- Sound Support -->
	</div>
	
	<?php
		$maxID = 0;

		echo "<div id='stack_container_shadow'>\n";
		echo "<br/>";
		echo "</div>\n";
		
		$first_card = true;
		$cardListContents = "";
		
		echo "<div id='stack' class='stack' onmouseup='handleStackMouseUp( this, " . $stack['id'] . " )' onmousedown='stackClicked(); handleStackMouseDown( this, " . $stack['id'] . " )' onmouseover='handleStackMouseOver( this, " . $stack['id'] . " )' onmousemove='handleStackMouseMove( this, " . $stack['id'] . " )' onmouseout='handleStackMouseOut( this, " . $stack['id'] . " )' onkeypress='handleStackKeyDown( this, " . $stack['id'] . " )'>";
	
		while( $card = mysql_fetch_array( $cards, MYSQL_ASSOC ) )
		{
			$buttonListContents = "";
			$fieldListContents = "";
			$blockListContents = "";
			$buttonListCounter = 0;
			$fieldListCounter = 0;
			$blockListCounter = 0;
			$buttonZListContents = "";
			$fieldZListContents = "";
			$blockZListContents = "";
			$zindexcounter = 15;

			echo "<div id='card_" . $card['card_id'] . "' name='card_" . $card['name'] . "' class='stack_container'" . ($first_card ? " style='display:block;" : " style='display:none;" ) . ( ( $card['image'] != "" ) ? "background-image: url(\"" . $card['image'] . "\");" : "" ) . "' onmouseup='handleCardMouseUp( this, " . $card['stacks_id'] . ", " . $card['card_id']  . ")' onkeypress='handleCardKeyDown( this, " . $card['stacks_id'] . ", " . $card['card_id']  . ")'>\n";
			$cardListContents .= "cardList[" . $cardListCounter++ . "] = \"" . $card['card_id'] . "\";\n";

			$first_card = false;
			
			$parts = mysql_query( "SELECT * FROM parts WHERE cards_id=" . $card['card_id'] . " AND stacks_id=" . $card['stacks_id'] );
			
			while( $part = mysql_fetch_array( $parts, MYSQL_ASSOC ) )
			{
				$maxID++;
				$enabled = $part['enabled'];
				$visible = $part['visible'];

				if( $enabled == 1 )
				{
					$disabled = "";
				}
				else
				{
					$disabled = "disabled";
				}
				
				if( $visible == 1 )
				{
					$visibility = "visible";
				}
				else
				{
					$visibility = "hidden";
				}
				
				if( $part['locktext'] == 1 )
				{
					$readonly = "readonly";
				}
				else
				{
					$readonly = "";
				}
				
				switch ( $part['type'] )
				{
					case 0:
						// Push Button
						// We now need to see what style the button is, so we can load it correctly
						
						$buttonLabel = "";
						if( $part['showname'] == 1 )
						{
							$buttonLabel = $part['name'];
						}
						
						$buttonListContents .= "card_". $card['card_id'] . "_buttonList[" . $buttonListCounter++ . "] = \"" . $part['part_id'] . "\";\n";
						$buttonZListContents .= "card_". $card['card_id'] . "_buttonZList[" . ($buttonListCounter-1) . "] = \"" . ( $zindexcounter - 1 ) . "\";\n";
						
						if ($part['style'] == 9)// If the button is a check box
						{
							// Check to see what the hilite is
							if( $part['hilite'] == 1 )
							{
								$checked = "checked";
							}
							else
							{
								$checked = "";
							}
							echo "<table style=\"visibility: " . $visibility . "; position: absolute; top: " . $part['top'] . "px; left: " . $part['left'] . "px; width: " . $part['width'] . "px; height: " . $part['height'] . "px;z-index: " . $zindexcounter++ . ";\" id='obj" . $part['part_id'] . "' border=0 name='btn_" . $part['stacks_id'] . "_" . $part['cards_id'] . "_" . $part['name'] . "' zindex='undefined' onmouseup=\"javascript:handleBtnMouseUp( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\" onmousedown=\"javascript:handleBtnMouseDown( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\" onmouseover=\"javascript:handleBtnMouseOver( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\" onmouseout=\"javascript:handleBtnMouseOut( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\"  onmousemove=\"javascript:handleBtnMouseMove( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\" ondblclick=\"javascript:if( userLevel == 1 ){ openButtonEditor( this, cardID ); }\"><tr><td valign='middle' width=1><input type='checkbox' id=\"obj" . $part['part_id'] . "_child\" " . $disabled . " " . $checked . "></td><td valign=\"middle\" style=\"font-size: 13px;\"><label for=\"obj" . $part['part_id'] . "_child\">" . $buttonLabel . "</label></td></tr></table>";
						}
						else if ($part['style'] == 10)// If the button is a radio button
						{
							// Check to see what the hilite is
							if( $part['hilite'] == 1 )
							{
								$checked = "checked";
							}
							else
							{
								$checked = "";
							}
							echo "<table style=\"visibility: " . $visibility . "; position: absolute; top: " . $part['top'] . "px; left: " . $part['left'] . "px; width: " . $part['width'] . "px; height: " . $part['height'] . "px;z-index: " . $zindexcounter++ . ";\" id='obj" . $part['part_id'] . "' border=0 name='btn_" . $part['stacks_id'] . "_" . $part['cards_id'] . "_" . $part['name'] . "' zindex='undefined' onmouseup=\"javascript:handleBtnMouseUp( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\" onmousedown=\"javascript:handleBtnMouseDown( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\" onmouseover=\"javascript:handleBtnMouseOver( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\" onmouseout=\"javascript:handleBtnMouseOut( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\"  onmousemove=\"javascript:handleBtnMouseMove( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\" ondblclick=\"javascript:if( userLevel == 1 ){ openButtonEditor( this, cardID ); }\"><tr><td valign='middle' width=1><input type='radio' id=\"obj" . $part['part_id'] . "_child\" " . $disabled . " " . $checked . " name=\"" . $part['family'] . "\"></td><td valign=\"middle\" style=\"font-size: 13px;\"><label for=\"obj" . $part['part_id'] . "_child\">" . $buttonLabel . "</label></td></tr></table>";
						}
						else if ($part['style'] == 4)// If the button is a popup
						{
							// this still needs work - popup buttons are going to be tricky
						}
						else // If the button is a "normal" button, just with a different class, then the code is almost the same
						{
							// First we need to convert the numbers from $part['style'] into the names of the classes we are using
							switch ( $part['style'] )
							{
								case 0:
									$className = "transparent";
									break;
								case 1:
									$className = "opaque";
									break;
								case 2:
									$className = "rectangle";
									break;
								case 3:
									$className = "shadow";
									break;
								case 5:
									$className = "roundrect";
									break;
								case 6:
									$className = "oval";
									break;
								case 7:
									$className = "standard";
									break;
								case 9:
									$className = "default";
									break;
							}
							
							// Now we need to work out the classes from the hilite and the autohilite properties
							if( $part['hilite'] == 1 && $part['autohilite'] == 0 )
							{
								$className = $className . "HiliteNoAuto";
							}
							else if( $part['hilite'] == 1 )
							{
								$className = $className . "Hilite";
							}
							else if( $part['autohilite'] == 0 )
							{
								$className = $className . "NoAuto";
							}
							
							// Now that we have the class names for the buttons, we can actually create the button
							echo "<button " . $disabled . " class='" . $className . "' name='btn_" . $part['stacks_id'] . "_" . $part['cards_id'] . "_" . $part['name'] . "' id='obj" . $part['part_id'] . "' style='visibility: " . $visibility . "; position: absolute; top: " . $part['top'] . "px; left: " . $part['left'] . "px; width: " . $part['width'] . "px; height: " . $part['height'] . "px;z-index: " . $zindexcounter++ . ";' onmouseup=\"javascript:handleBtnMouseUp( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\" onmousedown=\"javascript:handleBtnMouseDown( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\" onmouseover=\"javascript:handleBtnMouseOver( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\" onmouseout=\"javascript:handleBtnMouseOut( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\"  onmousemove=\"javascript:handleBtnMouseMove( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\" ondblclick=\"if( userLevel == 1 ){ openButtonEditor( this, cardID ); }\" />" . $buttonLabel . "</button>\n\n";
						}
						
						// Get the information for the button arrays
						
						$buttonIDList .= "buttonIDList[buttonIDList.length] = 'obj" . $part['part_id'] . "';\n";
						$buttonEnabledList .= "buttonEnabledList[buttonEnabledList.length] = " . $enabled . ";\n";
						$buttonVisibleList .= "buttonVisibleList[buttonVisibleList.length] = " . $visible . ";\n";
						$buttonStyleList .= "buttonStyleList[buttonStyleList.length] = " . $part['style'] . ";\n";
						$buttonFamilyList .= "buttonFamilyList[buttonFamilyList.length] = " . $part['family'] . ";\n";
						$buttonHiliteList .= "buttonHiliteList[buttonHiliteList.length] = " . $part['hilite'] . ";\n";
						$buttonAutoHiliteList .= "buttonAutoHiliteList[buttonAutoHiliteList.length] = " . $part['autohilite'] . ";\n";
						
						break;
					
					case 1:
						// Multiline Field
						
						// We need to convert the style numbers into the correct class for the field
						switch( $part['style'] )
						{
							case 0:
								$className = "fld_transparent";
								break;
							case 1:
								$className = "fld_opaque";
								break;
							case 2:
								$className = "fld_rectangle";
								break;
							case 3:
								$className = "fld_shadow";
								break;
							case 4:
								$className = "fld_scrolling";
								break;
						}
						// Now we need to work out the classes from the hilite and the autohilite properties
						if( $part['dontwrap'] == 1 )
						{
							$wrap = "off";
						}
						else
						{
							$wrap = "on";
						}
						
						if( $part['multiplelines'] == 1 )
						{
							$multiple = "multiple";
						}
						else
						{
							$multiple = "";
						}
						
						if( $part['autoselect'] == 0 )
						{
							echo "<textarea " . $readonly . " wrap='" . $wrap . "' id='obj" . $part['part_id'] . "' class='" . $className . "' name='fld_" .  $part['stacks_id'] . "_" . $part['cards_id'] . "_" . $part['name'] . "' style='visibility: " . $visibility . "; position: absolute; top: " . $part['top'] . "px; left: " . $part['left'] . "px; width: " . $part['width'] . "px; height: " . $part['height'] . "px; font-family: Arial, sans-serif; font-size: 12px;z-index: " . $zindexcounter++ . ";' onmouseup=\"javascript:handleFldMouseUp( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\" onmousedown=\"javascript:handleFldMouseDown( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\" onmouseover=\"javascript:handleFldMouseOver( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\" onmouseout=\"javascript:handleFldMouseOut( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\"  onmousemove=\"javascript:handleFldMouseMove( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\" onfocus=\"if( userLevel == 0 ){focusElement=this};\" ondblclick=\"if( userLevel == 2 ){ openFieldEditor( this, cardID ); }\" onblur=\"if( userLevel == 0){focusElement='';}\" onkeypress=\"javascript:handleFldKeyDown( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\">" . $part['value'] . "</textarea>";
						}
						else
						{
							echo "<select " . $multiple . " size='3000' " . $readonly . " wrap='" . $wrap . "' id='obj" . $part['part_id'] . "' class='" . $className . "' name='fld_" .  $part['stacks_id'] . "_" . $part['cards_id'] . "_" . $part['name'] . "' style='visibility: " . $visibility . "; position: absolute; top: " . $part['top'] . "px; left: " . $part['left'] . "px; width: " . $part['width'] . "px; height: " . $part['height'] . "px; font-family: Arial, sans-serif; font-size: 12px;z-index: " . $zindexcounter++ . ";' onmouseup=\"javascript:handleFldMouseUp( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\" onmousedown=\"javascript:handleFldMouseDown( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\" onmouseover=\"javascript:handleFldMouseOver( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\" onmouseout=\"javascript:handleFldMouseOut( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\"  onmousemove=\"javascript:handleFldMouseMove( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\" onfocus=\"if( userLevel == 0 ){focusElement=this};\" ondblclick=\"if( userLevel == 2 ){ openFieldEditor( this, cardID ); }\" onblur=\"if( userLevel == 0){focusElement='';}\" onkeypress=\"javascript:handleFldKeyDown( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\">";
							$selectOptions = explode("\n", $part['value']);
							for( $i = 0; $i < count($selectOptions); $i++ )
							{
								echo "<option>" . $selectOptions[$i] . "</option>";
							}
							echo "</select>";
						}
						$fieldListContents .= "card_". $card['card_id'] . "_fieldList[" . $fieldListCounter++ . "] = \"" . $part['part_id'] . "\";\n";
						$fieldZListContents .= "card_" . $card['card_id'] . "_fieldZList[" . ($fieldListCounter-1) . "] = \"" . ( $zindexcounter - 1 ) . "\";\n";
						
						$fieldIDList .= "fieldIDList[fieldIDList.length] = 'obj" . $part['part_id'] . "';\n";
						$fieldEnabledList .= "fieldEnabledList[fieldEnabledList.length] = " . $enabled . ";\n";
						$fieldVisibleList .= "fieldVisibleList[fieldVisibleList.length] = " . $visible . ";\n";
						$fieldStyleList .= "fieldStyleList[fieldStyleList.length] = " . $part['style'] . ";\n";
						$fieldLockTextList .= "fieldLockTextList[fieldLockTextList.length] = " . $part['locktext'] . ";\n";
						
						break;
						
					case 2:
						// Block
						echo "<div id='obj" . $part['part_id'] . "' name='blk_" . $part['stacks_id'] . "_" . $part['cards_id'] . "_" . $part['name'] . "' style='visibility: " . $visibility . "; 	position: absolute; top: " . $part['top'] . "px; left: " . $part['left'] . "px; width: " . $part['width'] . "px; height: " . $part['height'] . "px; font-family: Arial, sans-serif; font-size: 12px;z-index: " . $zindexcounter++ . ";' onmouseup=\"javascript:handleBlkMouseUp( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\" onmousedown=\"javascript:handleBlkMouseDown( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\" onmouseover=\"javascript:handleFldMouseOver( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\" onmouseout=\"javascript:handleBlkMouseOut( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\"  onmousemove=\"javascript:handleBlkMouseMove( this, " . $part['stacks_id'] . ", " . $part['cards_id'] . ", " . $part['part_id'] . " );\" onfocus=\"if( userLevel == 0 ){focusElement=this};\"  ondblclick=\"if( userLevel == 3 ){ openBlockEditor( this, cardID ); }\" onblur=\"if( userLevel == 3 ){focusElement='';}\" >" . $part['value'] . "</div>";
						$blockListContents .= "card_". $card['card_id'] . "_blockList[" . $blockListCounter++ . "] = \"" . $part['part_id'] . "\";\n";
						$blockZListContents .= "card_" . $card['card_id'] . "_blockZList[" . ($blockListCounter-1) . "] = \"" . ( $zindexcounter - 1 ) . "\";\n";
						
						$blockIDList .= "blockIDList[blockIDList.length] = 'obj" . $part['part_id'] . "';\n";
						$blockEnabledList .= "blockEnabledList[blockEnabledList.length] = " . $enabled . ";\n";
						$blockVisibleList .= "blockVisibleList[blockVisibleList.length] = " . $visible . ";\n";
						$blockStyleList .= "blockStyleList[blockStyleList.length] = " . $part['style'] . ";\n";
						$blockContentsList .= "blockContentsList[blockContentsList.length] = '" . mysql_real_escape_string( $part['value'] ) . "';\n";
						$blockDisableScript .= "hpop__setEnabled( document.getElementById( 'obj" . $part['part_id'] . "' ), " . $enabled . " );\n";
						
						break;
				}
			}
			
			echo "<script type='text/javascript'>\n";
			echo "card_". $card['card_id'] . "_buttonList = new Array();\n";
			echo $buttonListContents;
			echo "card_". $card['card_id'] . "_fieldList = new Array();\n";
			echo $fieldListContents;
			echo "card_". $card['card_id'] . "_blockList = new Array();\n";
			echo $blockListContents;
			echo "card_". $card['card_id'] . "_buttonZList = new Array();\n";
			echo $buttonZListContents;
			echo "card_". $card['card_id'] . "_fieldZList = new Array();\n";
			echo $fieldZListContents;
			echo "card_". $card['card_id'] . "_blockZList = new Array();\n";
			echo $blockZListContents;
			echo "</script>\n";
			echo "</div>\n";
		}
		echo "</div>\n";
			
		echo "<script type='text/javascript'>\n";
		echo "topID = " . $maxID . ";\n";
		echo "cardList = new Array();\n";
		echo $cardListContents;
		
		echo "cardID = cardList[0];\n";
		echo "</script>\n";
		
		echo "<script type='text/javascript'>";
		echo "\nbuttonIDList = new Array();\n";
		echo $buttonIDList;
		echo "\nbuttonEnabledList = new Array();\n";
		echo $buttonEnabledList;
		echo "\nbuttonVisibleList = new Array();\n";
		echo $buttonVisibleList;
		echo "\nbuttonStyleList = new Array();\n";
		echo $buttonStyleList;
		echo "\nbuttonFamilyList = new Array();\n";
		echo $buttonFamilyList;
		echo "\nbuttonHiliteList = new Array();\n";
		echo $buttonHiliteList;
		echo "\nbuttonAutoHiliteList = new Array();\n";
		echo $buttonAutoHiliteList;
		echo "</script>";
		
		echo "<script type='text/javascript'>";
		echo "\nfieldIDList = new Array();\n";
		echo $fieldIDList;
		echo "\nfieldEnabledList = new Array();\n";
		echo $fieldEnabledList;
		echo "\nfieldVisibleList = new Array();\n";
		echo $fieldVisibleList;
		echo "\nfieldStyleList = new Array();\n";
		echo $fieldStyleList;
		echo "\nfieldLockTextList = new Array();\n";
		echo $fieldLockTextList;
		echo "</script>";
		
		echo "<script type='text/javascript'>";
		echo "\nblockIDList = new Array();\n";
		echo $blockIDList;
		echo "\nblockEnabledList = new Array();\n";
		echo $blockEnabledList;
		echo "\nblockVisibleList = new Array();\n";
		echo $blockVisibleList;
		echo "\nblockStyleList = new Array();\n";
		echo $blockStyleList;
		echo "\nblockContentsList = new Array();\n";
		echo $blockContentsList;
		echo $blockDisableScript;
		echo "</script>";
	?>

<!-- Script Editor -->
<div style="display:none;">
	<div class="script_editor" id="scripteditor">
		<table width="100%" height="100%" cellspacing="5" border="0">
			<tr>
				<td height="25">
					<span style="display:block;padding: 10px; font-family: 'Trebuchet MS', Verdana, Arial, sans-serif; font-size: 18px; font-weight: bold; color: #009;" id="scriptHeader">Editing Script for Object ID 9 ("Name")</span>
					<input type="hidden" id="sourceObjectID" />
					<input type="hidden" id="sourceCardID" />
				</td>
			</tr>
			<tr>
				<td height="100%">
					<textarea style="width:100%;height:100%;" id="scriptField" onfocus="focusElement=this;" onblur="focusElement='';"></textarea>
				</td>
			</tr>
		</table>
	</div>
</div>

<!-- Card Script Editor -->
<div style="display:none;">
	<div class="script_editor" id="cardscripteditor">
		<table width="100%" height="100%" cellspacing="5" border="0">
			<tr>
				<td height="25">
					<span style="display:block;padding: 10px; font-family: 'Trebuchet MS', Verdana, Arial, sans-serif; font-size: 18px; font-weight: bold; color: #009;" id="cardScriptHeader">Editing Script for Card ID 9 ("Name")</span>
					<input type="hidden" id="cardSourceCardID" />
				</td>
			</tr>
			<tr>
				<td height="100%">
					<textarea style="width:100%;height:100%;" id="cardScriptField" onfocus="focusElement=this;" onblur="focusElement='';"></textarea>
				</td>
			</tr>
		</table>
	</div>
</div>

<!-- Contents Editor -->
<div style="display:none;">
	<div class="script_editor" id="contentseditor">
		<table width="100%" height="100%" cellspacing="5" border="0">
			<tr>
				<td height="25">
					<span style="display:block;padding: 10px; font-family: 'Trebuchet MS', Verdana, Arial, sans-serif; font-size: 18px; font-weight: bold; color: #009;" id="contentsHeader">Editing Contents for Object ID 9 ("Name")</span>
					<input type="hidden" id="contentsObjectID" />
					<input type="hidden" id="contentsCardID" />
				</td>	
			</tr>
			<tr>
				<td height="100%">
					<textarea style="width:100%;height:100%;" id="contentsField" onfocus="focusElement=this;" onblur="focusElement='';"></textarea>
				</td>
			</tr>
		</table>
	</div>
</div>

<!-- Button Editor -->
<div style="display:none;">
	<div class="button_editor" id="buttoneditor">
		<input type="hidden" id="BEsourceObjectID" />
		<input type="hidden" id="BEsourceCardID" />
		<table width="100%" height="100%" cellspacing="5" border="0">
			<tr>
				<td>Name:</td>
				<td><input type="text" id="button_editor_name" style="width:100%;" onkeydown="if(window.event){keynum=event.keyCode;}else{keynum=event.which}if(keynum==13||keynum==10){eval(inlinecompiler(document.getElementById('msgboxcontent').value));return false;}" onfocus="focusElement=this;" onblur="focusElement='';" /></td>
			</tr>
			<tr>
				<td colspan="2"></td>
			</tr>
			<tr>
				<td valign="top">
					<table width="100%" cellspacing="0" cellpadding="2" border="0">
						<tr>
							<td width="50">ID:</td>
							<td id='button_editor_id'>0000</td>
						</tr>
						<tr>
							<td width="50">Order:</td>
							<td id='button_editor_order'>4</td>
						</tr>
					</table>
				</td>
				<td valign="top">
					<table>
						<tr>
							<td width="95px"><input type="checkbox" id="button_editor_visible" />Visible</td>
							<td width="95px"><input type="checkbox" id="button_editor_hilite" />Hilite</td>
							<td width="108px">
								<select id="button_editor_style">
									<option value="transparent">Transparent</option>
									<option value="opaque">Opaque</option>
									<option value="rectangle">Rectangle</option>
									<option value="shadow">Shadow</option>
									<option value="popup">Popup</option>
									<option value="roundrect">RoundRect</option>
									<option value="oval">Oval</option>
									<option value="standard">Standard</option>
									<option value="default">Default</option>
									<option value="checkbox">Checkbox</option>
									<option value="radiobutton">Radiobutton</option>
								</select>
							</td>
						</tr>
						<tr>
							<td width="95px"><input type="checkbox" id="button_editor_enabled" />Enabled</td>
							<td width="95px"><input type="checkbox" id="button_editor_autohilite" />Auto Hilite</td>
							<td width="108px"><input type="checkbox" id="button_editor_showname" />Show Name</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2"><hr/></td>
			</tr>
			<tr>
				<td><input type="button" value="Script..." style="width:75px;" onclick="javascript:openScriptEditor(document.getElementById(document.getElementById('BEsourceObjectID').value), document.getElementById('BEsourceCardID').value );" /></td>
				<td align="right"><input type="button" value="Cancel" style="width:75px;" onclick="dontAsk=true;Windows.closeAll();" /> <input type="button" value="OK" style="width:75px;" onclick="dontAsk=true;commitButtonEditor();Windows.closeAll();" /></td>
			</tr>
		</table>
	</div>
</div>

<!-- Field Editor -->
<div style="display:none;">
	<div class="button_editor" id="fieldeditor">
		<input type="hidden" id="FieldBEsourceObjectID" />
		<input type="hidden" id="FieldBEsourceCardID" />
		<input type="hidden" id="FieldFullName" />
		<table width="100%" height="100%" cellspacing="5" border="0">
			<tr>
				<td>Name:</td>
				<td><input type="text" id="field_editor_name" style="width:100%;" onkeydown="if(window.event){keynum=event.keyCode;}else{keynum=event.which}if(keynum==13||keynum==10){eval(inlinecompiler(document.getElementById('msgboxcontent').value));return false;}" onfocus="focusElement=this;" onblur="focusElement='';" /></td>
			</tr>
			<tr>
				<td colspan="2"></td>
			</tr>
			<tr>
				<td valign="top">
					<table width="100%" cellspacing="0" cellpadding="2" border="0">
						<tr>
							<td width="50">ID:</td>
							<td id='field_editor_id'>0000</td>
						</tr>
						<tr>
							<td width="50">Order:</td>
							<td id='field_editor_order'>4</td>
						</tr>
					</table>
				</td>
				<td valign="top">
					<table>
						<tr>
							<td width="95px"><input type="checkbox" id="field_editor_visible" />Visible</td>
							<td width="95px"><input type="checkbox" id="field_editor_locktext" />LockText</td>
							<td width="100px"><input type="checkbox" id="field_editor_autoselect" />Auto Select</td>
						</tr>
						<tr>
							<td width="95px"><input type="checkbox" id="field_editor_enabled" />Enabled</td>
							<td width="95px">
								<select id="field_editor_style">
									<option value="transparent">Transparent</option>
									<option value="opaque">Opaque</option>
									<option value="rectangle">Rectangle</option>
									<option value="shadow">Shadow</option>
									<option value="scrolling">Scrolling</option>
								</select>
							</td>
							<td width="100px"><input type="checkbox" id="field_editor_dontwrap" />Don't Wrap</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2"><hr/></td>
			</tr>
			<tr>
				<td><input type="button" value="Script..." style="width:75px;" onclick="javascript:openScriptEditor(document.getElementById(document.getElementById('FieldBEsourceObjectID').value), document.getElementById('FieldBEsourceCardID').value );" /></td>
				<td align="right"><input type="button" value="Cancel" style="width:75px;" onclick="dontAsk=true;Windows.closeAll();" /> <input type="button" value="OK" style="width:75px;" onclick="dontAsk=true;commitFieldEditor(document.getElementById('FieldFullName').value);Windows.closeAll();" /></td>
			</tr>
		</table>
	</div>
</div>

<!-- Block Editor -->
<div style="display:none;">
	<div class="button_editor" id="blockeditor">
		<input type="hidden" id="BlockBEsourceObjectID" />
		<input type="hidden" id="BlockBEsourceCardID" />
		<input type="hidden" id="BlockFullName" />
		<table width="100%" height="100%" cellspacing="5" border="0">
			<tr>
				<td>Name:</td>
				<td><input type="text" id="block_editor_name" style="width:100%;" onkeydown="if(window.event){keynum=event.keyCode;}else{keynum=event.which}if(keynum==13||keynum==10){eval(inlinecompiler(document.getElementById('msgboxcontent').value));return false;}" onfocus="focusElement=this;" onblur="focusElement='';" /></td>
			</tr>
			<tr>
				<td colspan="2"></td>
			</tr>
			<tr>
				<td valign="top">
					<table width="100%" cellspacing="0" cellpadding="2" border="0">
						<tr>
							<td width="50">ID:</td>
							<td id='block_editor_id'>0000</td>
						</tr>
						<tr>
							<td width="50">Order:</td>
							<td id='block_editor_order'>4</td>
						</tr>
					</table>
				</td>
				<td valign="top">
					<input type="checkbox" id="block_editor_visible" /> Visible
					<br/>
					<input type="checkbox" id="block_editor_enabled" /> Enabled
				</td>
			</tr>
			<tr>
				<td colspan="2"><hr/></td>
			</tr>
			<tr>
				<td>
					<input type="button" value="Script..." style="width:75px;" onclick="javascript:openScriptEditor(document.getElementById(document.getElementById('BlockBEsourceObjectID').value), document.getElementById('BlockBEsourceCardID').value );" />
					<input type="button" value="Contents..." style="width:75px;" onclick="javascript:openContentsEditor(document.getElementById(document.getElementById('BlockBEsourceObjectID').value), document.getElementById('BlockBEsourceCardID').value );" />
				</td>
				<td align="right"><input type="button" value="Cancel" style="width:75px;" onclick="dontAsk=true;Windows.closeAll();" /> <input type="button" value="OK" style="width:75px;" onclick="dontAsk=true;commitBlockEditor(document.getElementById('BlockFullName').value);Windows.closeAll();" /></td>
			</tr>
		</table>
	</div>
</div>

<!-- Card Info -->
<div style="display:none;">
	<div class="button_editor" id="cardeditor">
		<input type="hidden" id="CardBEsourceObjectID" />
		<input type="hidden" id="CardBEsourceCardID" />
		<input type="hidden" id="CardFullName" />
		<table width="100%" height="100%" cellspacing="5" border="0">
			<tr>
				<td>Name:</td>
				<td><input type="text" id="card_editor_name" style="width:100%;" onkeydown="if(window.event){keynum=event.keyCode;}else{keynum=event.which}if(keynum==13||keynum==10){eval(inlinecompiler(document.getElementById('msgboxcontent').value));return false;}" onfocus="focusElement=this;" onblur="focusElement='';" /></td>
			</tr>
			<tr>
				<td colspan="2"></td>
			</tr>
			<tr>
				<td valign="middle">
					<table width="100%" cellspacing="0" cellpadding="2" border="0">
						<tr>
							<td width="50">ID:</td>
							<td id='card_editor_id'>0000</td>
						</tr>
						<tr>
							<td width="50">Order:</td>
							<td id='card_editor_order'>4</td>
						</tr>
					</table>
				</td>
				<td valign="top">
					<table>
						<tr>
							<td valign="middle" style="width:40px;">Card Image:</td>
							<td>
								<select id="card_editor_images" size="4" style="width:240px"></select>
							</td>
							<td valign="bottom"><form method="post" enctype="multipart/form-data"><input id="imageUploader" onchange="uploadImage();" type="file" name="imageUploader" style="width:90px; margin-bottom:-16px;" /></form></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2"><hr/></td>
			</tr>
			<tr>
				<td><input type="button" value="Script..." style="width:75px;" onclick="javascript:openCardScriptEditor('card_' + document.getElementById('card_editor_id').innerHTML );" /></td>
				<td align="right"><input type="button" value="Cancel" style="width:75px;" onclick="dontAsk=true;Windows.closeAll();" /> <input type="button" value="OK" style="width:75px;" onclick="dontAsk=true;commitCardEditor(document.getElementById('FieldFullName').value);Windows.closeAll();" /></td>
			</tr>
		</table>
	</div>
</div>

<!-- New Stack -->
<div style="display:none;">
	<div class="button_editor" id="newstack">
		<table width="100%" height="100%" cellspacing="5" border="0">
			<tr>
				<td>Name:</td>
				<td><input type="text" id="create_stack_name" style="width:100%;" onblur="focusElement='';" /></td>
			</tr>
			<tr>
				<td>Size:</td>
				<td>
					<select id="ddlViewBy">
						<option value="small">Small (320 x 240)</option>
						<option value="medium">Medium (640 x 480)</option>
						<option value="large">Large (960 x 720)</option>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top">
			</td>
			</tr>
			<tr>
				<td colspan="2"><hr/></td>
			</tr>
			<tr>
				<td></td>
				<td align="right"><input type="button" value="Cancel" style="width:75px;" onclick="dontAsk=true;Windows.closeAll();" /> <input type="button" value="OK" style="width:75px;" onclick="dontAsk=true;Windows.closeAll();createStack();" /></td>
		</table>
	</div>
</div>

<!-- Share Stack -->
<div style="display:none;">
	<div class="button_editor" id="sharestack">
		<table width="100%" height="100%" cellspacing="3" border="0">
			<tr>
				<td>Name:</td>
				<td><input type="text" id="sharestack_name" style="width:100%;" value="<?php echo $stack['name']; ?>" onblur="focusElement='';" /></td>
			</tr>
			<tr>
				<td>Public Description:</td>
				<td><textarea id="sharestack_description" rows="4" cols="55"><?php echo $stack['public_description']; ?></textarea></td>
			</tr>
			<tr>
				<td valign="top"></td>
			</tr>
			<tr>
				<td colspan="2"><hr/></td>
			</tr>
			<tr>
				<td></td>
				<td align="right"><input type="button" value="Cancel" style="width:75px;" onclick="dontAsk=true;Windows.closeAll();" /> <input type="button" value="OK" style="width:75px;" onclick="dontAsk=true;Windows.closeAll();shareStackSend();" /></td>
			</tr>
		</table>
	</div>
</div>

<!-- Open Stack -->
<div style="display:none;">
	<div class="button_editor" id="openstack">
		<table width="100%" height="100%" cellspacing="3" border="0">
			<tr>
				<td>Stack:</td>
				<td><select id="openstack_name" name="openstack_name" size=4 style="width: 400px"><?php if ($getusersid != "1"){ echo '<option value="1">Introduction</option>';}
					$sql="SELECT * FROM stacks where users_id = '" . $getusersid . "' ORDER BY id";
					$result=mysql_query($sql);
					while($rows=mysql_fetch_array($result))
					{
						?>
						<option value="<? echo $rows['id']; ?>"><? echo $rows['name']; ?></option>
						<?php
					}
					?>
				</select></td>
			</tr>
			<tr>
				<td valign="top"></td>
			</tr>
			<tr>
				<td colspan="2"><hr/></td>
			</tr>
			<tr>
				<td></td>
				<td align="right"><input type="button" value="Cancel" style="width:75px;" onclick="dontAsk=true;Windows.closeAll();" /> <input type="button" value="OK" style="width:75px;" onclick="dontAsk=true;Windows.closeAll();openSelectedStack();" /></td>
		</table>
	</div>
</div>

<!-- Messagebox/Card Manager -->
<div id="msgbox" class="unselectable">
	<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td width="14"><img src="images/messagebox_01.gif" width="14" height="25"></td>
			<td width="100%" style="background-image:url('images/messagebox_02.gif');"><img src="images/messagebox_07.png" width="203" height="25"></td>
			<td width="19"><img src="images/messagebox_03.gif" width="19" height="25"></td>
		</tr>
		<tr>
			<td width="14"><img src="images/messagebox_04.gif" width="14" height="62"></td>
			<td id="msgboximage" width="100%" style="background-image:url('images/messagebox_05.gif');"><br/></td>
			<td width="19"><img src="images/messagebox_06.gif" width="19" height="62"></td>
		</tr>
	</table>
	<input id="msgboxcontent" type="text" style="position: absolute; top: 38px; left: 12px; width: <?php if( ($stack['width'] + 5) < 500 ) { echo 500-22; } else { echo ($stack['width'] - 22); } ?>px; height:20px; border: none;" onkeydown="if(window.event){keynum=event.keyCode;}else{keynum=event.which}if(keynum==13||keynum==10){eval(inlinecompiler(document.getElementById('msgboxcontent').value));document.getElementById('msgboxcontent').select();return false;}else if( keynum == 244 || keynum == 17 || keynum == 91 || keynum == 93 ){cmdDown = true;}" onfocus="focusElement=this;" onblur="focusElement='';" onkeyup="if(window.event){keynum=event.keyCode;}else{keynum=event.which} if( keynum == 244 || keynum == 17 || keynum == 91 || keynum == 93 ){cmdDown = false;}" />
	<div class="cardmanagercontent" id="cardmanagercontent">
		<div class="card-manager-editing">
			<div class="add-card" onclick="createCard('Unitiled Card ' + (cardList.length + 1));">
				<div class="plus-icon"><b><strong>+</strong></b></div>
			</div>
			<div class="remove-card" onclick="deleteCardID(getCurrentCardID());">
				<div class="remove-icon"><b><strong>-</strong></b></div>
			</div>
		</div>
		<div class="card-properties" onclick="openCardEditor(document.getElementById('card_' + getCurrentCardID()));">
			<div class="remove-icon"><b><strong>-</strong></b></div>
		</div>
		<div id="card-manager-info" class="card-manager-info">
			<b>Card Name</b><br />Card <b>x</b> of <b>y</b>
		</div>
		<div class="card-manager-navigation">
			<div class="card-slideshow" onclick="changeNextCard();">
				<div class="next-triangle"></div>
			</div>
			<div class="card-slideshow" onclick="changePrevCard();">
				<div class="prev-triangle"></div>
			</div>
		</div>
		<div class="stack-properties" onclick="openStackEditor();">
			<div class="remove-icon"><b><strong>-</strong></b></div>
		</div>
	</div>
</div>

<!-- MsgBox/CardManager Buttons -->
<div class="msgbox_selector" onclick="switchToMsgBox();"></div>
<div class="card_manager_selector" onclick="switchToCardManager();"></div>

<!-- Tools Menu -->
<div id="tools" class="unselectable">
	<div class="toolcell_selected" id="toolcell0" style="position: absolute; padding: 0px; top: 28px; left: 4px; width: 79px; height: 43px;" onclick="javascript:changeeditmode( 0 );"><br/></div>
	<?php
		if ($getusersid == $stack['users_id'])
		{
			echo '
				<div class="toolcell" id="toolcell1" style="position: absolute; padding: 0px; top: 76px; left: 4px; width: 79px; height: 43px;" onclick="javascript:changeeditmode( 1 );" ondblclick="javascript:makePart( 0, prompt( \'Create new button named...\') );"><br/></div>
				<div class="toolcell" id="toolcell2" style="position: absolute; padding: 0px; top: 124px; left: 4px; width: 79px; height: 43px;" onclick="javascript:changeeditmode( 2 );" ondblclick="javascript:makePart( 1, prompt( \'Create new field named...\') );"><br/></div>
				<div class="toolcell" id="toolcell3" style="position: absolute; padding: 0px; top: 172px; left: 4px; width: 79px; height: 43px;" onclick="javascript:changeeditmode( 3 );" ondblclick="javascript:makePart( 2, prompt( \'Create new block named...\') );"><br/></div>
			';
		}
	?>
</div>

<!-- Options Menu -->
<div id="options" class="unselectable">
	<div class="optioncell" id="optioncell0" style="position: absolute; padding: 0px; top: 28px; left: 4px; width: 79px; height: 43px;" onclick="newStack()"><br/></div>
	<div class="optioncell" id="optioncell1" style="position: absolute; padding: 0px; top: 76px; left: 4px; width: 79px; height: 43px;" onclick="openStack()"><br/></div>
	<?php
		if ($getusersid == $stack['users_id'])
		{
			echo '
				<div class="optioncell" id="optioncell2" style="position: absolute; padding: 0px; top: 124px; left: 4px; width: 79px; height: 43px;" onclick="doSave()"><br/></div>
				<div class="optioncell" id="optioncell3" style="position: absolute; padding: 0px; top: 172px; left: 4px; width: 79px; height: 43px;" onclick="confirmDeleteStack()"><br/></div>
				<div class="optioncell" id="optioncell4" style="position: absolute; padding: 0px; top: 220px; left: 4px; width: 79px; height: 43px;" onclick="shareStack()"><br/></div>
			';
		}
	?>
</div>

<!-- Save Indicator -->
<div id="saveindicator" style="position: absolute; top: 100%; left: 0px; margin-top: -50px; width: 150px; height: 50px;background-image: url( 'images/saving.gif' ); display:none;">
	<br/>
</div>

<script type="text/javascript">
	soundManagerInit();
</script>

<!-- Preload Images -->
<div id="preloadImages" class="preloadImages">
	<!-- This DIV is used to preload any images that are needed to be instantly ready in other parts of the page -->
</div>
<!--</div>-->
</body>
</html>