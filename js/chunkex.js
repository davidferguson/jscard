// ------------------------------------------------------------------ //
// Chunk Expression Library for JavaScript                            //
// (c) 2006 Jonathynne/Ginny Rebecca Bettencourt / Kreative Software  //
// http://www.kreativekorp.com                                        //
//                                                                    //
// You may do anything you wish with this code, provided the above    //
// copyright notice remains intact. Please indicate any changes made. //
// ------------------------------------------------------------------ //

// -----------//
// How to Use //
// ---------- //
//
// cxl_count(text, tokens);
//     text is, for example, "Apple Computer, Inc."
//     tokens is an array, for example, ('chars', 'word', 2)
//     result in this case would be 9 (number of CHARS in WORD 2 of ...)
//
// cxl_get(text, tokens);
//     text is, for example, "Apple Computer, Inc."
//     tokens is an array, for example, ('char', 3, 4, 'word', 2, 3)
//     result in this case would be "mp" (get CHAR 3 to 4 of WORD 2 to 3 of ...)
//
// cxl_delete(text, tokens);
//     text is, for example, "Apple Computer, Inc."
//     tokens is an array, for example, ('char', 6, 8, 'word', 2)
//     result in this case would be "Apple Compu, Inc." (delete CHAR 6 to 8 of WORD 2 of ...)
//
// Get the idea?
//
// cxl_start(text, tokens) returns the position of the first
//     character of the chunk expression, starting from zero.
//
// cxl_end(text, tokens) returns the position of the last
//     character of the chunk expression, starting from zero, plus one.
//
// (These two are used with JavaScript's substring() method on strings.)
//
// cxl_into(text, tokens, newtext) inserts newtext
//     into (tokens) of text.
//
// cxl_before(text, tokens, newtext) inserts newtext
//     before (tokens) of text.
//
// cxl_after(text, tokens, newtext) inserts newtext
//     after (tokens) of text.
//
// *** *** *** NOTICE! *** *** ***
//
// This:
//
// text = cxl_delete(text, tokens);
//
// DOES NOT do the same thing as this:
//
// var s = cxl_start(text, tokens);
// var e = cxl_end(text, tokens);
// text = text.substring(0,s) + text.substring(e,text.length);
//
// E.g., with text = "Apple Computer, Inc.", tokens = ('word', 2),
// first method (cxl_delete) gives "Apple Inc.",
// second method (substring) gives "Apple  Inc.".
// Notice the extra space.
//

var cxl_mid = parseInt( "0x7FFFFFFF", 16 ); //middle, rounding up (HyperCard's middle)
var cxl_mdd = parseInt( "0x80000000", 16 ); //middle, rounding down
var cxl_any = parseInt( "0x80000001", 16 ); //any, randomly chosen; must call cxl_randomize() before using

var cxl_item_delimiter = ',';
var cxl_list_delimiter = '\b';
var cxl_element_delimiter = '\x7F';
var cxl_random = Math.random();

// ----------------- //
// Oddball Functions //
// ----------------- //

// must be called before any operation using cxl_any //
function cxl_randomize()
{
	cxl_random = Math.random();
}

function cxl_save_delimiters()
{
	var a = new Array(0);
	a[0] = cxl_item_delimiter;
	a[1] = cxl_list_delimiter;
	a[2] = cxl_element_delimiter;
	return a;
}

function cxl_restore_delimiters(a)
{
	cxl_item_delimiter = a[0];
	cxl_list_delimiter = a[1];
	cxl_element_delimiter = a[2];
}

function cxl_reset_delimiters()
{
	cxl_item_delimiter = ',';
	cxl_list_delimiter = '\b';
	cxl_element_delimiter = '\x7F';
}

// ---------------------------------- //
// Primitives - Count, Start, and End //
// ---------------------------------- //

