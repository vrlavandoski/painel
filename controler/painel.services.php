<?
require_once(SYSTEM_DIR . "utils/persistent.mysqli.class.php");
require_once(SYSTEM_DIR . "model/clientes.model.php");
require_once(SYSTEM_DIR . "model/configuracoes.model.php");
session_start();

function obterDadosGrafico(){	
	
	$cli1 = new Clientes();
	$cli1->obterClientesAtivos();	
	$cont=0;
	$obj1 = "";	
	while($obj1 = $cli1->getRow()){
		$leg[$cont]['dataField'] = "field_".$obj1->id;
		$leg[$cont]['displayText'] = $obj1->siglaGrafico;
		$leg[$cont]['color'] = "#".$obj1->corGrafico;
		$leg[$cont]['lineWidth'] = '2';	
		$cont ++;
	}	
	
	$conf = new Configuracoes();
	$conf->obterConfiguracoes();
	$objConf = $conf->getRow();
	$aConf["titulo"] = $objConf->titulo;
	$aConf["valorMaximoY"] = $objConf->valorMaximoY;
	$aConf["intervaloY"] = $objConf->intervaloY;
	$aConf["animacao"] = $objConf->animacao;	
	
	$limite = $cli1->getNumRows() * $objConf->valorMaximoX;
	$cli2 = new Clientes();
	$cli2->obterLeiturasGrafico($limite);
	while($obj2 = $cli2->getRow()){		
		$acumulador[$obj2->data][] = $obj2;
	}
	
	$c1=0;	
	foreach($acumulador as $key => $aLeituras){			
			$min = substr($key, 14, 2);
			$intervalo = substr($key, 11, 5);
			switch ($objConf->intervaloX) {
				case 10: 
					if (($min == "00") || ($min == "10") || ($min == "20") || ($min == "30") || ($min == "40") || ($min == "50")) {			
						$aGrupoLeitura[$c1]["data"] = $intervalo;
					} else {
						$aGrupoLeitura[$c1]["data"] = "";
					}
					break;
				case 30:
					if (($min == "00") || ($min == "30")) {			
						$aGrupoLeitura[$c1]["data"] = $intervalo;
					} else {
						$aGrupoLeitura[$c1]["data"] = "";
					}
					break;
				case 60:
					if ($min == "00") {			
						$aGrupoLeitura[$c1]["data"] = $intervalo;
					} else {
						$aGrupoLeitura[$c1]["data"] = "";
					}
					break;				
			}			
		
		foreach($aLeituras as $key => $aLeitura){
			$idCliente = $aLeitura->idCliente;
			$aGrupoLeitura[$c1]["field_".$idCliente] = $aLeitura->leitura/100;
		}
		$c1 ++;
	}	
	$objResponse = new xajaxResponse();	
	$objResponse->script('montarGrafico('.json_encode($aConf).','.json_encode($leg).','.json_encode($aGrupoLeitura).')');
	return $objResponse;	
}

function listarClientes($busca=""){
	$_SESSION['busca'] = $busca;
	$objResponse = new xajaxResponse();		
	$objResponse->assign("datatable","innerHTML",listar($busca));
	return $objResponse;
}

function listar($nome){
	$clientes = new Clientes();		
	$clientes->obterClientesPorNome($nome);
	$lista = '<form name="dataForm" id="dataForm">';			
	$lista .= '<table>';
	$lista .= '<tr>' .
				'<th></th>' .
				'<th align="left">Nome</th>' .
				'<th align="left">Sigla</th>' .
				'<th align="left">Cor no gráfico</th>' .
				'<th align="left">Ip</th>' .
				'<th align="left">Porta</th>' .				
				'<th align="left">Ativo</th>' .
				'</tr>';
	while($obj = $clientes->getRow()) {			
		$lista .= "<tr onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className=''\" style='cursor:pointer;'>";
		$lista .= "<td width='3%'><input type='checkbox' id='marcado".$obj->id."' name='marcado".$obj->id."' value='".$obj->id."'></td>";		
		$lista .= "<td onClick='abrirPopupEdicaoCliente(".$obj->id.");'>".$obj->nome."</td>";
		$lista .= "<td onClick='abrirPopupEdicaoCliente(".$obj->id.");'>".$obj->siglaGrafico."</td>";
		$lista .= "<td style='width:100px' onClick='abrirPopupEdicaoCliente(".$obj->id.");'><div style='background-color:#".$obj->corGrafico."'>".$obj->corGrafico."</div></td>";
		$lista .= "<td onClick='abrirPopupEdicaoCliente(".$obj->id.");'>".$obj->ip."</td>";
		$lista .= "<td onClick='abrirPopupEdicaoCliente(".$obj->id.");'>".$obj->porta."</td>";
		$lista .= "<td onClick='abrirPopupEdicaoCliente(".$obj->id.");'>".$obj->ativo."</td>";
		$lista .= "</tr>";		
	}	
	$lista .= '</table>';	
	$lista .= '</form>';	
	return $lista;
}	

