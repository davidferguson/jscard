function syncObjectToCoord( whatObject, coordx, coordy, offsx, offsy )
{
	whatObject.style.left = Math.round( coordx - offsx );
	whatObject.style.top = Math.round( coordy - offsy );
}

function syncObjectLeftToCoord( whatObject, coordx, coordy, offsx, offsy )
{
	var oldLeft = hpop__filterProperty( whatObject.style.left );
	if( ( hpop__filterProperty( whatObject.style.width ) * 1 ) + ( oldLeft - Math.round( coordx - offsx ) ) > dragMinimum )
	{
		whatObject.style.left = Math.round( coordx - offsx );
		whatObject.style.width = ( hpop__filterProperty( whatObject.style.width ) * 1 ) + ( oldLeft - Math.round( coordx - offsx ) );
	}
}

function syncObjectTopToCoord( whatObject, coordx, coordy, offsx, offsy )
{
	var oldTop = hpop__filterProperty( whatObject.style.top );
	if ( ( hpop__filterProperty( whatObject.style.height ) * 1 ) + ( oldTop - Math.round( coordy - offsy ) ) > dragMinimum )
	{
		whatObject.style.top = Math.round( coordy - offsy );
		whatObject.style.height = ( hpop__filterProperty( whatObject.style.height ) * 1 ) + ( oldTop - Math.round( coordy - offsy ) );
	}
	else
	{
		objectBottom = ( hpop__filterProperty( whatObject.style.top ) * 1 ) + ( hpop__filterProperty( whatObject.style.height ) * 1 );
		whatObject.style.height = dragMinimum;
		whatObject.style.top = objectBottom - dragMinimum;
	}
}

function syncObjectTopLeftToCoord( whatObject, coordx, coordy, offsx, offsy )
{
	var oldLeft = hpop__filterProperty( whatObject.style.left );
	var oldTop = hpop__filterProperty( whatObject.style.top );
	if ( ( ( hpop__filterProperty( whatObject.style.width ) * 1 ) + ( oldLeft - Math.round( coordx - offsx ) ) > dragMinimum ) )
	{
		whatObject.style.left = Math.round( coordx - offsx );
		whatObject.style.width = ( hpop__filterProperty( whatObject.style.width ) * 1 ) + ( oldLeft - Math.round( coordx - offsx ) );
	}
	else
	{
		objectRight = ( hpop__filterProperty( whatObject.style.left ) * 1 ) + ( hpop__filterProperty( whatObject.style.width ) * 1 );
		whatObject.style.width = dragMinimum;
		whatObject.style.left = objectRight - dragMinimum;
	}
	
	if ( ( ( hpop__filterProperty( whatObject.style.height ) * 1 ) + ( oldTop - Math.round( coordy - offsy ) ) > dragMinimum ) )
	{
		whatObject.style.top = Math.round( coordy - offsy );
		whatObject.style.height = ( hpop__filterProperty( whatObject.style.height ) * 1 ) + ( oldTop - Math.round( coordy - offsy ) );
	}
	else
	{
		objectBottom = ( hpop__filterProperty( whatObject.style.top ) * 1 ) + ( hpop__filterProperty( whatObject.style.height ) * 1 );
		whatObject.style.height = dragMinimum;
		whatObject.style.top = objectBottom - dragMinimum;
	}
}

function syncObjectRightToCoord( whatObject, coordx, coordy, offsx, offsy )
{
	var oldWidth = hpop__filterProperty( whatObject.style.width );
	var oldRight = (hpop__filterProperty( whatObject.style.left )*1) + (oldWidth*1);
	if ( ( oldWidth * 1 ) + ( coordx - oldRight ) + ( offsx * 1 ) > dragMinimum )
	{
		whatObject.style.width =  ( oldWidth * 1 ) + ( coordx - oldRight ) + ( offsx * 1 );
	}
	else
	{
		whatObject.style.width = dragMinimum;
	}
}