function cxl_chunk_count(text, chunk)
{
	switch (chunk.toLowerCase()) {
	
	case 'char': case 'chars': case 'character': case 'characters':
		return text.length;
		break;
		
	case 'word': case 'words':
		return cxl_word_count(text);
		break;
		
	case 'item': case 'items':
		if (text.length == 0) return 0;
		return text.split(cxl_item_delimiter).length;
		break;
		
	case 'list': case 'lists':
		if (text.length == 0) return 0;
		return text.split(cxl_list_delimiter).length;
		break;
		
	case 'elem': case 'elems': case 'element': case 'elements':
		if (text.length == 0) return 0;
		return text.split(cxl_element_delimiter).length;
		break;
		
	case 'line': case 'lines':
		if (text.length == 0) return 0;
		return text.split('\n').length;
		break;
		
	case 'para': case 'paras': case 'paragraph': case 'paragraphs':
		return cxl_paragraph_count(text);
		break;
		
	case 'sent': case 'sents': case 'sentence': case 'sentences':
		return cxl_sentence_count(text);
		break;
		
	case 'byte': case 'bytes':
		return text.length;
		break;
		
	case 'short': case 'shorts':
		return Math.ceil(text.length / 2);
		break;
		
	case 'long': case 'longs':
		return Math.ceil(text.length / 4);
		break;
		
	default:
		return 0;
		break;
		
	}
}

function cxl_chunk_start(text, chunk, st, en)
{
	var count = cxl_chunk_count(text, chunk);
	var start = cxl_astart(chunk, st, count);
	var end   = cxl_aend(chunk, start, en, count);
	var cc, co;
	switch (chunk.toLowerCase()) {
	
	case 'char': case 'chars': case 'character': case 'characters':
		return start - 1;
		break;
		
	case 'word': case 'words':
		return cxl_word_start(text, start);
		break;
		
	case 'item': case 'items':
		if (start < 1) return 0;
		cc = 1; co = 0;
		while (1) {
			if (cc >= start) return co;
			cc++;
			co = text.indexOf(cxl_item_delimiter, co) + cxl_item_delimiter.length;
			if (co < cxl_item_delimiter.length) return text.length;
		}
		break;
		
	case 'list': case 'lists':
		if (start < 1) return 0;
		cc = 1; co = 0;
		while (1) {
			if (cc >= start) return co;
			cc++;
			co = text.indexOf(cxl_list_delimiter, co) + cxl_list_delimiter.length;
			if (co < cxl_list_delimiter.length) return text.length;
		}
		break;
		
	case 'elem': case 'elems': case 'element': case 'elements':
		if (start < 1) return 0;
		cc = 1; co = 0;
		while (1) {
			if (cc >= start) return co;
			cc++;
			co = text.indexOf(cxl_element_delimiter, co) + cxl_element_delimiter.length;
			if (co < cxl_element_delimiter.length) return text.length;
		}
		break;
		
	case 'line': case 'lines':
		if (start < 1) return 0;
		cc = 1; co = 0;
		while (1) {
			if (cc >= start) return co;
			cc++;
			co = text.indexOf('\n', co) + 1;
			if (co < 1) return text.length;
		}
		break;
		
	case 'para': case 'paras': case 'paragraph': case 'paragraphs':
		return cxl_paragraph_start(text, start);
		break;
		
	case 'sent': case 'sents': case 'sentence': case 'sentences':
		return cxl_sentence_start(text, start);
		break;
		
	case 'byte': case 'bytes':
		return start;
		break;
		
	case 'short': case 'shorts':
		return start*2;
		break;
		
	case 'long': case 'longs':
		return start*4;
		break;
		
	default:
		return 0;
		break;
		
	}
}

