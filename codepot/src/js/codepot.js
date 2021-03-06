function codepot_utf8_encode(argString) {
  //  discuss at: http://phpjs.org/functions/utf8_encode/
  // original by: Webtoolkit.info (http://www.webtoolkit.info/)
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: sowberry
  // improved by: Jack
  // improved by: Yves Sucaet
  // improved by: kirilloid
  // bugfixed by: Onno Marsman
  // bugfixed by: Onno Marsman
  // bugfixed by: Ulrich
  // bugfixed by: Rafal Kukawski
  // bugfixed by: kirilloid
  //   example 1: utf8_encode('Kevin van Zonneveld');
  //   returns 1: 'Kevin van Zonneveld'

  if (argString === null || typeof argString === 'undefined') {
    return '';
  }

  var string = (argString + ''); // .replace(/\r\n/g, "\n").replace(/\r/g, "\n");
  var utftext = '',
    start, end, stringl = 0;

  start = end = 0;
  stringl = string.length;
  for (var n = 0; n < stringl; n++) {
    var c1 = string.charCodeAt(n);
    var enc = null;

    if (c1 < 128) {
      end++;
    } else if (c1 > 127 && c1 < 2048) {
      enc = String.fromCharCode(
        (c1 >> 6) | 192, (c1 & 63) | 128
      );
    } else if ((c1 & 0xF800) != 0xD800) {
      enc = String.fromCharCode(
        (c1 >> 12) | 224, ((c1 >> 6) & 63) | 128, (c1 & 63) | 128
      );
    } else { // surrogate pairs
      if ((c1 & 0xFC00) != 0xD800) {
        throw new RangeError('Unmatched trail surrogate at ' + n);
      }
      var c2 = string.charCodeAt(++n);
      if ((c2 & 0xFC00) != 0xDC00) {
        throw new RangeError('Unmatched lead surrogate at ' + (n - 1));
      }
      c1 = ((c1 & 0x3FF) << 10) + (c2 & 0x3FF) + 0x10000;
      enc = String.fromCharCode(
        (c1 >> 18) | 240, ((c1 >> 12) & 63) | 128, ((c1 >> 6) & 63) | 128, (c1 & 63) | 128
      );
    }
    if (enc !== null) {
      if (end > start) {
        utftext += string.slice(start, end);
      }
      utftext += enc;
      start = end = n + 1;
    }
  }

  if (end > start) {
    utftext += string.slice(start, stringl);
  }

  return utftext;
}


