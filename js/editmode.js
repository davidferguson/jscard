function resetAllButtonsToEditMode( )
{
	var i, j;
	var btnlist;
	
	for( i = 0; i < cardList.length; i++ )
	{
		btnlist = eval( "card_" + cardID + "_buttonList" );
		for( j = 0; j < btnlist.length; j++ )
		{
			document.getElementById( 'obj' + btnlist[j] ).style.border = normalEditBorder;
		}
	}
}

function resetAllFieldsToEditMode( )
{
	var i, j;
	var fldlist;
	
	for( i = 0; i < cardList.length; i++ )
	{
		fldlist = eval( "card_" + cardID + "_fieldList" );
		for( j = 0; j < fldlist.length; j++ )
		{
			document.getElementById( 'obj' + fldlist[j] ).style.border = normalEditBorder;
		}
	}
}

function resetAllBlocksToEditMode( )
{
	var i, j;
	var blklist;
	
	for( i = 0; i < cardList.length; i++ )
	{
		blklist = eval( "card_" + cardID + "_blockList" );
		for( j = 0; j < blklist.length; j++ )
		{
			document.getElementById( 'obj' + blklist[j] ).style.border = normalEditBorder;
		}
	}
}

function makePart( elemType, elemName )
{
	if( elemName === "" || elemName === false || elemName === null )
	{
		return false;
	}
	topID = topID + 1;
	var i;
	
	//Rip Focus Away from Current Element
	if( focusElement != '' )
	{
		focusElement.blur();
		focusElement = '';
	}
	
	switch ( elemType )
	{
		case 0:
			var newElement = document.createElement( "button" );
			newElement.setAttribute( "value", "" );
			newElement.setAttribute( "name", "btn_" + stackID + "_" + cardID + "_" + elemName );
			newElement.setAttribute( "value", elemName );
			newElement.setAttribute( "id", "obj" + topID );
			newElement.setAttribute( "style", "visibility: visible; position: absolute; top: 10px; left: 10px; width: 150px; height: 25px;" );
			newElement.setAttribute( "onmouseup", "handleBtnMouseUp( this, " + stackID + ", " + cardID + ", " + topID + " );" );
			newElement.setAttribute( "onmousedown", "handleBtnMouseDown( this, " + stackID + ", " + cardID + ", " + topID + " );" );
			newElement.setAttribute( "onmouseover", "handleBtnMouseOver( this, " + stackID + ", " + cardID + ", " + topID + " );" );
			newElement.setAttribute( "onmouseout", "handleBtnMouseOut( this, " + stackID + ", " + cardID + ", " + topID + " );" );
			newElement.setAttribute( "onmousemove", "handleBtnMouseMove( this, " + stackID + ", " + cardID + ", " + topID + " );" );
			newElement.setAttribute( "ondblclick", "if( userLevel == 1 ){ openButtonEditor( this, cardID ); }" );
			document.getElementById( 'card_' + cardID ).appendChild( newElement );
			var btnlist = eval( "card_" + cardID + "_buttonList" );
			btnlist[btnlist.length] = topID;
			var btnzlist = eval( "card_" + cardID + "_buttonZList" );
			var maxz = 0;
			for( i = 0; i < btnzlist.length; i++ )
			{
				if ( btnzlist[i] > maxz )
				{
					maxz = btnzlist[i];
				}
			}
			btnzlist[btnzlist.length] = maxz + 1;
			document.getElementById( 'obj' + topID ).innerHTML = elemName;
			document.getElementById( 'obj' + topID ).style.zIndex = maxz + 1;
			if ( userLevel == 1 ) {
				resetAllButtonsToEditMode();
				document.getElementById( 'obj' + topID ).style.zIndex = ( maxz * 1 ) + 51;
				document.getElementById( 'obj' + topID ).style.border = normalEditSelectBorder;
				dragElement = newElement;
			}
			eval( "card_" + cardID + "_buttonList = btnlist" );
			eval( "card_" + cardID + "_buttonZList = btnzlist" );
			eval( "part_" + stackID + "_" + cardID + "_" + topID + "_script = \"\"" );
			eval( "part_" + stackID + "_" + cardID + "_" + topID + "_scriptx = \"on mouseUp\\n\\nend mouseUp\"" );
			
			// Now we have to add the button to our lists
			buttonIDList.push("obj" + topID);
			buttonEnabledList.push(1);
			buttonVisibleList.push(1);
			buttonStyleList.push(5);
			buttonFamilyList.push(0);
			buttonHiliteList.push(0);
			buttonAutoHiliteList.push(1);
			newElement.className = "btn_editing";
			
			break;
			
		case 1:
			var newElement = document.createElement( "textarea" );
			newElement.setAttribute( "value", "" );
			newElement.setAttribute( "name", "fld_" + stackID + "_" + cardID + "_"  + elemName );
			newElement.setAttribute( "id", "obj" + topID );
			newElement.setAttribute( "style", "visibility: visible; position: absolute; top: 10px; left: 10px; width: 150px; height: 150px; font-family: Arial, sans-serif; font-size: 12px;" );
			newElement.setAttribute( "onmouseup", "handleFldMouseUp( this, " + stackID + ", " + cardID + ", " + topID + " );" );
			newElement.setAttribute( "onmousedown", "handleFldMouseDown( this, " + stackID + ", " + cardID + ", " + topID + " );" );
			newElement.setAttribute( "onmouseover", "handleFldMouseOver( this, " + stackID + ", " + cardID + ", " + topID + " );" );
			newElement.setAttribute( "onmouseout", "handleFldMouseOut( this, " + stackID + ", " + cardID + ", " + topID + " );" );
			newElement.setAttribute( "onmousemove", "handleFldMouseMove( this, " + stackID + ", " + cardID + ", " + topID + " );" );
			newElement.setAttribute( "ondblclick", "if( userLevel == 2 ){ openFieldEditor( this, cardID ); }" );
			newElement.setAttribute("onfocus", "if( userLevel == 0 ){focusElement=this};" );
			newElement.setAttribute("onblur", "if( userLevel == 0 ){focusElement=''};" );
			document.getElementById( 'card_' + cardID ).appendChild( newElement );
			eval( "part_" + stackID + "_" + cardID + "_" + topID + "_script = \"\"" );
			eval( "part_" + stackID + "_" + cardID + "_" + topID + "_scriptx = \"on mouseUp\\n\\nend mouseUp\"" );
			document.getElementById( 'obj' + topID ).innerHTML = elemName;
			var fldlist = eval( "card_" + cardID + "_fieldList" );
			fldlist[fldlist.length] = topID;
			var fldzlist = eval( "card_" + cardID + "_fieldZList" );
			var maxz = 0;
			for( var i = 0; i < fldzlist.length; i++ )
			{
				if ( fldzlist[i] > maxz )
				{
					maxz = fldzlist[i];
				}
			}
			fldzlist[fldzlist.length] = maxz + 1;
			document.getElementById( 'obj' + topID ).style.zIndex = maxz + 1;
			if ( userLevel == 2 ) {
				resetAllFieldsToEditMode();
				document.getElementById( 'obj' + topID ).style.zIndex = ( maxz * 1 ) + 51;
				document.getElementById( 'obj' + topID ).style.border = normalEditSelectBorder;
				dragElement = newElement;
			}
			eval( "card_" + cardID + "_fieldList = fldlist" );
			eval( "card_" + cardID + "_fieldZList = fldzlist" );
			
			// Now we have to add the field to our lists
			fieldIDList.push("obj" + topID);
			fieldEnabledList.push(1);
			fieldVisibleList.push(1);
			fieldStyleList.push(2);
			fieldLockText.push(0);
			//newElement.className = "btn_editing";
			
			break;
			
		case 2:
			var newElement = document.createElement( "div" );
			newElement.setAttribute( "name", "blk_" + stackID + "_" + cardID + "_" + elemName );
			newElement.setAttribute( "id", "obj" + topID );
			newElement.setAttribute( "style", "visibility: visible; position: absolute; top: 10px; left: 10px; width: 150px; height: 150px;font-family: Arial, sans-serif;font-size: 12px;" );
			newElement.setAttribute( "onmouseup", "handleBlkMouseUp( this, " + stackID + ", " + cardID + ", " + topID + " );" );
			newElement.setAttribute( "onmousedown", "handleBlkMouseDown( this, " + stackID + ", " + cardID + ", " + topID + " );" );
			newElement.setAttribute( "onmouseover", "handleBlkMouseOver( this, " + stackID + ", " + cardID + ", " + topID + " );" );
			newElement.setAttribute( "onmouseout", "handleBlkMouseOut( this, " + stackID + ", " + cardID + ", " + topID + " );" );
			newElement.setAttribute( "onmousemove", "handleBlkMouseMove( this, " + stackID + ", " + cardID + ", " + topID + " );" );
			newElement.setAttribute( "ondblclick", "if( userLevel == 3 ){ openBlockEditor( this, cardID ); }" );
			newElement.setAttribute("onfocus", "if( userLevel == 0 ){focusElement=this};" );
			newElement.setAttribute("onblur", "if( userLevel == 0 ){focusElement=''};" );
			document.getElementById( 'card_' + cardID ).appendChild( newElement );
			eval( "part_" + stackID + "_" + cardID + "_" + topID + "_script = \"\"" );
			eval( "part_" + stackID + "_" + cardID + "_" + topID + "_scriptx = \"on mouseUp\\n\\nend mouseUp\"" );
			//document.getElementById( 'obj' + topID ).innerHTML = elemName;
			var blklist = eval( "card_" + cardID + "_blockList" );
			blklist[blklist.length] = topID;
			var blkzlist = eval( "card_" + cardID + "_blockZList" );
			var maxz = 0;
			for( var i = 0; i < blkzlist.length; i++ )
			{
				if ( blkzlist[i] > maxz )
				{
					maxz = blkzlist[i];
				}
			}
			blkzlist[blkzlist.length] = maxz + 1;
			document.getElementById( 'obj' + topID ).style.zIndex = maxz + 1;
			if ( userLevel == 3 ) {
				resetAllBlocksToEditMode();
				document.getElementById( 'obj' + topID ).style.zIndex = ( maxz * 1 ) + 51;
				document.getElementById( 'obj' + topID ).style.border = normalEditSelectBorder;
				dragElement = newElement;
			}
			eval( "card_" + cardID + "_blockList = blklist" );
			eval( "card_" + cardID + "_blockZList = blkzlist" );
			
			// Now we have to add the block to our lists
			blockIDList.push("obj" + topID);
			blockEnabledList.push(1);
			blockVisibleList.push(1);
			blockStyleList.push(0);
			blockContentsList.push(elemName);
			newElement.className = "blk_editing";
			
			break;

	}
}
