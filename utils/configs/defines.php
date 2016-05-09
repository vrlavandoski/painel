<?php

define("SYSTEM_DIR", realpath(dirname(__FILE__) . "/../../") . "/");
define("BASE_DIR", "b=painel&amp;");
define("SERVER_DIR", $_SERVER ['HTTP_HOST'] . '/painel/');
define("HOST","localhost"); 
define("USER", "root");
define("PASS", "asdf000");
define("DB", "painel"); 
define("PORT", 3306);
$protocolo = 'http://';
define("HTTP_DIR", $protocolo . SERVER_DIR);
define("APPLICATION_TILLE", "Painel");
define('DEBUG', true);

?>