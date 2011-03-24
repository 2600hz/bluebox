<?php
include('MongoCdr.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
<title>Welcome // TITLE</title>


<link rel="stylesheet" href="css/blueprint/screen.css" type="text/css" media="screen, projection">
<link rel="stylesheet" href="css/blueprint/print.css" type="text/css" media="print">	
<!--[if lt IE 8]><link rel="stylesheet" href="css/blueprint/ie.css" type="text/css" media="screen, projection"><![endif]-->
<link rel="stylesheet" type="text/css" media="screen" href="css/flick/jquery-ui-1.7.2.custom.css" />
<link rel="stylesheet" type="text/css" media="screen" href="css/ui.jqgrid.css" />


<script src="js/jquery-1.3.2.min.js" type="text/javascript"></script>
<script src="js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="js/jquery.jqGrid.min.js" type="text/javascript"></script>
<link href="style.css" rel="stylesheet" type="text/css" /> 
<script type="text/javascript">

</script>
<link href="css/blueprint/plugins/buttons/screen.css" rel="stylesheet" type="text/css" />
<link href="css/blueprint/plugins/fancy-type/screen.css" rel="stylesheet" type="text/css" />
</head>

<body>
<br />
<div class="container wrapper">
	<div class="span-12">
		<h1 style="padding-left:30px">(( VoiceBus )) </h1>
	</div>

	<div class="span-12 last" align="right">
		<a href="#" class="button">Main</a>
		<a href="#" class="button">CDR</a>
		<a href="#" class="button">Stats</a>
		<a href="#" class="button">DID / Service </a>
		<a href="#" class="button">Account</a>	</div>
	
	<div class="span-18 colborder" style="padding:15px">
		<table id="list"></table> 
		<div id="pager"></div> 
	</div>
	
	<div class="span-4 loud last">
		<h3>Direction</h3>
		<p class="quiet">Determines if the call was placed or recieved</p>

		<h3>Destination</h3>
		<p class="quiet">Determines if the call was placed or recieved</p>

	</div>

	<div class="span-24 last">
		<p class="quiet center">Copyright 2009-2010 </p>
	</div>
</div>



</body>
</html>
