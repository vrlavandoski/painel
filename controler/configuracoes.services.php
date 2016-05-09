<?
require_once(SYSTEM_DIR . "utils/persistent.mysqli.class.php");
require_once(SYSTEM_DIR . "model/configuracoes.model.php");
session_start();


function obterConfiguracoes(){
	$objResponse = new xajaxResponse();
	$conf = new Configuracoes();
	$conf->obterConfiguracoes();
	$obj = $conf->getRow();
	$objResponse->assign("titulo", "value", $obj->titulo);
	$objResponse->assign("valorMaximoX", "value", $obj->valorMaximoX);
	$objResponse->assign("valorMaximoY", "value", $obj->valorMaximoY);
	$objResponse->assign("intervaloX", "value", $obj->intervaloX);
	$objResponse->assign("intervaloY", "value", $obj->intervaloY);
	$objResponse->assign("animacao", "value", $obj->animacao);	
	return $objResponse;
}

function salvarConfiguracoes($aDados){
	$objResponse = new xajaxResponse();
	if (!validarConfiguracoes($aDados, $objResponse)) {
		$objResponse->assign("mensagem-configuracao", "className", "errors");
		$objResponse->assign("mensagem-configuracao", "innerHTML", "<h4>Não foi possível salvar as configurações</h4>Verifique os campos destacados acima.");
		return $objResponse;
	} else {
		$objResponse->assign("mensagem-configuracao", "className", "");
		$objResponse->assign("mensagem-configuracao", "innerHTML", "");
	}
	$conf = new Configuracoes();	
	$conf->alterarConfiguracoes($aDados);	
	$objResponse->alert("Configurações salvas.");
	return $objResponse;
}

function validarConfiguracoes($aDados, $objResponse){
	$retorno = true;
	if($aDados["titulo"]=='') {
		$objResponse->assign("titulo","className","warning");
		$objResponse->assign("titulo-message","className", "tip-message");
		$objResponse->assign("titulo-message","innerHTML","Informe um título para o gráfico.");
		$retorno = false;
	} else {
		$objResponse->assign("titulo","className","");
		$objResponse->assign("titulo-message","className", "nomessage");
		$objResponse->assign("titulo-message","innerHTML","");
	}
	if($aDados["valorMaximoX"]=='') {
		$objResponse->assign("valorMaximoX","className","warning");
		$objResponse->assign("valorMaximoX-message","className", "tip-message");
		$objResponse->assign("valorMaximoX-message","innerHTML","Informe o valor máximo de exibição para a linha do X.");
		$retorno = false;
	} else {
		$objResponse->assign("valorMaximoX","className","");
		$objResponse->assign("valorMaximoX-message","className", "nomessage");
		$objResponse->assign("valorMaximoX-message","innerHTML","");
	}
	if($aDados["valorMaximoY"]=='') {
		$objResponse->assign("valorMaximoY","className","warning");
		$objResponse->assign("valorMaximoY-message","className", "tip-message");
		$objResponse->assign("valorMaximoY-message","innerHTML","Informe o valor máximo de exibição para a linha do Y.");
		$retorno = false;
	} else {
		$objResponse->assign("valorMaximoY","className","");
		$objResponse->assign("valorMaximoY-message","className", "nomessage");
		$objResponse->assign("valorMaximoY-message","innerHTML","");
	}	
	return $retorno;
}

?>