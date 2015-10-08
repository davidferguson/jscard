var passmouseup = true;
var passmousedown = true;
var passmouseenter = true;
var passmousewithin = true;
var passmouseleave = true;
var passkeydown = true;

String.prototype.contains = function(it) { return this.indexOf(it) != -1; };

function handleBtnMouseUp( whatElement, stack_id, card_id, part_id )
{
	if( userLevel == 0 )
	{
		if( passmouseup == true )
		{
			script = eval("part_" + stack_id + "_" + card_id + "_" + part_id + "_script");
			if( script.toLowerCase().contains("function mouseup") )
			{
				passmouseup = false;
			}
			
			jsCCurrentObject = whatElement;
			eval( eval( buttonEventStub + "part_" + stack_id + "_" + card_id + "_" + part_id + "_script" ) + "mouseup();" );
		}
	}
	else
		{
			clearInterval( dragInterval );
		}
}

function handleBtnMouseDown( whatElement, stack_id, card_id, part_id )
{
	if( userLevel == 0 )
	{
		if( passmousedown == true )
		{
			script = eval("part_" + stack_id + "_" + card_id + "_" + part_id + "_script");
			if( script.toLowerCase().contains("function mousedown") )
			{
				passmousedown = false;
			}
			
			jsCCurrentObject = whatElement;
			eval( eval( buttonEventStub + "part_" + stack_id + "_" + card_id + "_" + part_id + "_script" ) + "mousedown();" );
		}
	}
	else if ( userLevel == 1 )
		{
			var freezemousex = mousex;
			var freezemousey = mousey;
			buttonArray = eval( "card_" + cardID + "_buttonList" );
			for( i = 0; i < buttonArray.length; i++ )
			{
				obj = document.getElementById( 'obj' + buttonArray[i] );
				document.getElementById( 'obj' + buttonArray[i] ).className = "btn_editing";
			}
			//whatElement.style.border= normalEditSelectBorder;
			whatElement.className = "btn_editing_selected";
			
			dragElement = whatElement;
			dragOffsetX = freezemousex - hpop__filterProperty( whatElement.style.left );
			dragOffsetY = freezemousey - hpop__filterProperty( whatElement.style.top );
			
			if ( dragOffsetX <= dragRegion && dragOffsetY <= dragRegion )
			{
				// DRAG TOP LEFT
				dragInterval = setInterval( "javascript:syncObjectTopLeftToCoord( dragElement, mousex, mousey, dragOffsetX, dragOffsetY );", 100 );
			}
			else if ( ( freezemousex >= ( ( ( hpop__filterProperty( whatElement.style.width )*1) + ( hpop__filterProperty( whatElement.style.left )*1) ) - dragRegion )  ) && ( freezemousey >= ( ( ( hpop__filterProperty( whatElement.style.height )*1) + ( hpop__filterProperty( whatElement.style.top )*1) ) - dragRegion ) ) )
			{
				// DRAG BOTTOM RIGHT
				dragOffsetX = ( ( hpop__filterProperty( whatElement.style.width ) * 1 ) + ( hpop__filterProperty ( whatElement.style.left ) * 1 ) - freezemousex );
				dragOffsetY = ( ( hpop__filterProperty( whatElement.style.height ) * 1 ) + ( hpop__filterProperty ( whatElement.style.top ) * 1 ) - freezemousey );
				dragInterval = setInterval( "javascript:syncObjectBottomRightToCoord( dragElement, mousex, mousey, dragOffsetX, dragOffsetY );", 100 );
			}
			else if ( dragOffsetY <= dragRegion && ( freezemousex >= ( ( ( hpop__filterProperty( whatElement.style.width )*1) + ( hpop__filterProperty( whatElement.style.left )*1) ) - dragRegion ) ) )
			{
				// DRAG TOP RIGHT
				dragOffsetX = ( ( hpop__filterProperty( whatElement.style.width ) * 1 ) + ( hpop__filterProperty ( whatElement.style.left ) * 1 ) - freezemousex );
				dragInterval = setInterval( "javascript:syncObjectTopRightToCoord( dragElement, mousex, mousey, dragOffsetX, dragOffsetY );", 100 );
			}
			else if ( dragOffsetX <= dragRegion && ( freezemousey >= ( ( ( hpop__filterProperty( whatElement.style.height )*1) + ( hpop__filterProperty( whatElement.style.top )*1) ) - dragRegion ) ) )
			{
				// DRAG BOTTOM LEFT
				dragOffsetY = ( ( hpop__filterProperty( whatElement.style.height ) * 1 ) + ( hpop__filterProperty ( whatElement.style.top ) * 1 ) - freezemousey );
				dragInterval = setInterval( "javascript:syncObjectBottomLeftToCoord( dragElement, mousex, mousey, dragOffsetX, dragOffsetY );", 100 );
			}
			else if ( dragOffsetX <= dragRegion )
			{
				// DRAG LEFT
				dragInterval = setInterval( "javascript:syncObjectLeftToCoord( dragElement, mousex, mousey, dragOffsetX, dragOffsetY );", 100 );
			}
			else if ( dragOffsetY <= dragRegion )
			{
				// DRAG TOP
				dragInterval = setInterval( "javascript:syncObjectTopToCoord( dragElement, mousex, mousey, dragOffsetX, dragOffsetY );", 100 );
			}
			else if ( freezemousex >= ( ( ( hpop__filterProperty( whatElement.style.width )*1) + ( hpop__filterProperty( whatElement.style.left )*1) ) - dragRegion ) )
			{
				// DRAG RIGHT
				dragOffsetX = ( ( hpop__filterProperty( whatElement.style.width ) * 1 ) + ( hpop__filterProperty ( whatElement.style.left ) * 1 ) - freezemousex );
				dragInterval = setInterval( "javascript:syncObjectRightToCoord( dragElement, mousex, mousey, dragOffsetX, dragOffsetY );", 100 );
			}
			else if ( freezemousey >= ( ( ( hpop__filterProperty( whatElement.style.height )*1) + ( hpop__filterProperty( whatElement.style.top )*1) ) - dragRegion ) )
			{
				// DAG BOTTOM
				dragOffsetY = ( ( hpop__filterProperty( whatElement.style.height ) * 1 ) + ( hpop__filterProperty ( whatElement.style.top ) * 1 ) - freezemousey );
				dragInterval = setInterval( "javascript:syncObjectBottomToCoord( dragElement, mousex, mousey, dragOffsetX, dragOffsetY );", 100 );
			}
			else
			{
				// DRAG MOVE
				dragInterval = setInterval( "javascript:syncObjectToCoord( dragElement, mousex, mousey, dragOffsetX, dragOffsetY );", 100 );
			}
			return false;
		}
}

