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
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.

*/


define( "TOKEN_STATE_WHITESPACE",	"0" );
define( "TOKEN_STATE_IDENTIFIER",		"1" );
define( "TOKEN_STATE_STRING",			"2" );
define( "TOKEN_STATE_OPERATOR",		"3" );

define( "OPERATOR_BINARY",				"0" );
define( "OPERATOR_UNARY", 				"1" );


$waitEndings = array();
$currentIndent = 0;




function tryLog ( $something )
{

}

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





function tokenize( $tokenizeSomething )
{
	$state = TOKEN_STATE_WHITESPACE;

	$operatorChars = array( "@", ",", "+", "-", "/", "<", "&", ">", "=", "^", "(", ")", "'", "[", "]", "*" );
	$whiteSpaceChars = array( " ", "\t" );

	$curToken = array ( "value" => "", "type" => "", "offset" => "" );
	
	$backupTokens = array();
	$symbols = array();

	for ( $i = 0; $i < strlen( $tokenizeSomething ); $i++ )
	{
		$curChar = $tokenizeSomething[$i];
		$opOffset = array_search( $curChar, $operatorChars );

		// Okay, so first we switch on the state, and
		// behave differently depending on what state
		// we're in. We start out as whitespace, because
		// we want to know when the first token starts.
		
		switch ( $state )
		{
		
			case TOKEN_STATE_WHITESPACE:
				tryLog (  "Skipping whitespace...<br/>" );	
				if ( ! in_array( $curChar, $whiteSpaceChars ) ) // No whitespace? We just started an identifier!
				{
					tryLog (  "Found the start of an something: " . $curChar . "<br/>" );
					if ( $curChar == '"' )
					{
						tryLog (  "It's a string.<br/>" );
						$curToken["type"] = $state = TOKEN_STATE_STRING;
						$curToken["offset"] = $i;
					}
					else if ( $opOffset )
					{
						tryLog (  "It's an operator.<br/>" );
						$curToken["type"] = $state = TOKEN_STATE_OPERATOR;
						$curToken["value"] .= $curChar;
						$curToken["offset"] = $i;
					}
					else
					{
						tryLog (  "It's an identifier.<br/>" );
						$curToken["type"]  = $state = TOKEN_STATE_IDENTIFIER;	 // Remember state change.
						$curToken["value"] .= $curChar;					   		// Remember this char.
						$curToken["offset"] = $i;							   // Remember token position.
					}
					
				}
				// else we don't care about whitespace and just forget it.
				break;
				
			case TOKEN_STATE_STRING:
				tryLog ( "Continuing string read..." );
				if ( $curChar == '"' )
				{
					tryLog (  "End of string found. Saving...<br/>" );
					array_push( $symbols, $curToken );
					$curToken = array ( "value" => "", "type" => "", "offset" => "" );
					$curToken["type"] = $state = TOKEN_STATE_WHITESPACE;
				}
				else
				{
					tryLog (  "Adding character " . $curChar . "<br/>" );
					$curToken["value"] .= $curChar;
				}
				break;
				
			case TOKEN_STATE_IDENTIFIER:
				tryLog (  "Reading identifier...<br/>" );
				if ( in_array( $curChar, $whiteSpaceChars ) || $curChar == '"' || $opOffset ) // Hit white space, operator? End of token!
				{
					
					tryLog (  "We've hit the end of the identifier. Saving...<br/>" );
					
					$curToken["value"] = strtolower( $curToken["value"] );
					
					array_push( $symbols, $curToken );
										
					$curToken = array ( "value" => "", "type" => "", "offset" => "" );
					
					if ( $curChar == '"' )
					{
						tryLog (  "Starting a new string...<br/>" );
						$curToken["type"] = $stage = TOKEN_STATE_STRING;
						$curToken["offset"] = $i;
					}
					else if ( $opOffset )
					{
						tryLog (  "Starting a new operator ( " . $curChar . " )...<br/>" );
						$curToken["type"] = $state = TOKEN_STATE_OPERATOR;
						$curToken["value"] .= $curChar;
						$curToken["offset"] = $i;
					}
					else
					{
						$curToken["type"] = $state = TOKEN_STATE_WHITESPACE;
					}
				}
				else
				{
					tryLog (  "Adding to identifier: " . $curChar . "<br/>" );
					$curToken["value"] .= $curChar; // Not whitespace? Add char to token!
				}
				break;
				
			case TOKEN_STATE_OPERATOR:
				tryLog (  "Continuing operator read...<br/>" );
				if ( ! $opOffset )
				{
					tryLog (  "End of operator found.<br/>" );
					// It's not an operator. End the token.
					array_push( $symbols, $curToken );
					$curToken = array ( "value" => "", "type" => "", "offset" => "" );
					
					if ( $curChar == '"' )
					{
						tryLog( "Starting a new string...<br/>" );
						$curToken["type"] = $state = TOKEN_STATE_STRING;
						$curToken["offset"] = $i;
					}
					else if ( in_array( $curChar, $whiteSpaceChars ) )
					{
						tryLog( "Found whitespace.<br/>" );
						$state = TOKEN_STATE_WHITESPACE;
					}
					else
					{
						tryLog( "Starting a new identifier ( " . $curChar . " )...<br/>" );
						$curToken["type"] = $state = TOKEN_STATE_IDENTIFIER;
						$curToken["offset"] = $i;
						$curToken["value"] .= $curChar;
					}
				}
				else
				{
					if ( $curToken["value"] == ">=" || $curToken["value"] == "<=" || $curToken["value"] == "&&" || $curToken["value"] == "<>" || $curToken["value"] == "--" )
					{
						array_push( $symbols, $curToken );
						$curToken = array ( "value" => "", "type" => "", "offset" => "" );
						
						$curToken["type"] == TOKEN_STATE_OPERATOR;
						$curToken["offset"] == $i;
						$curToken["value"] .= $curChar;
					}
					else if
						(
							( ( $curChar == '=' ) && $tokenizeSomething[ $i - 1 ] == '>' || $tokenizeSomething[ $i - 1 ] == '<' )
							|| ( ( $curChar == '&' ) && $tokenizeSomething[ $i - 1 ] == '&' )
							|| ( ( $curChar == '>' ) && $tokenizeSomething[ $i - 1 ] == '<' )
							|| ( ( $curChar == '-' ) && $tokenizeSomething[ $i - 1 ] == '-' )
						)
					{
						$curToken["value"] .= $curChar;
					}
					else
					{
						array_push( $symbols, $curToken );
						$curToken = array ( "value" => "", "type" => "", "offset" => "" );
						
						$curToken["type"] = TOKEN_STATE_OPERATOR;
						$curToken["offset"] = $i;
						$curToken["value"] .= $curChar;
					}
					
				}
				
			break;
			
		}
		
	}
	
	if ( $state = TOKEN_STATE_IDENTIFIER || $state = TOKEN_STATE_STRING || $state == TOKEN_STATE_OPERATOR )
	{
						array_push( $symbols, $curToken );
						$curToken = array ( "value" => "", "type" => "", "offset" => "" );
	}
	
	// Smart Re-Tokenizer
	
	// Copy symbols to backupTokens

	$backupTokens = $symbols;

	$symbols = array();
	
	for ( $i = 0; $i < count( $backupTokens ); $i++ )
	{
		if ( ( ( count ( $backupTokens ) - 1 - $i ) >= 4 )
		&& $backupTokens[ $i ]["value"] == "there"
		&& $backupTokens[ $i + 1 ]["value"] == "is"
		&& $backupTokens[ $i + 2 ]["value"] == "not"
		&& ( $backupTokens[ $i + 3 ]["value"] == "a"
		|| $backupTokens[ $i + 3 ]["value"] == "an" ) )
		{
			array_push( $symbols, array ( "value" => "there is not a", "offset" => $backupTokens[ $i ]["offset"], "type" => TOKEN_STATE_OPERATOR ) );
			$i += 3;
		}
		else if ( ( ( count ( $backupTokens ) - 1 - $i ) >= 3 )
			&& $backupTokens[ $i ]["value"] == "there"
			&& $backupTokens[ $i + 1 ]["value"] == "is"
			&& (
				   $backupTokens[ $i + 2 ]["value"] == "a"
				|| $backupTokens[ $i + 2 ]["value"] == "an"
			) )
		{
			array_push( $symbols, array ( "value" => "there is a", "offset" => $backupTokens[ $i ]["offset"], "type" => TOKEN_STATE_OPERATOR ) );
			$i += 2;
		}
		else if ( ( ( count ( $backupTokens ) - 1 - $i ) >= 3 )
			&& $backupTokens[ $i ]["value"] == "there"
			&& $backupTokens[ $i + 1 ]["value"] == "is"
			&& $backupTokens[ $i + 2 ]["value"] == "no" )
		{
			array_push( $symbols, array ( "value" => "there is no", "offset" => $backupTokens[ $i ]["offset"], "type" => TOKEN_STATE_OPERATOR ) );
			$i += 2;
		}
		else if ( ( ( count ( $backupTokens ) - 1 - $i ) >= 3 )
			&& $backupTokens[ $i ]["value"] == "does"
			&& $backupTokens[ $i + 1 ]["value"] == "not"
			&& $backupTokens[ $i + 2 ]["value"] == "contain" )
		{
			array_push( $symbols, array ( "value" => "does not contain", "offset" => $backupTokens[ $i ]["offset"], "type" => TOKEN_STATE_OPERATOR ) );
			$i += 2;
		}
		else if ( ( ( count ( $backupTokens ) - 1 - $i ) >= 3 )
			&& $backupTokens[ $i ]["value"] == "is"
			&& $backupTokens[ $i + 1 ]["value"] == "not"
			&& $backupTokens[ $i + 2 ]["value"] == "within" )
		{
			array_push( $symbols, array ( "value" => "is not within", "offset" => $backupTokens[ $i ]["offset"], "type" => TOKEN_STATE_OPERATOR ) );
			$i += 2;
		}
		else if ( ( ( count ( $backupTokens ) - 1 - $i ) >= 3 )
			&& $backupTokens[ $i ]["value"] == "is"
			&& $backupTokens[ $i + 1 ]["value"] == "not"
			&& $backupTokens[ $i + 2 ]["value"] == "in" )
		{
			array_push( $symbols, array ( "value" => "is not in", "offset" => $backupTokens[ $i ]["offset"], "type" => TOKEN_STATE_OPERATOR ) );
			$i += 2;
		}
		else if ( ( ( count ( $backupTokens ) - 1 - $i ) >= 3 )
			&& $backupTokens[ $i ]["value"] == "there"
			&& $backupTokens[ $i + 1 ]["value"] == "is"
			&& $backupTokens[ $i + 2 ]["value"] == "no" )
		{
			array_push( $symbols, array ( "value" => "there is no", "offset" => $backupTokens[ $i ]["offset"], "type" => TOKEN_STATE_OPERATOR ) );
			$i += 2;
		}
		else if ( ( ( count ( $backupTokens ) - 1 - $i ) >= 3 )
			&& $backupTokens[ $i ]["value"] == "is"
			&& $backupTokens[ $i + 1 ]["value"] == "not"
			&& (
				   $backupTokens[ $i + 2 ]["value"] == "a"
				|| $backupTokens[ $i + 2 ]["value"] == "an"
			) )
		{
			array_push( $symbols, array ( "value" => "is not a", "offset" => $backupTokens[ $i ]["offset"], "type" => TOKEN_STATE_OPERATOR ) );
			$i += 2;
		}
		else if ( ( ( count ( $backupTokens ) - 1 - $i ) >= 3 )
			&& $backupTokens[ $i ]["value"] == "isn"
			&& $backupTokens[ $i + 1 ]["value"] == "'"
			&& $backupTokens[ $i + 2 ]["value"] == "t" )
		{
			array_push( $symbols, array ( "value" => "is not", "offset" => $backupTokens[ $i ]["offset"], "type" => TOKEN_STATE_OPERATOR ) );
			$i += 2;
		}
		else if ( ( ( count ( $backupTokens ) - 1 - $i ) >= 2 )
			&& $backupTokens[ $i ]["value"] == "is"
			&& $backupTokens[ $i + 1 ]["value"] == "not" )
		{
			array_push( $symbols, array ( "value" => "is not", "offset" => $backupTokens[ $i ]["offset"], "type" => TOKEN_STATE_OPERATOR ) );
			$i += 1;
		}
		else if ( ( ( count ( $backupTokens ) - 1 - $i ) >= 2 )
			&& $backupTokens[ $i ]["value"] == "is"
			&& $backupTokens[ $i + 1 ]["value"] == "in" )
		{
			array_push( $symbols, array ( "value" => "is in", "offset" => $backupTokens[ $i ]["offset"], "type" => TOKEN_STATE_OPERATOR ) );
			$i += 1;
		}
		else if ( $backupTokens[$i]["value"] == "and" && $backupTokens[$i]["type"] != TOKEN_STATE_STRING )
		{
			array_push( $symbols, array ( "value" => "and", "offset" => $backupTokens[ $i ]["offset"], "type" => TOKEN_STATE_OPERATOR ) );
		}
		else if ( $backupTokens[$i]["value"] == "or" && $backupTokens[$i]["type"] != TOKEN_STATE_STRING )
		{
			array_push( $symbols, array ( "value" => "or", "offset" => $backupTokens[ $i ]["offset"], "type" => TOKEN_STATE_OPERATOR ) );
		}
		else if ( $backupTokens[$i]["value"] == "is" && $backupTokens[$i]["type"] != TOKEN_STATE_STRING )
		{
			array_push( $symbols, array ( "value" => "is", "offset" => $backupTokens[ $i ]["offset"], "type" => TOKEN_STATE_OPERATOR ) );
		}
		else if ( $backupTokens[$i]["value"] == "not" && $backupTokens[$i]["type"] != TOKEN_STATE_STRING )
		{
			array_push( $symbols, array ( "value" => "not", "offset" => $backupTokens[ $i ]["offset"], "type" => TOKEN_STATE_OPERATOR ) );
		}
		else
		{
			array_push( $symbols, $backupTokens[ $i ] );
		}
	}
	
	return $symbols;
	
}