function cxl_chunk_end(text, chunk, st, en)
{
	var count = cxl_chunk_count(text, chunk);
	var start = cxl_astart(chunk, st, count);
	var end   = cxl_aend(chunk, start, en, count);
	switch (chunk.toLowerCase()) {
	
	case 'char': case 'chars': case 'character': case 'characters':
		return end;
		break;
		
	case 'word': case 'words':
		return cxl_word_end(text, end);
		break;
		
	case 'item': case 'items':
		if (end < 1) return 0;
		cc = 0; co = 0;
		while (1) {
			cc++;
			co = text.indexOf(cxl_item_delimiter, co);
			if (co < 0) return text.length;
			if (cc >= end) return co;
			co += cxl_item_delimiter.length;
		}
		break;
		
	case 'list': case 'lists':
		if (end < 1) return 0;
		cc = 0; co = 0;
		while (1) {
			cc++;
			co = text.indexOf(cxl_list_delimiter, co);
			if (co < 0) return text.length;
			if (cc >= end) return co;
			co += cxl_list_delimiter.length;
		}
		break;
		
	case 'elem': case 'elems': case 'element': case 'elements':
		if (end < 1) return 0;
		cc = 0; co = 0;
		while (1) {
			cc++;
			co = text.indexOf(cxl_element_delimiter, co);
			if (co < 0) return text.length;
			if (cc >= end) return co;
			co += cxl_element_delimiter.length;
		}
		break;
		
	case 'line': case 'lines':
		if (end < 1) return 0;
		cc = 0; co = 0;
		while (1) {
			cc++;
			co = text.indexOf('\n', co);
			if (co < 0) return text.length;
			if (cc >= end) return co;
			co++;
		}
		break;
		
	case 'para': case 'paras': case 'paragraph': case 'paragraphs':
		return cxl_paragraph_end(text, end);
		break;
		
	case 'sent': case 'sents': case 'sentence': case 'sentences':
		return cxl_sentence_end(text, end);
		break;
		
	case 'byte': case 'bytes':
		return end + 1;
		break;
		
	case 'short': case 'shorts':
		return end*2 + 2;
		break;
		
	case 'long': case 'longs':
		return end*4 + 4;
		break;
		
	default:
		return 0;
		break;
		
	}
}

// ------------------------------- //
// Main Chunk Expression Functions //
// ------------------------------- //

function cxl_count(text, tokens)
{
	var ch = '';
	var field = 0;
	var depth = 0;
	var chunk = new Array(0);
	var start = new Array(0);
	var end   = new Array(0);
	var i;
	for (i=0; i<tokens.length; i++) {
		var e = tokens[i];
		if (!isNaN(e)) {
			field++;
			switch (field) {
			case 1:
				start[depth] = e;
			case 2:
				end[depth] = e;
				break;
			}
		} else if (e.length > 0 && !isNaN(parseInt(e))) {
			field++;
			switch (field) {
			case 1:
				start[depth] = parseInt(e);
			case 2:
				end[depth] = parseInt(e);
				break;
			}
		} else if (e.length > 3) {
			if (ch == '') {
				ch = e;
			} else if (field > 0) {
				depth++;
				field=0;
				chunk[depth] = e;
			} else {
				chunk[depth] = e;
			}
		}
	}
	if (field > 0) depth++;
	
	var t = text;
	var s = 0;
	var e = t.length;
	while (depth > 0) {
		depth--;
		var ls = cxl_chunk_start(t, chunk[depth], start[depth], end[depth]);
		var le = cxl_chunk_end(t, chunk[depth], start[depth], end[depth]);
		t = t.substring(ls,le);
		s = s + ls;
		e = s + t.length;
	}
	return cxl_chunk_count(t,ch);
}

function cxl_start(text, tokens)
{
	var field = 0;
	var depth = 0;
	var chunk = new Array(0);
	var start = new Array(0);
	var end   = new Array(0);
	var i;
	for (i=0; i<tokens.length; i++) {
		var e = tokens[i];
		if (!isNaN(e)) {
			field++;
			switch (field) {
			case 1:
				start[depth] = e;
			case 2:
				end[depth] = e;
				break;
			}
		} else if (e.length > 0 && !isNaN(parseInt(e))) {
			field++;
			switch (field) {
			case 1:
				start[depth] = parseInt(e);
			case 2:
				end[depth] = parseInt(e);
				break;
			}
		} else if (e.length > 3) {
			if (field > 0) {
				depth++;
				field=0;
				chunk[depth] = e;
			} else {
				chunk[depth] = e;
			}
		}
	}
	if (field > 0) depth++;
	
	var t = text;
	var s = 0;
	var e = t.length;
	while (depth > 0) {
		depth--;
		var ls = cxl_chunk_start(t, chunk[depth], start[depth], end[depth]);
		var le = cxl_chunk_end(t, chunk[depth], start[depth], end[depth]);
		t = t.substring(ls,le);
		s = s + ls;
		e = s + t.length;
	}
	return s;
}