function handleBtnMouseOver( whatElement, stack_id, card_id, part_id )
{
	if( userLevel == 0 )
	{
		if( passmouseenter == true )
		{
			script = eval("part_" + stack_id + "_" + card_id + "_" + part_id + "_script");
			if( script.toLowerCase().contains("function mouseenter") )
			{
				passmouseenter = false;
			}
			
			jsCCurrentObject = whatElement;
			eval( eval( buttonEventStub + "part_" + stack_id + "_" + card_id + "_" + part_id + "_script" ) + "mouseenter();" );
		}
	}
}

function handleBtnMouseOut( whatElement, stack_id, card_id, part_id )
{
	if( userLevel == 0 )
	{
		if( passmouseleave == true )
		{
			script = eval("part_" + stack_id + "_" + card_id + "_" + part_id + "_script");
			if( script.toLowerCase().contains("function mouseleave") )
			{
				passmouseleave = false;
			}
			
			jsCCurrentObject = whatElement;
			eval( eval( buttonEventStub + "part_" + stack_id + "_" + card_id + "_" + part_id + "_script" ) + "mouseleave();" );
		}
	}
}

function handleBtnMouseMove( whatElement, stack_id, card_id, part_id )
{
	if( userLevel == 0 )
	{
		if( passmousewithin == true )
		{
			script = eval("part_" + stack_id + "_" + card_id + "_" + part_id + "_script");
			if( script.toLowerCase().contains("function mousewithin") )
			{
				passmousewithin = false;
			}
			
			jsCCurrentObject = whatElement;
			eval( eval( buttonEventStub + "part_" + stack_id + "_" + card_id + "_" + part_id + "_script" ) + "mousewithin();" );
		}
	}
}

function handleFldMouseUp( whatElement, stack_id, card_id, part_id )
{
	if( userLevel == 0 )
	{
		if( passmouseup == true )
		{
			script = eval("part_" + stack_id + "_" + card_id + "_" + part_id + "_script");
			if( script.toLowerCase().contains("function mouseup") )
			{
				passmouseup = false;
			}
			
			jsCCurrentObject = whatElement;
			eval( eval( buttonEventStub + "part_" + stack_id + "_" + card_id + "_" + part_id + "_script" ) + "mouseup();" );
		}
	}
	else
		{
			clearInterval( dragInterval );
		}
}

