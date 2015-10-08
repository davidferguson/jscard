function hpop__binaryAdd ( x, y )
{
	return ( x * 1 ) + ( y * 1 );
}

function hpop__binarySubtract( x, y )
{
	return x - y;
}

function hpop__binaryMultiply( x, y )
{
	return x * y;
}

function hpop__binaryDivide( x, y )
{
	return x / y;
}

function hpop__binaryExp( x, y )
{
	return pow( x, y );
}

function hpop__binaryConcat( x, y )
{
	return x + "" + y;
}

function hpop__binaryConcat2( x, y )
{
	return x + " " + y;
}

function hpop__binaryGT( x, y )
{
	return x > y;
}

function hpop__binaryGTE( x, y )
{
	return x >= y;
}

function hpop__binaryLT( x, y )
{
	return x < y;
}

function hpop__binaryLTE( x, y )
{
	return x <= y;
}

function hpop__binaryEq( x, y )
{
	if ( isNaN( x ) )
	{
		x = x.toLowerCase();
	}
	
	if( isNaN( y ) )
	{
		y = y.toLowerCase();
	}
	return x == y;
}

function hpop__binaryNotEq( x, y )
{
	if ( isNaN( x ) )
	{
		x = x.toLowerCase();
	}
	
	if( isNaN( y ) )
	{
		y = y.toLowerCase();
	}

	return x != y;
}

function hpop__binaryAnd( x, y )
{
	return x && y;
}

function hpop__binaryOr( x, y )
{
	return x || y;
}
