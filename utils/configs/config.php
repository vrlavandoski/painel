<?php
require_once("defines.php");
require_once("requires.php");

ini_set('magic_quotes_gpc', "Off");
header("content-type:text/html;charset=utf-8");

date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_ALL, "en_US");

$objetoTemporario = new ConnectionMysqli();


?>