function handleFldMouseDown( whatElement, stack_id, card_id, part_id )
{
	if( userLevel == 0 )
	{
		if( passmousedown == true )
		{
			script = eval("part_" + stack_id + "_" + card_id + "_" + part_id + "_script");
			if( script.toLowerCase().contains("function mousedown") )
			{
				passmousedown = false;
			}
			
			jsCCurrentObject = whatElement;
			eval( eval( buttonEventStub + "part_" + stack_id + "_" + card_id + "_" + part_id + "_script" ) + "mousedown();" );
		}
	}
	else if ( userLevel == 2 )
		{
		
			var freezemousex = mousex;
			var freezemousey = mousey;
			fldArray = eval( "card_" + cardID + "_fieldList" );
			for( i = 0; i < fldArray.length; i++ )
			{
				obj = document.getElementById( 'obj' + fldArray[i] );
				obj.className = "fld_editing";
			}
			whatElement.className = "fld_editing_selected";
			
			dragElement = whatElement;
			dragOffsetX = freezemousex - hpop__filterProperty( whatElement.style.left );
			dragOffsetY = freezemousey - hpop__filterProperty( whatElement.style.top );
			
			if ( dragOffsetX <= 5 && dragOffsetY <= 5 )
			{
				// DRAG TOP LEFT
				dragInterval = setInterval( "javascript:syncObjectTopLeftToCoord( dragElement, mousex, mousey, dragOffsetX, dragOffsetY );", 100 );
			}
			else if ( ( freezemousex >= ( ( ( hpop__filterProperty( whatElement.style.width )*1) + ( hpop__filterProperty( whatElement.style.left )*1) ) - 5 )  ) && ( freezemousey >= ( ( ( hpop__filterProperty( whatElement.style.height )*1) + ( hpop__filterProperty( whatElement.style.top )*1) ) - 5 ) ) )
			{
				// DRAG BOTTOM RIGHT
				dragOffsetX = ( ( hpop__filterProperty( whatElement.style.width ) * 1 ) + ( hpop__filterProperty ( whatElement.style.left ) * 1 ) - freezemousex );
				dragOffsetY = ( ( hpop__filterProperty( whatElement.style.height ) * 1 ) + ( hpop__filterProperty ( whatElement.style.top ) * 1 ) - freezemousey );
				dragInterval = setInterval( "javascript:syncObjectBottomRightToCoord( dragElement, mousex, mousey, dragOffsetX, dragOffsetY );", 100 );
			}
			else if ( dragOffsetY <= 5 && ( freezemousex >= ( ( ( hpop__filterProperty( whatElement.style.width )*1) + ( hpop__filterProperty( whatElement.style.left )*1) ) - 5 ) ) )
			{
				// DRAG TOP RIGHT
				dragOffsetX = ( ( hpop__filterProperty( whatElement.style.width ) * 1 ) + ( hpop__filterProperty ( whatElement.style.left ) * 1 ) - freezemousex );
				dragInterval = setInterval( "javascript:syncObjectTopRightToCoord( dragElement, mousex, mousey, dragOffsetX, dragOffsetY );", 100 );
			}
			else if ( dragOffsetX <= 5 && ( freezemousey >= ( ( ( hpop__filterProperty( whatElement.style.height )*1) + ( hpop__filterProperty( whatElement.style.top )*1) ) - 5 ) ) )
			{
				// DRAG BOTTOM LEFT
				dragOffsetY = ( ( hpop__filterProperty( whatElement.style.height ) * 1 ) + ( hpop__filterProperty ( whatElement.style.top ) * 1 ) - freezemousey );
				dragInterval = setInterval( "javascript:syncObjectBottomLeftToCoord( dragElement, mousex, mousey, dragOffsetX, dragOffsetY );", 100 );
			}
			else if ( dragOffsetX <= 5 )
			{
				// DRAG LEFT
				dragInterval = setInterval( "javascript:syncObjectLeftToCoord( dragElement, mousex, mousey, dragOffsetX, dragOffsetY );", 100 );
			}
			else if ( dragOffsetY <= 5 )
			{
				// DRAG TOP
				dragInterval = setInterval( "javascript:syncObjectTopToCoord( dragElement, mousex, mousey, dragOffsetX, dragOffsetY );", 100 );
			}
			else if ( freezemousex >= ( ( ( hpop__filterProperty( whatElement.style.width )*1) + ( hpop__filterProperty( whatElement.style.left )*1) ) - 5 ) )
			{
				// DRAG RIGHT
				dragOffsetX = ( ( hpop__filterProperty( whatElement.style.width ) * 1 ) + ( hpop__filterProperty ( whatElement.style.left ) * 1 ) - freezemousex );
				dragInterval = setInterval( "javascript:syncObjectRightToCoord( dragElement, mousex, mousey, dragOffsetX, dragOffsetY );", 100 );
			}
			else if ( freezemousey >= ( ( ( hpop__filterProperty( whatElement.style.height )*1) + ( hpop__filterProperty( whatElement.style.top )*1) ) - 5 ) )
			{
				// DAG BOTTOM
				dragOffsetY = ( ( hpop__filterProperty( whatElement.style.height ) * 1 ) + ( hpop__filterProperty ( whatElement.style.top ) * 1 ) - freezemousey );
				dragInterval = setInterval( "javascript:syncObjectBottomToCoord( dragElement, mousex, mousey, dragOffsetX, dragOffsetY );", 100 );
			}
			else
			{
				// DRAG MOVE
				dragInterval = setInterval( "javascript:syncObjectToCoord( dragElement, mousex, mousey, dragOffsetX, dragOffsetY );", 100 );
			}
			return false;
		}
}