function stitchArray( $tokens )
{
	$line = "";
	// This function intelligently stitches a line back together from the tokens.
	foreach ( $tokens as $token )
	{
		if ( $token["type"] == TOKEN_STATE_STRING )
		{
			$line .= "\"" . $token["value"] . "\" ";
		}
		else
		{
			$line .= $token["value"] . " ";
		}
	}
	return $line;
}

function compileLine( $tokens )
{
	
	global $inlineFlagStack;
	global $localVarRegistry;
	global $currentFunction;
	global $globalVarRegistry;
	global $pramsRegistry;
	
	//For Wait Function
	global $currentIndent;
	global $waitEndings;
	
	if ( trim( $tokens[ 0 ][ "value" ] ) == "" )
	{
		return "";
	}
	
	$inlineFlag = array_pop( $inlineFlagStack );
	array_push( $inlineFlagStack, false );
	
	switch ( $tokens[ 0 ][ "value" ] )
	{
		case "on":
		
			$lvr = array_pop( $localVarRegistry );
			$lvr["h" . strtolower( $tokens[ 1 ]["value"] ) ] = array("it");
			array_push( $localVarRegistry, $lvr );
			array_push( $globalVarRegistry, array() );
			
			array_pop( $pramsRegistry );
			$prr = array();
			
			array_pop( $currentFunction );
			array_push( $currentFunction, "h" . strtolower( $tokens[ 1 ]["value"] ) );
		
			$source .= "function " . strtolower( $tokens[ 1 ][ "value" ] ) . " ( ";
			
			if ( count ( $tokens ) > 2 )
			{				
				for ( $i = 2; $i < count( $tokens ); $i += 2 )
				{
					$source .= strtolower( $tokens[ $i ][ "value" ] ) ;
					array_push( $prr, strtolower( $tokens[ $i ]["value"] ) );
									
					if ( $i < count( $tokens ) - 1 )
					{
						 $source .= ", ";
					}
					else
					{
						$source .= " )\n{\n@localvars_h" . strtolower( $tokens[ 1 ]["value"] ) . "\n";
					}
				}
			}
			else
			{
				$source .= " )\n{\n@localvars_h" . strtolower( $tokens[ 1 ]["value"] ) . "\n";
			}
			array_push( $pramsRegistry, $prr );
			break;
			
		case "create":
			if ( $tokens[$start + 1]["value"] == "button" || $tokens[$start + 1]["value"] == "btn" )
			{
				$expr = fetchExpression( $tokens, $start + 2 );
				$source .= "makePart( 0, " . $expr["result"] . " );";
			}
			else if ( $tokens[$start + 1]["value"] == "field" || $tokens[$start + 1]["value"] == "fld" )
			{
				$expr = fetchExpression( $tokens, $start + 2 );
				$source .= "makePart( 1, " . $expr["result"] . " );";
			}
			else if ( $tokens[ $start + 1]["value"] == "block" || $tokens[$start + 1]["value"] == "blk" )
			{
				$expr = fetchExpression( $tokens, $start + 2 );
				$source .= "makePart( 2, " . $expr["result"] . ");";
			}
			else if ( $tokens[ $start + 1]["value"] == "card" || $tokens[$start + 1]["value"] == "cd" )
			{
				$expr = fetchExpression( $tokens, $start + 2 );
				$source .= "createCard( " . $expr["result"] . " );\n";
			}
			break;
			
		case "delete":
			if ( $tokens[$start+1]["value"] == "card" )
			{
				if( $tokens[$start+2]["value"] == "id" )
				{
					$expr = fetchExpression( $tokens, $start + 3 );
					$source .= "deleteCardID( " . $expr["result"] . " );\n";
				}
				else
				{
					$expr = fetchExpression( $tokens, $start + 2 );
					$source .= "deleteCardName( " . $expr["result"] . ")\n";
				}
			}
			else if ( $tokens[$start+1]["value"] == "button" || $tokens[$start+1] == "btn" )
			{
				if ( $tokens[$start+2]["value"] == "id" )
				{
					$expr = fetchExpression( $tokens, $start + 3 );
					$source .= "deleteElement( document.getElementById( 'obj' + " . $expr["result"] . " ) );\n";
				}
				else
				{
					$expr = fetchExpression( $tokens, $start + 2 );
					$source .= "deleteElement( document.getElementsByName('btn_' + stackID + '_' + cardID + '_' + " . $expr["result"] . " )[0] );\n";
				}
			}
			else if ( $tokens[$start+1]["value"] == "field" || $tokens[$start+1] == "fld" )
			{
				if ( $tokens[$start+2]["value"] == "id" )
				{
					$expr = fetchExpression( $tokens, $start + 3 );
					$source .= "deleteElement( document.getElementById( 'obj' + " . $expr["result"] . " ) );\n";
				}
				else
				{
					$expr = fetchExpression( $tokens, $start + 2 );
					$source .= "deleteElement( document.getElementsByName('fld_' + stackID + '_' + cardID + '_' + " . $expr["result"] . " )[0] );\n";
				}
			}
			else if ( $tokens[$start+1]["value"] == "block" || $tokens[$start+1] == "blk" )
			{
				if ( $tokens[$start+2]["value"] == "id" )
				{
					$expr = fetchExpression( $tokens, $start + 3 );
					$source .= "deleteElement( document.getElementById( 'obj' + " . $expr["result"] . " ) );\n";
				}
				else
				{
					$expr = fetchExpression( $tokens, $start + 2 );
					$source .= "deleteElement( document.getElementsByName('blk_' + stackID + '_' + cardID + '_' + " . $expr["result"] . " )[0] );\n";
				}
			}

		case "do":
			$expr = fetchExpression( $tokens, $start + 1 );
			if ( $tokens[ $expr["position"] - 1 ]["value"] == "as" )
			{
				if ( strtolower( $tokens[ $expr["position"] ]["value"] ) == "javascript" )
				{
					$source .= "eval( " . $expr["result"] . " );\n";
				}
				else if ( strtolower( $tokens[ $expr["position"] ]["value"] ) == "jstalk" )
				{
					$source .= "eval( inlinecompiler( " . $expr["result"] . " ) );\n";
				}
			}
			else
			{
				$source .= "eval( inlinecompiler( " . $expr["result"] . " ) );\n";
			}
			break;
			
		case "beep":
			if ( count( $tokens ) > 1 )
			{
				$expr = fetchExpression( $tokens, 1 );
				$source .= "soundManager.play( 'beep', " . $expr["result"] . " );\n";
			}
			else
			{
				$source .= "soundManager.play( 'beep' );\n";
			}
			break;
		
		case "play":
			if( $tokens[ 1 ]["value"] == "stop" )
			{
				$expr = fetchExpression( $tokens, 2 );
				$source .= "soundManager.stop( " . $expr["result"] . " );\n";
			}
			else
			{
				$expr = fetchExpression( $tokens, 1 );
				$source .= "soundManager.play( " . $expr["result"] . " );\n";
			}
			break;



			
		
		case "get":
			$expr = fetchExpression( $tokens, $start + 1 );
			$source .= "it = " . $expr["result"] . ";";
			break;

		case "function":
		
			$lvr = array_pop( $localVarRegistry );
			$lvr["f" . strtolower( $tokens[ 1 ]["value"] ) ] = array("it");
			array_push( $localVarRegistry, $lvr );
			array_push( $globalVarRegistry, array() );

			array_pop( $currentFunction );		
			array_push( $currentFunction, "f" . strtolower( $tokens[ 1 ]["value"] ) );
			array_pop( $pramsRegistry );
			$prr = array();
		
			$source .= "function " . strtolower( $tokens[ 1 ][ "value" ] ) . " ( ";
			
			if ( count ( $tokens ) > 2 )
			{			
				for ( $i = 2; $i < count( $tokens ); $i += 2 )
				{
					$source .= strtolower( $tokens[ $i ][ "value" ] );
					array_push( $prr, strtolower( $tokens[ $i ][ "value" ] ) );
									
					if ( $i < count( $tokens ) - 1 )
					{
						 $source .= ", ";
					}
					else
					{
						$source .= " )\n{\n@localvars_f" . strtolower( $tokens[ 1 ]["value"] ) . "\n";
					}
				}
			}
			else
			{
				$source .= " )\n{\n@localVars_f" . strtolower( $tokens[ 1 ]["value"] ) . "\n";
			}
			array_push( $pramsRegistry, $prr );
			break;

			
		case "global":
			$gvr = array_pop( $globalVarRegistry );
			for ( $i = 1; $i < count( $tokens ); $i += 2 )
			{
				array_push( $gvr, strtolower( $tokens[ $i ]["value"] ) );
			}
			array_push( $globalVarRegistry, $gvr );
			break;
		
		case "answer":
			$expr = fetchExpression( $tokens, $i + 1 );
			$i = $expr[ "position" ];
			$source .= "alert( " . $expr[ "result" ] . " );\n";
			break;
		
		case "ask":
			$expr = fetchExpression( $tokens, $i + 1 );
			$i = $expr["position"];
			if ( $tokens[ $i - 1 ]["value"] == "with" )
			{
				$expr2 = fetchExpression( $tokens, $i );
				$source .= "it = prompt( " . $expr["result"] . ", " . $expr2["result"] . " );\n";
			}
			else
			{
				$source .= "it = prompt( " . $expr["result"] . ", '' );\n";
			}
			break;
		
		case "put":
			$expr = fetchExpression( $tokens, $i + 1 );
			$i = $expr[ "position" ];
			if ( $expr[ "position" ] >= count( $tokens ) )
			{
				// That's the end of the line, so this is an output put.
				$source .= "document.getElementById('msgboxcontent').value = ( " . $expr[ "result" ] . " );\n";
			}
			else
			{
			   // Copy Value
			   $container = fetchContainer( $tokens, $i, $tokens[ $i - 1 ]["value"] );
			   if ( array_key_exists( "assign", $container ) )
			   {
				   //$source = $container["assign"] . " = " . str_replace( "@", $expr["result"], $container["result"] ) . ";\n";
				   $source .= str_replace( "@" ,str_replace( "@", $expr["result"], $container["result"] ), $container["assign"] ) . "\n";
			   }
			   else
			   {
				   $source .= str_replace( "@", $expr[ "result" ], $container[ "result" ] ) . "\n";
			   }
			}
			break;




		case "wait":
			
			$currentIndent++;
			
			if( $tokens[1]["value"] == "until" )
			{
				$flag = fetchExpression( $tokens, 2 );
				$i = $flag["position"];
				$flag = $flag["result"];
				
				$source = "intervals[" . $currentIndent . "] = setInterval(function(){ if(" . $flag . "){clearInterval(intervals[" . $currentIndent . "]);";
				$waitEndings[$currentIndent] = "} }, 10);";
			}
			else if( $tokens[1]["value"] == "while" )
			{
				$flag = fetchExpression( $tokens, 2 );
				$i = $flag["position"];
				$flag = $flag["result"];
				
				$source = "intervals[" . $currentIndent . "] = setInterval(function(){ if(! (" . $flag . ")){clearInterval(intervals[" . $currentIndent . "]);";
				$waitEndings[$currentIndent] = "} }, 10);";
			}
			else
			{
				$time = fetchExpression( $tokens, $i + 1 );
				$i = $time["position"];
				$time = $time["result"];

				// As the code can be "wait 3 seconds" or "wait for 3 seconds", we need to check if the second word returned is "for", and
				// if it is, then run the code to retrieve the number again, this time to retrieve the actual time
				if ($time == "for")
				{
					$time = fetchExpression( $tokens, $i - 1 );
					$i = $time["position"];
					$time = $time["result"];
				}
				else
				{
					$units = fetchExpression( $tokens, $i - 1 );
					$i = $units["position"];
					$units = $units["result"];
					$ending = "s()";
					if ( (substr( $units, -strlen( $ending ) ) == $ending) == 1)
					{
						$units = substr($units, 0, -2);
					}
					if( $units == "seconds" || $units == "second" )
					{
						$time = $time*1000;
					}
					else if( $units == "ticks" || $units == "tick" )
					{
						$time = ($time/60)*1000;
					}
					else if( $units == "milliseconds" || $unit = "milliseconds" )
					{
						$time = $time;
					}
					else
					{
						$time = ($time/60)*1000;
					}
				}

				$source .= "setTimeout(function(){";
				$waitEndings[$currentIndent] = "}, " . $time . ");";
			}
			
			/*if ( $tokens[ 1 ]["value"] == "until" )
			{
				
			}
			else if ( $tokens[ 1 ]["value"] == "while" )
			{
				
			}
			else
			{
			 	$units = fetchExpression( $tokens, $i - 1 );
				$i = $units["position"];
				$units = $units["result"];
				
				$ending = "s( ) ";
				if ( (substr( $units, -strlen( $ending ) ) == $ending) == 1)
				{
				$units = substr($units, 0, -4);
				}
				
				if ($units == "")
				{
					$units = "ticks";
				}
				
				$source .= "waitTime(" . $time . ", " . $units . ");\n";
			
			}*/
		
			break;



		case "add":
			$expr = fetchExpression( $tokens, $start + 1 );
			$i = $expr["position"];
			
			$expr2 = fetchExpression( $tokens, $i );
			
			$container = fetchContainer( $tokens, $i, "into" );
			if ( array_key_exists( "assign", $container ) )
			{
				$source = $container["assign"] . " = " . str_replace( "@", "( ( " . $expr2["result"] . " ) * 1 ) + ( 1 * ( " . $expr["result"] . " ) ) ", $container["result"] );
			}
			else
			{
				$source = str_replace( "@", "( ( " . $expr2["result"] . " ) * 1 ) + ( 1 * ( " . $expr["result"] . " ) ) ", $container["result"] );
			}
			
			break;
			
		case "subtract":
			$expr = fetchExpression( $tokens, $start + 1 );
			$i = $expr["position"];
			
			$expr2 = fetchExpression( $tokens, $i );
			
			$container = fetchContainer( $tokens, $i, "into" );
			if ( array_key_exists( "assign", $container ) )
			{
				$source = $container["assign"] . " = " . str_replace( "@", "( ( " . $expr2["result"] . " ) * 1 ) - ( 1 * ( " . $expr["result"] . " ) ) ", $container["result"] );
			}
			else
			{
				$source = str_replace( "@", "( ( " . $expr2["result"] . " ) * 1 ) - ( 1 * ( " . $expr["result"] . " ) ) ", $container["result"] );
			}
			
			break;
			
		case "multiply":
			$expr = fetchExpression( $tokens, $start + 1 );
			$i = $expr["position"];
			
			$expr2 = fetchExpression( $tokens, $i );
			
			$container = fetchContainer( $tokens, $start + 1, "into" );
			if ( array_key_exists( "assign", $container ) )
			{
				$source = $container["assign"] . " = " . str_replace( "@", "( ( " . $expr2["result"] . " ) * 1 ) * ( 1 * ( " . $expr["result"] . " ) ) ", $container["result"] );
			}
			else
			{
				$source = str_replace( "@", "( ( " . $expr2["result"] . " ) * 1 ) * ( 1 * ( " . $expr["result"] . " ) ) ", $container["result"] );
			}
			
			break;
			
		case "divide":
			$expr = fetchExpression( $tokens, $start + 1 );
			$i = $expr["position"];
			
			$expr2 = fetchExpression( $tokens, $i );
			
			$container = fetchContainer( $tokens, $start + 1, "into" );
			if ( array_key_exists( "assign", $container ) )
			{
				$source = $container["assign"] . " = " . str_replace( "@",  "( 1 * ( " . $expr["result"] . " ) ) / ( ( " . $expr2["result"] . " ) * 1 )", $container["result"] );
			}
			else
			{
				$source = str_replace( "@", "( 1 * ( " . $expr["result"] . " ) ) / ( ( " . $expr2["result"] . " ) * 1 ) ", $container["result"] );
			}
			
			break;

		
		case "set":
			
			/*$property = setObjectProperty( $tokens, 1 );
			$i = $property["position"] + 1;
			$property = $property["result"];
			
			$object = fetchObject( $tokens, $i );
			$i = $object["position"];
			$object = $object["result"];*/
			
			$property = setObjectProperty( $tokens, 1 );
			if( $property )
			{
				// If the property is an object property
				$i = $property["position"] + 1;
				$property = $property["result"];
				
				$object = fetchObject( $tokens, $i );
				$i = $object["position"];
				$object = $object["result"];
			}
			else
			{
				// If the property is a global property
				$property = fetchGlobalProperty( $tokens, 1 );
				$i = $property["position"];
				$property = $property["result"];
				
				if( $tokens[$i]["value"] == "of" )
				{
					$ignoreObject = fetchObject( $tokens, $i+1 );
					$i = $ignoreObject["position"];
				}
				else
				{
					$i++;
				}
			}
			
			// There could be more than one factor, we use the fetchFactorList function
			$expr = fetchFactorList( $tokens, $i );
			$count = $expr["totalFactors"];
			$expr = $expr["result"];
			
			if( $count != 1 )
			{
				$expr = "'" . $expr . "'";
			}
			
			$source = str_replace( "@", $object, str_replace( "%", $expr, $property ) ); 

			
			break;
		
		case "if":
			$currentIndent++;
			
			$expr = fetchExpression( $tokens, $i + 1 );
			$source .= "if( " . $expr["result"] . " )\n{\n";
			if ( $tokens[ count( $tokens ) - 1 ]["value"] != "then" )
			{
				$source .= "\n" . compile( stitchArray( array_slice( $tokens, $expr["position"] ) ), true ) . "\n}\n";
				array_pop( $inlineFlagStack );
				array_push( $inlineFlagStack, true );
			}
			break;
			
		case "else":
		
			$currentIndent++;
			//$source .= "// " . ( ! $inlineFlag ) . "\n\n";
			if ( ! $inlineFlag )
			{
				$source .= "}";
			}
		
			if ( $tokens[ $i + 1 ]["value"] == "if" )
			{
				$expr = fetchExpression( $tokens, $i + 2 );
				$source .= "\nelse if( " . $expr["result"] . " )\n{\n";
				if ( $tokens[ count( $tokens ) -1 ]["value"] != "then" )
				{
					$source .= "\n" . compile( stitchArray( array_slice( $tokens, $expr["position"] ) ), true ) . "\n}\n";
					array_pop( $inlineFlagStack );
					array_push( $inlineFlagStack, true );
				}
			}
			else
			{
				$source .= "\nelse\n{\n";
				if ( count( $tokens ) > 1 )
				{
					$source .= "\n" . compile( stitchArray( array_slice( $tokens, 1 ) ), true ) . "\n}\n";
					array_pop( $inlineFlagStack );
					array_push( $inlineFlagStack, true );
				}
			}
			break;
		
		case "repeat":
		
			$currentIndent++;
			
			if ( count( $tokens ) == 1 || $tokens[ 1 ]["value"] == "forever" )
			{
				$source .= "while( 1 )\n{";
			}
			else if ( $tokens[ count( $tokens ) - 1 ]["value"] == "times" )
			{
				$expr = fetchExpression( $tokens, 1 );
				$loopCounter = "LOOPCOUNTER" . rand();
				$loopMax = "LOOPMAX" . rand();
				$source .= $loopMax . " = " . $expr["result"] . ";\nfor( " . $loopCounter . " = 0; " . $loopCounter . " < " . $loopMax . "; " . $loopCounter . "++ )\n{\n";
			}
			else if ( $tokens[ 1 ]["value"] == "until" )
			{
				$expr = fetchExpression( $tokens, 2 );
				$source .= "while( ! ( " . $expr["result"] . " ) )\n{\n";
			}
			else if ( $tokens[ 1 ]["value"] == "while" )
			{
				$expr = fetchExpression( $tokens, 2 );
				$source .= "while( " . $expr["result"] . " )\n{\n";
			}
			else if ( $tokens[ 1 ]["value"] == "with" )
			{
				$kvar = $tokens[ 2 ]["value"];
				$kdirection = 1;
				
				$expr = fetchExpression( $tokens, 4 );
				
				$kstart = $expr["result"];
				
				$i = $expr["position"];
				
				if ( $tokens[ $i ]["value"] == "to" && $tokens[ $i - 1 ]["value"] == "down" )
				{
					$kdirection = 0;
					$i++;
				}
				
				$expr = fetchExpression( $tokens, $i );
				
				$kstop = $expr["result"];
				
				$source .= "for( " . $kvar . " = " . $kstart . "; " . $kvar . ( $kdirection ? " <= " : " >= " ) . $kstop . "; " . $kvar . ( $kdirection ? "++" : "--" ) . " )\n{\n";
				
			}
			else
			{
				$expr = fetchExpression( $tokens, 1 );
				$loopCounter = "LOOPCOUNTER" . rand();
				$loopMax = "LOOPMAX" . rand();
				$source .= $loopMax . " = " . $expr["result"] . ";\nfor( " . $loopCounter . " = 0; " . $loopCounter . " < " . $loopMax . "; " . $loopCounter . "++ )\n{\n";
			}
			
			break;
			
		case "sort":
			if( ( $tokens[ 1 ][ "value" ] == "ascending" ) || ( $tokens[ 1 ][ "value" ] == "descending" ) || ( $tokens[ 1 ][ "value" ] == "numeric" ) )
			{
				$sortType = $tokens[ 1 ]["value"];
				$i = 2;
			}
			else
			{
				$sortType = "ascending";
				$i = 1;
			}
			
			$sortChunk = $tokens[ $i ]["value"];
			
			$sortContainer = fetchContainer( $tokens, $i + 2, 'into' );
			$sortValue = fetchExpression( $tokens, $i + 2 );
			$i = $sortContainer[ "position" ];
			
			if ( $tokens[ $i - 1 ]["value"] != "by" )
			{
				$sortFunction = "currentElement";
			}
			else
			{
				$sortFunction = fetchExpression( $tokens, $i );
				$sortFunction = str_replace( "\"", "\\\"", $sortFunction["result"] );
			}
			
			$source = str_replace( "@", "hpop__sort( '" . $sortType . "', '" . $sortChunk . "', " . $sortValue['result'] . ", \"" .  $sortFunction . "\" )" ,$sortContainer["result"] );
			
			break;
		
		case "end":
			
			// First we need to see if there are any wait's to do
			while( isset($waitEndings[$currentIndent]) )
			{
				if( isset($waitEndings[$currentIndent]) )
				{
					$source .= $waitEndings[$currentIndent];
					$currentIndent = $currentIndent - 1;
				}
			}
			
			if ( $tokens[ 1 ]["value"] != "if" && $tokens[ 1 ]["value"] != "repeat" )
			{
				array_pop( $currentFunction );
				array_pop( $pramsRegistry );
				array_push( $pramsRegistry, array() );
				array_push( $currentFunction, "global" );
				array_pop( $globalVarRegistry );
			}
		
			if ( ! $inlineFlag || $tokens[ 1 ]["value"] != "if" )
			{
				$source .= "}";
			}

			$source .= "\n";
		
			// Minor pretty-printing.
			if ( $tokens[ 1 ]["value"] != "if" && $tokens[ 2 ]["value"] != "repeat" )
			{
				$source .= "\n";
			}
		
			$currentIndent = $currentIndent - 1;
			
			break;
			
		case "return":
		
			$expr = fetchExpression( $tokens, $start + 1 );
		
			$source = "return( " . $expr["result"] . ");\n";
			break;
		
		case "exit":
			if( $tokens[1]["value"] == "repeat" )
			{
				$source = "break;";
			}
			else if( $tokens[1]["value"] == "to" && $tokens[2]["value"] == "hypercard")
			{
				$source = "return;";
			}
			else
			{
				$source = "return;";
			}
			break;
		
		case "next":
			if( $tokens[1]["value"] == "repeat" )
			{
				$source = "continue;";
			}
			break;
	
		case "send":
			if ( trim( $tokens[ 1 ][ "value" ] != "" ) )
			{
				$source = strtolower( $tokens[ 1 ][ "value" ] ) . "( ";
				
				if( count( $tokens ) == 1 )
				{
					$source .= ");\n";
				}
				else
				{
					$expr = fetchExpression( $tokens, $start + 2 );
					$i = $expr[ "position" ];
					while( $i < count( $tokens ) )
					{
						$source .= $expr[ "result" ] . ", ";
						$expr = fetchExpression( $tokens, $i );
						$i = $expr[ "position" ];
					}
					$source .= $expr[ "result" ] . " );\n";
				}
			}
			else
			{
				$source = "";
			}
			break;
		
		case "enable":
			$object = fetchObject( $tokens, 1 );
			$object = $object["result"];
			
			$source = "hpop__setEnabled(" . $object . ", 1);";
			break;

		case "disable":
			$object = fetchObject( $tokens, 1 );
			$object = $object["result"];
			
			$source = "hpop__setEnabled(" . $object . ", 0);";
			break;
		
		case "show":
			if( $tokens[1]["value"] == "button" || $tokens[1]["value"] == "btn" || $tokens[1]["value"] == "field" || $tokens[1]["value"] == "fld" || $tokens[1]["value"] == "block" || $tokens[1]["value"] == "blk" || $tokens[1]["value"] == "me" )
			{
				$object = fetchObject( $tokens, 1 );
				$i = $object["position"];
				$object = $object["result"];
				
				logThis("searchword: " . $tokens[$i-1]["value"]);
				
				if( $tokens[$i-1]["value"] == "at" )
				{
					$xPos = fetchFactor($tokens, $i);
					$i = $xPos["position"];
					$xPos = $xPos["result"];
					$yPos = fetchFactor($tokens, $i);
					$yPos = $yPos["result"];
					logThis("x:" . $xPos . "y:" . $yPos);
				}
			
				$source = "hpop__setVisible(" . $object . ", 1);";
				break;
			}
		
		case "hide":
			$object = fetchObject( $tokens, 1 );
			$object = $object["result"];
			
			$source = "hpop__setVisible(" . $object . ", 0);";
			break;
			
		case "go":
			if( $tokens[ 1 ]["value"] == "to" )
			{
				$start = 1;
			}
			else
			{
				$start = 0;
			}
			
			//echo $tokens[ $start + 2 ]["value"] . " " . $tokens[ $start + 1 ]["value"];
			
			if ( $tokens[ $start + 1 ]["value"] == "card" && $tokens[ $start ]["value"] != "next" && $tokens[ $start ]["value"] != "prev" && $tokens[ $start ]["value"] != "previous" )
			{
				if ( $tokens[ $start + 2 ]["value"] == "id" )
				{
					$expr = fetchExpression( $tokens, $start + 3 );
					$source = "changeCardToID( \"card_\" + " . $expr["result"] . " );";
				}
				else
				{
					$expr = fetchExpression( $tokens, $start + 2 );
					$source = "changeCardToName( \"card_\" + " . $expr["result"] . ");";
				}
			}
			else if ( $tokens[ $start ]["value"] == "next" || $tokens[ $start + 1 ]["value"] == "next" || $tokens[ $start + 2 ]["value"] == "next" )
			{
				$source = "changeNextCard();\n";
			}
			else if ( $tokens[ $start ]["value"] == "previous" || $tokens[ $start + 1 ]["value"] == "previous" || $tokens[ $start + 2 ]["value"] == "previous" || $tokens[ $start ]["value"] == "prev" || $tokens[ $start + 1 ]["value"] == "prev" || $tokens[ $start + 1 ]["value"] == "prev" )
			{
				$source = "changePrevCard();\n";
			}
			break;
			
		default:
		
			if ( trim( $tokens[ 0 ][ "value" ] != "" ) )
			{
				$source = strtolower( $tokens[ 0 ][ "value" ] ) . "( ";
				
				if( count( $tokens ) == 1 )
				{
					$source .= ");\n";
				}
				else
				{
					$expr = fetchExpression( $tokens, $start + 1 );
					$i = $expr[ "position" ];
					while( $i < count( $tokens ) )
					{
						$source .= $expr[ "result" ] . ", ";
						$expr = fetchExpression( $tokens, $i );
						$i = $expr[ "position" ];
					}
					$source .= $expr[ "result" ] . " );\n";
				}
			}
			else
			{
				$source = "";
			}
			break;
	}
	
	return $source;
	
}

