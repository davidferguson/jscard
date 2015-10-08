// HyperTalk-esque Function Library
// (c) 2006 Rebecca Bettencourt
// You may do anything you want with this code,
// provided the copyright notice is kept intact.

function i_(x)
{
	if (typeof x == "string") return parseInt(x);
	if (typeof x == "number") return Math.round(x);
	if (typeof x == "boolean") return (x)?(1):(0);
	return NaN;
}

function n_(x)
{
	if (typeof x == "string") return parseFloat(x);
	if (typeof x == "number") return x;
	if (typeof x == "boolean") return (x)?(1.0):(0.0);
	return NaN;
}

function s_(x)
{
	if (typeof x == "string") return x;
	if (typeof x == "number") return x.toString(10);
	if (typeof x == "boolean") return (x)?("true"):("false");
	return "";
}

function b_(x)
{
	if (typeof x == "string") return (x == "true" || x == "on" || x == "yes");
	if (typeof x == "number") return (x != 0);
	if (typeof x == "boolean") return x;
	return false;
}

function min()
{
	var i;
	var j;
	var k;
	if (arguments.length == 0) return NaN;
	j = n_(arguments[0]);
	for (i=1; i<arguments.length; i++)
	{
		k = n_(arguments[i]);
		if (k<j) { j=k; }
	}
	return j;
}

function max()
{
	var i;
	var j;
	var k;
	if (arguments.length == 0) return NaN;
	j = n_(arguments[0]);
	for (i=1; i<arguments.length; i++)
	{
		k = n_(arguments[i]);
		if (k>j) { j=k; }
	}
	return j;
}

function sum()
{
	var i;
	var j=0;
	for (i=0; i<arguments.length; i++)
	{
		j += n_(arguments[i]);
	}
	return j;
}

function product()
{
	var i;
	var j=1;
	for (i=0; i<arguments.length; i++)
	{
		j *= n_(arguments[i]);
	}
	return j;
}

function average()
{
	var i;
	var j=0;
	for (i=0; i<arguments.length; i++)
	{
		j += n_(arguments[i]);
	}
	return j/arguments.length;
}

function svariance()
{
	var i;
	var j=0;
	var k=0;
	for (i=0; i<arguments.length; i++)
	{
		j += n_(arguments[i]);
		k += n_(arguments[i])*n_(arguments[i]);
	}
	return (k - j * j / arguments.length) / (arguments.length-1);
}

function pvariance()
{
	var i;
	var j=0;
	var k=0;
	for (i=0; i<arguments.length; i++)
	{
		j += n_(arguments[i]);
		k += n_(arguments[i])*n_(arguments[i]);
	}
	return (k - j * j / arguments.length) / arguments.length;
}

function sstddev()
{
	var i;
	var j=0;
	var k=0;
	for (i=0; i<arguments.length; i++)
	{
		j += n_(arguments[i]);
		k += n_(arguments[i])*n_(arguments[i]);
	}
	return Math.sqrt((k - j * j / arguments.length) / (arguments.length-1));
}

function pstddev()
{
	var i;
	var j=0;
	var k=0;
	for (i=0; i<arguments.length; i++)
	{
		j += n_(arguments[i]);
		k += n_(arguments[i])*n_(arguments[i]);
	}
	return Math.sqrt((k - j * j / arguments.length) / arguments.length);
}

function geomean()
{
	var i;
	var j=1;
	for (i=0; i<arguments.length; i++)
	{
		j *= n_(arguments[i]);
	}
	return Math.pow(j,1/arguments.length);
}

function left(x,y)
{
	return s_(x).substr(0,n_(y));
}

function mid()
{
	if (arguments.length == 2) {
		return s_(arguments[0]).substr(n_(arguments[1])-1);
	} else if (arguments.length == 3) {
		return s_(arguments[0]).substr(n_(arguments[1])-1,n_(arguments[2]));
	} else {
		return "";
	}
}

function right(x,y)
{
	return s_(x).substr(-n_(y),n_(y));
}

function instr(x,y)
{
	return s_(x).indexOf(s_(y))+1;
}

function rinstr(x,y)
{
	return s_(x).lastIndexOf(s_(y))+1;
}

function ucase(x)
{
	return s_(x).toUpperCase();
}

function lcase(x)
{
	return s_(x).toLowerCase();
}

function offset(x,y)
{
	return s_(y).indexOf(s_(x))+1;
}

function annuity(rate,pds)
{
	return (1 - Math.pow(1 + n_(rate), -n_(pds))) / n_(rate);
}

function compound(rate,pds)
{
	return Math.pow(1 + n_(rate), n_(pds));
}

