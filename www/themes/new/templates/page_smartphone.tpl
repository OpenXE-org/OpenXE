<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<script type="text/javascript" src="./js/jquery/jquery-3.5.0.min.js"></script>
<script type="text/javascript" src="[JQUERYMIGRATESRC]"></script>
<title>[HTMLTITLE]</title>


<style>
body {  font-family: Arial,Helvetica,sans-serif;}
input { zoom:2.0;width:65px; }
input.home { zoom:2.5;width:95px; }
input.artikel { zoom:2.5;width:80px; background-color:red; }
input.lager { zoom:2.5;width:80px; background-color:red; }
h2 { zoom:1.0; text-align:center;}
h3 { zoom:1.0;}
img { zoom:1.0;}

.hinweis { background-color: red; width:90%; z-index:10; position:absolute; padding: 5px 5px; color:white; font-weight:bold;}

</style>
<script type="text/javascript">
$(function()
  {
  if($(".hinweis" ).text()!="")
  {
    $(".hinweis").ready(function () {
    $(".hinweis").delay(5000).fadeOut(500, function () {
    $(".hinweis").remove();
      });
    });
  } else {
    $(".hinweis").remove();
  } 
 }
);
</script>

<body>
<div class="hinweis" id="hinweis">[MESSAGE]</div>

[PAGE]
<br>
<br>
<center>        
<div id="footer" style="zoom:0.1">
  		&copy; [YEAR] WaWision GmbH <br>WaWision &reg; |
		Versionsnummer: [REVISION]  
</div>
</center>
        <!-- end FOOTER -->
</body>

</html>