function obterCliente($id){
	$objResponse = new xajaxResponse();
	$cliente = new Clientes();
	$cliente->obterCliente($id);
	$obj = $cliente->getRow();
	$objResponse->assign("id", "value", $obj->id);
	$objResponse->assign("nome", "value", $obj->nome);
	$objResponse->assign("siglaGrafico", "value", $obj->siglaGrafico);
	$objResponse->assign("corGrafico", "value", $obj->corGrafico);
	$objResponse->assign("ip", "value", $obj->ip);
	$objResponse->assign("porta", "value", $obj->porta);
	$objResponse->assign("ativo", "value", $obj->ativo);
	$objResponse->script("ajustarCorCampo('".$obj->corGrafico."')");
	return $objResponse;
}

function salvarCliente($id, $aDados){
	$objResponse = new xajaxResponse();	
	if (!validar($aDados, $objResponse)) {
		$objResponse->assign("mensagem-cliente", "className", "errors");
		$objResponse->assign("mensagem-cliente", "innerHTML", "<h4>Não foi possível salvar a máquina cliente</h4>Verifique os campos destacados acima.");
		return $objResponse;
	} else {
		$objResponse->assign("mensagem-cliente", "className", "");
		$objResponse->assign("mensagem-cliente", "innerHTML", "");
	}
	$cliente = new Clientes();
	if($id > 0){
		$cliente->alterarCliente($id, $aDados);
	}else{
		$cliente->adicionarCliente($aDados);
	}	
	$objResponse->assign("datatable","innerHTML",listar($_SESSION['busca']));
	$objResponse->script("fecharPopupCadastroCliente()");	
	return $objResponse;
}

function excluirClientes($aClientes){
	$objResponse = new xajaxResponse();	
	$cliente = new Clientes();
	foreach ($aClientes as $id){
		$cliente->excluir($id);
	}	
	$objResponse->assign("datatable","innerHTML",listar($_SESSION['busca']));
	return $objResponse;
}

function validar($aDados, $objResponse){
	$retorno = true;
	if($aDados["nome"]=='') {
		$objResponse->assign("nome","className","warning");
		$objResponse->assign("nome-message","className", "tip-message");
		$objResponse->assign("nome-message","innerHTML","Informe um nome para a máquina cliente.");
		$retorno = false;
	} else {
		$objResponse->assign("nome","className","");
		$objResponse->assign("nome-message","className", "nomessage");
		$objResponse->assign("nome-message","innerHTML","");
	}
	if($aDados["siglaGrafico"]=='') {
		$objResponse->assign("siglaGrafico","className","warning");
		$objResponse->assign("sigla-message","className", "tip-message");
		$objResponse->assign("sigla-message","innerHTML","Informe uma sigla para ser usada na legenda do gráfico.");
		$retorno = false;
	} else {
		$objResponse->assign("siglaGrafico","className","");
		$objResponse->assign("sigla-message","className", "nomessage");
		$objResponse->assign("sigla-message","innerHTML","");
	}
	if($aDados["corGrafico"]=='') {
		$objResponse->assign("corGrafico","className","warning");
		$objResponse->assign("cor-message","className", "tip-message");
		$objResponse->assign("cor-message","innerHTML","Informe uma cor para representar esta máquina no gráfico.");
		$retorno = false;
	} else {
		$objResponse->assign("corGrafico","className","");
		$objResponse->assign("cor-message","className", "nomessage");
		$objResponse->assign("cor-message","innerHTML","");
	}
	if($aDados["ip"]=='') {
		$objResponse->assign("ip","className","warning");
		$objResponse->assign("ip-message","className", "tip-message");
		$objResponse->assign("ip-message","innerHTML","Informe o endereço ip desta máquina cliente.");
		$retorno = false;
	} else {
		$objResponse->assign("ip","className","");
		$objResponse->assign("ip-message","className", "nomessage");
		$objResponse->assign("ip-message","innerHTML","");
	}
	if($aDados["porta"]=='') {
		$objResponse->assign("porta","className","warning");
		$objResponse->assign("porta-message","className", "tip-message");
		$objResponse->assign("porta-message","innerHTML","Informe a porta ssh para conexão a esta máquina.");
		$retorno = false;
	} else {
		$objResponse->assign("porta","className","");
		$objResponse->assign("porta-message","className", "nomessage");
		$objResponse->assign("porta-message","innerHTML","");
	}
	return $retorno;
}

?>