function random(x)
{
	return Math.floor(Math.random() * n_(x))+1;
}

function floor(x)
{
	return Math.floor(n_(x));
}

function ceil(x)
{
	return Math.ceil(n_(x));
}

function round(x)
{
	return Math.round(n_(x));
}

function trunc(x)
{
	var y = n_(x);
	return (y<0)?(Math.ceil(y)):(Math.floor(y));
}

function abs(x)
{
	return Math.abs(n_(x));
}

function sgn(x)
{
	var y = n_(x);
	if (y<0) { return -1; }
	else if (y>0) { return 1; }
	else { return 0; }
}

function sqrt(x)
{
	return Math.sqrt(n_(x));
}

function cbrt(x)
{
	var y = n_(x);
	if (y<0) {
		return -Math.pow(-y,1.0/3.0);
	} else {
		return Math.pow(y,1.0/3.0);
	}
}

function exp(x)
{
	return Math.exp(n_(x));
}

function exp1(x)
{
	return Math.exp(n_(x))-1;
}

function exp2(x)
{
	return Math.pow(2.0,n_(x));
}

function exp10(x)
{
	return Math.pow(10.0,n_(x));
}

function ln(x)
{
	return Math.log(n_(x));
}

function ln1(x)
{
	return Math.log(n_(x)+1);
}

function log2(x)
{
	return Math.log(n_(x))/Math.log(2.0);
}

function log10(x)
{
	return Math.log(n_(x))/Math.log(10.0);
}

function sin(x)
{
	return Math.sin(n_(x));
}

function cos(x)
{
	return Math.cos(n_(x));
}

function tan(x)
{
	return Math.tan(n_(x));
}

function csc(x)
{
	return 1/Math.sin(n_(x));
}

function sec(x)
{
	return 1/Math.cos(n_(x));
}

function cot(x)
{
	return 1/Math.tan(n_(x));
}

function asin(x)
{
	return Math.asin(n_(x));
}

function acos(x)
{
	return Math.acos(n_(x));
}

function atan()
{
	if (arguments.length == 1) {
		return Math.atan(n_(arguments[0]));
	} else if (arguments.length == 2) {
		return Math.atan2(n_(arguments[0]),n_(arguments[1]));
	}
	return NaN;
}

function atan2(y,x)
{
	return Math.atan2(n_(y),n_(x));
}

function acsc(x)
{
	return Math.asin(1/n_(x));
}

function asec(x)
{
	return Math.acos(1/n_(x));
}

function acot(x)
{
	return Math.atan(1/n_(x));
}

function sinh(x)
{
	return ( Math.exp(n_(x)) - Math.exp(-n_(x)) ) / 2.0;
}

function cosh(x)
{
	return ( Math.exp(n_(x)) + Math.exp(-n_(x)) ) / 2.0;
}

function tanh(x)
{
	return ( Math.exp(n_(x)) - Math.exp(-n_(x)) ) / ( Math.exp(n_(x)) + Math.exp(-n_(x)) );
}

function csch(x)
{
	return 2.0 / ( Math.exp(n_(x)) - Math.exp(-n_(x)) );
}

function sech(x)
{
	return 2.0 / ( Math.exp(n_(x)) + Math.exp(-n_(x)) );
}

function coth(x)
{
	return ( Math.exp(n_(x)) + Math.exp(-n_(x)) ) / ( Math.exp(n_(x)) - Math.exp(-n_(x)) );
}

function asinh(x)
{
	return Math.log(n_(x) + Math.sqrt(n_(x)*n_(x) + 1));
}

function acosh(x)
{
	return Math.log(n_(x) + Math.sqrt(n_(x)-1) * Math.sqrt(n_(x)+1));
}

function atanh(x)
{
	return (Math.log(1+n_(x)) - Math.log(1-n_(x)))/2;
}

function acsch(x)
{
	return asinh(1/n_(x));
}

function asech(x)
{
	return acosh(1/n_(x));
}

function acoth(x)
{
	return atanh(1/n_(x));
}

function theta(x,y)
{
	return Math.atan2(n_(y),n_(x));
}

function radius(x,y)
{
	return Math.sqrt(n_(x)*n_(x) + n_(y)*n_(y));
}

function xcoord(r,th)
{
	return n_(r)*Math.cos(n_(th));
}

function ycoord(r,th)
{
	return n_(r)*Math.sin(n_(th));
}

function factorial(x)
{
	var y = i_(x);
	var i;
	var j=1;
	if (y<0) { return NaN; }
	for (i=y; i>0; i--) {
		j *= i;
	}
	return j;
}

function pick(n,k)
{
	return factorial(n)/factorial(n-k);
}

