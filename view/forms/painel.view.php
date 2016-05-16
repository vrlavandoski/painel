<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="en" />
	<meta name="GENERATOR" content="PHPEclipse 1.2.0" />
	<title>Carga Servidores</title>
</head>
<link rel="stylesheet" href="./utils/lib/js/jqx.base.css" type="text/css" />
<link rel="stylesheet" href="./view/styles/grafico.css" type="text/css" />
<script type="text/javascript" src="./utils/lib/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="./utils/lib/js/jqxcore.js"></script>
<script type="text/javascript" src="./utils/lib/js/jqxchart.js"></script>
<script type="text/javascript" src="./utils/lib/js/jqxdata.js"></script>
<script type="text/javascript" src="./utils/lib/js/grafico.js"></script>


<body style="background-color: #000;">
<!--
<div id='chartContainer' style="width:100%; height:100%"></div>
-->

<table style="width:100%; height: 100%;">
	<tr>
		<td style="width:95%; height: 100%;">
			<div id='chartContainer' style="width:100%; height: 100%;"></div>			
		</td>		
		
	</tr>
</table>


<script type="text/javascript">
$(document).ready(function(){
		iniciarTimer();
		initGrafico();		
});
</script>

</body>
</html>