function cxl_end(text, tokens)
{
	var field = 0;
	var depth = 0;
	var chunk = new Array(0);
	var start = new Array(0);
	var end   = new Array(0);
	var i;
	for (i=0; i<tokens.length; i++) {
		var e = tokens[i];
		if (!isNaN(e)) {
			field++;
			switch (field) {
			case 1:
				start[depth] = e;
			case 2:
				end[depth] = e;
				break;
			}
		} else if (e.length > 0 && !isNaN(parseInt(e))) {
			field++;
			switch (field) {
			case 1:
				start[depth] = parseInt(e);
			case 2:
				end[depth] = parseInt(e);
				break;
			}
		} else if (e.length > 3) {
			if (field > 0) {
				depth++;
				field=0;
				chunk[depth] = e;
			} else {
				chunk[depth] = e;
			}
		}
	}
	if (field > 0) depth++;
	
	var t = text;
	var s = 0;
	var e = t.length;
	while (depth > 0) {
		depth--;
		var ls = cxl_chunk_start(t, chunk[depth], start[depth], end[depth]);
		var le = cxl_chunk_end(t, chunk[depth], start[depth], end[depth]);
		t = t.substring(ls,le);
		s = s + ls;
		e = s + t.length;
	}
	return e;
}

function cxl_get(text, tokens)
{
	var field = 0;
	var depth = 0;
	var chunk = new Array(0);
	var start = new Array(0);
	var end   = new Array(0);
	var i;
	for (i=0; i<tokens.length; i++) {
		var e = tokens[i];
		if (!isNaN(e)) {
			field++;
			switch (field) {
			case 1:
				start[depth] = e;
			case 2:
				end[depth] = e;
				break;
			}
		} else if (e.length > 0 && !isNaN(parseInt(e))) {
			field++;
			switch (field) {
			case 1:
				start[depth] = parseInt(e);
			case 2:
				end[depth] = parseInt(e);
				break;
			}
		} else if (e.length > 3) {
			if (field > 0) {
				depth++;
				field=0;
				chunk[depth] = e;
			} else {
				chunk[depth] = e;
			}
		}
	}
	if (field > 0) depth++;
	
	var t = text;
	var s = 0;
	var e = t.length;
	while (depth > 0) {
		depth--;
		var ls = cxl_chunk_start(t, chunk[depth], start[depth], end[depth]);
		var le = cxl_chunk_end(t, chunk[depth], start[depth], end[depth]);
		t = t.substring(ls,le);
		s = s + ls;
		e = s + t.length;
	}
	return t;
}