function handleFldMouseOver( whatElement, stack_id, card_id, part_id )
{
	if( userLevel == 0 )
	{
		if( passmouseenter == true )
		{
			script = eval("part_" + stack_id + "_" + card_id + "_" + part_id + "_script");
			if( script.toLowerCase().contains("function mouseenter") )
			{
				passmouseenter = false;
			}
			
			jsCCurrentObject = whatElement;
			eval( eval( buttonEventStub + "part_" + stack_id + "_" + card_id + "_" + part_id + "_script" ) + "mouseenter();" );
		}
	}
}

function handleFldMouseOut( whatElement, stack_id, card_id, part_id )
{
	if( userLevel == 0 )
	{
		if( passmouseleave == true )
		{
			script = eval("part_" + stack_id + "_" + card_id + "_" + part_id + "_script");
			if( script.toLowerCase().contains("function mouseleave") )
			{
				passmouseleave = false;
			}
			
			jsCCurrentObject = whatElement;
			eval( eval( buttonEventStub + "part_" + stack_id + "_" + card_id + "_" + part_id + "_script" ) + "mouseleave();" );
		}
	}
}

function handleFldMouseMove( whatElement, stack_id, card_id, part_id )
{
	if( userLevel == 0 )
	{
		if( passmousewithin == true )
		{
			script = eval("part_" + stack_id + "_" + card_id + "_" + part_id + "_script");
			if( script.toLowerCase().contains("function mousewithin") )
			{
				passmousewithin = false;
			}
			
			jsCCurrentObject = whatElement;
			eval( eval( buttonEventStub + "part_" + stack_id + "_" + card_id + "_" + part_id + "_script" ) + "mousewithin();" );
		}
	}
}

function handleFldKeyDown( whatElement, stack_id, card_id, part_id )
{
	if( userLevel == 0 )
	{
		if( passkeydown == true )
		{
			script = eval("part_" + stack_id + "_" + card_id + "_" + part_id + "_script");
			if( script.toLowerCase().contains("function keydown") )
			{
				passkeydown = false;
			}
			
			jsCCurrentObject = whatElement;
			
			theKey = getKey();
			
			eval( eval( buttonEventStub + "part_" + stack_id + "_" + card_id + "_" + part_id + "_script" ) + "keydown( '" + theKey + "' );" );
		}
	}
}

function handleBlkMouseUp( whatElement, stack_id, card_id, part_id )
{
	if( userLevel == 0 )
	{
		if( passmouseup == true )
		{
			script = eval("part_" + stack_id + "_" + card_id + "_" + part_id + "_script");
			if( script.toLowerCase().contains("function mouseup") )
			{
				passmouseup = false;
			}
			
			jsCCurrentObject = whatElement;
			eval( eval( buttonEventStub + "part_" + stack_id + "_" + card_id + "_" + part_id + "_script" ) + "mouseup();" );
		}
	}
	else
		{
			clearInterval( dragInterval );
		}
}