function choose(n,k)
{
	return factorial(n)/(factorial(k)*factorial(n-k));
}

function seconds()
{
	var d = new Date(); //today's date
	var e = new Date(); //epoch
	e.setTime(0);
	e.setFullYear(1904);
	return Math.floor((d.getTime()-e.getTime())/1000);
}

function secs()
{
	return seconds();
}

function time()
{
	var d = new Date();
	if (arguments.length == 0 || arguments[0] == 'short' || arguments[0] == 'abbr' || arguments[0] == 'abbrev' || arguments[0] == 'abbreviated') {
		var h = (d.getHours() % 12);
		var m = ('00'+s_(d.getMinutes())).substr(-2,2);
		var s = ('00'+s_(d.getSeconds())).substr(-2,2);
		var a = (d.getHours() < 12)?("AM"):("PM");
		if (!h) { h = 12; }
		return s_(h)+':'+m+' '+a;
	} else if (arguments[0] == 'long' || arguments[0] == 'english') {
		var h = (d.getHours() % 12);
		var m = ('00'+s_(d.getMinutes())).substr(-2,2);
		var s = ('00'+s_(d.getSeconds())).substr(-2,2);
		var a = (d.getHours() < 12)?("AM"):("PM");
		if (!h) { h = 12; }
		return s_(h)+':'+m+':'+s+' '+a;
	} else {
		return '';
	}
}

function date()
{
	var d = new Date();
	if (arguments.length == 0 || arguments[0] == 'short') {
		var m = d.getMonth()+1;
		var da = d.getDate();
		var y = d.getFullYear();
		return s_(m)+'/'+s_(da)+'/'+s_(y);
	} else if (arguments[0] == 'abbr' || arguments[0] == 'abbrev' || arguments[0] == 'abbreviated') {
		var days = new Array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
		var months = new Array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
		var dw = days[d.getDay()];
		var m = months[d.getMonth()];
		var da = d.getDate();
		var y = d.getFullYear();
		return dw+', '+m+' '+s_(da)+', '+s_(y);
	} else if (arguments[0] == 'long' || arguments[0] == 'english') {
		var days = new Array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
		var months = new Array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
		var dw = days[d.getDay()];
		var m = months[d.getMonth()];
		var da = d.getDate();
		var y = d.getFullYear();
		return dw+', '+m+' '+s_(da)+', '+s_(y);
	} else {
		return '';
	}
}

function ticks()
{
	var d = new Date();
	var ms = d.getTime();
	return Math.round( ms / 16.66666666667 );
}

function milliseconds()
{
	var d = new Date();
	return d.getTime();
}

function version()
{
	return "0.3.1";
}

function mouseh()
{
	return Math.round(mousex);
}

function mousev()
{
	return Math.round(mousey);
}

function mouseloc()
{
	return mouseh() + "," + mousev();
}

function mouse()
{
	if(mouseDown)
	{
		return "down";
	}
	return "up";
}

function mouseclick()
{
	return mouseDown;
}

function commandkey()
{
	e = window.event;
	if( e.metaKey )
	{
		return "down";
	}
	else
	{
		return "up";
	}
}

function optionkey()
{
	e = window.event;
	if( e.altKey )
	{
		return "down";
	}
	else
	{
		return "up";
	}
}

function altkey()
{
	return optionkey();
}

function shiftkey()
{
	e = window.event;
	if( e.shiftKey )
	{
		return "down";
	}
	else
	{
		return "up";
	}
}

function selectedbutton(family)
{
	var buttonLength = buttonIDList.length;
	var counter = 0;
	var buttonFamily;
	var buttonHilite;
	var currentButton;
	while( counter < buttonLength )
	{
		currentButton = document.getElementById(buttonIDList[counter]);
		
		buttonFamily = hpop__getFamily(currentButton);
		if( buttonFamily == family )
		{
			buttonHilite = hpop__getHilite(currentButton);
			if( buttonHilite )
			{
				return "button id " + buttonIDList[counter].substring(3);
			}
		}
		counter++;
	}
	return false;
}

function clickloc()
{
	return clickLocation;
}

function clickv()
{
	return clickLocation.split(",")[1];
}

function clickh()
{
	return clickLocation.split(",")[0];
}

// HyperTalk functions NOT implemented:
// clickChunk, clickLine, clickText, clickH, clickV
// foundChunk, foundField, foundLine, foundText
// selectedChunk, selectedField, selectedLine, selectedText, selectedLoc
// screenRect, systemVersion
// menus, programs, stacks, windows, voices
// sound, speech, tool
// number, param, params, paramCount, result
// diskSpace, heapSpace, stackSpace
// destination, target

// value has been implemented in compiler.php