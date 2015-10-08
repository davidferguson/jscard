/*Properties implemented IN THIS FILE:
name, script, multipleLines, dontWrap, autoSelect, ADDRESS, location, bottomRight, topLeft, enabled, visible, style, family, lockText, hilite, autoHilite, contents(fieldValue, blockValue), type, rect
*/

//Image has also been implemented for cards (not a HyperTalk property)

function hpop__setImage( whatCard, filePath )
{
	whatCard.style.backgroundImage = "url('" + filePath + "')";
	return true;
}

function hpop__getImage( whatCard )
{
	var fullImage = whatCard.style.backgroundImage;
	fullImage = fullImage.split("/");
	var imageName = fullImage[fullImage.length];
	imageName = imageName.slice(-2);
	return imageName;
}

function hpop__filterProperty( propValue )
{
	if ( propValue.substr( propValue.length -2, 2 ) == "px" )
	{
		return propValue.substr( 0, propValue.length - 2 );
	}
}

function hpop__setName( obj, objName )
{
	if ( hpop__getType( obj ) == "button" )
	{
		buttonStyle = hpop__getStyle( obj );
		if( buttonStyle == "radiobutton" || buttonStyle == "checkbox" )
		{
			obj.name = "btn_" + stackID + "_" + cardID + "_" + objName;
			mainDiv = obj.firstChild.firstChild;
			label = mainDiv.getElementsByTagName('td')[1];
			label.firstChild.innerHTML = objName;
			obj.setAttribute('name', "btn_" + stackID + "_" + cardID + "_" + objName);
		}
		else if( buttonStyle == "popup" )
		{
			
		}
		else
		{
			obj.name = "btn_" + stackID + "_" + cardID + "_" + objName;
			obj.innerHTML = objName;
		}
	}
	else if ( hpop__getType( obj ) == "field" )
	{
		obj.name = "fld_" + stackID + "_" + cardID + "_" + objName;
	}
	else if ( hpop__getType( obj ) == "block" )
	{
		obj.name = "blk_" + stackID + "_" + cardID + "_" + objName;
	}
	else
	{
		obj.name = objName;
	}
}

function hpop__setScript( obj, objScript )
{
	var objectID = obj.getAttribute('id');
	objectID = objectID.substr( 3 );

	eval( "part_" + stackID + "_" + cardID + "_" + objectID + "_scriptx = objScript;");	
	eval( "part_" + stackID + "_" + cardID + "_" + objectID + "_script = inlinecompiler( objScript, true );");
}

function hpop__getScript( obj )
{
	var objectID = obj.getAttribute('id');
	objectID = objectID.substr( 3 );
	
	return eval( "part_" + stackID + "_" + cardID + "_" + objectID + "_scriptx" );
}