function cxl_delete(text, tokens)
{
	var field = 0;
	var depth = 0;
	var chunk = new Array(0);
	var start = new Array(0);
	var end   = new Array(0);
	var i;
	for (i=0; i<tokens.length; i++) {
		var e = tokens[i];
		if (!isNaN(e)) {
			field++;
			switch (field) {
			case 1:
				start[depth] = e;
			case 2:
				end[depth] = e;
				break;
			}
		} else if (e.length > 0 && !isNaN(parseInt(e))) {
			field++;
			switch (field) {
			case 1:
				start[depth] = parseInt(e);
			case 2:
				end[depth] = parseInt(e);
				break;
			}
		} else if (e.length > 3) {
			if (field > 0) {
				depth++;
				field=0;
				chunk[depth] = e;
			} else {
				chunk[depth] = e;
			}
		}
	}
	if (field > 0) depth++;
	
	var t = text;
	var s = 0;
	var e = t.length;
	while (depth > 1) {
		depth--;
		var ls = cxl_chunk_start(t, chunk[depth], start[depth], end[depth]);
		var le = cxl_chunk_end(t, chunk[depth], start[depth], end[depth]);
		t = t.substring(ls,le);
		s = s + ls;
		e = s + t.length;
	}
	if (depth > 0) {
		depth--;
		// must normalize start and end ourselves; you'll see //
		var cnt = cxl_chunk_count(t, chunk[depth]);
		var sta = cxl_astart(chunk[depth], start[depth], cnt);
		var ena = cxl_aend(chunk[depth], sta, end[depth], cnt);
		// somewhat counterintuitive thingamabob coming up //
		var ls = cxl_chunk_start(t, chunk[depth], sta, sta);
		var le = cxl_chunk_start(t, chunk[depth], ena+1, ena+1);
		// you're not seeing things; that's correct //
		t = t.substring(ls,le);
		s = s + ls;
		e = s + t.length;
	}
	return text.substring(0,s) + text.substring(e,text.length);
}

function cxl_into(text, tokens, newtext)
{
	var field = 0;
	var depth = 0;
	var chunk = new Array(0);
	var start = new Array(0);
	var end   = new Array(0);
	var i;
	for (i=0; i<tokens.length; i++) {
		var e = tokens[i];
		if (!isNaN(e)) {
			field++;
			switch (field) {
			case 1:
				start[depth] = e;
			case 2:
				end[depth] = e;
				break;
			}
		} else if (e.length > 0 && !isNaN(parseInt(e))) {
			field++;
			switch (field) {
			case 1:
				start[depth] = parseInt(e);
			case 2:
				end[depth] = parseInt(e);
				break;
			}
		} else if (e.length > 3) {
			if (field > 0) {
				depth++;
				field=0;
				chunk[depth] = e;
			} else {
				chunk[depth] = e;
			}
		}
	}
	if (field > 0) depth++;
	
	var t = text;
	var s = 0;
	var e = t.length;
	while (depth > 0) {
		depth--;
		var ls = cxl_chunk_start(t, chunk[depth], start[depth], end[depth]);
		var le = cxl_chunk_end(t, chunk[depth], start[depth], end[depth]);
		t = t.substring(ls,le);
		s = s + ls;
		e = s + t.length;
	}
	return text.substring(0,s) + newtext + text.substring(e,text.length);
}

function cxl_before(text, tokens, newtext)
{
	var field = 0;
	var depth = 0;
	var chunk = new Array(0);
	var start = new Array(0);
	var end   = new Array(0);
	var i;
	for (i=0; i<tokens.length; i++) {
		var e = tokens[i];
		if (!isNaN(e)) {
			field++;
			switch (field) {
			case 1:
				start[depth] = e;
			case 2:
				end[depth] = e;
				break;
			}
		} else if (e.length > 0 && !isNaN(parseInt(e))) {
			field++;
			switch (field) {
			case 1:
				start[depth] = parseInt(e);
			case 2:
				end[depth] = parseInt(e);
				break;
			}
		} else if (e.length > 3) {
			if (field > 0) {
				depth++;
				field=0;
				chunk[depth] = e;
			} else {
				chunk[depth] = e;
			}
		}
	}
	if (field > 0) depth++;
	
	var t = text;
	var s = 0;
	var e = t.length;
	while (depth > 0) {
		depth--;
		var ls = cxl_chunk_start(t, chunk[depth], start[depth], end[depth]);
		var le = cxl_chunk_end(t, chunk[depth], start[depth], end[depth]);
		t = t.substring(ls,le);
		s = s + ls;
		e = s + t.length;
	}
	return text.substring(0,s) + newtext + text.substring(s,text.length);
}