function syncObjectBottomToCoord( whatObject, coordx, coordy, offsx, offsy )
{
	var oldHeight = hpop__filterProperty( whatObject.style.height );
	var oldBottom = (hpop__filterProperty( whatObject.style.top )*1) + (oldHeight*1);
	if ( ( oldHeight * 1 ) + ( coordy - oldBottom ) + ( offsy * 1 ) > dragMinimum )
	{
		whatObject.style.height =  ( oldHeight * 1 ) + ( coordy - oldBottom ) + ( offsy * 1 );
	}
	else
	{
		whatObject.style.height = dragMinimum;
	}
}

function syncObjectBottomRightToCoord( whatObject, coordx, coordy, offsx, offsy )
{
	var oldHeight = hpop__filterProperty( whatObject.style.height );
	var oldBottom = (hpop__filterProperty( whatObject.style.top )*1) + (oldHeight*1);
	var oldWidth = hpop__filterProperty( whatObject.style.width );
	var oldRight = (hpop__filterProperty( whatObject.style.left )*1) + (oldWidth*1);
	
	if ( ( oldWidth * 1 ) + ( coordx - oldRight ) + ( offsx * 1 ) > dragMinimum )
	{
		whatObject.style.width =  ( oldWidth * 1 ) + ( coordx - oldRight ) + ( offsx * 1 );
	}
	else
	{
		whatObject.style.width = dragMinimum;
	}

	if ( ( oldHeight * 1 ) + ( coordy - oldBottom ) + ( offsy * 1 ) > dragMinimum )
	{
		whatObject.style.height =  ( oldHeight * 1 ) + ( coordy - oldBottom ) + ( offsy * 1 );
	}
	else
	{
		whatObject.style.height = dragMinimum;
	}
}

function syncObjectTopRightToCoord( whatObject, coordx, coordy, offsx, offsy )
{
	var oldWidth = hpop__filterProperty( whatObject.style.width );
	var oldRight = (hpop__filterProperty( whatObject.style.left )*1) + (oldWidth*1);
	var oldTop = hpop__filterProperty( whatObject.style.top );

	if ( ( oldWidth * 1 ) + ( coordx - oldRight ) + ( offsx * 1 ) > dragMinimum )
	{
		whatObject.style.width =  ( oldWidth * 1 ) + ( coordx - oldRight ) + ( offsx * 1 );
	}
	else
	{
		whatObject.style.width = dragMinimum;
	}

	if ( ( ( hpop__filterProperty( whatObject.style.height ) * 1 ) + ( oldTop - Math.round( coordy - offsy ) ) > dragMinimum ) )
	{
		whatObject.style.top = Math.round( coordy - offsy );
		whatObject.style.height = ( hpop__filterProperty( whatObject.style.height ) * 1 ) + ( oldTop - Math.round( coordy - offsy ) );
	}
	else
	{
		objectBottom = ( hpop__filterProperty( whatObject.style.top ) * 1 ) + ( hpop__filterProperty( whatObject.style.height ) * 1 );
		whatObject.style.height = dragMinimum;
		whatObject.style.top = objectBottom - dragMinimum;
	}
}

function syncObjectBottomLeftToCoord( whatObject, coordx, coordy, offsx, offsy )
{
	var oldHeight = hpop__filterProperty( whatObject.style.height );
	var oldBottom = (hpop__filterProperty( whatObject.style.top )*1) + (oldHeight*1);
	var oldLeft = hpop__filterProperty( whatObject.style.left );
	
	if ( ( oldHeight * 1 ) + ( coordy - oldBottom ) + ( offsy * 1 ) > dragMinimum )
	{
		whatObject.style.height =  ( oldHeight * 1 ) + ( coordy - oldBottom ) + ( offsy * 1 );
	}
	else
	{
		whatObject.style.height = dragMinimum;
	}
	
	if ( ( ( hpop__filterProperty( whatObject.style.width ) * 1 ) + ( oldLeft - Math.round( coordx - offsx ) ) > dragMinimum ) )
	{
		whatObject.style.left = Math.round( coordx - offsx );
		whatObject.style.width = ( hpop__filterProperty( whatObject.style.width ) * 1 ) + ( oldLeft - Math.round( coordx - offsx ) );
	}
	else
	{
		objectRight = ( hpop__filterProperty( whatObject.style.left ) * 1 ) + ( hpop__filterProperty( whatObject.style.width ) * 1 );
		whatObject.style.width = dragMinimum;
		whatObject.style.left = objectRight - dragMinimum;
	}
}