function precedence ( $operator )
{
	if ( array_search( $operator, array( "", "not", "^" ) ) != false )
	{
		return 7;
	}
	
	if ( array_search( $operator, array( "", "*", "/", "div", "mod" ) ) != false )
	{
		return 6;
	}
	
	if ( array_search( $operator, array( "", "+", "-" ) ) != false )
	{
		return 5;
	}
	
	if ( array_search( $operator, array( "", "<", ">", "<=", ">=" ) ) != false )
	{
		return 4;
	}
	
	if ( array_search( $operator, array( "", "=", "is", "<>", "is not", "isn't", ) ) != false )
	{
		return 3;
	}
	
	if ( array_search( $operator, array( "", "&", "&&" ) ) != false )
	{
		return 2;
	}
		
	if ( array_search( $operator, array( "", "is in", "is not in", "does not contain", "contains" ) ) != false )
	{
		return 1;
	}
	
	if ( array_search( $operator, array( "", "and", "or" ) ) != false )
	{
		return 0;
	}




}

function operatorToFactor( $operator )
{
	$operators =	array( "", "+", "-", "*", "/", "^", "&", "&&", ">", ">=", "<", "<=", "=", "is", "<>", "is not", "isn't", "and", "or" );
	$opfunctions =	array( "", "hpop__binaryAdd", "hpop__binarySubtract", "hpop__binaryMultiply", "hpop__binaryDivide", "hpop__binaryExp", "hpop__binaryConcat", "hpop__binaryConcat2", "hpop__binaryGT", "hpop__binaryGTE", "hpop__binaryLT", "hpop__binaryLTE", "hpop__binaryEq", "hpop__binaryEq", "hpop__binaryNotEq", "hpop__binaryNotEq", "hpop__binaryNotEq", "hpop__binaryAnd", "hpop__binaryOr" );
		
	if ( array_search( $operator, $operators ) != false )
		return $opfunctions[ array_search( $operator, $operators ) ];
	else
		return false;
}

