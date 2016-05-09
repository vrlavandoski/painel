<?php
require_once(SYSTEM_DIR . "utils/lib/php/xajax/xajax_core/xajax.inc.php");

$xajax = new xajax(HTTP_DIR."controler/painel.server.php");
$xajax->configure('javascript URI',HTTP_DIR.'utils/lib/php/xajax');

$xajax->register(XAJAX_FUNCTION, "obterDadosGrafico");
$xajax->register(XAJAX_FUNCTION, "listarClientes");
$xajax->register(XAJAX_FUNCTION, "obterCliente");
$xajax->register(XAJAX_FUNCTION, "salvarCliente");
$xajax->register(XAJAX_FUNCTION, "excluirClientes");
$xajax->register(XAJAX_FUNCTION, "salvarConfiguracoes");

?>