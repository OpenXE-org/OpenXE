<style>
	table.help {
		border: 1px solid #cccccc;
		margin-top: 5px;
	}

	table.help td {
		padding: 2px 5px;
		background-color: #dddddd;
	}

	table.help tr.header td {
		background-color: #6EB6D5;
		color: white;
		font-weight: 600;
	}


</style>

Allgemeine Regeln
<TABLE class="help">
<TR class="header"><TD>Regular Expression</TD><TD>Suchwort</TD></TR>
<TR><TD>foo</TD><TD>Der String "foo"</TD></TR>
<TR><TD>^foo</TD><TD>"foo" am Anfang des Strings</TD></TR>
<TR><TD>foo$</TD><TD>"foo" am Ende des Strings</TD></TR>
<TR><TD>[abc]</TD><TD>a, b, oder c</TD></TR>
<TR><TD>[a-z]</TD><TD>Alle kleingeschriebenen Buchstaben</TD></TR>
<TR><TD>[^A-Z]</TD><TD>Alle nicht-großgeschriebenen Buchstaben</TD></TR>
<TR><TD>(gif|jpg)</TD><TD>Matches either "gif" or "jpeg"</TD></TR>
<TR><TD>[a-z]+</TD><TD>Einen oder mehrere kleingeschriebene Buchstaben</TD></TR>
<TR><TD>[0-9\.\-]</TD><TD>Alle Zahlen, Buchstaben und Minus-Zeichen</TD></TR>
<TR><TD>^[a-zA-Z0-9_]{1,}$</TD><TD>Jedes Wort mit mindestens einer Ziffer oder Unterstrich (_)</TD></TR>
<TR><TD>([wx])([yz])</TD><TD>wy, wz, xy, oder xz</TD></TR>
<TR><TD>[^A-Za-z0-9]</TD><TD>Jedes Symbol (Kein Buchstabe oder Ziffer)</TD></TR>
</TABLE>


<br><br>
Punkte, Backslashes sowie Fragezeichen m&uuml;ssen immer durch einen Slash escaped werden
<table class="help">
	<tr class="header"><td>Suchwort</td><td>Regex-Ausdruck</td></tr>
	<tr><td>../../../../data</td><td>\.\.\/\.\.\/\.\.\/\.\.\/\.\.\/data</td></tr>
	<tr><td>product_info.php?info=p105_ICnova-SAM9G45-OEM.html</td><td>product_info\.php\?info=p105_ICnova-SAM9G45-OEM\.html</td></tr>
	<tr><td>http://www.abc.de/data</td><td>http:\/\/www\.abc.de\/data</td></tr>
</table>