function fetchExpression( $tokens, $start )
{
	$expressionStack = array();
	$expressionStack2 = array();
	$operatorStack = array();
	$precedenceStack = array();
	
	$pflag = false;
	
	$curFactor = fetchFactor( $tokens, $start );
	
	$i = $curFactor["position"];
	
	if ( ! operatorToFactor( $tokens[$i-1]["value"] ) )
	{
		return array( "result" => $curFactor["result"], "position" => $curFactor["position"] );
	}
	
	$operator = $tokens[$i-1]["value"];
	
	array_push( $expressionStack, $curFactor["result"] );
	array_push( $expressionStack2, operatorToFactor( $operator ) . "( " );
	array_push( $precedenceStack, "1" );
	array_push( $operatorStack, $operator );
			
	while( 1 )
	{
				
		$oldFactor = $curFactor;
		
		$oldOperator = $operator;
		
		$curFactor = fetchFactor( $tokens, $i );
	
		$i = $curFactor["position"];
		
		if ( ! operatorToFactor( $tokens[$i-1]["value"] ) )
		{
			break;
		}
	
		$operator = $tokens[$i-1]["value"];
		
		$topOperator = array_pop( $operatorStack );
		array_push( $operatorStack, $topOperator );
		
		if ( precedence( $operator ) > precedence( $oldOperator ) )
		{
			array_push( $precedenceStack, "1" );
			
			array_push( $expressionStack, $curFactor["result"] );
			
			array_push( $expressionStack2, operatorToFactor( $operator ) . "( " );

			array_push( $operatorStack, $operator );
			
			$pflag = true;
		}
		else if ( precedence( $operator ) < precedence( $topOperator ) )
		{
			
			$thisExpr = array_pop ( $expressionStack );
			
			$thisExpr .= ", " . $curFactor["result"] . " ) ";
												
			array_push( $expressionStack, $thisExpr );
						
			while( precedence( $operator ) < precedence( $topOperator ) )
			{
						
				$parenCount = array_pop( $precedenceStack );
				$thisExpr = array_pop( $expressionStack );
								
				$thisExpr2 = array_pop( $expressionStack ); // Here's my bug.
				$thisExpr3 = array_pop( $expressionStack2 );
				if ( trim( $thisExpr2 ) == "" )
				{
					$thisExpr2 = $thisExpr3 . $thisExpr;
				}
				else
				{
					$thisExpr2 .= ", " . $thisExpr3 . $thisExpr . " ) ";
				}
				
				array_push( $expressionStack, $thisExpr2 );
				
				array_pop( $operatorStack );
				
				array_pop( $operatorStack );
				
				$topOperator = array_pop( $operatorStack );
				array_push( $operatorStack, $topOperator );
			}
			
			$thisExpr = array_pop( $expressionStack2 );
			
			$thisExpr = operatorToFactor( $operator ) . " ( " . $thisExpr;
			
			array_push( $expressionStack2, $thisExpr );
			
			$pflag = false;
			
		}
		else
		{
					$tempExpr = array_pop( $expressionStack );
			$tempExpr2 = array_pop( $expressionStack2 );
			if ( $pflag )
				array_push( $expressionStack, $tempExpr . ", " . $curFactor["result"] . " ) " );
			else
				array_push( $expressionStack, $tempExpr . ", " . $curFactor["result"] . " ) " );
			

			
			$pflag = false;
			
			array_push( $expressionStack2, operatorToFactor( $operator ) . "( " . $tempExpr2 );
			array_push( $precedenceStack, array_pop( $precedenceStack ) + 1 );
			

		}		
		
		$tempExpr = array_pop( $expressionStack );
		array_push( $expressionStack, $tempExpr );
					
	}
	
	$thisExpr = array_pop( $expressionStack );
	
	$thisExpr .= ", " . $curFactor["result"] . " ) ";
	
	array_push( $expressionStack, $thisExpr );
	
	
	while( count( $operatorStack ) >= 1 )
	{
	
		$parenCount = array_pop( $precedenceStack );
		$thisExpr = array_pop( $expressionStack );
		
		$thisExpr2 = array_pop( $expressionStack );
		$thisExpr3 = array_pop( $expressionStack2 );
		
		if ( trim( $thisExpr2 ) == "" )
		{
			$thisExpr2 = $thisExpr3 . $thisExpr;
		}
		else
		{
			$thisExpr2 .= ", " . $thisExpr3 . $thisExpr . " ) ";
		}
				
		array_push( $expressionStack, $thisExpr2 );
		
		array_pop( $operatorStack );
		
	}

	
	$tempExpr2 = array_pop( $expressionStack2 );
	$tempExpr = array_pop( $expressionStack );
	
	return array( "result" => $tempExpr2 . $tempExpr, "position" => $i );

}

