<?php
require_once(SYSTEM_DIR . "utils/lib/php/xajax/xajax_core/xajax.inc.php");

$xajax = new xajax(HTTP_DIR."controler/configuracoes.server.php");
$xajax->configure('javascript URI',HTTP_DIR.'utils/lib/php/xajax');

$xajax->register(XAJAX_FUNCTION, "salvarConfiguracoes");
$xajax->register(XAJAX_FUNCTION, "obterConfiguracoes");

?>