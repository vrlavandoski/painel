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
<script type="text/javascript" src="./utils/lib/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="./utils/lib/js/jqxcore.js"></script>
<script type="text/javascript" src="./utils/lib/js/jqxchart.js"></script>
<script type="text/javascript" src="./utils/lib/js/jqxdata.js"></script>
<script type="text/javascript" src="./utils/lib/js/grafico.js"></script>

<style>
.painel{background-color:#2E8B57;width:100%;font-weight:bold;color:#000; margin-bottom:8px;}
.painelOrange{background-color:orange;width:100%;font-weight:bold;color:#000; margin-bottom:8px;}
.c1{background-color:#D3DF88;}
.c2{background-color:#FFE500;}
.c3{background-color:#FFCC00;}
.c4{background-color:#FFB200;}
.c5{background-color:#FF9900;}
.c6{background-color:#FF8000;}
.c7{background-color:#FF6600;}
.c8{background-color:#FF3300;}
.c9{background-color:#FF0000;}
.c10{background-color:#741717; text-decoration: blink;}

.painelRed{background-color:red;width:100%;font-weight:bold;color:#000; margin-bottom:8px;}
.painelBlue{background-color:#4682B4;width:100%;font-weight:bold;color:#ffffff; margin-bottom:8px;}
.titulo{border:solid 1px #000;font-size:10pt;text-align:center;}
.result{font-size:20pt;text-align:center;}
.resultMenor{font-size:16pt;text-align:center;}
.placa{width:120px; vertical-align:top;}
.graficoPrincipal{margin-left:-100px;background-color:#000; padding-bottom:0px;}
#graficoAc{margin-left:-95px;}
#graficoAcUsr{margin-left:-95px;}
#graficoAreaOutros{margin-left:-95px;}
.fonteBranca{color:#ffffff; text-align:center; size:15pt;}
body { -moz-transition:background 1s; /* Firefox 4 */ }


</style>

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