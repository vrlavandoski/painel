<?php 	

require_once("utils/configs/config.php");
require_once(SYSTEM_DIR . "controler/painel.register.php");
if(isset($xajax)){
	$xajax->printJavascript();
}

include(SYSTEM_DIR . "view/forms/painel.view.php");