function parseFactor( $factor )
{
	if ( array_key_exists( "operator", $factor ) )
	{
		$factor1 = parseFactor( $factor["factor1"] );
		$factor2 = parseFactor( $factor["factor2"] );
		
		return $factor["operator"] . " ( " . $factor1 . ", " . $factor2 . " ) ";
	}
	else
	{
		return $factor["result"];
	}
}

function mapKeyWord( $keyword )
{
	$keywords =	array( "", "zero", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine", "return", "quote", "empty", "true", "false" );
	$keyvalues =	array( "", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "numtochar( 10 )", "numtochar( 34 )", "\"\"", "true", "false" );
		
	if ( array_search( $keyword, $keywords ) != false )
		return $keyvalues[ array_search( $keyword, $keywords ) ];
	else
		return false;
}



function isAChunkWord( $whatWord )
{
	return array_search ( $whatWord, array( "", "word", "char", "character", "paragraph", "item", "list", "line", "sentence", "element", "byte", "short", "long", "para", "sent", "elem" ) ) != false;
}

function isAPluralChunkWord( $whatWord )
{
	return array_search ( $whatWord, array( "", "words", "chars", "characters", "paragraphs", "items", "lists", "lines", "sentences", "elements", "bytes", "shorts", "longs", "paras", "sents", "elems" ) ) != false;
}

function isAnElement( $whatWord )
{
	return array_search ( $whatWord, array( "", "button", "field", "block", "buttons", "fields", "blocks", "btn", "fld", "blk", "btns", "flds", "blks" ) ) != false;
}

function magicFunction( $whatWord )
{
	$whatWord = strToLower( $whatWord );
	return array_search ( $whatWord, array( "", "numtochar", "chartonum", "length", "random", "floor", "ceil", "round", "trunc", "abs", "sgn", "sqrt", "cbrt", "exp", "exp1", "exp2", "exp10", "ln", "ln1", "log2", "log10", "sin", "cos", "tan", "csc", "sec", "cot", "asin", "acos", "acsc", "asec", "acot", "sinh", "cosh", "tanh", "csch", "sech", "coth", "asinh", "acosh", "atanh", "acsch", "asech", "acoth", "theta", "factorial", "selectedbutton" ) ) != false;
}

function magicFunctionNoPrams( $whatWord )
{
	$whatWord = strToLower( $whatWord );
	return array_search ( $whatWord, array( "", "seconds", "secs", "time", "date", "ticks", "milliseconds", "version", "mouseh", "mousev", "mouseloc", "shiftkey", "commandkey", "optionkey", "altkey", "mouse", "mouseclick", "clickloc", "clickv", "clickh" ) ) != false;
}

function isAKeyword( $whatWord )
{
	$whatWord = strToLower( $whatWord );
	return array_search ( $whatWord, array( "", "up", "down", "transparent", "opaque", "rectangle", "shadow", "popup", "scrolling", "roundrect", "oval", "standard", "default", "checkbox", "radiobutton" ) ) != false;
}

function fetchFactor( $tokens, $start )
{

	global $currentFunction;
	global $localVarRegistry;
	global $globalVarRegistry;
	global $pramsRegistry;
	
	if ( $tokens[ $start ]["value"] == "the" )
	{
		$start++;
	}
	
	while( $tokens[ $start ]["value"] == "not" || $tokens[ $start ]["value"] == "-" )
	{
		if( $tokens[ $start ]["value"] == "not" )
		{
			$prefix .= "! ";
		}
		else if( $tokens[ $start ]["value"] == "-" )
		{
			$prefix .= "- ";
		}
		$start++;
	}

	if ( $tokens[ $start ]["value"] == "field" || $tokens[ $start ]["value"] == "fld" )
	{
		if ( $tokens[ $start + 1 ]["value"] == "id" )
		{
			$factor = fetchFactor( $tokens, $start + 2 );
			$i = $factor["position"];
			return array( "position" => $i, "result" => $prefix . "hpop__getContents( document.getElementById( 'obj' + " . $factor["result"] . " ) )" );
		}
		else
			$factor = fetchFactor( $tokens, $start + 1 );
			$i = $factor["position"];
			return array( "position" => $i, "result" => $prefix . "hpop__getContents( document.getElementsByName( 'fld_' + stackID + '_' + cardID + '_' + " . $factor["result"] . "  )[0] )" );
	}
	else if ( $tokens[ $start ]["value"] == "block" || $tokens[ $start ]["value"] == "blk" )
	{
		if ( $tokens[ $start + 1 ]["value"] == "id" )
		{
			$factor = fetchFactor( $tokens, $start + 2 );
			$i = $factor["position"];
			return array( "position" => $i, "result" => $prefix . "hpop__getContents( document.getElementById( 'obj' + " . $factor["result"] . " ) )" );
		}
		else
			$factor = fetchFactor( $tokens, $start + 1 );
			$i = $factor["position"];
			return array( "position" => $i, "result" => $prefix . "hpop__getContents( document.getElementsByName( 'blk_' + stackID + '_' + cardID + '_' + " . $factor["result"] . "  )[0] )" );
	}
	// These factors are used for the styles
	else if ( $tokens[ $start ]["value"] == "check" && $tokens[ $start +1 ]["value"] == "box")
	{
		return array( "position" => $start + 2, "result" => $prefix . "'checkbox'");
	}
	else if ( $tokens[ $start ]["value"] == "round" && $tokens[ $start +1 ]["value"] == "rect")
	{
		return array( "position" => $start + 2, "result" => $prefix . "'roundrect'");
	}
	else if ( $tokens[ $start ]["value"] == "radio" && $tokens[ $start +1 ]["value"] == "button")
	{
		return array( "position" => $start + 2, "result" => $prefix . "'radiobutton'");
	}
	// This is the end of the style factors
	else if ( $tokens[ $start ]["value"] == "each" )
	{
		return array( "position" => $start + 2, "result" => $prefix . "currentElement" );
	}
	else if ( $tokens[ $start ]["value"] == "number" )
	{
		if ( isAPluralChunkWord( strtolower( $tokens[ $start + 2 ]["value"] ) ) )
 		{
 			$expr = fetchExpression( $tokens, $start + 4 );
 			return array( "position" => $expr["position"], "result" => $prefix . "cxl_count( " . $expr["result"] . ", new Array( '" . strtolower( $tokens[ $start + 2 ]["value"] ) . "' ) ) " );
 		}
		else if( isAnElement( strtolower( $tokens[ $start + 2 ]["value"] ) ) )
		{
			return array( "position" => $start + 3, "result" => $prefix . "elementCount( '" . strtolower( $tokens[ $start + 2 ]["value"] ) . "' )");
		}
		else if( strtolower( $tokens[ $start + 2 ]["value"] ) == "cards" )
		{
			return array( "position" => $start + 3, "result" => $prefix . "cardCount()");
		}
	}
	else if ( $tokens[ $start ]["value"] == "this" )
	{
		if ( $tokens[ $start + 1 ]["value"] == "block" || $tokens[ $start + 1 ]["value"] == "blk" )
		{
			return array( "position" => $start + 3, "result" => $prefix . "hpop__getBlockValue( jsCCurrentObject )" );
		}
		else if ( $tokens[ $start + 1 ]["value"] == "button" || $tokens[ $start + 1 ]["value"] == "btn" || $tokens[ $start + 1 ]["value"] == "field" || $tokens[ $start + 1 ]["value"] == "fld" )
		{
			return array( "position" => $start + 3, "result" => $prefix . "hpop__getFieldValue( jsCCurrentObject )" );
		}
	}
	else if ( $tokens[ $start ]["value"] == "me" )
	{
		return array( "position" => $start + 2, "result" => "( ( jsCCurrentObject.tagName=='DIV' ) ? jsCCurrentObject.innerHTML : jsCCurrentObject.value )" );
	}
	else if ( magicFunction( $tokens[ $start ]["value"] ) && $tokens[ $start + 1 ]["value"] == "of" )
	{
		$factor = fetchFactor( $tokens, $start + 2 );
		return array( "position" => $factor["position"], "result" => $prefix . strtolower( $tokens[ $start ]["value"] ) . "( " . $factor["result"] . " ) " );
	}
	else if ( magicFunction( $tokens[ $start ]["value"] ) && $tokens[ $start + 1 ]["value"] != "of" )
	{
		$functionName = strtolower( $tokens[ $start ]["value"] );
		$functionName = str_replace("(", "", $functionName);
		$functionName = str_replace(")", "", $functionName);
		$functionName = str_replace(" ", "", $functionName);
		
		$factor = fetchFactor( $tokens, $start + 1 );
		$factor["result"] = preg_replace("/(^\s*\()|(\)\s*$)/", "", $factor["result"]);
		
		return array( "position" => $factor["position"], "result" => $prefix . $functionName . "(" . $factor["result"] . ")");
	}
	else if ( magicFunctionNoPrams( $tokens[ $start ]["value"] ) )
	{
		$functionName = strtolower( $tokens[ $start ]["value"] );
		$functionName = str_replace("(", "", $functionName);
		$functionName = str_replace(")", "", $functionName);
		$functionName = str_replace(" ", "", $functionName);
		$functionName = $functionName . "()";
		
		return array( "position" => $start + 2, "result" => $prefix . $functionName );
	}
	else if ( isAKeyword( strtolower($tokens[ $start ]["value"]) ) )
	{
		$keyword = strtolower( $tokens[ $start ]["value"] );
		$keyword = "'" . $keyword . "'";
		
		return array( "position" => $start + 1, "result" => $prefix . $keyword );
	}
	else if( strToLower( $tokens[$start]["value"]) == "value" )
	{
		if( $tokens[ $start + 1 ]["value"] == "of" )
		{
			$factor = fetchFactor( $tokens, $start + 2 );
			return array( "position" => $factor["position"], "result" => $prefix . "eval( " . $factor["result"] . " ) " );
		}
		else
		{
			$factor = fetchFactor( $tokens, $start + 1 );
			$factor["result"] = preg_replace("/(^\s*\()|(\)\s*$)/", "", $factor["result"]);
		
			return array( "position" => $factor["position"], "result" => $prefix . "eval(" . $factor["result"] . ")");
		}
	}
	else if ( ( $tokens[ $start ]["value"] == "avg" || $tokens[ $start ]["value"] == "average" ) && $tokens[ $start + 1 ]["value"] == "of" )
	{
		$list = fetchFactorList( $tokens, $start + 2 );
		return array( "position" => $list["position"], "result" => prefix . "average( " . $list["result"] . " ) " );
	}
	else if ( ( $tokens[ $start ]["value"] == "the" && ( $tokens[ $start + 1 ]["value"] == "message" || $tokens[ $start + 1 ]["value"] == "msg" ) && $tokens[ $start + 2 ]["value"] == "box"  )||
				( ( $tokens[ $start ]["value"] == "message" || $tokens[ $start ]["value"] == "msg" ) && $tokens[ $start + 1]["value"] == "box" ) ||
				( $tokens[ $start ]["value"] == "the" && $tokens[ $start + 1 ]["value"] == "msgbox" ) ||
				( $tokens[ $start ]["value"] == "msgbox" ) )
	{
		// Weird special case for the message box, since there are so many possible abbreviations and permutations.
		$pi = $start;
		if ( $tokens[ $pi ]["value"] == "the" )
		{
			$pi ++;
		}
		
		if ( $tokens[ $pi ] == "msgbox" )
		{
			$pi += 1;
		}
		else
		{
			$pi += 2;
		}
		
		return array( "position" => $pi, "result" => "document.getElementById( 'msgboxcontent' ).value" );
		
	}
	else if ( isAChunkWord( strtolower( $tokens[ $start ]["value"] ) ) )
	{
		// Formal chunk expression
		$chunkType = strtolower( $tokens[ $start ]["value"] );
		$expr1 = fetchExpression( $tokens, $start + 1 );
		$i = $expr1["position"];
		if ( $tokens[ $i - 1 ]["value"] != "to" )
		{
			$expr2 = $expr1;
		}
		else
		{
			$expr2 = fetchExpression( $tokens, $i );
			$i = $expr2["position"];
		}
		
		$expr3 = fetchFactor( $tokens, $i );
		$i = $expr3["position"];
		
		return array( "position" => $i, "result" => $prefix . "cxl_get( " . $expr3["result"] . ", new Array( '" . strtolower( $chunkType ) . "', " . $expr1["result"] . ", " . $expr2["result"] . " ) )" );
	}
	else if ( $tokens[ $start ]["value"] == "block" || $tokens[ $start ]["value"] == "blk" )
	{
		if ( $tokens[ $start + 1 ]["value"] == "id" )
		{
			$factor = fetchFactor( $tokens, $start + 2 );
			$i = $factor["position"];
			return array( "position" => $i, "result" => $prefix . "hpop__getBlockValue( document.getElementById( 'obj' + " . $factor["result"] . " ) )" );
		}
		else
			$factor = fetchFactor( $tokens, $start + 1 );
			$i = $factor["position"];
			return array( "position" => $i, "result" => $prefix . "hpop__getBlockValue( document.getElementsByName(" . $factor["result"] . " )[0] )" );
	}
	else if ( $tokens[ $start ]["value"] == "(" )
	{
		$expr = fetchExpression( $tokens, $start + 1 );
		$i = $expr["position"];
		return array( "position" => $i + 1, "result" => $prefix . "( " . $expr["result"] ." )" );
	}
	else if ( $property = fetchObjectProperty ( $tokens, $start ) )
	{
		$i = $property["position"] + 1;
		$property = $property["result"];
		$object = fetchObject( $tokens, $i );
		
		return array( "position" => $object["position"], "result" => $prefix . str_replace( "@", $object["result"], $property ) );
		
	}
	else if ( $property = fetchInlineGlobalProperty ( $tokens, $start ) )
	{
		$i = $property["position"];
		$property = $property["result"];
		
		return array( "position" => $i+1, "result" => $prefix . $property );
		
	}
	else if ( $tokens[ $start ]["type"] == TOKEN_STATE_STRING )
	{
		$myReturnValue = '"' . $tokens[ $start ]["value"] . '"';
	}
	else
	{
		// Functions
		if ( $tokens[ $start ]["type"] == TOKEN_STATE_IDENTIFIER && $tokens[ $start + 1 ]["value"] == "(" )
		{
			// We've found a function.
			if ( $tokens[ $start + 2 ]["value"] == ")" )
			{
				// Empty Function
				return array( "position" => $i + 5, "result" => $prefix . $tokens[ $start ]["value"] . "( ) " );
			}
			else
			{
				$myReturnValue = $tokens[ $start ]["value"] . "( ";
				$expr = fetchExpression( $tokens, $start + 2 );
				$i = $expr["position"];
				while( $tokens[ $i - 1 ]["value"] != ")" )
				{
					$myReturnValue .= $expr["result"] . ", ";
					$expr = fetchExpression( $tokens, $i );
					$i = $expr["position"];
				}
				
				$myReturnValue .= $expr["result"] . " )";
				return array( "position" => $i + 1, "result" => $prefix . $myReturnValue );
			}
		}
		else
		{
			if ( is_numeric ( trim( $tokens[ $start ]["value"] ) ) )
			{
				$myReturnValue = $tokens[ $start ]["value"];
			}
			else if ( $kw = mapKeyWord( trim( $tokens[ $start ]["value"] ) ) )
			{
				$myReturnValue = $kw;
			}
			else
			{
		
				$curF = array_pop( $currentFunction );
				array_push( $currentFunction, $curF );
				
				$gvr = array_pop( $globalVarRegistry );
				array_push( $globalVarRegistry, $gvr );
				
				$lvr = array_pop( $localVarRegistry );
				array_push( $localVarRegistry, $lvr );
				
				$prr = array_pop( $pramsRegistry );
				array_push( $pramsRegistry, $prr );
						
				if ( $curF != "global" && ! in_array( strtolower( $tokens[ $start ]["value"] ), $lvr[$curF] ) && ! in_array( strtolower( $tokens[ $start ]["value"] ), $gvr ) && ! in_array( strtolower( $tokens[ $start ]["value"] ), $prr ) )
				{
					array_push( $lvr[$curF], strtolower( $tokens[ $start ]["value"] ) );
					array_pop( $localVarRegistry );
					array_push( $localVarRegistry, $lvr );
				}

				$myReturnValue = strtolower( $tokens[ $start ]["value"] );
			}
		}
	}
	return array( "position" => $start + 2, "result" => $prefix . $myReturnValue );
}

function fetchObject( $tokens, $start )
{
	if( $tokens[ $start ]["value"] == "field" || $tokens[ $start ]["value"] == "fld"  )
	{
		if ( $tokens[ $start + 1 ]["value"] == "id" )
		{
			$factor = fetchFactor( $tokens, $start + 2 );
			$i = $factor["position"];
			return array( "position" => $i, "result" => "document.getElementById( 'obj' + " . $factor["result"] . ")" );
		}
		else
		{
			$factor = fetchFactor( $tokens, $start + 1 );
			$i = $factor["position"];
			//return array( "position" => $i, "result" => "document.getElementsByName( \"fld_\" + stackID + \"_\" + cardID + \"_\" + " . $factor["result"] . ")[0]" );
			return array( "position" => $i, "result" => "getFieldNameOrNumber( " . $factor["result"] . " )" );
		}
	}
	else if ( $tokens[ $start ]["value"] == "block" || $tokens[ $start ]["value"] == "blk"  )
	{
		if ( $tokens[ $start + 1 ]["value"] == "id" )
		{
			$factor = fetchFactor( $tokens, $start + 2 );
			$i = $factor["position"];
			return array( "position" => $i, "result" => "document.getElementById( 'obj' + " . $factor["result"] . " )" );
		}
		else
		{
			$factor = fetchFactor( $tokens, $start + 1 );
			$i = $factor["position"];
			//return array( "position" => $i, "result" => "document.getElementsByName( \"blk_\" + stackID + \"_\" + cardID + \"_\" + " . $factor["result"] . " )[0]" );
			return array( "position" => $i, "result" => "getBlockNameOrNumber( " . $factor["result"] . " )" );
		}
	}
	else if ( $tokens[ $start ]["value"] == "button" || $tokens[ $start ]["value"] == "btn"  )
	{
		if ( $tokens[ $start + 1 ]["value"] == "id" )
		{
			$factor = fetchFactor( $tokens, $start + 2 );
			$i = $factor["position"];
			return array( "position" => $i, "result" => "document.getElementById( 'obj' + " . $factor["result"] . " )" );
		}
		else
		{
			$factor = fetchFactor( $tokens, $start + 1 );
			$i = $factor["position"];
			//return array( "position" => $i, "result" => "document.getElementsByName( \"btn_\" + stackID + \"_\" + cardID + \"_\" + " . $factor["result"] . " )[0]" );
			return array( "position" => $i, "result" => "getButtonNameOrNumber( " . $factor["result"] . " )" );
		}
	}
	else if ( $tokens[ $start ]["value"] == "card" || $tokens[ $start ]["value"] == "cd"  )
	{
		if ( $tokens[ $start + 1 ]["value"] == "id" )
		{
			$factor = fetchFactor( $tokens, $start + 2 );
			$i = $factor["position"];
			return array( "position" => $i, "result" => "document.getElementById( 'card_' + " . $factor["result"] . " )" );
		}
		else
		{
			$factor = fetchFactor( $tokens, $start + 1 );
			$i = $factor["position"];
			//return array( "position" => $i, "result" => "document.getElementsByName( \"card_\" + " . $factor["result"] . " )[0]" );
			return array( "position" => $i, "result" => "getCardNameOrNumber( " . $factor["result"] . " )" );
		}
	}
	else if ( $tokens[ $start ]["value"] == "this" )
	{
		if( $tokens[$start + 1]["value"] == "button" || $tokens[$start + 1]["value"] == "field" || $tokens[$start + 1]["value"] == "block" || $tokens[$start + 1]["value"] == "btn" || $tokens[$start + 1]["value"] == "fld" || $tokens[$start + 1]["value"] == "blk")
		{
			return array( "position" => $start + 3, "result" => "jsCCurrentObject" );
		}
		else if( $tokens[$start + 1]["value"] == "card" )
		{
			return array( "position" => $i, "result" => "document.getElementById( 'card_' + getCurrentCardID() )" );
		}
		else if( $tokens[$start + 1]["value"] == "stack" )
		{
			return array( "position" => $i, "result" => "document.getElementById( 'stack' )" );
		}
	}
	else if ( $tokens[ $start ]["value"] == "me" )
	{
		return array( "position" => $start + 2, "result" => "jsCCurrentObject" );
	}
	else
	{
		return array( "position" => $start+1, "result" => $tokens[ $start ]["value"] );
	}
}

function fetchFactorList( $tokens, $start )
{
	$currentList = "";
	$count = 0;
		
	do
	{
	
	$factor = fetchFactor( $tokens, $start );
	
	$start = $factor["position"] - 1;
	$currentList .= $factor["result"] . ", ";
	
	$start++;
	$count++;
	
	} while( $tokens[ $start - 1 ]["value"] == "," );
	
	return array( "position" => $start - 1, "totalFactors" => $count, "result" => substr( $currentList, 0, strlen( $currentList ) - 2 ) );
	
}

function setObjectProperty( $tokens, $start )
{

	if( $tokens[$start]["value"] == "the" )
	{
		$start ++;
	}

	if ( $tokens[$start]["value"] == "top" )
	{
		return array( "position" => $start + 1, "result" => "@.style.top = ( % );" );
	}
	else if ( $tokens[$start]["value"] == "left" )
	{
		return array( "position" => $start + 1, "result" => "@.style.left = ( % );" );
	}
	if ( $tokens[$start]["value"] == "width" )
	{
		return array( "position" => $start + 1, "result" => "@.style.width = ( % );" );
	}
	else if ( $tokens[$start]["value"] == "height" )
	{
		return array( "position" => $start + 1, "result" => "@.style.height = ( % );" );
	}
	if ( $tokens[$start]["value"] == "bottom" )
	{
		return array( "position" => $start + 1, "result" => "@.style.top = ( % - (@.offsetHeight) );" );
	}
	else if ( $tokens[$start]["value"] == "right" )
	{
		return array( "position" => $start + 1, "result" => "@.style.left = ( % - (@.offsetWidth) );" );
	}
	else if ( $tokens[$start]["value"] == "topleft" )
	{
		return array( "position" => $start + 1, "result" => "hpop__setTopLeft( @, % );" );
	}
	else if ( $tokens[$start]["value"] == "bottomright" )
	{
		return array( "position" => $start + 1, "result" => "hpop__setBottomRight( @, % );" );
	}
	else if ( $tokens[$start]["value"] == "location" || $tokens[$start]["value"] == "loc" )
	{
		return array( "position" => $start + 1, "result" => "hpop__setLocation( @, % );" );
	}
	else if ( $tokens[$start]["value"] == "visible" )
	{
		return array( "position" => $start + 1, "result" => "hpop__setVisible( @, % );" );
	}
	else if ( $tokens[$start]["value"] == "hidden" )
	{
		return array( "position" => $start + 1, "result" => "hpop__setVisible( @, ! % );" );
	}
	else if ( $tokens[$start]["value"] == "name" )
	{
		return array( "position" => $start + 1,"result" => "hpop__setName( @, % );" );
	}
	else if ( $tokens[$start]["value"] == "enabled" )
	{
		return array( "position" => $start + 1,"result" => "hpop__setEnabled( @, % );" );
	}
	else if ( $tokens[$start]["value"] == "script" )
	{
		return array( "position" => $start + 1,"result" => "hpop__setScript( @, % );" );
	}
	else if ( $tokens[ $start ]["value"] == "locktext" )
	{
		return array( "position" => $start + 1, "result" => "hpop__setLockText( @, % );" );
	}
	else if ( $tokens[$start]["value"] == "lock" && $tokens[$start+1]["value"] == "text" )
	{
		return array( "position" => $start + 2, "result" => "hpop__setLockText( @, % );" );
	}
	else if ( $tokens[ $start ]["value"] == "autohilite" )
	{
		return array( "position" => $start + 1, "result" => "hpop__setAutoHilite( @, % );" );
	}
	else if ( $tokens[$start]["value"] == "auto" && $tokens[$start+1]["value"] == "hilite" )
	{
		return array( "position" => $start + 2, "result" => "hpop__setAutoHilite( @, % );" );
	}
	else if ( $tokens[ $start ]["value"] == "autohighlight" )
	{
		return array( "position" => $start + 1, "result" => "hpop__setAutoHilite( @, % );" );
	}
	else if ( $tokens[$start]["value"] == "auto" && $tokens[$start+1]["value"] == "highlight" )
	{
		return array( "position" => $start + 2, "result" => "hpop__setAutoHilite( @, % );" );
	}
	else if ( $tokens[ $start ]["value"] == "checkable" )
	{
		return array( "position" => $start + 1, "result" => "hpop__setCheckable( @, % );" );
	}
	else if ( $tokens[ $start ]["value"] == "style" )
	{
		return array( "position" => $start + 1, "result" => "hpop__setStyle( @, % );" );
	}
	else if ( $tokens[ $start ]["value"] == "family" )
	{
		return array( "position" => $start + 1, "result" => "hpop__setFamily( @, % );" );
	}
	else if ( $tokens[ $start ]["value"] == "hilite" )
	{
		return array( "position" => $start + 1, "result" => "hpop__setHilite( @, % );" );
	}
	else if ( $tokens[ $start ]["value"] == "autoselect" )
	{
		return array( "position" => $start + 1, "result" => "hpop__setAutoSelect( @, % );" );
	}
	else if ( $tokens[$start]["value"] == "auto" && $tokens[$start+1]["value"] == "select" )
	{
		return array( "position" => $start + 2, "result" => "hpop__setAutoSelect( @, % );" );
	}
	else if ( $tokens[ $start ]["value"] == "dontwrap" )
	{
		return array( "position" => $start + 1, "result" => "hpop__setDontWrap( @, % );" );
	}
	else if ( $tokens[$start]["value"] == "dont" && $tokens[$start+1]["value"] == "wrap" )
	{
		return array( "position" => $start + 2, "result" => "hpop__setDontWrap( @, % );" );
	}
	else if ( $tokens[ $start ]["value"] == "multiplelines" )
	{
		return array( "position" => $start + 1, "result" => "hpop__setMultipleLines( @, % );" );
	}
	else if ( $tokens[$start]["value"] == "multiple" && $tokens[$start+1]["value"] == "lines" )
	{
		return array( "position" => $start + 2, "result" => "hpop__setMultipleLines( @, % );" );
	}
	else if ( $tokens[ $start ]["value"] == "showname" )
	{
		return array( "position" => $start + 1, "result" => "hpop__setShowName( @, % );" );
	}
	else if ( $tokens[ $start ]["value"] == "image" )
	{
		return array( "position" => $start + 1, "result" => "hpop__setImage( @, % );" );
	}
	else if ( $tokens[ $start ]["value"] == "rect" || $tokens[ $start ]["value"] == "rectangle" )
	{
		return array( "position" => $start + 1, "result" => "hpop__setRect( @, % );" );
	}
	else
	{
		return false;
	}
}

function fetchGlobalProperty( $tokens, $start )
{

	if( $tokens[$start]["value"] == "the" )
	{
		$start ++;
	}

	if ( strtolower($tokens[$start]["value"]) == "itemdel" || strtolower($tokens[$start]["value"] == "itemdelimiter") )
	{
		return array( "position" => $start + 1, "result" => "cxl_item_delimiter = % ;" );
	}
	else
	{
		return false;
	}
}

function fetchObjectProperty( $tokens, $start )
{

	if( $tokens[$start]["value"] == "the" )
	{
		$start ++;
	}

	if ( $tokens[$start]["value"] == "top" )
	{
		return array( "position" => $start + 1, "result" => "parseInt(@.style.top,10)" );
	}
	else if ( $tokens[$start]["value"] == "left" )
	{
		return array( "position" => $start + 1, "result" => "parseInt(@.style.left,10)" );
	}
	if ( $tokens[$start]["value"] == "width" )
	{
		return array( "position" => $start + 1, "result" => "parseInt(@.style.width,10)" );
	}
	else if ( $tokens[$start]["value"] == "height" )
	{
		return array( "position" => $start + 1, "result" => "parseInt(@.style.height,10)" );
	}
	if ( $tokens[$start]["value"] == "bottom" )
	{
		return array( "position" => $start + 1, "result" => "(parseInt(@.offsetTop,10)) + (parseInt(@.offsetHeight,10))" );
	}
	else if ( $tokens[$start]["value"] == "right" )
	{
		return array( "position" => $start + 1, "result" => "(parseInt(@.offsetLeft,10)) + (parseInt(@.offsetWidth,10))" );
	}
	else if ( $tokens[$start]["value"] == "topleft" )
	{
		return array( "position" => $start + 1, "result" => "hpop__getTopLeft( @ )" );
	}
	else if ( $tokens[$start]["value"] == "bottomright" )
	{
		return array( "position" => $start + 1, "result" => "hpop__getBottomRight( @ )" );
	}
	else if ( $tokens[$start]["value"] == "location" || $tokens[$start]["value"] == "loc" )
	{
		return array( "position" => $start + 1, "result" => "hpop__getLocation( @ )" );
	}
	else if ( $tokens[$start]["value"] == "visible" )
	{
		return array( "position" => $start + 1, "result" => "hpop__getVisible( @ )" );
	}
	else if ( $tokens[$start]["value"] == "hidden" )
	{
		return array( "position" => $start + 1, "result" => "! hpop__getVisible( @ )" );
	}
	else if ( $tokens[$start]["value"] == "name" )
	{
		return array( "position" => $start + 1, "result" => "hpop__getShortName( @ )" );
	}
	else if ( $tokens[$start]["value"] == "enabled" )
	{
		return array( "position" => $start + 1, "result" => "hpop__getEnabled( @ )" );
	}
	else if ( $tokens[$start]["value"] == "script" )
	{
		return array( "position" => $start+1, "result" => "hpop__getScript( @ )" );
	}
	else if ( $tokens[$start]["value"] == "locktext" )
	{
		return array( "position" => $start+1, "result" => "hpop__getLockText( @ )" );
	}
	else if ( $tokens[$start]["value"] == "lock" && $tokens[$start+1]["value"] == "text" )
	{
		return array( "position" => $start+2, "result" => "hpop__getLockText( @ )" );
	}
	else if ( $tokens[$start]["value"] == "autohilite" )
	{
		return array( "position" => $start+1, "result" => "hpop__getAutoHilite( @ )" );
	}
	else if ( $tokens[$start]["value"] == "auto" && $tokens[$start+1]["value"] == "hilite" )
	{
		return array( "position" => $start+2, "result" => "hpop__getAutoHilite( @ )" );
	}
	else if ( $tokens[$start]["value"] == "autohighlight" )
	{
		return array( "position" => $start+1, "result" => "hpop__getAutoHilite( @ )" );
	}
	else if ( $tokens[$start]["value"] == "auto" && $tokens[$start+1]["value"] == "highlight" )
	{
		return array( "position" => $start+2, "result" => "hpop__getAutoHilite( @ )" );
	}
	else if ( $tokens[$start]["value"] == "checkable" )
	{
		return array( "position" => $start+1, "result" => "hpop__getCheckable( @ )" );
	}
	else if ( $tokens[$start]["value"] == "style" )
	{
		return array( "position" => $start+1, "result" => "hpop__getStyle( @ )" );
	}
	else if ( $tokens[$start]["value"] == "family" )
	{
		return array( "position" => $start+1, "result" => "hpop__getFamily( @ )" );
	}
	else if ( $tokens[$start]["value"] == "hilite" || $tokens[$start]["value"] == "highlight" )
	{
		return array( "position" => $start+1, "result" => "hpop__getHilite( @ )" );
	}
	else if ( $tokens[$start]["value"] == "autoselect" )
	{
		return array( "position" => $start+1, "result" => "hpop__getAutoSelect( @ )" );
	}
	else if ( $tokens[$start]["value"] == "auto" && $tokens[$start+1]["value"] == "select" )
	{
		return array( "position" => $start+2, "result" => "hpop__getAutoSelect( @ )" );
	}
	else if ( $tokens[$start]["value"] == "dontwrap" )
	{
		return array( "position" => $start+1, "result" => "hpop__getDontWrap( @ )" );
	}
	else if ( $tokens[$start]["value"] == "type" )
	{
		return array( "position" => $start+1, "result" => "hpop__getType( @ )" );
	}
	else if ( $tokens[$start]["value"] == "multiplelines" )
	{
		return array( "position" => $start+1, "result" => "hpop__getMultipleLines( @ )" );
	}
	else if ( $tokens[$start]["value"] == "multiple" && $tokens[$start+1]["value"] == "lines" )
	{
		return array( "position" => $start+2, "result" => "hpop__getMultipleLines( @ )" );
	}
	else if ( $tokens[$start]["value"] == "showname" )
	{
		return array( "position" => $start+1, "result" => "hpop__getShowName( @ )" );
	}
	else if ( $tokens[$start]["value"] == "image" )
	{
		return array( "position" => $start+1, "result" => "hpop__getImage( @ )" );
	}
	else if ( $tokens[$start]["value"] == "rect" || $tokens[$start]["value"] == "rectangle" )
	{
		return array( "position" => $start+1, "result" => "hpop__getRect( @ )" );
	}
	else
	{
		return false;
	}
}

function fetchInlineGlobalProperty( $tokens, $start )
{
	if( $tokens[$start]["value"] == "the" )
	{
		$start ++;
	}
	
	if ( strtolower($tokens[$start]["value"]) == "itemdel" || strtolower($tokens[$start]["value"] == "itemdelimiter") )
	{
		return array( "position" => $start + 1, "result" => "cxl_item_delimiter" );
	}
	else if ( strtolower($tokens[$start]["value"]) == "address" || strtolower($tokens[$start]["value"] == "ip") )
	{
		return array( "position" => $start + 1, "result" => "hpop__getAddress()" );
	}
	else
	{
		return false;
	}
}

function fetchContainer( $tokens, $start, $place )
{
	global $localVarRegistry;
	global $globalVarRegistry;
	global $currentFunction;
	global $pramsRegistry;
	
	if ( $tokens[ $start ]["value"] == "field" || $tokens[ $start ]["value"] == "fld" )
	{
		if ( $tokens[ $start + 1 ]["value"] == "id" )
		{
			$factor = fetchFactor( $tokens, $start + 2 );
			$i = $factor["position"];
			if ( $place == "into" )
			{
				//return array( "position" => $i, "result" => "document.getElementById( 'obj' + " . $factor["result"] . ").value = @;" );
				return array( "position" => $i, "result" => "hpop__setFieldValue( document.getElementById( 'obj' + " . $factor["result"] . "), @, 'into' );" );
			}
			else if ( $place == "after" )
			{
				//return array( "position" => $i, "result" => "document.getElementById( 'obj' + " . $factor["result"] . ").value += '' + ( @ );" );
				return array( "position" => $i, "result" => "hpop__setFieldValue( document.getElementById( 'obj' + " . $factor["result"] . "), @, 'after');" );
			}
			else if ( $place == "before" )
			{
				//return array( "position" => $i, "result" => "document.getElementById( 'obj' + " . $factor["result"] . ").value = ( @ ) + '' + document.getElementById( 'obj' + " . $factor["result"] . ").value;" );
				return array( "position" => $i, "result" => "hpop__setFieldValue( document.getElementById( 'obj' + " . $factor["result"] . "), @, 'before');" );
			}
		}
		else
			$factor = fetchFactor( $tokens, $start + 1 );
			$i = $factor["position"];
			if ( $place == "into" )
			{
				//return array( "position" => $i, "result" => "document.getElementsByName( \"fld_\" + stackID + \"_\" + cardID + \"_\" + " . $factor["result"] . ")[0].value = @;" );
				return array( "position" => $i, "result" => "hpop__setFieldValue( document.getElementsByName( \"fld_\" + stackID + \"_\" + cardID + \"_\" + " . $factor["result"] . ")[0], @, 'into');" );
			}
			else if ( $place == "after" )
			{
				//return array( "position" => $i, "result" => "document.getElementsByName( \"fld_\" + stackID + \"_\" + cardID + \"_\" + " . $factor["result"] . ")[0].value += '' + ( @ );" );
				return array( "position" => $i, "result" => "hpop__setFieldValue( document.getElementsByName( \"fld_\" + stackID + \"_\" + cardID + \"_\" + " . $factor["result"] . ")[0], @, 'after');" );
			}
			else if ( $place == "before" )
			{
				//return array( "position" => $i, "result" => "document.getElementsByName( \"fld_\" + stackID + \"_\" + cardID + \"_\" + " . $factor["result"] . ")[0].value = ( @ ) + '' + document.getElementsByName( \"fld_\" + stackID + \"_\" + cardID + \"_\" + " . $factor["result"] . ")[0].value;" );
				return array( "position" => $i, "result" => "hpop__setFieldValue( document.getElementsByName( \"fld_\" + stackID + \"_\" + cardID + \"_\" + " . $factor["result"] . ")[0], @, 'before');" );
			}
	}
	else if ( $tokens[ $start ]["value"] == "block" || $tokens[ $start ]["value"] == "blk" )
	{
		if ( $tokens[ $start + 1 ]["value"] == "id" )
		{
			$factor = fetchFactor( $tokens, $start + 2 );
			$i = $factor["position"];
			if ( $place == "into" )
			{
				return array( "position" => $i, "result" => "hpop__setBlockValue( document.getElementById( 'obj' + " . $factor["result"] . " ), @, 'into' );" );
			}
			else if ( $place == "after" )
			{
				//return array( "position" => $i, "result" => "document.getElementById( 'obj' + " . $factor["result"] . " ).innerHTML += '' + ( @ );" );
				return array( "position" => $i, "result" => "hpop__setBlockValue( document.getElementById( 'obj' + " . $factor["result"] . " ), @, 'after' );" );
			}
			else if ( $place == "before" )
			{
				//return array( "position" => $i, "result" => "document.getElementById( 'obj' + " . $factor["result"] . " ).innerHTML = ( @)  + '' + document.getElementById( 'obj' + " . $factor["result"] . " ).innerHTML;" );
				return array( "position" => $i, "result" => "hpop__setBlockValue( document.getElementById( 'obj' + " . $factor["result"] . " ), @, 'before' );" );
			}
		}
		else
			$factor = fetchFactor( $tokens, $start + 1 );
			$i = $factor["position"];
			if ( $place == "into" )
			{
				//return array( "position" => $i, "result" => "document.getElementsByName( \"blk_\" + stackID + \"_\" + cardID + \"_\" + " . $factor["result"] . " )[0].innerHTML = @;" );
				return array( "position" => $i, "result" => "hpop__setBlockValue( document.getElementsByName( \"blk_\" + stackID + \"_\" + cardID + \"_\" + " . $factor["result"] . " )[0], @, 'into' );" );
			}
			else if ( $place == "after" )
			{
				//return array( "position" => $i, "result" => "document.getElementsByName( \"blk_\" + stackID + \"_\" + cardID + \"_\" + " . $factor["result"] . " )[0].innerHTML .= '' + ( @ );" );
				return array( "position" => $i, "result" => "hpop__setBlockValue( document.getElementsByName( \"blk_\" + stackID + \"_\" + cardID + \"_\" + " . $factor["result"] . " )[0], @, 'after' );" );
			}
			else if ( $place == "before" )
			{
				//return array( "position" => $i, "result" => "document.getElementsByName( \"blk_\" + stackID + \"_\" + cardID + \"_\" + " . $factor["result"] . " )[0].innerHTML = ( @ ) + '' + document.getElementsByName( \"blk_\" + stackID + \"_\" + cardID + \"_\" + " . $factor["result"] . " )[0].innerHTML;" );
				return array( "position" => $i, "result" => "hpop__setBlockValue( document.getElementsByName( \"blk_\" + stackID + \"_\" + cardID + \"_\" + " . $factor["result"] . " )[0], @, 'before' );" );
			}
	}
	else if ( ( $tokens[ $start ]["value"] == "the" && ( $tokens[ $start + 1 ]["value"] == "message" || $tokens[ $start + 1 ]["value"] == "msg" ) && $tokens[ $start + 2 ]["value"] == "box"  )||
				( ( $tokens[ $start ]["value"] == "message" || $tokens[ $start ]["value"] == "msg" ) && $tokens[ $start + 1]["value"] == "box" ) ||
				( $tokens[ $start ]["value"] == "the" && $tokens[ $start + 1 ]["value"] == "msgbox" ) ||
				( $tokens[ $start ]["value"] == "msgbox" ) )
	{
		// Weird special case for the message box, since there are so many possible abbreviations and permutations.
		$pi = $start;
		if ( $tokens[ $pi ]["value"] == "the" )
		{
			$pi ++;
		}
		
		if ( $tokens[ $pi ] == "msgbox" )
		{
			$pi += 2;
		}
		else
		{
			$pi += 3;
		}
		
		return array( "position" => $pi, "result" => "document.getElementById( 'msgboxcontent' ).value = @;" );
		
	}
	else if( isAChunkWord( $tokens[ $start ]["value"] ) )
	{
		// Formal chunk expression
		$chunkArray = "";
		$i = $start + 1;
		
		while( isAChunkWord( strtolower( $tokens[ $i - 1 ]["value"] ) ) )
		{
		  $chunkArray .= "'" . strtolower( $tokens[ $i - 1 ]["value"] ) . "'";
		  $expr1 = fetchExpression( $tokens, $i );
		  $i = $expr1["position"];
		  
		  $chunkArray .= ", ";
		  if ( $tokens[ $i - 1 ]["value"] != "to" )
		  {
			  $expr2 = $expr1;
		  }
		  else
		  {
			  $expr2 = fetchExpression( $tokens, $i );
			  $i = $expr2["position"];
		  }
		  $chunkArray .= $expr1["result"] . ", " . $expr2["result"] . ", ";
		  $i++;
		}
		
		$expr4 = fetchExpression( $tokens, $i - 1 );
		
		$expr3 = fetchContainer( $tokens, $i - 1, 'into' );
		$i = $expr3["position"];

		$chunkArray = "new Array( " . substr( $chunkArray, 0, strlen( $chunkArray ) - 2 ) . " ) ";
		
		if ( $place == "into" )
		{
			return array( "position" => $i, "result" => $prefix . "cxl_into( " . $expr4["result"] . ", " . $chunkArray . ", @ )", "assign" => $expr3["result"] );
		}
		else if ( $place == "after" )
		{
			return array( "position" => $i, "result" => $prefix . "cxl_after( " . $expr3["result"] . ", " . $chunkArray . ", @ )", "assign" => $expr3["result"] );
		}	
		else if ( $place == "before" )
		{
			return array( "position" => $i, "result" => $prefix . "cxl_before( " . $expr3["result"] . ", " . $chunkArray . ", @ )", "assign" => $expr3["result"] );
		}
	}
	else
	{
		
		$curF = array_pop( $currentFunction );
		array_push( $currentFunction, $curF );
		
		$gvr = array_pop( $globalVarRegistry );
		array_push( $globalVarRegistry, $gvr );
		
		$lvr = array_pop( $localVarRegistry );
		array_push( $localVarRegistry, $lvr );
		
		$prr = array_pop( $pramsRegistry );
		array_push( $pramsRegistry, $prr );
				
		if ( $curF != "global" && ! in_array( strtolower( $tokens[ $start ]["value"] ), $lvr[$curF] ) && ! in_array( strtolower( $tokens[ $start ]["value"] ) , $gvr ) && ! in_array( strtolower( $tokens[ $start ]["value"] ), $prr ) )
		{
			array_push( $lvr[$curF], strtolower( $tokens[ $start ]["value"] ) );
			array_pop( $localVarRegistry );
			array_push( $localVarRegistry, $lvr );
		}
		
		if ( $place == "into" )
		{
			return array( "position" => $start+1, "result" => strtolower( $tokens[ $start ]["value"] ) . " = @;" );
		}
		else if ( $place == "after" )
		{
			return array( "position" => $start+1, "result" => strtolower( $tokens[ $start ]["value"] ) . " += '' + ( @ );" );
		}
		else if ( $place == "before" )
		{
			return array( "position" => $start+1, "result" => strtolower( $tokens[ $start ]["value"] ) . " = ( @ ) + '' + " . strtolower( $tokens[ $start ]["value"] ) . ";" );
		}
	}
}

function stripComments( $someline )
{
	$inquote = false;
	for( $i = 0; $i < strlen( $someline ); $i++ )
	{
		if ( $someline[$i] == "\"" )
		{
			if ( $inquote )
			{
				$inquote = false;
			}
			else
			{
				$inquote = true;
			}
		}
		else if ( $someline[$i] == "-" && $someline[$i+1] == "-" && !$inquote )
		{
			return ( trim( substr( $someline, 0, $i ) ) );
		}
	}
	return $someline;
}

function compile( $script, $noheader = false )
{
	clearLog();
	logThis($script);
	
	global $inlineFlagStack;
	global $localVarRegistry;
	global $currentFunction;
	global $globalVarRegistry;
	global $pramsRegistry;

	
	if ( ! is_array( $inlineFlagStack ) )
	{
		$inlineFlagStack = array();
	}
	
	if ( ! is_array( $pramsRegistry ) )
	{
		$pramsRegistry = array();
	}
	
	array_push( $pramsRegistry, array() );
	
	if( ! is_array( $localVarRegistry ) )
	{
		$localVarRegistry = array();
	}
	
	if( ! is_array( $currentFunction ) )
	{
		$currentFunction = array();
	}
	
	if( ! is_array( $globalVarRegistry ) )
	{
		$globalVarRegistry = array();
	}
	
	array_push( $currentFunction, "global" );
	array_push( $localVarRegistry, array( "", array() ) );
	array_push( $inlineFlagStack, false );

	$source = "";
	
	$script = explode( "\n", $script );
	
	if( ! $noheader ) { $source  .= "/* Generated By jsCc */\n\n"; }
	
	if( $script[0] == "/* Generated By jsCc */" )
	{
		return ( "[jsCc: error: Line 1] Cannot compile already compiled code." );
	}
	
	$doCompile = true;
	

	
	foreach ( $script as $scriptLine )
	{
	
		$scriptLine = stripComments( $scriptLine );
		if ( $scriptLine != "" )
		{
			if ( $scriptLine == "[begin js]" )
			{
				$doCompile = false;
				continue;
			}
			
			if ( $doCompile == true )
			{
				$source .= ((!$noheader) ? "\n// " . $scriptLine . "\n" : "" ) .  compileLine( tokenize( $scriptLine ) );
			}
			else
			{
				if ( $scriptLine == "[end js]" )
				{
					$doCompile = true;
				}
				else
				{
					$source .= $scriptLine . "\n"; 
				}
			}
		}
	}
	
	array_pop( $inlineFlagStack );
	
	$lvr = array_pop( $localVarRegistry );
	
	
	$lvk = array_keys( $lvr );
		
	foreach ( $lvk as $lkey )
	{
		$lvars = $lvr[$lkey];
		
		$vars = "";
		
		if ( is_array( $lvr[$lkey] ) )
		{
			foreach( $lvars as $lvar )
			{
				$vars .= "var " . $lvar . " = \"" . $lvar . "\";\n";
			}
			$source = str_replace( "@localvars_" . $lkey, $vars, $source );
		}
	}
	
	array_pop( $pramsRegistry );
	$source = str_replace( "\r", "", str_replace( "\n", "\\n", str_replace( "\"", "\\\"", $source ) ) );
	
	logThis($source);
	
	return $source;
}

?>