function handleBlkMouseDown( whatElement, stack_id, card_id, part_id )
{
	if( userLevel == 0 )
	{
		if( passmousedown == true )
		{
			script = eval("part_" + stack_id + "_" + card_id + "_" + part_id + "_script");
			if( script.toLowerCase().contains("function mousedown") )
			{
				passmousedown = false;
			}
			
			jsCCurrentObject = whatElement;
			eval( eval( buttonEventStub + "part_" + stack_id + "_" + card_id + "_" + part_id + "_script" ) + "mousedown();" );
		}
	}
	else if ( userLevel == 3 )
		{
		
			var freezemousex = mousex;
			var freezemousey = mousey;
			blkArray = eval( "card_" + cardID + "_blockList" );
			for( i = 0; i < blkArray.length; i++ )
			{
				obj = document.getElementById( 'obj' + blkArray[i] );
				obj.className = "blk_editing";
			}
			whatElement.className = "blk_editing_selected";
			
			dragElement = whatElement;
			dragOffsetX = freezemousex - hpop__filterProperty( whatElement.style.left );
			dragOffsetY = freezemousey - hpop__filterProperty( whatElement.style.top );
			
			if ( dragOffsetX <= 5 && dragOffsetY <= 5 )
			{
				// DRAG TOP LEFT
				dragInterval = setInterval( "javascript:syncObjectTopLeftToCoord( dragElement, mousex, mousey, dragOffsetX, dragOffsetY );", 100 );
			}
			else if ( ( freezemousex >= ( ( ( hpop__filterProperty( whatElement.style.width )*1) + ( hpop__filterProperty( whatElement.style.left )*1) ) - 5 )  ) && ( freezemousey >= ( ( ( hpop__filterProperty( whatElement.style.height )*1) + ( hpop__filterProperty( whatElement.style.top )*1) ) - 5 ) ) )
			{
				// DRAG BOTTOM RIGHT
				dragOffsetX = ( ( hpop__filterProperty( whatElement.style.width ) * 1 ) + ( hpop__filterProperty ( whatElement.style.left ) * 1 ) - freezemousex );
				dragOffsetY = ( ( hpop__filterProperty( whatElement.style.height ) * 1 ) + ( hpop__filterProperty ( whatElement.style.top ) * 1 ) - freezemousey );
				dragInterval = setInterval( "javascript:syncObjectBottomRightToCoord( dragElement, mousex, mousey, dragOffsetX, dragOffsetY );", 100 );
			}
			else if ( dragOffsetY <= 5 && ( freezemousex >= ( ( ( hpop__filterProperty( whatElement.style.width )*1) + ( hpop__filterProperty( whatElement.style.left )*1) ) - 5 ) ) )
			{
				// DRAG TOP RIGHT
				dragOffsetX = ( ( hpop__filterProperty( whatElement.style.width ) * 1 ) + ( hpop__filterProperty ( whatElement.style.left ) * 1 ) - freezemousex );
				dragInterval = setInterval( "javascript:syncObjectTopRightToCoord( dragElement, mousex, mousey, dragOffsetX, dragOffsetY );", 100 );
			}
			else if ( dragOffsetX <= 5 && ( freezemousey >= ( ( ( hpop__filterProperty( whatElement.style.height )*1) + ( hpop__filterProperty( whatElement.style.top )*1) ) - 5 ) ) )
			{
				// DRAG BOTTOM LEFT
				dragOffsetY = ( ( hpop__filterProperty( whatElement.style.height ) * 1 ) + ( hpop__filterProperty ( whatElement.style.top ) * 1 ) - freezemousey );
				dragInterval = setInterval( "javascript:syncObjectBottomLeftToCoord( dragElement, mousex, mousey, dragOffsetX, dragOffsetY );", 100 );
			}
			else if ( dragOffsetX <= 5 )
			{
				// DRAG LEFT
				dragInterval = setInterval( "javascript:syncObjectLeftToCoord( dragElement, mousex, mousey, dragOffsetX, dragOffsetY );", 100 );
			}
			else if ( dragOffsetY <= 5 )
			{
				// DRAG TOP
				dragInterval = setInterval( "javascript:syncObjectTopToCoord( dragElement, mousex, mousey, dragOffsetX, dragOffsetY );", 100 );
			}
			else if ( freezemousex >= ( ( ( hpop__filterProperty( whatElement.style.width )*1) + ( hpop__filterProperty( whatElement.style.left )*1) ) - 5 ) )
			{
				// DRAG RIGHT
				dragOffsetX = ( ( hpop__filterProperty( whatElement.style.width ) * 1 ) + ( hpop__filterProperty ( whatElement.style.left ) * 1 ) - freezemousex );
				dragInterval = setInterval( "javascript:syncObjectRightToCoord( dragElement, mousex, mousey, dragOffsetX, dragOffsetY );", 100 );
			}
			else if ( freezemousey >= ( ( ( hpop__filterProperty( whatElement.style.height )*1) + ( hpop__filterProperty( whatElement.style.top )*1) ) - 5 ) )
			{
				// DAG BOTTOM
				dragOffsetY = ( ( hpop__filterProperty( whatElement.style.height ) * 1 ) + ( hpop__filterProperty ( whatElement.style.top ) * 1 ) - freezemousey );
				dragInterval = setInterval( "javascript:syncObjectBottomToCoord( dragElement, mousex, mousey, dragOffsetX, dragOffsetY );", 100 );
			}
			else
			{
				// DRAG MOVE
				dragInterval = setInterval( "javascript:syncObjectToCoord( dragElement, mousex, mousey, dragOffsetX, dragOffsetY );", 100 );
			}
			return false;
		}
}

