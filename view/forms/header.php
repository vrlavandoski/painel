<html>
	<head>
		<title><?= APPLICATION_TILLE;?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<link rel="stylesheet" type="text/css" href="view/styles/style.css"/>
		<link rel="stylesheet" type="text/css" href="view/styles/jquery-ui-1.8.4.custom.css"/>		   
        <script type="text/javascript" src="utils/lib/js/jquery-1.4.2.js"></script>
        <script type="text/javascript" src="utils/lib/js/jquery-ui-1.8.4.custom.min.js"></script>
        <script type="text/javascript" src="utils/lib/js/jquery.boxy.js"></script>
        <script type="text/javascript" src="utils/lib/js/jquery.effects.core.js"></script>
        <script type="text/javascript" src="utils/lib/js/jquery.effects.blind.js"></script>      
    </head>
    
    <!-- 
    echo addCacheControlJS("libs/scripts/utils.js");
	echo addCacheControlJS("libs/scripts/ui-1.1.js");
	echo addCacheControlJS("view/js/form.menu.js");   
    echo addCacheControlJS("libs/jquery/jquery-1.4.2.js"); 
      
    echo addCacheControlJS("libs/jquery/jquery.autocomplete.js");
  
   
    echo addCacheControlJS("libs/scripts/mascaras.js");   
    
   
    
     -->
    
	<? 	
		if(isset($xajax)){
			$xajax->printJavascript();
		}
    ?>
 
	<body>
	<div id="carregando" style="display:none;">            
        <img src="./view/images/loading.gif" style="float: left; margin-left: 5px;"/><b>Aguarde...</b>
    </div>
    <div id="geral">
		<div id="conteudo">					
			<div id=meio>