function cxl_after(text, tokens, newtext)
{
	var field = 0;
	var depth = 0;
	var chunk = new Array(0);
	var start = new Array(0);
	var end   = new Array(0);
	var i;
	for (i=0; i<tokens.length; i++) {
		var e = tokens[i];
		if (!isNaN(e)) {
			field++;
			switch (field) {
			case 1:
				start[depth] = e;
			case 2:
				end[depth] = e;
				break;
			}
		} else if (e.length > 0 && !isNaN(parseInt(e))) {
			field++;
			switch (field) {
			case 1:
				start[depth] = parseInt(e);
			case 2:
				end[depth] = parseInt(e);
				break;
			}
		} else if (e.length > 3) {
			if (field > 0) {
				depth++;
				field=0;
				chunk[depth] = e;
			} else {
				chunk[depth] = e;
			}
		}
	}
	if (field > 0) depth++;
	
	var t = text;
	var s = 0;
	var e = t.length;
	while (depth > 0) {
		depth--;
		var ls = cxl_chunk_start(t, chunk[depth], start[depth], end[depth]);
		var le = cxl_chunk_end(t, chunk[depth], start[depth], end[depth]);
		t = t.substring(ls,le);
		s = s + ls;
		e = s + t.length;
	}
	return text.substring(0,e) + newtext + text.substring(e,text.length);
}

// --------------------------- //
// Private Functions - General //
// --------------------------- //

function cxl_astart(chunk, start, count)
{
	var a = ((chunk == 'byte' || chunk == 'bytes' || chunk == 'short' || chunk == 'shorts' || chunk == 'long' || chunk == 'longs')?(0):(1));
	
	     if (start == cxl_mid) return Math.ceil ((count-1)/2)     +a;
	else if (start == cxl_mdd) return Math.floor((count-1)/2)     +a;
	else if (start == cxl_any) return Math.floor(cxl_random*count)+a;
	else if (start  < 0      ) return count+start                 +a;
	else                       return start;
}

function cxl_aend(chunk, start, end, count)
{
	var a = ((chunk == 'byte' || chunk == 'bytes' || chunk == 'short' || chunk == 'shorts' || chunk == 'long' || chunk == 'longs')?(0):(1));
	
	     if (end == cxl_mid) return Math.max(  Math.ceil ((count-1)/2)     +a  , start);
	else if (end == cxl_mdd) return Math.max(  Math.floor((count-1)/2)     +a  , start);
	else if (end == cxl_any) return Math.max(  Math.floor(cxl_random*count)+a  , start);
	else if (end  < 0      ) return Math.max(  count+end                   +a  , start);
	else                     return Math.max(  Math.min  (count+a-1,end)       , start);
}

function cxl_isbreak(c)
{
	return (c == '\n' || c == '\r');
}

function cxl_iswhite(c)
{
	return (c == '\n' || c == '\r' || c == '\t' || c == ' ');
}

function cxl_issentender(c)
{
	return (c == '.' || c == '!' || c == '?');
}

function cxl_makeplural(s)
{
	if (s.substring(s.length-1,s.length) != 's') return (s+"s");
	return s;
}

function cxl_makesingular(s)
{
	if (s.substring(s.length-1,s.length) == 's') return (s.substring(0,s.length-1));
	return s;
}

// ------------------------------ //
// Private Functions - Paragraphs //
// ------------------------------ //

function cxl_paragraph_count(text)
{
	var n,s;
	n = 0;
	s = 0;
	while (s < text.length && cxl_isbreak(text.charAt(s))) s++;
	while (s < text.length) {
		n++;
		while (s < text.length && !cxl_isbreak(text.charAt(s))) s++;
		while (s < text.length && cxl_isbreak(text.charAt(s))) s++;
	}
	return n;
}

function cxl_paragraph_start(text, num)
{
	var n,s;
	n = 0;
	s = 0;
	while (s < text.length && cxl_isbreak(text.charAt(s))) s++;
	while (s < text.length) {
		n++;
		if (n == num) return s;
		while (s < text.length && !cxl_isbreak(text.charAt(s))) s++;
		while (s < text.length && cxl_isbreak(text.charAt(s))) s++;
	}
	return (num<=0)?(0):(text.length);
}