function handleBlkMouseOver( whatElement, stack_id, card_id, part_id )
{
	if( userLevel == 0 )
	{
		if( passmouseenter == true )
		{
			script = eval("part_" + stack_id + "_" + card_id + "_" + part_id + "_script");
			if( script.toLowerCase().contains("function mouseenter") )
			{
				passmouseenter = false;
			}
			
			jsCCurrentObject = whatElement;
			eval( eval( buttonEventStub + "part_" + stack_id + "_" + card_id + "_" + part_id + "_script" ) + "mouseenter();" );
		}
	}
}

function handleBlkMouseOut( whatElement, stack_id, card_id, part_id )
{
	if( userLevel == 0 )
	{
		if( passmouseleave == true )
		{
			script = eval("part_" + stack_id + "_" + card_id + "_" + part_id + "_script");
			if( script.toLowerCase().contains("function mouseleave") )
			{
				passmouseleave = false;
			}
			
			jsCCurrentObject = whatElement;
			eval( eval( buttonEventStub + "part_" + stack_id + "_" + card_id + "_" + part_id + "_script" ) + "mouseleave();" );
		}
	}
}

function handleBlkMouseMove( whatElement, stack_id, card_id, part_id )
{
	if( userLevel == 0 )
	{
		if( passmousewithin == true )
		{
			script = eval("part_" + stack_id + "_" + card_id + "_" + part_id + "_script");
			if( script.toLowerCase().contains("function mousewithin") )
			{
				passmousewithin = false;
			}
			
			jsCCurrentObject = whatElement;
			eval( eval( buttonEventStub + "part_" + stack_id + "_" + card_id + "_" + part_id + "_script" ) + "mousewithin();" );
		}
	}
}

function handleCardMouseUp( whatElement, stack_id, card_id )
{
	if( userLevel == 0 )
	{
		if( passmouseup == true )
		{
			script =  eval ( "card_" + stack_id + "_" + card_id + "_script" );
			if( script.toLowerCase().contains("function mouseup") )
			{
				passmouseup = false;
			}
			eval( eval ( buttonEventStub + "card_" + stack_id + "_" + card_id + "_script" ) + "mouseup();" );
		}
	}
}

function handleCardMouseDown( whatElement, stack_id, card_id )
{
	if( userLevel == 0 )
	{
		if( passmousedown == true )
		{
			script =  eval ( "card_" + stack_id + "_" + card_id + "_script" );
			if( script.toLowerCase().contains("function mousedown") )
			{
				passmousedown = false;
			}
			eval( eval ( buttonEventStub + "card_" + stack_id + "_" + card_id + "_script" ) + "mousedown();" );
		}
	}
}

function handleCardMouseOver( whatElement, stack_id, card_id )
{
	if( userLevel == 0 )
	{
		if( passmouseenter == true )
		{
			script =  eval ( "card_" + stack_id + "_" + card_id + "_script" );
			if( script.toLowerCase().contains("function mouseenter") )
			{
				passmouseenter = false;
			}
			eval( eval ( buttonEventStub + "card_" + stack_id + "_" + card_id + "_script" ) + "mouseenter();" );
		}
	}
}