function hpop__getMultipleLines( whatElement )
{
	if( hpop__getType( whatElement ) == "field" )
	{
		if( whatElement.tagName == "SELECT" )
		{
			return whatElement.multiple;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function hpop__setMultipleLines( whatElement, newMultipleLines )
{
	if( hpop__getType( whatElement ) == "field" )
	{
		if( whatElement.tagName == "SELECT" )
		{
			whatElement.multiple = newMultipleLines;
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function hpop__getDontWrap( whatElement )
{
	wrap = whatElement.getAttribute("wrap");
	if( wrap == "off" )
	{
		dontWrap = true;
	}
	else
	{
		dontWrap = false;
	}
	return dontWrap;
}

function hpop__setDontWrap( whatElement, newDontWrap )
{
	if( newDontWrap )
	{
		newDontWrap = "off";
	}
	else
	{
		newDontWrap = "on";
		hpop__setAutoSelect( whatElement, false );
	}
	whatElement.setAttribute("wrap", newDontWrap);
}

function hpop__getAutoSelect( whatElement )
{
	if( whatElement.tagName == "SELECT" )
	{
		return true;
	}
	else
	{
		return false;
	}
}

function hpop__setAutoSelect( whatElement, newAutoSelect )
{
	if( newAutoSelect )
	{
		if( ! hpop__getAutoSelect( whatElement ) )
		{
			hpop__setDontWrap( whatElement, true );
			// Copy all useful attributes about the existing element
			var newElementStyle = whatElement.getAttribute('style');
			var newElementID = whatElement.getAttribute('id');
			var newElementName = whatElement.name;
			var newElementValue = whatElement.value;
			var newElementZIndex = whatElement.zIndex;
			var newElementMouseUp = whatElement.getAttribute('onmouseup');
			var newElementMouseDown = whatElement.getAttribute('onmousedown');
			var newElementMouseOver = whatElement.getAttribute('onmouseover');
			var newElementMouseOut = whatElement.getAttribute('onmouseout');
			var newElementMouseMove = whatElement.getAttribute('onmousemove');
			var newElementDblClick = whatElement.getAttribute('ondblclick');
			var newElementFocus = whatElement.getAttribute('onfocus');
			var newElementBlur = whatElement.getAttribute('onblur');
			var newElementClass = whatElement.getAttribute('class');
			var newElementReadOnly = whatElement.readOnly;
			var newElementDisabled = whatElement.disabled;
			
			var newElementParent = whatElement.parentNode;
			
			// Delete the old element
			whatElement.parentNode.removeChild( whatElement );
			
			// Fashion a new tag in its likeness
			var newElement = document.createElement( "select" );
			newElement.setAttribute('size', '30000' );
			newElement.setAttribute('style', newElementStyle );
			newElement.setAttribute('id', newElementID );
			newElement.setAttribute('name', newElementName );
			//newElement.setAttribute('value', newElementValue );
			newElement.setAttribute('zindex', newElementZIndex );
			newElement.setAttribute('onmouseup', newElementMouseUp );
			newElement.setAttribute('onmousedown', newElementMouseDown );
			newElement.setAttribute('onmouseover', newElementMouseOver );
			newElement.setAttribute('onmouseout', newElementMouseOut );
			newElement.setAttribute('onmousemove', newElementMouseMove );
			newElement.setAttribute('ondblclick', newElementDblClick);
			newElement.setAttribute('onfocus', newElementFocus );
			newElement.setAttribute('onblur', newElementBlur );
			newElement.setAttribute('class', newElementClass );
			newElement.readOnly = newElementReadOnly;
			newElement.disabled = newElementDisabled;
			
			newElementValue = newElementValue.split('\n');
			for( var i = 0; i < newElementValue.length; i++ )
			{
				var thisOption = new Option( newElementValue[i], newElementValue[i] );
				newElement.options[newElement.options.length] = thisOption;
			}
			
			newElementParent.appendChild( newElement );
		}
	}
	else
	{
		if( hpop__getAutoSelect( whatElement ) )
		{
			// Convert Element to a <textarea> element
			
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
			var newElementDblClick = whatElement.getAttribute('ondblclick');
			var newElementFocus = whatElement.getAttribute('onfocus');
			var newElementBlur = whatElement.getAttribute('onblur');
			var newElementClass = whatElement.getAttribute('class');
			var newElementReadOnly = whatElement.readOnly;
			var newElementDisabled = whatElement.disabled;
			
			var newElementParent = whatElement.parentNode;
			
			var newElementValue = "";
			
			for( var i = 0; i < whatElement.options.length ; i ++ )
			{
				newElementValue += whatElement.options[i].value + '\n';
			}
			
			// Delete the old element
			whatElement.parentNode.removeChild( whatElement );
			
			// Fashion a new tag in its likeness
			var newElement = document.createElement( "textarea" );
			newElement.setAttribute('style', newElementStyle );
			newElement.setAttribute('id', newElementID );
			newElement.setAttribute('name', newElementName );
			//newElement.setAttribute('value', newElementValue );
			newElement.setAttribute('zindex', newElementZIndex );
			newElement.setAttribute('onmouseup', newElementMouseUp );
			newElement.setAttribute('onmousedown', newElementMouseDown );
			newElement.setAttribute('onmouseover', newElementMouseOver );
			newElement.setAttribute('onmouseout', newElementMouseOut );
			newElement.setAttribute('onmousemove', newElementMouseMove );
			newElement.setAttribute('ondblclick', newElementDblClick );
			newElement.setAttribute('onfocus', newElementFocus );
			newElement.setAttribute('onblur', newElementBlur );
			newElement.setAttribute('class', newElementClass );
			newElement.readOnly = newElementReadOnly;
			newElement.disabled = newElementDisabled;

			newElement.value = newElementValue.substr( 0, newElementValue.length - 1 );
			
			newElementParent.appendChild( newElement );
		}
	}
}

function hpop__getAddress()
{
	var xmlhttp = new XMLHttpRequest();
	
	xmlhttp.open("GET","http://api.hostip.info/get_html.php",false);
	xmlhttp.send();
	
	var hostipInfo = xmlhttp.responseText.split("\n");

	for (i=0; hostipInfo.length >= i; i++)
	{
		var ipAddress = hostipInfo[i].split(":");
		if ( ipAddress[0] == "IP" )
		{
			return ipAddress[1];
		}
	}

    return false;
}

function hpop__getLocation( whatElement )
{
	var locX = parseInt((whatElement.offsetTop),10)+(parseInt((whatElement.offsetHeight),10))/2;
	var locY = parseInt((whatElement.offsetLeft),10)+(parseInt((whatElement.offsetWidth),10))/2;
	
	var elementLocation = locX + "," + locY;
	return elementLocation;
}

function hpop__setLocation( whatElement, newLocation )
{
	var items = newLocation.split(cxl_item_delimiter).length - 1;
	
	if( items > 2 )
	{
		alert("There is a maximum number of two items for the location property.");
		return false;
	}
	
	var newX = parseInt(newLocation.split(cxl_item_delimiter)[0]);
	var newY = parseInt(newLocation.split(cxl_item_delimiter)[1]);
	
	whatElement.style.top = ( newX - (parseInt((whatElement.offsetHeight),10))/2 );
	whatElement.style.left = ( newY - (parseInt((whatElement.offsetWidth),10))/2 );
	return true;
}

function hpop__getBottomRight( whatElement )
{
	var bottom = parseInt((whatElement.offsetTop),10)+parseInt((whatElement.offsetHeight),10);
	var right = parseInt((whatElement.offsetLeft),10)+parseInt((whatElement.offsetWidth),10);
	var bottomRight = bottom + "," + right;
	return bottomRight;
}

function hpop__setBottomRight( whatElement, newBottomRight )
{
	var items = newBottomRight.split(cxl_item_delimiter).length - 1;
	
	if( items > 2 )
	{
		alert("There is a maximum number of two items for the bottomRight property.");
		return false;
	}
	
	var newBottom = parseInt(newBottomRight.split(cxl_item_delimiter)[0]);
	var newRight = parseInt(newBottomRight.split(cxl_item_delimiter)[1]);
	
	whatElement.style.top = ( newBottom - parseInt((whatElement.offsetHeight),10) );
	whatElement.style.left = ( newRight - parseInt((whatElement.offsetWidth),10) );
	return true;
}

function hpop__getTopLeft( whatElement )
{
	var top = whatElement.offsetTop;
	var left = whatElement.offsetLeft;
	var topLeft = whatElement.offsetTop + "," + whatElement.offsetLeft;
	return topLeft;
}

function hpop__setTopLeft( whatElement, newTopLeft )
{
	var itemDel = ",";
	var items = newTopLeft.split(itemDel).length - 1;
	
	if( items > 2 )
	{
		alert("There is a maximum number of two items for the topLeft property.");
		return false;
	}
	
	var newTop = parseInt(newTopLeft.split(itemDel)[0]);
	var newLeft = parseInt(newTopLeft.split(itemDel)[1]);
	whatElement.style.top = newTop;
	whatElement.style.left = newLeft;
	return true;
}

function hpop__getEnabled( whatElement )
{
	var elementIndex;
	var elementEnabled;
	// This function is used to get the enabled of an object. The enabled of objects are stored in an list, rather than actually
	// checking what the enabled of the element is, which has the advantage that it will return the correct enabled, even when in
	// an element editing mode, and that element could have been enabled whilst in that mode.
	
	// First we need to find the element's ID
	var elementID = hpop__getID( whatElement );
	
	// We now need to find out the type of the element, so we can check in the correct list
	var elementType = hpop__getType( whatElement );
	
	if( elementType == "button" )
	{
		// If the element is a button, then we need to use the button lists
		
		// Now we need to find the index of the button in the ID list
		elementIndex = buttonIDList.indexOf( elementID );
		
		// Now we can retrieve the enabled from the enabled list
		elementEnabled = buttonEnabledList[elementIndex];
	}
	else if( elementType == "field" )
	{
		// If the element is a field, then we need to use the field lists
		
		// Now we need to find the index of the field in the ID list
		elementIndex = fieldIDList.indexOf( elementID );
		
		// Now we can retrieve the enabled from the enabled list
		elementEnabled = fieldEnabledList[elementIndex];
	}
	else if( elementType == "block" )
	{
		// If the element is a block, then we need to use the block lists
		// Now we need to find the index of the block in the ID list
		elementIndex = blockIDList.indexOf( elementID );
		
		// Now we can retrieve the enabled from the enabled list
		elementEnabled = blockEnabledList[elementIndex];
	}
	
	// Now that we have the enabled for the element, we can return it
	return elementEnabled;
}

function hpop__setEnabled( whatElement, newEnabled )
{
	var elementIndex;
	var buttonStyle;
	// This is the function that is used to set the enabled of an element.
	
	// There are two parts for setting the enabled of an element. The first is to
	// change the enabled in the list, and the second is to actually change the
	// disabled property of the element. However, we only change the disabled property
	// if we are not in that element's editing mode, to avoid the user not being able
	// to select that element for editing. The element's enabled in the list will still
	// be changed, so when the user exits out of the elements editing mode, the correct
	// enabled will be set.
	
	// The enabled of elements are stored in lists, so we will need to find out
	// the index of the old enabled in the list, so that we can write over it with
	// the new enabled.
	
	// First we need to get the ID of the element
	var elementID = hpop__getID( whatElement );
	
	// Now we need to find out the type of the element, so we can search in the correct list
	var elementType = hpop__getType( whatElement );
	
	if( elementType == "button" )
	{
		// If the element is a button, then we need to use the button lists
		
		// Now we need to find the index of the button in the ID list
		elementIndex = buttonIDList.indexOf( elementID );
		
		// Now that we know the index of the button, we can replace it with the new enabled
		buttonEnabledList[elementIndex] = newEnabled;
		
		// Now we need to check to see if we are not in button editing mode
		if( userLevel != 1 )
		{
			// If we are not in button editing mode, then change the disabled property
			// of the button to the opposite of newEnabled.
			buttonStyle = hpop__getStyle( whatElement );
			if( buttonStyle == "checkbox" || buttonStyle == "radiobutton" )
			{
				whatElement.firstChild.firstChild.firstChild.firstChild.disabled = ! newEnabled;
			}
			else
			{
				whatElement.disabled = ! newEnabled;
			}
		}
	}
	else if( elementType == "field" )
	{
		// If the element is a field, then we need to use the field lists
		
		// Now we need to find the index of the field in the ID list
		elementIndex = fieldIDList.indexOf( elementID );
		
		// Now that we know the index of the field, we can replace it with the new enabled
		fieldEnabledList[elementIndex] = newEnabled;
		
		// Now we need to check to see if we are not in field editing mode
		if( userLevel != 2 )
		{
			// If we are not in block editing mode, then change the disabled property
			// of the field to the opposite of newEnabled.
			whatElement.disabled = ! newEnabled;
		}
	}
	else if( elementType == "block" )
	{
		// If the element is a block, then we need to use the block lists
		
		// Now we need to find the index of the block in the ID list
		elementIndex = blockIDList.indexOf( elementID );
		
		// Now that we know the index of the block, we can replace it with the new enabled
		blockEnabledList[elementIndex] = newEnabled;
		
		// Now we need to check to see if we are not in block editing mode
		if( userLevel != 3 )
		{
			// If we are not in block editing mode, then change the disabled property
			// of all the elements inside of the DIV that support being disabled
			var inputs = whatElement.getElementsByTagName("input");
			for(i=0;i<inputs.length;i++)
			{
			   inputs[i].disabled = ! newEnabled;
			}
			var buttons = whatElement.getElementsByTagName("button");
			for(i=0;i<buttons.length;i++)
			{
			   buttons[i].disabled = ! newEnabled;
			}
		}
	}
}

function hpop__getVisible( whatElement )
{
	var elementIndex;
	var elementVisible;
	// This function is used to return the visible of an element, from it's value in the list
	
	// First we need to find the element's ID
	var elementID = hpop__getID( whatElement );
	
	// Now we need to find out the type of the element, so we can use the correct lists
	var elementType = hpop__getType( whatElement );
	
	if( elementType == "button" )
	{
		// If the element is a button, then we need to use the button lists
		
		// Now we need to find the index of the button in the ID list
		elementIndex = buttonIDList.indexOf( elementID );
		
		// Now we can find out the elements visible from the visible list
		elementVisible = buttonVisibleList[elementIndex];
	}
	else if( elementType == "field" )
	{
		// If the element is a field, then we need to use the field lists
		
		// Now we need to find the index of the field in the ID list
		elementIndex = fieldIDList.indexOf( elementID );
		
		// Now we can find out the elements visible from the visible list
		elementVisible = fieldVisibleList[elementIndex];
	}
	else if( elementType == "block" )
	{
		// If the element is a block, then we need to use the block lists
		
		// Now we need to find the index of the block in the ID list
		elementIndex = blockIDList.indexOf( elementID );
		
		// Now we can find out the elements visible from the visible list
		elementVisible = blockVisibleList[elementIndex];
	}
	
	// Now that we have found the visible of the element, we can return it
	return elementVisible;
}

function hpop__setVisible( whatElement, newVisible )
{
	var visibilityProperty;
	var elementType;
	var elementIndex;
	// This is the function that is used to set the visible of an element.
	
	// There are two parts for setting the visible of an element. The first is to
	// change the visible in the list, and the second is to actually change the
	// hidden property of the element. However, we only change the hidden property
	// if we are not in that element's editing mode, to avoid the user not being able
	// to select that element for editing. The element's visible in the list will still
	// be changed, so when the user exits out of the elements editing mode, the correct
	// visible will be set.
	
	// The visible of elements are stored in lists, so we will need to find out
	// the index of the old visible in the list, so that we can write over it with
	// the new visible.
	
	// First we need to get the ID of the element
	var elementID = hpop__getID( whatElement );
	
	// To set the visibility property of the element, we need to convert the true and false
	// received from newVisible to "hidden" and "visible"
	if( newVisible )
	{
		visibilityProperty = "visible";
	}
	else
	{
		visibilityProperty = "hidden";
	}
	
	// Now we need to find out the type of the element, so we can search in the correct list
	elementType = hpop__getType( whatElement );
	
	if( elementType == "button" )
	{
		// If the element is a button, then we need to use the button lists
		
		// Now we need to find the index of the button in the ID list
		elementIndex = buttonIDList.indexOf( elementID );
		
		// Now that we know the index of the button, we can replace it with the new visible
		buttonVisibleList[elementIndex] = newVisible;
		
		// Now we need to check to see if we are not in button editing mode
		if( userLevel != 1 )
		{
			
			// If we are not in button editing mode, then change the hidden property
			// of the button to the opposite of newVisible.
			whatElement.style.visibility = visibilityProperty;
		}
	}
	else if( elementType == "field" )
	{
		// If the element is a field, then we need to use the field lists
		
		// Now we need to find the index of the field in the ID list
		elementIndex = fieldIDList.indexOf( elementID );
		
		// Now that we know the index of the field, we can replace it with the new visible
		fieldVisibleList[elementIndex] = newVisible;
		
		// Now we need to check to see if we are not in field editing mode
		if( userLevel != 2 )
		{
			// If we are not in field editing mode, then change the hidden property
			// of the field to the opposite of newVisible.
			whatElement.style.visibility = visibilityProperty;
		}
	}
	else if( elementType == "block" )
	{
		// If the element is a block, then we need to use the block lists
		
		// Now we need to find the index of the block in the ID list
		elementIndex = blockIDList.indexOf( elementID );
		
		// Now that we know the index of the block, we can replace it with the new visible
		blockVisibleList[elementIndex] = newVisible;
		
		// Now we need to check to see if we are not in block editing mode
		if( userLevel != 3 )
		{
			// If we are not in block editing mode, then change the hidden property
			// of the block to the opposite of newVisible.
			whatElement.style.visibility = visibilityProperty;
		}
	}
}

function hpop__getStyle( whatElement )
{
	var elementIndex;
	var elementStyle;
	// This function is used to return the style of an element, from it's value in the list
	
	// First we need to find the element's ID
	var elementID = hpop__getID( whatElement );
	
	// Now we need to find out the type of the element, so we can use the correct lists
	var elementType = hpop__getType( whatElement );
	
	if( elementType == "button" )
	{
		// If the element is a button, then we need to use the button lists
		
		// Now we need to find the index of the button in the ID list
		elementIndex = buttonIDList.indexOf( elementID );
		
		// Now we can find out the elements visible from the visible list
		elementStyle = buttonStyleList[elementIndex];
	}
	else if( elementType == "field" )
	{
		// If the element is a field, then we need to use the field lists
		
		// Now we need to find the index of the field in the ID list
		elementIndex = fieldIDList.indexOf( elementID );
		
		// Now we can find out the elements visible from the visible list
		elementStyle = fieldStyleList[elementIndex];
	}
	else if( elementType == "block" )
	{
		// If the element is a block, then we need to use the block lists
		
		// Now we need to find the index of the block in the ID list
		elementIndex = blockIDList.indexOf( elementID );
		
		// Now we can find out the elements visible from the visible list
		elementStyle = blockStyleList[elementIndex];
	}
	// Now that we have the numerical style, we need to convert it into the worded style
	// using the convertNumStyle function
	elementStyle = convertNumStyle( elementStyle, whatElement );
	
	// Now that we have found the visible of the element, we can return it
	return elementStyle;
}

function hpop__setStyle( whatElement, newStyle )
{
	var oldStyle;
	var elementIndex;
	// This is the function that is used to set the style of an element.
	
	// There are two parts for setting the style of an element. The first is to
	// change the style in the list, and the second is to actually change the
	// class of the element to reflect the new style. However, we only change the
	// class if we are not in that element's editing mode, to make sure that the
	// grey square button that is used whilst in editing mode is not changed by
	// accident.
	
	// The style of elements are stored in lists, so we will need to find out
	// the index of the old style in the list, so that we can write over it with
	// the new style.
	
	// First we need to get the ID of the element
	var elementID = hpop__getID( whatElement );
	
	// To set the class property of the element, we need to convert the text received from
	// newStyle into the class names that can be easily used. The only thing that could be
	// different is the case, so just to be sure, we will make it all lower case.
	var newStyle = newStyle.toLowerCase();
	
	// We also need to know the number style, so we can put it into the lists. To find this,
	// we can use the convertWordStyle function
	var numStyle = convertWordStyle( newStyle, whatElement );
	
	// Now we need to find out the type of the element, so we can search in the correct list
	var elementType = hpop__getType( whatElement );
	
	if( elementType == "button" )
	{
		// If the element is a button, then we need to use the button lists
		
		// But first, we need to actually change the button's appearance, if
		// we are not in button editing mode.
		//if( userLevel != 1 )
		//{
			// If we are not in button editing mode, then we can actually change the appearance
			// of the button.
			
			// The first thing we need to do is see if we are going to be changing the class of
			// the button, or is we will actually be making it a checkbox, radiobutton or popup
			var elementID = whatElement.id;
			if( newStyle == "checkbox" )
			{
				// We can use the hpop__setCheck function here
				hpop__setCheck( whatElement, true );
				whatElement = document.getElementById(elementID);
				whatElement.className = newStyle;
			}
			else if( newStyle == "radiobutton" )
			{
				hpop__setRadio( whatElement, true );
				whatElement = document.getElementById(elementID);
				whatElement.className = newStyle;
			}
			else if( newStyle == "popup" )
			{
				alert("jsCard Error: The style 'popup' has not been implemented yet.");
			}
			else
			{
				// If we are not changing the style to a radio, check or popup, then we need see
				// if we are to be changing it from a checkbox, radiobutton or popup.
				oldStyle = hpop__getStyle( whatElement );
				if( oldStyle == "radiobutton" || oldStyle == "checkbox" )
				{
					// If the element is currently a checkbox or radiobutton, then we need to convert
					// it back to a regular button before we can set the actual style.
					hpop__setRound( whatElement );
					whatElement = document.getElementById(elementID);
				}
				else if( oldStyle == "popup" )
				{
					alert("jsCard Error: The style 'popup' has not been implemented.");
				}
				// Now that the button is a 'regular' button, we can set the correct class.
				
				// However, if the button has been converted from or to a checkbox/radiobutton,
				// trying to select it with whatElement will not work, as this refers to an
				// element that has been deleted. Therefore, we need to reselect it.
				whatElement = document.getElementById(elementID);
				
				// We also need to see what the hilite and autohilite is, so we can set the correct
				// class for the element
				if( ( ! hpop__getAutoHilite( whatElement ) ) && ( hpop__getHilite( whatElement ) ) )
				{
					newStyle = newStyle + "Hilite" + "NoAuto";
				}
				else if( hpop__getHilite( whatElement ) )
				{
					newStyle = newStyle + "Hilite";
				}
				else if( ! hpop__getAutoHilite( whatElement ) )
				{
					newStyle = newStyle + "NoAuto";
				}
				whatElement.className = newStyle;
			}
		//}
		//else
		//{
			
		//}
		if( userLevel == 1 )
		{
			if( newStyle == "checkbox" || newStyle == "radiobutton" )
			{
				whatElement.firstChild.firstChild.firstChild.firstChild.disabled = true;
			}
			whatElement.className = "btn_editing_selected";
		}
		// Now we can update the lists
		// Now we need to find the index of the button in the ID list
		elementIndex = buttonIDList.indexOf( elementID );
		
		// Now that we know the index of the button, we can replace it with the new style
		buttonStyleList[elementIndex] = numStyle;
	}
	else if( elementType == "field" )
	{
		// If the element is a field, then we need to use the field lists
		
		// Now we need to find the index of the field in the ID list
		elementIndex = fieldIDList.indexOf( elementID );
		
		// Now that we know the index of the field, we can replace it with the new style
		fieldStyleList[elementIndex] = numStyle;
		
		// Now we need to check to see if we are not in field editing mode
		if( userLevel != 2 )
		{
			// If we are not in field editing mode, then we can actually change the class
			// of the field to the newStyle
			whatElement.className = "fld_" + newStyle;
		}
	}
	else if( elementType == "block" )
	{
		// If the element is a block, then we need to use the block lists
		
		// Now we need to find the index of the block in the ID list
		elementIndex = blockIDList.indexOf( elementID );
		
		// Now that we know the index of the block, we can replace it with the new style
		blockStyleList[elementIndex] = numStyle;
		
		// Now we need to check to see if we are not in block editing mode
		if( userLevel != 3 )
		{
			// If we are not in block editing mode, then we can actually change the class
			// of the block to the newStyle
			whatElement.className = newStyle;
		}
	}
}

function hpop__getFamily( whatElement )
{
	// This function is used to return the family of an button
	
	// First we need to find the button's ID
	var buttonID = hpop__getID( whatElement );
	
	// Then we need to find the button's index in the ID list
	var buttonIndex = buttonIDList.indexOf( buttonID );
	
	// Now we can find the family from the list
	var buttonFamily = buttonFamilyList[buttonIndex];
	
	return buttonFamily;
}

function hpop__setFamily( whatElement, newFamily )
{
	// This function is used to change the family of a button
	
	// There are two parts to changing the family. The first is to change the family
	// in the buttonFamilyList list, and the second is to check to see if the button
	// is a checkbox or radiobutton, and if so, change the name property OF THE ACTUAL
	// CHECKBOX OR RADIOBUTTON (not the table which surrounds it).
	
	// First we need to find the button's ID
	var buttonID = hpop__getID( whatElement );
	
	// Then we need to find the button's index in the ID list
	var buttonIndex = buttonIDList.indexOf( buttonID );
	
	// Now that we know the index, we can actually change the family in the array.
	buttonFamilyList[buttonIndex] = newFamily;
	
	// Now we need to check to see if it is a checkbox or radiobutton
	var buttonStyle = hpop__getStyle( whatElement );
	if( buttonStyle == "checkbox" || buttonStyle == "radiobutton" )
	{
		whatElement.firstChild.firstChild.firstChild.firstChild.setAttribute("name",newFamily);
	}
}

function hpop__getLockText( whatElement )
{
	var elementLockText;
	// This function is used to return the locktext of an field, from it's value in the list
	
	// First we need to find the element's ID
	var elementID = hpop__getID( whatElement );
	
	// Now we need to find out the type of the element, so we can use the correct lists
	var elementType = hpop__getType( whatElement );
	
	if( elementType == "field" )
	{
		// Now we need to find the index of the field in the ID list
		var elementIndex = fieldIDList.indexOf( elementID );
		
		// Now we can find out the elements visible from the visible list
		elementLockText = fieldLockTextList[elementIndex];
	}
	else
	{
		alert("jsCard Error: The locktext property only works on fields.");
	}
	
	// Now that we have found the visible of the element, we can return it
	return elementLockText;
}

function hpop__setLockText( whatElement, newLockText )
{
	// This is the function that is used to set the locktext of an element.
	
	// First we need to get the ID of the element
	var elementID = hpop__getID( whatElement );
	
	// Now we need to find out the type of the element, so we can search in the correct list
	var elementType = hpop__getType( whatElement );
	
	if( elementType == "field" )
	{
		// Now we need to find the index of the field in the ID list
		var elementIndex = fieldIDList.indexOf( elementID );
		
		// Now that we know the index of the field, we can replace it with the new locktext
		fieldLockTextList[elementIndex] = newLockText;
		
		// Now we need to check to see if we are not in field editing mode
		if( userLevel != 2 )
		{
			// If we are not in field editing mode, then change the hidden property
			// of the field to the opposite of newVisible.
			whatElement.readOnly = newLockText;
		}
	}
	else
	{
		alert("jsCard Error: The locktext property only works on fields.");
	}
}

function hpop__getHilite( whatElement )
{
	// This function is used to get the hilite of elements
	
	// First we need to find out the type of the element
	var elementType = hpop__getType( whatElement );
	
	// Then we need to find out the element's ID
	var elementID = hpop__getID( whatElement );
	
	if( elementType == "button" )
	{
		// If the element is a button, then we need to use the button lists
		
		/*if( userlevel === 0 )
		{
			elementStyle = hpop__getStyle( whatElement );
			if( elementStyle == "checkbox" || elementStyle == "radiobutton" )
			{
				return whatElement.firstChild.firstChild.firstChild.firstChild.checked;
			}
		}*/
		
		// Now we need to find the index of the button in the ID list
		var elementIndex = buttonIDList.indexOf( elementID );
		
		// Now we can retrieve the enabled from the enabled list
		var elementHilite = buttonHiliteList[elementIndex];
		
		return elementHilite;
	}
	else
	{
		alert("jsCard Error: The 'hilite' property only works for buttons.");
	}
}


function hpop__setHilite( whatElement, newHilite )
{
	var elementClass;
	// This is the function that is used to set the hilite of an element.
	
	// There are two parts for setting the hilite of an element. The first is to
	// change the hilite in the list, and the second is to actually change the
	// class/checked  of the element. However, we only change the class/checked property
	// if we are not in that element's editing mode, to avoid the user not being able
	// to select that element for editing. The element's hilite in the list will still
	// be changed, so when the user exits out of the elements editing mode, the correct
	// hilite will be set.
	
	// The hilite of elements are stored in lists, so we will need to find out
	// the index of the old enabled in the list, so that we can write over it with
	// the new hilite.
	
	// First we need to get the ID of the element
	var elementID = hpop__getID( whatElement );
	
	// Now we need to find out the type of the element, so we can search in the correct list
	var elementType = hpop__getType( whatElement );
	
	if( elementType == "button" )
	{
		// If the element is a button, then we need to use the button lists
		
		// Now we need to find the index of the button in the ID list
		elementIndex = buttonIDList.indexOf( elementID );
		
		// Now that we know the index of the button, we can replace it with the new hilite
		buttonHiliteList[elementIndex] = newHilite;
		
		// Now we need to check to see if we are not in button editing mode
		if( userLevel != 1 )
		{
			// If we are not in button editing mode, then change the class/checked property
			// of the button to the newHilite
			var elementStyle = hpop__getStyle( whatElement );
			if( elementStyle == "checkbox" || elementStyle == "radiobutton" )
			{
				whatElement.firstChild.firstChild.firstChild.firstChild.checked = newHilite;
			}
			else if( elementStyle == "popup" )
			{
				alert("jsCard Error: Popup buttons have not been implemented yet");
			}
			else
			{
				if( newHilite )
				{
						if( hpop__getAutoHilite( whatElement ) )
						{
							elementClass = elementStyle + "Hilite";
						}
						else
						{
							elementClass = elementStyle + "HiliteNoAuto";
						}
						whatElement.className = elementClass;
				}
				else
				{
					if( ! hpop__getAutoHilite( whatElement ) )
					{
						whatElement.className = elementStyle + "NoAuto";
					}
					else
					{
						whatElement.className = elementStyle;
					}
				}
			}
		}
	}
	else
	{
		alert("jsCard Error: The hilite property can only be set for buttons.");
	}
}


function hpop__getAutoHilite( whatElement )
{
	// This function is used to get the autohilite of elements
	
	// First we need to find out the type of the element
	var elementType = hpop__getType( whatElement );
	
	// Then we need to find out the element's ID
	var elementID = hpop__getID( whatElement );
	
	if( elementType == "button" )
	{
		// If the element is a button, then we need to use the button lists
		
		// Now we need to find the index of the button in the ID list
		var elementIndex = buttonIDList.indexOf( elementID );
		
		// Now we can retrieve the enabled from the enabled list
		var elementAutoHilite = buttonAutoHiliteList[elementIndex];
		
		return elementAutoHilite;
	}
	else
	{
		alert("jsCard Error: The 'autohilite' property only works for buttons.");
	}
}


function hpop__setAutoHilite( whatElement, newAutoHilite )
{
	var elementClass;
	// This is the function that is used to set the autohilite of an element.
	
	// There are two parts for setting the autohilite of an element. The first is to
	// change the autohilite in the list, and the second is to actually change the
	// class  of the element. However, we only change the class property
	// if we are not in that element's editing mode, to avoid the user not being able
	// to select that element for editing. The element's auothilite in the list will still
	// be changed, so when the user exits out of the elements editing mode, the correct
	// autohilite will be set.
	
	// The auothilite of elements are stored in lists, so we will need to find out
	// the index of the old enabled in the list, so that we can write over it with
	// the new autohilite.
	
	if( newAutoHilite == "true" )
	{
		newAutoHilite = true;
	}
	else if( newAutoHilite == "false" )
	{
		newAutoHilite = false;
	}
	
	// First we need to get the ID of the element
	var elementID = hpop__getID( whatElement );
	
	// Now we need to find out the type of the element, so we can search in the correct list
	var elementType = hpop__getType( whatElement );
	
	if( elementType == "button" )
	{
		// If the element is a button, then we need to use the button lists
		
		// Now we need to find the index of the button in the ID list
		var elementIndex = buttonIDList.indexOf( elementID );
		
		// Now that we know the index of the button, we can replace it with the new autohilite
		buttonAutoHiliteList[elementIndex] = newAutoHilite;
		
		// Now we need to check to see if we are not in button editing mode
		if( userLevel != 1 )
		{
			// If we are not in button editing mode, then change the class
			// of the button to the newAutoHilite
			// Now we need to see what the style of the button is
			elementStyle = hpop__getStyle( whatElement );
			if( elementStyle == "checkbox" || elementStyle == "radiobutton" )
			{
				// If the element is a radiobutton, checkbox, or popup, then do nothing
				//whatElement.firstChild.firstChild.firstChild.firstChild.checked = newHilite;
			}
			else if( elementStyle == "popup" )
			{
				
			}
			else
			{
				if( ! newAutoHilite )
				{
					if( hpop__getHilite( whatElement ) )
					{
						elementClass = elementStyle + "HiliteNoAuto";
					}
					else
					{
						elementClass = elementStyle + "NoAuto";
					}
					whatElement.className = elementClass;
				}
				else
				{
					if( hpop__getHilite( whatElement ) )
					{
						elementClass = elementStyle + "Hilite";
					}
					else
					{
						elementClass = elementStyle;
					}
					whatElement.className = elementClass;
				}
			}
		}
	}
	else
	{
		alert("jsCard Error: The hilite property can only be set for buttons.");
	}
}

function hpop__setFieldValue( whatElement, whatValue, whatPlace )
{
	if( hpop__getType( whatElement ) == "field" )
	{
		if( whatPlace == 'into' )
		{
			if( ! hpop__getAutoSelect( whatElement ) )
			{
				whatElement.value = whatValue;
			}
			else
			{
				whatElement.innerHTML = "";
				whatValue = whatValue.split('\n');
				
				i = 0;
				var thisOption = new Option( whatValue[i], whatValue[i] );
				whatElement.appendChild(thisOption);
				i++;
				
				while( i < whatValue.length )
				{
					thisOption = new Option( whatValue[i], whatValue[i] );
					whatElement.insertBefore(thisOption, whatElement.firstChild);
					i++;
				}
			}
		}
		else if ( whatPlace == 'before' )
		{
			if( ! hpop__getAutoSelect( whatElement ) )
			{
				whatElement.value = whatValue + '' + whatElement.value;
			}
			else
			{
				whatValue = whatValue.split('\n');
				i = whatValue.length - 1;
				if( whatElement.hasChildNodes() )
				{
					whatElement.firstChild.innerHTML = whatValue[i] + whatElement.firstChild.innerHTML;
					i = i - 1;
				}
				while( i >= 0 )
				{
					var thisOption = new Option( whatValue[i], whatValue[i] );
					whatElement.insertBefore(thisOption, whatElement.firstChild);
					i = i - 1;
				}
			}
		}
		else if ( whatPlace == "after" )
		{
			if( ! hpop__getAutoSelect( whatElement ) )
			{
				whatElement.value = whatElement.value + '' + whatValue;
			}
			else
			{
				whatValue = whatValue.split('\n');
				if( whatElement.hasChildNodes() )
				{
					whatElement.lastChild.innerHTML = whatElement.lastChild.innerHTML + whatValue[0];
				}
				for( var i = 1; i < whatValue.length; i++ )
				{
					var thisOption = new Option( whatValue[i], whatValue[i] );
					whatElement.appendChild(thisOption);
				}
			}
		}
	}
}

function hpop__setBlockValue( whatElement, whatValue, whatPlace )
{
	if( hpop__getType( whatElement ) == "block" )
	{
		var elementID = hpop__getID( whatElement );
		var elementIndex = blockIDList.indexOf( elementID );
		
		if ( whatPlace == 'into' )
		{
			blockContentsList[elementIndex] = whatValue;
			if( userLevel != 3 )
			{
				whatElement.innerHTML = whatValue;
			}
		}
		else if ( whatPlace == 'before' )
		{
			blockContentsList[elementIndex] = whatValue + blockContentsList[elementIndex];
			if( userLevel != 3 )
			{
				whatElement.innerHTML = whatValue + '' + whatElement.innerHTML;
			}
		}
		else
		{
			blockContentsList[elementIndex] = blockContentsList[elementIndex] + whatValue;
			if( userLevel != 3 )
			{
				whatElement.innerHTML = whatElement.innerHTML + '' + whatValue;
			}
		}
	}
}

function hpop__getBlockValue( whatElement )
{
	var elementID = hpop__getID( whatElement );
	var elementIndex = blockIDList.indexOf( elementID );
	
	return blockContentsList[elementIndex];
}

function hpop__getContents( whatElement )
{
	// This function gets the value of the field whatElement, and returns it to the user
	
	// First, we need to see if the element whatElement is a <textarea> element
	if( hpop__getType( whatElement ) == "field" )
	{
		if ( whatElement.tagName == 'SELECT' )
		{
			// It is an autoSelect field - get all the lines together
			var newElementValue = "";
			
			for( var i = 0; i < whatElement.options.length ; i ++ )
			{
				newElementValue += whatElement.options[i].value + '\n';
			}
			
			return newElementValue.substr( 0, newElementValue.length - 1 );
		}
		else
		{
			// If it is a field, we can simply return it's value
			return whatElement.value;
		}
	}
	else if( hpop__getType( whatElement ) == "block" )
	{
		return hpop__getBlockValue( whatElement );
	}
}

function hpop__getType( whatElement )
{
	var elementName = whatElement.getAttribute('name');
	if (elementName.substring(0, 3) == "btn")
	{
		return "button";
	}
	else if (elementName.substring(0, 3) == "fld")
	{
		return "field";
	}
	else if (elementName.substring(0, 3) == "blk")
	{
		return "block";
	}
}

function hpop__getShowName( whatElement )
{
	if( hpop__getType( whatElement ) == "button" )
	{
		if( hpop__getStyle( whatElement ) == "checkbox" || hpop__getStyle( whatElement ) == "radiobutton" )
		{
			return whatElement.firstChild.firstChild.children[1].firstChild.innerHTML !== "";
		}
		else
		{
			return whatElement.innerHTML !== "";
		}
	}
}

function hpop__setShowName( whatElement, newShowName )
{
	if( hpop__getType( whatElement ) == "button" )
	{
		if( newShowName )
		{
			if( hpop__getStyle( whatElement ) == "checkbox" || hpop__getStyle( whatElement ) == "radiobutton" )
			{
				whatElement.firstChild.firstChild.children[1].firstChild.innerHTML = hpop__getShortName( whatElement );
			}	
			else
			{
				whatElement.innerHTML = hpop__getShortName( whatElement );
			}
		}
		else
		{
			if( hpop__getStyle( whatElement ) == "checkbox" || hpop__getStyle( whatElement ) == "radiobutton" )
			{
				whatElement.firstChild.firstChild.children[1].firstChild.innerHTML = "";
			}	
			else
			{
				whatElement.innerHTML = "";
			}
		}
	}
}

function hpop__getRect( whatElement )
{
	var top = parseInt(whatElement.style.top,10);
	var left = parseInt(whatElement.style.left,10);
	var bottom = (parseInt(whatElement.offsetTop,10)) + (parseInt(whatElement.offsetHeight,10));
	var right = (parseInt(whatElement.offsetLeft,10)) + (parseInt(whatElement.offsetWidth,10));
	return top + "," + left + "," + bottom + "," + right;
}

function hpop__setRect( whatElement, newRect )
{
	newRect = newRect.replace(/ /g, '');
	newRect = newRect.split(",");
	whatElement.style.top = ( newRect[0] );
	whatElement.style.left = ( newRect[1] );
	var newHeight = newRect[2] - newRect[0];
	var newWidth = newRect[3] - newRect[1];
	whatElement.style.width = ( newWidth );
	whatElement.style.height = ( newHeight );
}