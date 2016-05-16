<?php

function quote_smart_mysqli($value){
	sanitizeStrMysqli($value);
	$value = "'".$value."'";
	return $value;
}


function query_mysqli_error($err,$sql){
	errorLog("[Log] Query error\n\n",
	"empresa: " . Auth::$idEmpresa . "\n\n" .
	"nome: " . $_SESSION['empresa']['nome'] . "\n\n" .
	"browser: " . $_SERVER["HTTP_USER_AGENT"] . "\n\n" .
	"mysql_error:".$err."\n\n".
	"SQL: $sql \n\n" );	
}


function query_mysqli($sql) {	
	try {
		$result = mysqli_query(ConnectionMysqli::getConnection(), $sql);
	} catch(mysqli_sql_exception $err) {
		debug("ERRO MYSQLI");		
		if(DEBUG){
			debug($sql);
			debug($err);
		}		
		trigger_error($err);
		ob_start();
		debug_print_backtrace();
		$trace = ob_get_contents();
		ob_end_clean();		
		
	}
	return $result;
}


function sanitizeStrMysqli(&$param) {
	@$param = mysqli_real_escape_string(ConnectionMysqli::getConnectionSanitize(), $param);
}


function debug($param, $tipo = "") {	
	if(DEBUG) {
		switch ($tipo) {
			case "E":
				FB::error($param);
			break;
			case "I":
				FB::info($param);
				break;
			case "W":
				FB::warn($param);
				break;
			default:
				FB::log($param);
			break;
		}
	}
}

?>