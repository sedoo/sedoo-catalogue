// Email.js version 5
var tld_ = new Array()
tld_[0] = "com";
tld_[1] = "org";
tld_[2] = "net";
tld_[3] = "ws";
tld_[4] = "info";
tld_[5] = "int";
tld_[6] = "edu";
tld_[7] = "gov";
tld_[10] = "uk";
tld_[14] = "fr";
tld_[15] = "es";
tld_[16] = "de";
tld_[17] = "at";
tld_[18] = "it";
tld_[19] = "cat";
tld_[20] = "ch";
tld_[21] = "hr";
tld_[22] = "ro";
tld_[23] = "il";
tld_[24] = "nl";
tld_[25] = "gr";

var topDom_ = 15;
var m_ = "mailto:";
var a_ = "@";
var d_ = ".";

function mail(name, dom, tl, params)
{
	var s = e(name,dom,tl);
	document.write('<a href="'+m_+s+params+'">'+s+'</a>');
}
function mail2(name, dom, tl, params, display)
{
	document.write('<a href="'+m_+e(name,dom,tl)+params+'">'+display+'</a>');
}
function e(name, dom, tl)
{
	var s = name+a_;
	if (tl!=-2)
	{
		s+= dom;
		if (tl>=0)
			s+= d_+tld_[tl];
	}
	else
		s+= swapper(dom);
	return s;
}
function swapper(d)
{
	var s = "";
	for (var i=0; i<d.length; i+=2)
		if (i+1==d.length)
			s+= d.charAt(i)
		else
			s+= d.charAt(i+1)+d.charAt(i);
	return s.replace(/\?/g,'.');
}