function cxl_paragraph_end(text, num)
{
	var n,s;
	n = 0;
	s = 0;
	while (s < text.length && cxl_isbreak(text.charAt(s))) s++;
	while (s < text.length) {
		n++;
		while (s < text.length && !cxl_isbreak(text.charAt(s))) s++;
		if (n == num) return s;
		while (s < text.length && cxl_isbreak(text.charAt(s))) s++;
	}
	return (num<=0)?(0):(text.length);
}

// ----------------------------- //
// Private Functions - Sentences //
// ----------------------------- //

function cxl_sentence_count(text)
{
	var n,s;
	n = 0;
	s = 0;
	while (s < text.length && cxl_iswhite(text.charAt(s))) s++;
	while (s < text.length) {
		n++;
		while (s < text.length && !cxl_issentender(text.charAt(s))) s++;
		while (s < text.length && !cxl_iswhite(text.charAt(s))) s++;
		while (s < text.length && cxl_iswhite(text.charAt(s))) s++;
	}
	return n;
}

function cxl_sentence_start(text, num)
{
	var n,s;
	n = 0;
	s = 0;
	while (s < text.length && cxl_iswhite(text.charAt(s))) s++;
	while (s < text.length) {
		n++;
		if (n == num) return s;
		while (s < text.length && !cxl_issentender(text.charAt(s))) s++;
		while (s < text.length && !cxl_iswhite(text.charAt(s))) s++;
		while (s < text.length && cxl_iswhite(text.charAt(s))) s++;
	}
	return (num<=0)?(0):(text.length);
}

function cxl_sentence_end(text, num)
{
	var n,s;
	n = 0;
	s = 0;
	while (s < text.length && cxl_iswhite(text.charAt(s))) s++;
	while (s < text.length) {
		n++;
		while (s < text.length && !cxl_issentender(text.charAt(s))) s++;
		while (s < text.length && !cxl_iswhite(text.charAt(s))) s++;
		if (n == num) return s;
		while (s < text.length && cxl_iswhite(text.charAt(s))) s++;
	}
	return (num<=0)?(0):(text.length);
}

// ------------------------- //
// Private Functions - Words //
// ------------------------- //

function cxl_word_count(text)
{
	var n,s;
	n = 0;
	s = 0;
	while (s < text.length && cxl_iswhite(text.charAt(s))) s++;
	while (s < text.length) {
		n++;
		while (s < text.length && !cxl_iswhite(text.charAt(s))) s++;
		while (s < text.length && cxl_iswhite(text.charAt(s))) s++;
	}
	return n;
}

function cxl_word_start(text, num)
{
	var n,s;
	n = 0;
	s = 0;
	while (s < text.length && cxl_iswhite(text.charAt(s))) s++;
	while (s < text.length) {
		n++;
		if (n == num) return s;
		while (s < text.length && !cxl_iswhite(text.charAt(s))) s++;
		while (s < text.length && cxl_iswhite(text.charAt(s))) s++;
	}
	return (num<=0)?(0):(text.length);
}

function cxl_word_end(text, num)
{
	var n,s;
	n = 0;
	s = 0;
	while (s < text.length && cxl_iswhite(text.charAt(s))) s++;
	while (s < text.length) {
		n++;
		while (s < text.length && !cxl_iswhite(text.charAt(s))) s++;
		if (n == num) return s;
		while (s < text.length && cxl_iswhite(text.charAt(s))) s++;
	}
	return (num<=0)?(0):(text.length);
}

// ------- //
// Testing //
// ------- //