function handleCardMouseMove( whatElement, stack_id, card_id )
{
	if( userLevel == 0 )
	{
		if( passmousewithin == true )
		{
			script =  eval ( "card_" + stack_id + "_" + card_id + "_script" );
			if( script.toLowerCase().contains("function mousewithin") )
			{
				passmousewithin = false;
			}
			eval( eval ( buttonEventStub + "card_" + stack_id + "_" + card_id + "_script" ) + "mousewithin();" );
		}
	}
}

function handleCardMouseOut( whatElement, stack_id, card_id )
{
	if( userLevel == 0 )
	{
		if( passmouseleave == true )
		{
			script =  eval ( "card_" + stack_id + "_" + card_id + "_script" );
			if( script.toLowerCase().contains("function mouseleave") )
			{
				passmouseleave = false;
			}
			eval( eval ( buttonEventStub + "card_" + stack_id + "_" + card_id + "_script" ) + "mouseleave();" );
		}
	}
}

function handleCardKeyDown( whatElement, stack_id, card_id )
{
	if( userLevel == 0 )
	{
		if( passkeydown == true )
		{
			script =  eval ( "card_" + stack_id + "_" + card_id + "_script" );
			if( script.toLowerCase().contains("function keydown") )
			{
				passkeydown = false;
			}
			
			theKey = getKey();
			eval( eval ( buttonEventStub + "card_" + stack_id + "_" + card_id + "_script" ) + "keydown( '" + theKey + "' );" );
		}
	}
}

function handleStackMouseUp( whatElement, stack_id )
{
	if( userLevel == 0 )
	{
		if( passmouseup == true )
		{
			script =  eval ( "stack_" + stack_id + "_script" );
			if( script.toLowerCase().contains("function mouseup") )
			{
				passmouseup = false;
			}
			eval( eval ( buttonEventStub + "stack_" + stack_id + "_script" ) + "mouseup();" );
		}
		passmouseup = true;
	}
}

function handleStackMouseDown( whatElement, stack_id )
{
	if( userLevel == 0 )
	{
		if( passmousedown == true )
		{
			script =  eval ( "stack_" + stack_id + "_script" );
			if( script.toLowerCase().contains("function mousedown") )
			{
				passmousedown = false;
			}
			eval( eval ( buttonEventStub + "stack_" + stack_id + "_script" ) + "mousedown();" );
		}
		passmousedown = true;
	}
}

function handleStackMouseOver( whatElement, stack_id )
{
	if( userLevel == 0 )
	{
		if( passmouseenter == true )
		{
			script =  eval ( "stack_" + stack_id + "_script" );
			if( script.toLowerCase().contains("function mouseenter") )
			{
				passmouseenter = false;
			}
			eval( eval ( buttonEventStub + "stack_" + stack_id + "_script" ) + "mouseenter();" );
		}
		passmouseenter = true;
	}
}

function handleStackMouseMove( whatElement, stack_id )
{
	if( userLevel == 0 )
	{
		if( passmousewithin == true )
		{
			script =  eval ( "stack_" + stack_id + "_script" );
			if( script.toLowerCase().contains("function mousewithin") )
			{
				passmousewithin = false;
			}
			eval( eval ( buttonEventStub + "stack_" + stack_id + "_script" ) + "mousewithin();" );
		}
		passmousewithin = true;
	}
}

function handleStackMouseOut( whatElement, stack_id )
{
	if( userLevel == 0 )
	{
		if( passmouseleave == true )
		{
			script =  eval ( "stack_" + stack_id + "_script" );
			if( script.toLowerCase().contains("function mouseleave") )
			{
				passmouseleave = false;
			}
			eval( eval ( buttonEventStub + "stack_" + stack_id + "_script" ) + "mouseleave();" );
		}
		passmouseleave = true;
	}
}

function handleStackKeyDown( whatElement, stack_id )
{
	if( userLevel == 0 )
	{
		if( passkeydown == true )
		{
			script =  eval ( "stack_" + stack_id + "_script" );
			if( script.toLowerCase().contains("function keydown") )
			{
				passkeydown = false;
			}
			
			theKey = getKey();
			eval( eval ( buttonEventStub + "stack_" + stack_id + "_script" ) + "keydown( '" + theKey + "' );" );
		}
		passkeydown = true;
	}
}