function codepot_utf8_decode(str_data) {
  //  discuss at: http://phpjs.org/functions/utf8_decode/
  // original by: Webtoolkit.info (http://www.webtoolkit.info/)
  //    input by: Aman Gupta
  //    input by: Brett Zamir (http://brett-zamir.me)
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Norman "zEh" Fuchs
  // bugfixed by: hitwork
  // bugfixed by: Onno Marsman
  // bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // bugfixed by: kirilloid
  //   example 1: utf8_decode('Kevin van Zonneveld');
  //   returns 1: 'Kevin van Zonneveld'

  var tmp_arr = [],
    i = 0,
    ac = 0,
    c1 = 0,
    c2 = 0,
    c3 = 0,
    c4 = 0;

  str_data += '';

  while (i < str_data.length) {
    c1 = str_data.charCodeAt(i);
    if (c1 <= 191) {
      tmp_arr[ac++] = String.fromCharCode(c1);
      i++;
    } else if (c1 <= 223) {
      c2 = str_data.charCodeAt(i + 1);
      tmp_arr[ac++] = String.fromCharCode(((c1 & 31) << 6) | (c2 & 63));
      i += 2;
    } else if (c1 <= 239) {
      // http://en.wikipedia.org/wiki/UTF-8#Codepage_layout
      c2 = str_data.charCodeAt(i + 1);
      c3 = str_data.charCodeAt(i + 2);
      tmp_arr[ac++] = String.fromCharCode(((c1 & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
      i += 3;
    } else {
      c2 = str_data.charCodeAt(i + 1);
      c3 = str_data.charCodeAt(i + 2);
      c4 = str_data.charCodeAt(i + 3);
      c1 = ((c1 & 7) << 18) | ((c2 & 63) << 12) | ((c3 & 63) << 6) | (c4 & 63);
      c1 -= 0x10000;
      tmp_arr[ac++] = String.fromCharCode(0xD800 | ((c1 >> 10) & 0x3FF));
      tmp_arr[ac++] = String.fromCharCode(0xDC00 | (c1 & 0x3FF));
      i += 4;
    }
  }

  return tmp_arr.join('');
}

function codepot_sprintf() {
  //  discuss at: http://phpjs.org/functions/sprintf/
  // original by: Ash Searle (http://hexmen.com/blog/)
  // improved by: Michael White (http://getsprink.com)
  // improved by: Jack
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Dj
  // improved by: Allidylls
  //    input by: Paulo Freitas
  //    input by: Brett Zamir (http://brett-zamir.me)
  //   example 1: sprintf("%01.2f", 123.1);
  //   returns 1: 123.10
  //   example 2: sprintf("[%10s]", 'monkey');
  //   returns 2: '[    monkey]'
  //   example 3: sprintf("[%'#10s]", 'monkey');
  //   returns 3: '[####monkey]'
  //   example 4: sprintf("%d", 123456789012345);
  //   returns 4: '123456789012345'
  //   example 5: sprintf('%-03s', 'E');
  //   returns 5: 'E00'

  var regex = /%%|%(\d+\$)?([-+\'#0 ]*)(\*\d+\$|\*|\d+)?(\.(\*\d+\$|\*|\d+))?([scboxXuideEfFgG])/g;
  var a = arguments;
  var i = 0;
  var format = a[i++];

  // pad()
  var pad = function (str, len, chr, leftJustify) {
    if (!chr) {
      chr = ' ';
    }
    var padding = (str.length >= len) ? '' : new Array(1 + len - str.length >>> 0)
      .join(chr);
    return leftJustify ? str + padding : padding + str;
  };

  // justify()
  var justify = function (value, prefix, leftJustify, minWidth, zeroPad, customPadChar) {
    var diff = minWidth - value.length;
    if (diff > 0) {
      if (leftJustify || !zeroPad) {
        value = pad(value, minWidth, customPadChar, leftJustify);
      } else {
        value = value.slice(0, prefix.length) + pad('', diff, '0', true) + value.slice(prefix.length);
      }
    }
    return value;
  };

  // formatBaseX()
  var formatBaseX = function (value, base, prefix, leftJustify, minWidth, precision, zeroPad) {
    // Note: casts negative numbers to positive ones
    var number = value >>> 0;
    prefix = prefix && number && {
      '2': '0b',
      '8': '0',
      '16': '0x'
    }[base] || '';
    value = prefix + pad(number.toString(base), precision || 0, '0', false);
    return justify(value, prefix, leftJustify, minWidth, zeroPad);
  };

  // formatString()
  var formatString = function (value, leftJustify, minWidth, precision, zeroPad, customPadChar) {
    if (precision != null) {
      value = value.slice(0, precision);
    }
    return justify(value, '', leftJustify, minWidth, zeroPad, customPadChar);
  };

  // doFormat()
  var doFormat = function (substring, valueIndex, flags, minWidth, _, precision, type) {
    var number, prefix, method, textTransform, value;

    if (substring === '%%') {
      return '%';
    }

    // parse flags
    var leftJustify = false;
    var positivePrefix = '';
    var zeroPad = false;
    var prefixBaseX = false;
    var customPadChar = ' ';
    var flagsl = flags.length;
    for (var j = 0; flags && j < flagsl; j++) {
      switch (flags.charAt(j)) {
      case ' ':
        positivePrefix = ' ';
        break;
      case '+':
        positivePrefix = '+';
        break;
      case '-':
        leftJustify = true;
        break;
      case "'":
        customPadChar = flags.charAt(j + 1);
        break;
      case '0':
        zeroPad = true;
        customPadChar = '0';
        break;
      case '#':
        prefixBaseX = true;
        break;
      }
    }

    // parameters may be null, undefined, empty-string or real valued
    // we want to ignore null, undefined and empty-string values
    if (!minWidth) {
      minWidth = 0;
    } else if (minWidth === '*') {
      minWidth = +a[i++];
    } else if (minWidth.charAt(0) == '*') {
      minWidth = +a[minWidth.slice(1, -1)];
    } else {
      minWidth = +minWidth;
    }

    // Note: undocumented perl feature:
    if (minWidth < 0) {
      minWidth = -minWidth;
      leftJustify = true;
    }

    if (!isFinite(minWidth)) {
      throw new Error('sprintf: (minimum-)width must be finite');
    }

    if (!precision) {
      precision = 'fFeE'.indexOf(type) > -1 ? 6 : (type === 'd') ? 0 : undefined;
    } else if (precision === '*') {
      precision = +a[i++];
    } else if (precision.charAt(0) == '*') {
      precision = +a[precision.slice(1, -1)];
    } else {
      precision = +precision;
    }

    // grab value using valueIndex if required?
    value = valueIndex ? a[valueIndex.slice(0, -1)] : a[i++];

    switch (type) {
    case 's':
      return formatString(String(value), leftJustify, minWidth, precision, zeroPad, customPadChar);
    case 'c':
      return formatString(String.fromCharCode(+value), leftJustify, minWidth, precision, zeroPad);
    case 'b':
      return formatBaseX(value, 2, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
    case 'o':
      return formatBaseX(value, 8, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
    case 'x':
      return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
    case 'X':
      return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad)
        .toUpperCase();
    case 'u':
      return formatBaseX(value, 10, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
    case 'i':
    case 'd':
      number = +value || 0;
      // Plain Math.round doesn't just truncate
      number = Math.round(number - number % 1);
      prefix = number < 0 ? '-' : positivePrefix;
      value = prefix + pad(String(Math.abs(number)), precision, '0', false);
      return justify(value, prefix, leftJustify, minWidth, zeroPad);
    case 'e':
    case 'E':
    case 'f': // Should handle locales (as per setlocale)
    case 'F':
    case 'g':
    case 'G':
      number = +value;
      prefix = number < 0 ? '-' : positivePrefix;
      method = ['toExponential', 'toFixed', 'toPrecision']['efg'.indexOf(type.toLowerCase())];
      textTransform = ['toString', 'toUpperCase']['eEfFgG'.indexOf(type) % 2];
      value = prefix + Math.abs(number)[method](precision);
      return justify(value, prefix, leftJustify, minWidth, zeroPad)[textTransform]();
    default:
      return substring;
    }
  };

  return format.replace(regex, doFormat);
}


function codepot_htmlspecialchars(string, quote_style, charset, double_encode) {
  //       discuss at: http://phpjs.org/functions/htmlspecialchars/
  //      original by: Mirek Slugen
  //      improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  //      bugfixed by: Nathan
  //      bugfixed by: Arno
  //      bugfixed by: Brett Zamir (http://brett-zamir.me)
  //      bugfixed by: Brett Zamir (http://brett-zamir.me)
  //       revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  //         input by: Ratheous
  //         input by: Mailfaker (http://www.weedem.fr/)
  //         input by: felix
  // reimplemented by: Brett Zamir (http://brett-zamir.me)
  //             note: charset argument not supported
  //        example 1: htmlspecialchars("<a href='test'>Test</a>", 'ENT_QUOTES');
  //        returns 1: '&lt;a href=&#039;test&#039;&gt;Test&lt;/a&gt;'
  //        example 2: htmlspecialchars("ab\"c'd", ['ENT_NOQUOTES', 'ENT_QUOTES']);
  //        returns 2: 'ab"c&#039;d'
  //        example 3: htmlspecialchars('my "&entity;" is still here', null, null, false);
  //        returns 3: 'my &quot;&entity;&quot; is still here'

  var optTemp = 0,
    i = 0,
    noquotes = false;
  if (typeof quote_style === 'undefined' || quote_style === null) {
    quote_style = 2;
  }
  string = string.toString();
  if (double_encode !== false) {
    // Put this first to avoid double-encoding
    string = string.replace(/&/g, '&amp;');
  }
  string = string.replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');

  var OPTS = {
    'ENT_NOQUOTES': 0,
    'ENT_HTML_QUOTE_SINGLE': 1,
    'ENT_HTML_QUOTE_DOUBLE': 2,
    'ENT_COMPAT': 2,
    'ENT_QUOTES': 3,
    'ENT_IGNORE': 4
  };
  if (quote_style === 0) {
    noquotes = true;
  }
  if (typeof quote_style !== 'number') {
    // Allow for a single string or an array of string flags
    quote_style = [].concat(quote_style);
    for (i = 0; i < quote_style.length; i++) {
      // Resolve string input to bitwise e.g. 'ENT_IGNORE' becomes 4
      if (OPTS[quote_style[i]] === 0) {
        noquotes = true;
      } else if (OPTS[quote_style[i]]) {
        optTemp = optTemp | OPTS[quote_style[i]];
      }
    }
    quote_style = optTemp;
  }
  if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
    string = string.replace(/'/g, '&#039;');
  }
  if (!noquotes) {
    string = string.replace(/"/g, '&quot;');
  }

  return string;
}


function codepot_addslashes(str) {
  //  discuss at: http://phpjs.org/functions/addslashes/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Ates Goral (http://magnetiq.com)
  // improved by: marrtins
  // improved by: Nate
  // improved by: Onno Marsman
  // improved by: Brett Zamir (http://brett-zamir.me)
  // improved by: Oskar Larsson Högfeldt (http://oskar-lh.name/)
  //    input by: Denny Wardhana
  //   example 1: addslashes("kevin's birthday");
  //   returns 1: "kevin\\'s birthday"

  return (str + '')
    .replace(/[\\"']/g, '\\$&')
    .replace(/\u0000/g, '\\0');
}


function codepot_ascii_to_hex (x)
{
	var r="";

	var i;
	/*
	for(i=0; i<x.length; i++)
	{
		var tmp = x.charCodeAt(i).toString(16);
		if (tmp.length == 1) r += "0";
		r += tmp;
	}
	*/

	r = "!"; // the new style conversion begins with an exclamation mark
	for(i = 0; i < x.length; i++)
	{
		var seg;
		var c = x.charAt(i);
		if (c == "!")
		{
			seg = "!!";
		}
		else if (c == "|")
		{
			seg = "!|";
		}
		else if (c == "/")
		{
			seg = "|";
		}
		else if (c == "." || c == "_" || c == "-" || 
			    c == ":" || c == "@" || c == " ")
		{
			seg = c;
		}
		else
		{
			if (/^[A-Za-z0-9]$/.test (c))
			{
				seg = c;
			}
			else
			{
				var seg = x.charCodeAt(i).toString(16);
				if (seg.length == 1) seg = "0" + seg;
				seg = "!" + seg;
			}
		}
		r += seg;
	}

	return r;
}

function codepot_string_to_hex (x)
{
	var utf8 = codepot_utf8_encode(x);
	return codepot_ascii_to_hex(utf8);
}

function codepot_merge_path (base, path)
{
	//
	// if 'base' ends with '/', remove all leading slashes off 'path'
	// before adding 'base' and 'path'.
	//
	if (base.charAt(base.length - 1) == "/")
	{
		var i;
		for (i = 0; path.charAt(i) == '/'; i++);
		return base + path.substr(i);
	}
	else return base + path;
}

function codepot_get_ace_modes ()
{
	var modes = [];

	var Mode = function(name, caption, extensions) {
		this.name = name;
		this.caption = caption;
		this.mode = "ace/mode/" + name;
		this.extensions = extensions;
		if (/\^/.test(extensions)) {
			var re = extensions.replace(/\|(\^)?/g, function(a, b){
				return "$|" + (b ? "^" : "^.*\\.");
			}) + "$";
		} else {
			var re = "^.*\\.(" + extensions + ")$";
		}

		this.extRe = new RegExp(re, "gi");
	};

	Mode.prototype.supportsFile = function(filename) {
		return filename.match(this.extRe);
	};

	var supportedModes = {
		ABAP:        ["abap"],
		ABC:         ["abc"],
		ActionScript:["as"],
		ADA:         ["ada|adb|ads"],
		Apache_Conf: ["^htaccess|^htgroups|^htpasswd|^conf|htaccess|htgroups|htpasswd"],
		AsciiDoc:    ["asciidoc|adoc"],
		Assembly_x86:["asm"],
		AutoHotKey:  ["ahk"],
		BatchFile:   ["bat|cmd"],
		C9Search:    ["c9search_results"],
		C_Cpp:       ["cpp|c|cc|cxx|h|hh|hpp"],
		Cirru:       ["cirru|cr"],
		Clojure:     ["clj|cljs"],
		Cobol:       ["CBL|COB"],
		coffee:      ["coffee|cf|cson|^Cakefile"],
		ColdFusion:  ["cfm"],
		CSharp:      ["cs"],
		CSS:         ["css"],
		Curly:       ["curly"],
		D:           ["d|di"],
		Dart:        ["dart"],
		Diff:        ["diff|patch"],
		Dockerfile:  ["^Dockerfile"],
		Dot:         ["dot"],
		Dummy:       ["dummy"],
		DummySyntax: ["dummy"],
		Eiffel:      ["e"],
		EJS:         ["ejs"],
		Elixir:      ["ex|exs"],
		Elm:         ["elm"],
		Erlang:      ["erl|hrl"],
		Forth:       ["frt|fs|ldr"],
		FTL:         ["ftl"],
		Gcode:       ["gcode"],
		Gherkin:     ["feature"],
		Gitignore:   ["^.gitignore"],
		Glsl:        ["glsl|frag|vert"],
		golang:      ["go"],
		Groovy:      ["groovy"],
		HAML:        ["haml"],
		Handlebars:  ["hbs|handlebars|tpl|mustache"],
		Haskell:     ["hs"],
		haXe:        ["hx"],
		HTML:        ["html|htm|xhtml"],
		HTML_Ruby:   ["erb|rhtml|html.erb"],
		INI:         ["ini|conf|cfg|prefs"],
		Io:          ["io"],
		Jack:        ["jack"],
		Jade:        ["jade"],
		Java:        ["java"],
		JavaScript:  ["js|jsm"],
		JSON:        ["json"],
		JSONiq:      ["jq"],
		JSP:         ["jsp"],
		JSX:         ["jsx"],
		Julia:       ["jl"],
		LaTeX:       ["tex|latex|ltx|bib"],
		Lean:        ["lean|hlean"],
		LESS:        ["less"],
		Liquid:      ["liquid"],
		Lisp:        ["lisp"],
		LiveScript:  ["ls"],
		LogiQL:      ["logic|lql"],
		LSL:         ["lsl"],
		Lua:         ["lua"],
		LuaPage:     ["lp"],
		Lucene:      ["lucene"],
		Makefile:    ["^Makefile|^GNUmakefile|^makefile|^OCamlMakefile|make"],
		Markdown:    ["md|markdown"],
		Mask:        ["mask"],
		MATLAB:      ["matlab"],
		MEL:         ["mel"],
		MUSHCode:    ["mc|mush"],
		MySQL:       ["mysql"],
		Nix:         ["nix"],
		ObjectiveC:  ["m|mm"],
		OCaml:       ["ml|mli"],
		Pascal:      ["pas|p"],
		Perl:        ["pl|pm"],
		pgSQL:       ["pgsql"],
		PHP:         ["php|phtml"],
		Powershell:  ["ps1"],
		Praat:       ["praat|praatscript|psc|proc"],
		Prolog:      ["plg|prolog"],
		Properties:  ["properties"],
		Protobuf:    ["proto"],
		Python:      ["py"],
		R:           ["r"],
		RDoc:        ["Rd"],
		RHTML:       ["Rhtml"],
		Ruby:        ["rb|ru|gemspec|rake|^Guardfile|^Rakefile|^Gemfile"],
		Rust:        ["rs"],
		SASS:        ["sass"],
		SCAD:        ["scad"],
		Scala:       ["scala"],
		Scheme:      ["scm|rkt"],
		SCSS:        ["scss"],
		SH:          ["sh|bash|^.bashrc"],
		SJS:         ["sjs"],
		Smarty:      ["smarty|tpl"],
		snippets:    ["snippets"],
		Soy_Template:["soy"],
		Space:       ["space"],
		SQL:         ["sql"],
		SQLServer:   ["sqlserver"],
		Stylus:      ["styl|stylus"],
		SVG:         ["svg"],
		Tcl:         ["tcl"],
		Tex:         ["tex"],
		Text:        ["txt"],
		Textile:     ["textile"],
		Toml:        ["toml"],
		Twig:        ["twig"],
		Typescript:  ["ts|typescript|str"],
		Vala:        ["vala"],
		VBScript:    ["vbs|vb"],
		Velocity:    ["vm"],
		Verilog:     ["v|vh|sv|svh"],
		VHDL:        ["vhd|vhdl"],
		XML:         ["xml|rdf|rss|wsdl|xslt|atom|mathml|mml|xul|xbl|xaml"],
		XQuery:      ["xq"],
		YAML:        ["yaml|yml"],
		Django:      ["html"]
	};

	var nameOverrides = {
		ObjectiveC: "Objective-C",
		CSharp: "C#",
		golang: "Go",
		C_Cpp: "C and C++",
		coffee: "CoffeeScript",
		HTML_Ruby: "HTML (Ruby)",
		FTL: "FreeMarker"
	};

	var modesByName = {};
	for (var name in supportedModes) {
		var data = supportedModes[name];
		var displayName = (nameOverrides[name] || name).replace(/_/g, " ");
		var filename = name.toLowerCase();
		var mode = new Mode(filename, displayName, data[0]);
		//modesByName[filename] = mode;
		modes.push(mode);
	}

	return modes;
}
