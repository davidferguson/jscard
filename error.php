<?php
$error = $_GET['error'];

//work out what the errors could be, and what code these errors are
$notyourstack = 1;
$notloggedin = 2;
$stackdoesnotexist = 3;
$nostackspecified = 4;
$unspecified = 5;
$servererror = 6;

if ($error == $notyourstack)
{
$errornote = "You cannot access this stack, because you do not have sufficient user permissions.";
$errordescription = "	If you're seeing this page, you were probably linked to a non-public stack that isn't yours. If you were
			trying to view a shared stack, tell the author that they need to make their stack <b>Public,</b>
			before you can view it.<br>If you are the author of the stack you were trying to view, then something
			has gone wrong. Try going back, then your stack again.<br>If this error persists, then please fill out the
			error report form <a href='contact.php?error=1'><b>here</b></a> so we know what happened, and try to fix it.";

}

if ($error == $notloggedin)
{
$errornote = "Sadly, you need to be logged in to view this page.";
$errordescription = "	If you're seeing this page, you are not logged in. If you think you were, then maybe your session has expired.
			All this means is that you need to <a href='index.html'><b>log in</b></a> again. If you do not have an account,
			then you can click <a href='signup.html'><b>here</b></a> to get one.<br>If you just want to view the Public Stacks, without
			an account, then simply click <a href='public.php'><b>here,</b></a> and you will be presented with a whole list of stacks
			that people have made, then decided to share with the world.";

}


if ($error == $stackdoesnotexist)
{
$errornote = "Oh No! That stack does not exist!";
$errordescription = "	If you're seeing this page, the stack you tried to access does not exist. This could have happened because the
			creator of this stack has decided to delete it, or the creator has deleted their account.<br>If you think that
			this was your stack, the make sure the URL (web address) you typed in was correct.";

}


if ($error == $nostackspecified)
{
$errornote = "Err, you didn't specify a stack.";
$errordescription = "	If you're seeing this page, then it look like you did not specify a stack. This means that you went to <b>stack.php</b>, without
			telling jsCard what stack you wanted to view! Because of this, we can't show you a stack.<br>If you want a place to go
			to learn about jsCard, click <a href='stack.php?stack=1'>here,</a> where you will be presented with the <b>Introduction</b> stack.";

}



if ($error == $unspecified)
{
$errornote = "That's an error.";
$errordescription = "	If you're seeing this page, then an error has occurred.<br>Sorry about that.";

}


if ($error == $servererror)
{


$status = $_SERVER['REDIRECT_STATUS']; 
$codes = array( 
        403 => array('403 Forbidden', 'The server has refused to fulfill your request.<br>Try going back, then trying again.'), 
        404 => array('404 Not Found', 'The page that you requested was not found.<br>This basically means that you typed in an invalid URL, the text that finds webpages.<br>Check the URL and try again.'), 
        405 => array('405 Method Not Allowed', 'The method specified in the Request-Line is not allowed for the specified resource.'), 
        408 => array('408 Request Timeout', 'Your browser failed to sent a request in the time allowed by the server.<br>Try going back, then trying again.'), 
        500 => array('500 Internal Server Error', 'The request was unsuccessful due to an unexpected condition encountered by the server.'), 
        502 => array('502 Bad Gateway', 'The server received an invalid response from the upstream server while trying to fulfill the request.'), 
        504 => array('504 Gateway Timeout', 'The upstream server failed to send a request in the time allowed by the server.<br>Try going back, then trying again.') 
        ); 
         
$title = $codes[$status][0]; 
$message = $codes[$status][1]; 
if ($title == false || strlen($status) != 3) { 
    $message = 'Please supply a valid status code.'; 
} 




$errornote = "Server Error:  " . $title;
$errordescription = $message;
}



if ($error == "")
{
$errornote = "That's an error.";
$errordescription = "	If you're seeing this page, then an error has occurred.<br>Sorry about that.";

}


?>

<html>
	<head>
		<link rel="shortcut icon" href="logo.ico" />
		<title>jsCard :: Error</title>
		
		<style type="text/css">
		
		body
		{
		background-color: #607AC5;
		background-repeat: repeat-y;
		background-image: url( 'http://jscard.org/images/bkgnd.jpg' );
		background-position: center center;
		}
		
		#stage
		{
		position: absolute;
		top: 0px;
		width: 705px;
		height: 100%;
		left: 50%;
		margin-left: -352px;
		}
		
		#content
		{
		position: absolute;
		top: 0px;
		left: 50%;
		margin-left: -352px;
		width: 701px;
		}
		
		#maincolumn
		{
		position: absolute;
		top: 242px;
		left: 30px;
		width: 600px;
		font-family: "Trebuchet MS", Verdana, Arial, sans-serif;
		font-size: 12px;
		line-height: 22px;
		text-align: justify;
		}
		
			#maincolumn h1
			{
				font-size: 18px;
			}
			
			#maincolumn table td.inputlabel
			{
				width: 150px;
				font-family: "MS Trebuchet", Verdana, Arial, sans-serif;
				font-size: 13px;
				color: #888;
			}
			
			#maincolumn table td
			{
				padding-bottom: 8px;
			}
			
			#maincolumn table td input
			{
				font-family: "MS Trebuchet", Verdana, Arial, sans-serif;
				font-size: 14px;
				padding: 3px;
				width: 100%;
			}
			
			#maincolumn table td.buttonrow input
			{
				width: auto;
			}
			
.errnote
{
	font-size: 18px;
	padding: 10px;
	color: #B34F4F;
	background-color: #F4E1E1;
}

					
		</style>

		<script type="text/javascript">
		
/* Client-side access to querystring name=value pairs
	Version 1.2.3
	22 Jun 2005
	Adam Vandenberg
*/
function Querystring(qs) { // optionally pass a querystring to parse
	this.params = new Object()
	this.get=Querystring_get
	
	if (qs == null)
		qs=location.search.substring(1,location.search.length)

	if (qs.length == 0) return

// Turn <plus> back to <space>
// See: http://www.w3.org/TR/REC-html40/interact/forms.html#h-17.13.4.1
	qs = qs.replace(/\+/g, ' ')
	var args = qs.split('&') // parse out name/value pairs separated via &
	
// split out each name=value pair
	for (var i=0;i<args.length;i++) {
		var value;
		var pair = args[i].split('=')
		var name = unescape(pair[0])

		if (pair.length == 2)
			value = unescape(pair[1])
		else
			value = name
		
		this.params[name] = value
	}
}

function Querystring_get(key, default_) {
	// This silly looking line changes UNDEFINED to NULL
	if (default_ == null) default_ = null;
	
	var value=this.params[key]
	if (value==null) value=default_;
	
	return value
}
		
		</script>

	</head>
	
	<body>
	
	<div id="stage"><br/></div>
	
	<div id="content">
		<img src="http://jscard.org/images/header.jpg" border="0" />
		
		<div id="maincolumn">
			<h1>There has been an error.</h1>

			<p class="errnote">
				<?php echo $errornote; ?>
			</p>
			
			<p>
				<?php echo $errordescription; ?>
			</p>
			
		</div>
		
		</div>
		
	</div>
	
	</body>
</html>	