function cxl_chunk_test(text, chunk)
{
	var n = cxl_chunk_count(text, chunk);
	var s = "number of " + cxl_makeplural(chunk) + ": " + n + "\n\n";
	var i;
	for (i=0; i<=n+4; i++) {
		s += cxl_makesingular(chunk) + " " + i + ": " + text.substring(cxl_chunk_start(text, chunk, i, i), cxl_chunk_end(text, chunk, i, i)) + "\n";
	}
	s += "\n";
	for (i=-1; i>=-n-4; i--) {
		s += cxl_makesingular(chunk) + " " + i + ": " + text.substring(cxl_chunk_start(text, chunk, i, i), cxl_chunk_end(text, chunk, i, i)) + "\n";
	}
	s += "\n";
	for (i=-1; i<=n+2; i++) {
		s += cxl_makesingular(chunk) + " 1 to " + i + ": " + text.substring(cxl_chunk_start(text, chunk, 1, i), cxl_chunk_end(text, chunk, 1, i)) + "\n";
	}
	s += "\n";
	for (i=1; i>=-n-2; i--) {
		s += cxl_makesingular(chunk) + " " + i + " to -1: " + text.substring(cxl_chunk_start(text, chunk, i, -1), cxl_chunk_end(text, chunk, i, -1)) + "\n";
	}
	s += "\n";
	s += "mid " + cxl_makesingular(chunk) + ": " + text.substring(cxl_chunk_start(text, chunk, cxl_mid, cxl_mid), cxl_chunk_end(text, chunk, cxl_mid, cxl_mid)) + "\n";
	s += "mdd " + cxl_makesingular(chunk) + ": " + text.substring(cxl_chunk_start(text, chunk, cxl_mdd, cxl_mdd), cxl_chunk_end(text, chunk, cxl_mdd, cxl_mdd)) + "\n";
	cxl_randomize(); s += "any " + cxl_makesingular(chunk) + ": " + text.substring(cxl_chunk_start(text, chunk, cxl_any, cxl_any), cxl_chunk_end(text, chunk, cxl_any, cxl_any)) + "\n";
	cxl_randomize(); s += "any " + cxl_makesingular(chunk) + ": " + text.substring(cxl_chunk_start(text, chunk, cxl_any, cxl_any), cxl_chunk_end(text, chunk, cxl_any, cxl_any)) + "\n";
	cxl_randomize(); s += "any " + cxl_makesingular(chunk) + ": " + text.substring(cxl_chunk_start(text, chunk, cxl_any, cxl_any), cxl_chunk_end(text, chunk, cxl_any, cxl_any)) + "\n";
	cxl_randomize(); s += "any " + cxl_makesingular(chunk) + ": " + text.substring(cxl_chunk_start(text, chunk, cxl_any, cxl_any), cxl_chunk_end(text, chunk, cxl_any, cxl_any)) + "\n";
	return s;
}

function cxl_eval_test(text)
{
	var q = new Array(0);
	var qn = 0;
	while (1) {
		var qs = text.indexOf('\"');
		var qe = text.indexOf('\"',qs+1);
		if (qs < 0 || qe < 0) break;
		else {
			q[qn] = text.substring(qs+1,qe);
			qn++;
			text = text.substring(0,qs) + text.substring(qe+1,text.length);
		}
	}
	var stuff = text.split(' ');
	while (stuff[0] == '' || stuff[0].toLowerCase() == 'put' || stuff[0].toLowerCase() == 'the') stuff = stuff.slice(1);
	switch (stuff[0].toLowerCase()) {
	case 'number':
		return cxl_count(q[0],stuff.slice(1));
		break;
	case 'start':
		return cxl_start(q[0],stuff.slice(1));
		break;
	case 'end':
		return cxl_end(q[0],stuff.slice(1));
		break;
	case 'get':
		return cxl_get(q[0],stuff.slice(1));
		break;
	case 'delete':
		return cxl_delete(q[0],stuff.slice(1));
		break;
	case 'into':
		return cxl_into(q[1],stuff.slice(1),q[0]);
		break;
	case 'before':
		return cxl_before(q[1],stuff.slice(1),q[0]);
		break;
	case 'after':
		return cxl_after(q[1],stuff.slice(1),q[0]);
		break;
	default:
		return cxl_get(q,stuff);
		break;
	}
}

// ------------------------------- //
// End of Chunk Expression Library //
// ------------------------------- //
