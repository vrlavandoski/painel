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
	//die("Ocorreu um problema no sistema. A equipe de suporte foi notificada. Caso o problema persistir, <a href='" . HTTP_DIR . "tickets#list'>entre em contato conosco</a>.<br><br>");
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

function parseTipoPesquisaNf($value){
	
	$barras = getNumeroBarras($value);
	
	if (getNumeroBarras($value)==2){
		return 'Data';
	} else if (getNumeroBarras($value)==4){
		return 'Periodo';
	}else{		
		$valor = getNumberValue($value);
		if ($valor==''){
			return 'Nome';
		}else{
			return 'Numero';
		}		
	}
}

function SomarPes($valor,$pesoInical,$pesoFinal) {
    
    $peso = $pesoInical;    
    $soma = 0;
    for ($i=strlen($valor)-1; $i >= 0; $i--){ 
    
        $soma += $valor[$i]* $peso; 
        
        if ($peso < $pesoFinal){
            $peso++;
        }else {
            $peso = $pesoInical;
        }
        
    }
    
    return $soma;
    
}
function existeCaraceresEspeciaisPwd($string) {
	$especiais = array("'","\"");
	$encontrados = "";    
    for($i=0; $i<strlen($string); $i++){
    	$carac = substr($string,$i,1);
    	if(in_array($carac,$especiais)){
    		if($carac == " "){
    			$carac = "'espaço em branco'";
    		}
    		$encontrados .= "&nbsp;".$carac."&nbsp;";
    	}
    }     
    if(strlen($encontrados)>0){
    	return $encontrados;
    }else{
    	return false;
    }
}   


// error handler function
function myErrorHandler($errno, $errstr, $errfile, $errline)
{
    switch ($errno) {
    case E_USER_ERROR:
        echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
        echo "  Fatal error on line $errline in file $errfile";
        echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
        echo "Aborting...<br />\n";
        exit(1);
        break;

    case E_USER_WARNING:
        //echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
        break;

    case E_USER_NOTICE:
        //echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
        break;

    default:
		//die("error: $errstr<br>$errfile<br>$errline<br>".E_USER_ERROR);
        //echo "Unknown error type: [$errno] $errstr<br />\n";
        break;
    }

    /* Don't execute PHP internal error handler */
    return true;
}



// wrapper para a função fb do firephp
function debug($param, $tipo = "") {	
	if(DEBUG) {
		//if ($tipo != "") {
			newDebug($param, $tipo);
		//} else {
		//	fb($param);
			//ChromePhp::log($param);
		//}
	}
}

function newDebug($param, $tipo = "") {
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

function addCacheControl($filename) {
	$file_basename = substr($filename, 0, strripos($filename, '.')); 
    $file_ext      = substr($filename, strripos($filename, '.'));
    $file_date     = filemtime(SYSTEM_DIR.$filename);
    // return $file_basename."-".$file_date.$file_ext;
    return $file_basename . $file_ext . "?v=" . $file_date;
}

function addCacheControlJS($filename, $includeOldIE = true) {
	if ($includeOldIE) {
		return '<script type="text/javascript" src="'.HTTP_DIR.$filename.'?v=' . VERSAO_SISTEMA . '"></script>';
	} else {
		// just include for great browsers and ie10+
		return '<!--[if !IE]> -->' . PHP_EOL . '<script type="text/javascript" src="'.HTTP_DIR.$filename.'?v=' . VERSAO_SISTEMA . '"></script>' . PHP_EOL . '<!-- <![endif]-->';
	}
}

function addCacheControlCSS($filename, $media='') {
	if($media!=''){
	 	$media="media=\"$media\"";
	}
	return '<link rel="stylesheet" type="text/css" href="' .HTTP_DIR.$filename . '?v=' . VERSAO_SISTEMA . '"' . $media . '/>';
}

function minifyJs($filenames) {
	return '<script type="text/javascript" src="libs/m/' . BASE_DIR . 'f=' . $filenames . '"></script>';
}
		
function addHeaderDownload($fileName, $fileType="text/plain") {
	header('Expires: 0');
	header('Cache-Control: must-revalidate, pre-check=0, post-check=0');
	header('Cache-Control: public');
	header('Content-Description: File Transfer');
	header('Content-type: ' . $fileType);
	header('Content-Disposition: attachment; filename="' . $fileName . '"');
	header('Content-Transfer-Encoding: binary');	
}

function preencherADireita($campo, $tamanho, $letrasMaiusculas=false, $caracterPreenchimento=" ") {
	$result = removerAcentuacao($campo);
	if ($letrasMaiusculas) {
		$result = strtoupper($result);
	}
	$result = str_pad($result, $tamanho, $caracterPreenchimento, STR_PAD_RIGHT);
	return substr($result, 0, $tamanho);
}

function preencherAEsquerda($campo, $tamanho, $letrasMaiusculas=false, $caracterPreenchimento="0") {
	$result = removerAcentuacao($campo);
	if ($letrasMaiusculas) {
		$result = strtoupper($result);
	}
	$result = str_pad($result, $tamanho, $caracterPreenchimento, STR_PAD_LEFT);
	return substr($result, 0, $tamanho);
}

function escapeContraBarra($str){		
	$newStr = "";
	for ($i=0; $i< strlen($str); $i++){	    
	    if($str{$i} == chr(92)){
	    	$newStr .= $str{$i}.chr(92);
	    }else{
	    	$newStr .= $str{$i};
	    }	    
	}	
	return $newStr;
}

function generatePassword($length = 8, $strength = 8) {
	$vowels = 'aeuy';
	$consonants = 'bdghjmnpqrstvz';
	if ($strength & 1) {
		$consonants .= 'BDGHJLMNPQRSTVWXZ';
	}
	if ($strength & 2) {
		$vowels .= "AEUY";
	}
	if ($strength & 4) {
		$consonants .= '23456789';
	}
	if ($strength & 8) {
		$consonants .= '@#$%';
	}
	$password = '';
	$alt = time() % 2;
	for ($i = 0; $i < $length; $i++) {
		if ($alt == 1) {
			$password .= $consonants[(rand() % strlen($consonants))];
			$alt = 0;
		} else {
			$password .= $vowels[(rand() % strlen($vowels))];
			$alt = 1;
		}
	}
	return $password;
}

function getBinaryFile($file){
	$pont = fopen($file, "rb");
	$binario = addslashes(fread($pont, filesize($file)));
	fclose($pont);	
	return $binario;
}

function limpaPasta($dir){
	if ($handle = @opendir($dir)){
		while (($file = readdir($handle)) !== false){
			if (($file == ".") || ($file == "..")){
				continue;
			}
			if (is_dir($dir . '/' . $file)){
				limpaPasta($dir . '/' . $file);
			}else{
				@unlink($dir . '/' . $file); 
			}
		}
		@closedir($handle);	
	}
}

function getCaptionCampoDeErro($campo, $tipo = ""){
	if ($campo == "cnpj"){
		if ($tipo == "F") {
			$campo = "<li>CPF incorreto.</li>";
		} else {
			$campo = "<li>CNPJ incorreto.</li>";
		}
	} else if ($campo == "ie"){
		$campo = "<li>Inscrição estadual incorreta para o estado " . $tipo . ".</li>";
	} else if ($campo == "enderecoNro"){
		$campo = "<li>O campo Nº do endereço não foi preenchido.</li>";
	} else if ($campo == "fantasia"){
		$campo = "<li>O nome fantasia do contato não foi preenchido.</li>";
	} else if ($campo == "codigo"){
		$campo = "<li>O código do contato não foi preenchido.</li>";
	} else if ($campo == "cidade"){
		$campo = "<li>Um município válido deve ser informado.</li>";	
	} else if ($campo == "endereco"){
		$campo = "<li>Preencha o campo endereço do contato.</li>";	
	} else {
		$campo = "<li>Preencha o campo " . $campo  . " do contato.</li>";
	}
	return $campo;
}

function dividirCodigoParaImpressao($codigoOriginal = "") {
	$codigoImpresso = $codigoOriginal;
	if (strlen($codigoOriginal) > "15") {
		$codigoImpresso = "";
		$strCodigo = $codigoOriginal;
		while (trim($strCodigo) != "") {
			if (trim($codigoImpresso) != "") {
				$codigoImpresso .= " ";
			}
			$codigoImpresso .= trim(substr($strCodigo, 0, 15));
			$strCodigo = substr($strCodigo, 15);
		}
	}
	return $codigoImpresso;
}

function criarLogoGeracaoPDF() {
	$empresa = new EmpresaDados();
	if (possuiLogo(Auth :: $idEmpresa, $empresa)) {
		$aEmpresa = $empresa->obterAtributos();
		//$arquivo = SYSTEM_DIR . "tmp/" . Auth :: $idEmpresa . "/logotipo.jpg";
		$arquivo = TMP_PATH . Auth :: $idEmpresa . "/logotipo.jpg";
		if (! file_exists(TMP_PATH . Auth :: $idEmpresa . "/")) {
			mkdir(TMP_PATH . Auth :: $idEmpresa . "/", 0775);
		}
		
		$fp = fopen($arquivo, "w");
		fwrite($fp, $aEmpresa["logoMaior"]);
		fclose($fp);
	} else {
		$arquivo = IMG_LOGO;
	}
	return $arquivo;
}

function converteEncodingXmlImportado($xmlE){
       $encoding[0] = "UTF-8";
       $encoding[1] = "Windows-1252";
       $encoding[2] = "ISO-8859-1";
       $encoding[3] = "ISO-8859-15";
       $encoding[4] = "Windows-1251";
       
       if (mb_detect_encoding($xmlE, $encoding) != "UTF-8"){
        	$xmlE = utf8_encode($xmlE);
       }
       return $xmlE;
}

function getResumoTexto($texto,$tamanho,$tamanholink=30){
	$tam = strlen($texto);
	$novoTexto = "";
	$copiar = true;
	for ($i=0;$i<$tam;$i++){
			
		$caracter = substr($texto,$i,1);
			
		if ($copiar){
			$novoTexto .= $caracter;
		}
			
		if (strlen($novoTexto) >= $tamanho){
			if (($caracter == ' ')||($caracter == ',')||($caracter == '.')||($caracter == ':')||($caracter == '?')||($caracter == '!')||($caracter == '')){
				$novoTexto .= '...';
				$copiar = false;
				break;
			}				
		}					
	}
	return $novoTexto; 
}

function tamanhoString($srt){
	return mb_strlen($srt,"utf-8");
}
	
function converteEncodingArray($data){
       $encoding[0] = "UTF-8";
       $encoding[1] = "Windows-1252";
       $encoding[2] = "ISO-8859-1";
       $encoding[3] = "ISO-8859-15";
       $encoding[4] = "Windows-1251";
       
       $temp = array();
       foreach($data as $idx => $value){
      		if (mb_detect_encoding($value, $encoding) != "UTF-8"){      			
        	  	$temp[$idx] = utf8_encode($value);
            	//$temp[$idx] = mb_convert_encoding($value, "UTF-8", "Windows-1252, ISO-8859-1, ISO-8859-15, Windows-1251");
           	} else{
           		$temp[$idx] = $value;
           	}
			if(!check_utf8($temp[$idx])) {
				$temp[$idx] = utf8_encode($temp[$idx]);
			}
       }
       return $temp;
}

//GET PARAMETROS
function getRegistrosPaginacao() {
	if (!(isset($_SESSION["paginacao"]))) {
		$_SESSION["paginacao"] = ParameterEngine :: getParameter("paginacao");
	}
	return $_SESSION["paginacao"];
}

function getDecimaisPreco() {
	if (!(isset($_SESSION["casas_decimais_preco"]))) {
		$_SESSION["casas_decimais_preco"] = ParameterEngine :: getParameter("casas_decimais_preco");
	}
	return $_SESSION["casas_decimais_preco"];
}

function getDecimaisQuantidade() {
	if (!(isset($_SESSION["casas_decimais_quantidade"]))) {
		$_SESSION["casas_decimais_quantidade"] = ParameterEngine :: getParameter("casas_decimais_quantidade");
	}
	return $_SESSION["casas_decimais_quantidade"];
}

function getConfigVoltarParaListagem() {
	if (!(isset($_SESSION["interface_voltar_para_listagem"]))) {
		$_SESSION["interface_voltar_para_listagem"] = ParameterEngine :: getParameter("interface:voltarParaListagem");
	}
	return $_SESSION["interface_voltar_para_listagem"];
}

function getConfigEditarAutomaticamente() {
	if (!(isset($_SESSION["interface_editar_automaticamente"]))) {
		$_SESSION["interface_editar_automaticamente"] = ParameterEngine :: getParameter("interface:editarAutomaticamente");
	}
	return $_SESSION["interface_editar_automaticamente"];
}

function mensagens_possuiLogo($idEmpresa, $empresa) {	
// 	$objPlano = $empresa->obterPlano($idEmpresa);
// 	if ($objPlano->logo != "S") {
// 		return false;
// 	}
	$empresa->obter($idEmpresa);
	$aEmpresa = $empresa->obterAtributos();
	if ($aEmpresa['logo'] == "") {
		return false;
	}
	return true;
}

function criarLogoDaEmpresaEmAnexo() {
	$empresa = new EmpresaDados();
	if (mensagens_possuiLogo(Auth :: $idEmpresa, $empresa)) {
		$aEmpresa = $empresa->obterAtributos();
		//$arquivo[0] = SYSTEM_DIR . "tmp/" . Auth :: $idEmpresa . "/logotipo";
		$arquivo[0] = TMP_PATH . Auth :: $idEmpresa . "/logotipo";
		if (! file_exists(TMP_PATH . Auth :: $idEmpresa . "/")) {
			mkdir(TMP_PATH . Auth :: $idEmpresa . "/", 0777);
		}
		
		$fp = fopen($arquivo[0], "w");
		fwrite($fp, $aEmpresa["logo"]);
		fclose($fp);
	} else {
		$arquivo[0] = IMG_LOGO;
	}
	return $arquivo[0];

}

function criarLogoDaEmpresaAdminEmAnexo() {
// 	$empresa = new EmpresaDados();
// 	if (mensagens_possuiLogo(ID_EMPRESA_ADM, $empresa)) {
// 		$aEmpresa = $empresa->obterAtributos();
// 		//$arquivo[0] = SYSTEM_DIR . "tmp/" . ID_EMPRESA_ADM . "/logotipo";
// 		$arquivo[0] = TMP_PATH . ID_EMPRESA_ADM . "/logotipo";
// 		if (! file_exists(TMP_PATH . ID_EMPRESA_ADM . "/")) {
// 			mkdir(TMP_PATH . ID_EMPRESA_ADM . "/", 0777);
// 		}
		
// 		$fp = fopen($arquivo[0], "w");
// 		fwrite($fp, $aEmpresa["logo"]);
// 		fclose($fp);
// 	} else {
// 		$arquivo[0] = IMG_LOGO;
// 	}
	$arquivo[0] = IMG_LOGO;
	return $arquivo[0];

}

/* Tiny - Mensagens */
function converteEncodingTexto($value) {
	$encoding[0] = "UTF-8";
	$encoding[1] = "Windows-1252";
	$encoding[2] = "ISO-8859-1";
	$encoding[3] = "ISO-8859-15";
	$encoding[4] = "Windows-1251";
	
	$temp = array();
	if (mb_detect_encoding($value, $encoding) != "UTF-8") {
		$value = utf8_encode($value);
	}
	if (! check_utf8($value)) {
		$value = utf8_encode($value);
	}
	return $value;
}

function getMensagemBoasVindasInscricao($nomeEmpresa, $nomeLogin, $plano) {
	/*
	===================================================================================================
	INSCRIÇÃO
	===================================================================================================
	Obrigado por inscrever-se no Tiny
	
	Flex Sianlização Modular Ltda, seja bem-vindo(a) ao Tiny. 
	
	Esperamos que você encontre aqui todas as ferramentas necessárias para o gerenciamento dos seus recursos. 
	
	Você pode acessar o sistema através do endereço www.tiny.com.br
	Seu login é: igorjpll
	
	Uma visão geral do Tiny, bem como informações sobre os principais recursos do sistema, podem ser obtidos no endereço https://www.tiny.com.br/manuais-tiny
	
	Para o esclarecimento de dúvidas, solicitação de auxílio ou envio de sugestões, sinta-se à vontade para utilizar qualquer um dos nossos canais:
	- Ferramenta de suporte disponibilizada no próprio sistema, acessível através da opção "Suporte", localizada no canto superior direito da tela
	- E-mail: suporte@tiny.com.br
	- Telefone: (54) 3055-8200
	
	Estamos à disposição para o que você precisar.
	
	Atenciosamente,
	Equipe - Tiny
	*/
	$dadosMensagem = array();
	
	$dadosMensagem["logotipo"] = criarLogoDaEmpresaAdminEmAnexo();
	
	$plano = str_replace("plano ", "", $plano);
	$plano = str_replace("Plano ", "", $plano);
	$plano = str_replace("plano", "", $plano);
	$plano = str_replace("Plano", "", $plano);
	
	$estiloFontePadrao = "font-family: Lato,Helvetica,Arial; font-size: 11pt; text-align: left; font-weight: normal;";
	
	$html_mensagem = "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>" .
					 "<div>" .
		   			 "	<center>" .
		   			 "		<table>" .
		   			 "			<tr>" .
		   			 "				<td valign='top' style='border-collapse: collapse;' align='center'>" .
		   			 "					<table width='600px' cellspacing='0' cellpadding='0' border='0' style='border:1px solid #dddddd; padding: 20px;' align='center'>" .
		   			 "						<tr>" .
		   			 "							<td valign='top' style='border-collapse:collapse'>" .
		   			 "								<div><img src='cid:logotipo'></div>" .
		   			 "								<div style='color: #444; " . $estiloFontePadrao . "'><br/>" .
		   			 "									<h1 style='font-weight: bold; font-size: 12pt;'>" . $nomeEmpresa . "<span style='" . $estiloFontePadrao . "'>, seja bem-vindo(a) ao " . SISTEMA . "&nbsp;</span><span style='" . $estiloFontePadrao . "'>.</span></h1>" .
		   			 "									<span>" . converteEncodingTexto("Esperamos que você encontre aqui todas as ferramentas necessárias para o gerenciamento dos seus recursos.") . "</span><br/><br/>" .
		   			 "									<span>" . converteEncodingTexto("Você inscreveu-se no Plano " . $plano . " e pode acessar o sistema através do endereço ") . "<a href='" . \UtilsApi\obterValorParametroNaAdmin("adm:msg:url") . "'>" . \UtilsApi\obterValorParametroNaAdmin("adm:msg:url") . "</a></span><br/>" .
		   			 "									<span>" . converteEncodingTexto("Seu login é:") . "&nbsp;&nbsp;<span style='font-weight: bold; font-size: 12pt;'>" . $nomeLogin . "</span></span><br/>";
		   			 
	$html_mensagem .= "									<br/><span>" . converteEncodingTexto("Você tem até 30 dias para utilizar o sistema em qualquer um dos nossos planos pagos, sem nenhum custo. Este é o período de demonstração, durante o qual você pode experimentar todos os recursos do plano escolhido. Ao término desse período, você pode optar pela contratação ou voltar ao plano grátis.") . "</span><br/>";
	
	if (SISTEMA == "Tiny") {
		$html_mensagem .= "<br/>" .
						  "									<span>" . converteEncodingTexto("Uma visão geral do Tiny, bem como informações sobre os principais recursos do sistema, podem ser obtidos no manual do usuário, que pode ser acessado na seção \"Suporte\"") . "</span><br/>";
	}
	$html_mensagem .= "<br/>" .
					  "									<span>" . converteEncodingTexto("Para o esclarecimento de dúvidas, solicitação de auxílio ou envio de sugestões, sinta-se à vontade para utilizar qualquer um dos nossos canais:") . "</span><br/>" .
					  "									<ul><li style='line-height: 25px;'>" . converteEncodingTexto("Ferramenta de suporte disponibilizada no próprio sistema, acessível através da opção \"Suporte\", localizada no canto superior direito da tela") . "</li>" .
					  "									<li style='line-height: 25px;'>" . converteEncodingTexto("E-mail: suporte@tiny.com.br") . "</li>" .
					  "									<li style='line-height: 25px;'>" . converteEncodingTexto("Telefone: (54) 3055-8200") . "</li></ul>" .
					  "<br/>" .
					  "									<span>" . converteEncodingTexto("Estamos à disposição para o que você precisar.") . "</span><br/>" .
					  "<br/>" .
					  "									<span>" . converteEncodingTexto("Atenciosamente,") . "</span><br/>" .
					  "									<span>" . converteEncodingTexto("Equipe - " . SISTEMA) . "</span>" .
					  "								</div>" .
					  "							</td>" .
					  "						</tr>" .
					  "					</table>" .
					  "				</td>" .
					  "			</tr>" .
					  "		</table>" .
					  "	</center>" .
					  "</div>";
	$dadosMensagem["mensagem"] = $html_mensagem;
	return $dadosMensagem;
}

function getMensagemSuporte($nomeEmpresa, $nomeUsuario, $mensagem, $suporteHabilitado, $usuarioDeSuporte) {
	/*
	===================================================================================================
	SUPORTE
	===================================================================================================
	Empresa: Flex Sianlização Modular Ltda
	Usuário: Fulaninho 
	
	Mensagem:
	
	Erro no envio de e-mails
	
	Para o esclarecimento de dúvidas, solicitação de auxílio ou envio de sugestões, sinta-se à vontade para utilizar qualquer um dos nossos canais:
	- Ferramenta de suporte disponibilizada no próprio sistema, acessível através da opção "Suporte", localizada no canto superior direito da tela
	- E-mail: suporte@tiny.com.br
	- Telefone: (54) 3055-8200
	
	Estamos à disposição para o que você precisar.
	
	Atenciosamente,
	Equipe - Tiny
	*/
	$dadosMensagem = array();
	
	$dadosMensagem["logotipo"] = criarLogoDaEmpresaAdminEmAnexo();
	
	$plano = str_replace("plano ", "", $plano);
	$plano = str_replace("Plano ", "", $plano);
	$plano = str_replace("plano", "", $plano);
	$plano = str_replace("Plano", "", $plano);
	
	$estiloFontePadrao = "font-family: Lato,Helvetica,Arial; font-size: 11pt; text-align: left; font-weight: normal;";
	
	$html_mensagem = "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>" .
					 "<div>" .
		   			 "	<center>" .
		   			 "		<table>" .
		   			 "			<tr>" .
		   			 "				<td valign='top' style='border-collapse: collapse;' align='center'>" .
		   			 "					<table width='600px' cellspacing='0' cellpadding='0' border='0' style='border:1px solid #dddddd; padding: 20px;' align='center'>" .
		   			 "						<tr>" .
		   			 "							<td valign='top' style='border-collapse:collapse'>" .
		   			 "								<div><img src='cid:logotipo'></div>" .
		   			 "								<div style='color: #444; " . $estiloFontePadrao . "'><br/>" .
		   			 "									<span style='" . $estiloFontePadrao . "'>Empresa: &nbsp;</span><span style='" . $estiloFontePadrao . " font-weight: bold; font-size: 12pt;'>" . converteEncodingTexto($nomeEmpresa) . "</span><br/>" .
		   			 "									<span style='" . $estiloFontePadrao . "'>" . converteEncodingTexto("Usuário") . ": &nbsp;</span><span style='" . $estiloFontePadrao . " font-weight: bold; font-size: 12pt;'>" . converteEncodingTexto($nomeUsuario) . "</span><br/><br/>" .
		   			 "									<h1 style='" . $estiloFontePadrao . " border-bottom: 1px solid #ddd; font-weight: bold;'>Mensagem</h1>" .
		   			 "									<span>" . converteEncodingTexto($mensagem) . "</span><br/><br/>";
	if ($suporteHabilitado) {
		$html_mensagem .= "									<h1 style='" . $estiloFontePadrao . " border-bottom: 1px solid #ddd;'>&nbsp;</h1>" .
						  "									<span style='" . $estiloFontePadrao . " color: #600000;'>" . converteEncodingTexto("Usuário de suporte habilitado") . "</span><br/>" .
						  "									<span style='" . $estiloFontePadrao . "'>" . converteEncodingTexto("Usuário") . ": &nbsp;</span><span style='" . $estiloFontePadrao . " font-weight: bold; font-size: 12pt;'>" . converteEncodingTexto($usuarioDeSuporte) . "</span><br/><br/>";
	}
	$html_mensagem .= "								</div>" .
					  "							</td>" .
					  "						</tr>" .
					  "					</table>" .
					  "				</td>" .
					  "			</tr>" .
					  "		</table>" .
					  "	</center>" .
					  "</div>";
	$dadosMensagem["mensagem"] = $html_mensagem;
	return $dadosMensagem;
}

function getMensagemUsuarioSuporteHabilitado($nomeEmpresa, $nomeUsuario, $suporteHabilitado, $usuarioDeSuporte) {
	/*
	===================================================================================================
	SUPORTE
	===================================================================================================
	Empresa: Flex Sianlização Modular Ltda
	Usuário: Fulaninho 
	
	Mensagem:
	
	Erro no envio de e-mails
	
	Para o esclarecimento de dúvidas, solicitação de auxílio ou envio de sugestões, sinta-se à vontade para utilizar qualquer um dos nossos canais:
	- Ferramenta de suporte disponibilizada no próprio sistema, acessível através da opção "Suporte", localizada no canto superior direito da tela
	- E-mail: suporte@tiny.com.br
	- Telefone: (54) 3055-8200
	
	Estamos à disposição para o que você precisar.
	
	Atenciosamente,
	Equipe - Tiny
	*/
	$dadosMensagem = array();
	
	$dadosMensagem["logotipo"] = criarLogoDaEmpresaAdminEmAnexo();
	
	$plano = str_replace("plano ", "", $plano);
	$plano = str_replace("Plano ", "", $plano);
	$plano = str_replace("plano", "", $plano);
	$plano = str_replace("Plano", "", $plano);
	
	$estiloFontePadrao = "font-family: Lato,Helvetica,Arial; font-size: 11pt; text-align: left; font-weight: normal;";
	
	$html_mensagem = "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>" .
					 "<div>" .
		   			 "	<center>" .
		   			 "		<table>" .
		   			 "			<tr>" .
		   			 "				<td valign='top' style='border-collapse: collapse;' align='center'>" .
		   			 "					<table width='600px' cellspacing='0' cellpadding='0' border='0' style='border:1px solid #dddddd; padding: 20px;' align='center'>" .
		   			 "						<tr>" .
		   			 "							<td valign='top' style='border-collapse:collapse'>" .
		   			 "								<div><img src='cid:logotipo'></div>" .
		   			 "								<div style='color: #444; " . $estiloFontePadrao . "'><br/>" .
		   			 "									<span style='" . $estiloFontePadrao . "'>Empresa: &nbsp;</span><span style='" . $estiloFontePadrao . " font-weight: bold; font-size: 12pt;'>" . converteEncodingTexto($nomeEmpresa) . "</span><br/>" .
		   			 "									<span style='" . $estiloFontePadrao . "'>" . converteEncodingTexto("Usuário") . ": &nbsp;</span><span style='" . $estiloFontePadrao . " font-weight: bold; font-size: 12pt;'>" . converteEncodingTexto($nomeUsuario) . "</span><br/><br/>";
	if ($suporteHabilitado) {
		$html_mensagem .= "									<h1 style='" . $estiloFontePadrao . " border-bottom: 1px solid #ddd;'>&nbsp;</h1>" .
						  "									<span style='" . $estiloFontePadrao . " color: #600000;'>" . converteEncodingTexto("Usuário de suporte habilitado") . "</span><br/>" .
						  "									<span style='" . $estiloFontePadrao . "'>" . converteEncodingTexto("Usuário") . ": &nbsp;</span><span style='" . $estiloFontePadrao . " font-weight: bold; font-size: 12pt;'>" . converteEncodingTexto($usuarioDeSuporte) . "</span><br/><br/>";
	}
	$html_mensagem .= "								</div>" .
					  "							</td>" .
					  "						</tr>" .
					  "					</table>" .
					  "				</td>" .
					  "			</tr>" .
					  "		</table>" .
					  "	</center>" .
					  "</div>";
	$dadosMensagem["mensagem"] = $html_mensagem;
	return $dadosMensagem;
}

function getMensagemRespostaSuporte($nomeEmpresa, $nomeUsuario, $mensagem, $link = "") {
	/*
	===================================================================================================
	SUPORTE
	===================================================================================================
	Empresa: Flex Sianlização Modular Ltda
	Usuário: Fulaninho 
	
	Mensagem:
	
	Erro no envio de e-mails
	
	Para o esclarecimento de dúvidas, solicitação de auxílio ou envio de sugestões, sinta-se à vontade para utilizar qualquer um dos nossos canais:
	- Ferramenta de suporte disponibilizada no próprio sistema, acessível através da opção "Suporte", localizada no canto superior direito da tela
	- E-mail: suporte@tiny.com.br
	- Telefone: (54) 3055-8200
	
	Estamos à disposição para o que você precisar.
	
	Atenciosamente,
	Equipe - Tiny
	*/
	$dadosMensagem = array();
	
	$dadosMensagem["logotipo"] = criarLogoDaEmpresaAdminEmAnexo();
	
	$plano = str_replace("plano ", "", $plano);
	$plano = str_replace("Plano ", "", $plano);
	$plano = str_replace("plano", "", $plano);
	$plano = str_replace("Plano", "", $plano);
	
	$estiloFontePadrao = "font-family: Lato,Helvetica,Arial; font-size: 11pt; text-align: left; font-weight: normal;";
	
	$html_mensagem = "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>" .
					 "<div>" .
		   			 "	<center>" .
		   			 "		<table>" .
		   			 "			<tr>" .
		   			 "				<td valign='top' style='border-collapse: collapse;' align='center'>" .
		   			 "					<table width='600px' cellspacing='0' cellpadding='0' border='0' style='border:1px solid #dddddd; padding: 20px;' align='center'>" .
		   			 "						<tr>" .
		   			 "							<td valign='top' style='border-collapse:collapse'>" .
		   			 "								<div><img src='cid:logotipo'></div>" .
		   			 "								<div style='color: #444; " . $estiloFontePadrao . "'><br/>" .
		   			 "									<span style='" . $estiloFontePadrao . "'>Empresa: &nbsp;</span><span style='" . $estiloFontePadrao . " font-weight: bold; font-size: 12pt;'>" . converteEncodingTexto($nomeEmpresa) . "</span><br/>" .
		   			 "									<span style='" . $estiloFontePadrao . "'>" . converteEncodingTexto("Usuário"). ": &nbsp;</span><span style='" . $estiloFontePadrao . " font-weight: bold; font-size: 12pt;'>" . converteEncodingTexto($nomeUsuario) . "</span><br/><br/>" .
		   			 "									<h1 style='" . $estiloFontePadrao . " border-bottom: 1px solid #ddd; font-weight: bold;'>Mensagem</h1>" .
		   			 "									<span>" . converteEncodingTexto($mensagem) . "</span><br/><br/>" . $link .
					 "								</div>" .
					 "							</td>" .
					 "						</tr>" .
					 "					</table>" .
					 "				</td>" .
					 "			</tr>" .
					 "		</table>" .
					 "	</center>" .
					 "</div>";
	$dadosMensagem["mensagem"] = $html_mensagem;
	return $dadosMensagem;
}

function getMensagemUpgrade($nomeEmpresa, $nomePlano) {
	/*
	===================================================================================================
	UPGRADE
	===================================================================================================
	Tiny - Upgrade de plano
	
	Flex Sianlização Modular Ltda,

	Você realizou o upgrade de sua conta no Tiny para o plano Professional.
	Esperamos que você aproveite e goste dos novos recursos disponibilizados neste plano.

	Para o esclarecimento de dúvidas, solicitação de auxílio ou envio de sugestões, sinta-se à vontade para utilizar qualquer um dos nossos contatos:
	- Ferramenta de suporte disponibilizada no próprio sistema, acessível através da opção "Suporte", localizada no canto superior direito da tela
	- E-mail: suporte@tiny.com.br
	- Telefone: (54) 3055-8200

	Estamos à disposição para o que você precisar.

	Atenciosamente,
	Equipe - Tiny
	*/
	$dadosMensagem = array();
	
	$dadosMensagem["logotipo"] = criarLogoDaEmpresaAdminEmAnexo();
	
	$nomePlano = str_replace("plano ", "", $nomePlano);
	$nomePlano = str_replace("Plano ", "", $nomePlano);
	$nomePlano = str_replace("plano", "", $nomePlano);
	$nomePlano = str_replace("Plano", "", $nomePlano);
	
	$estiloFontePadrao = "font-family: Lato,Helvetica,Arial; font-size: 11pt; text-align: left; font-weight: normal;";
	
	$html_mensagem = "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>" .
					 "<div>" .
		   			 "	<center>" .
		   			 "		<table>" .
		   			 "			<tr>" .
		   			 "				<td valign='top' style='border-collapse: collapse;' align='center'>" .
		   			 "					<table width='600px' cellspacing='0' cellpadding='0' border='0' style='border:1px solid #dddddd; padding: 20px;' align='center'>" .
		   			 "						<tr>" .
		   			 "							<td valign='top' style='border-collapse:collapse'>" .
		   			 "								<div><img src='cid:logotipo'></div>" .
		   			 "								<div style='color: #444; " . $estiloFontePadrao . "'><br/>" .
		   			 "									<h1 style='font-weight: bold; font-size: 12pt;'>" . $nomeEmpresa . "<span style='" . $estiloFontePadrao . "'>,</span></h1>" .
		   			 "									<span>" . converteEncodingTexto("Você realizou o upgrade de sua conta no") . "&nbsp;" . SISTEMA . "&nbsp;para o Plano<span style='font-weight: bold; font-size: 12pt;'>&nbsp;" . converteEncodingTexto($nomePlano) . "</span>.</span><br/><br/>" .
		   			 "									<span>" . converteEncodingTexto("Você pode acessar o sistema através do endereço ") . "<a href='" . \UtilsApi\obterValorParametroNaAdmin("adm:msg:url") . "'>" . \UtilsApi\obterValorParametroNaAdmin("adm:msg:url") . "</a></span><br/><br/>" .
		   			 "									<span>" . converteEncodingTexto("Esperamos que você aproveite e goste dos novos recursos disponibilizados neste plano.") . "</span><br/><br/>" .
					 "									<span>" . converteEncodingTexto("Para o esclarecimento de dúvidas, solicitação de auxílio ou envio de sugestões, sinta-se à vontade para utilizar qualquer um dos nossos contatos:") . "</span><br/>" .
					 "									<ul><li style='line-height: 25px;'>" . converteEncodingTexto("Ferramenta de suporte disponibilizada no próprio sistema, acessível através da opção \"Suporte\", localizada no canto superior direito da tela") . "</li>" .
					 "									<li style='line-height: 25px;'>" . converteEncodingTexto("E-mail: suporte@tiny.com.br") . "</li>" .
					 "									<li style='line-height: 25px;'>" . converteEncodingTexto("Telefone: (54) 3055-8200") . "</li></ul>" .
					 "<br/>" .
					 "									<span>" . converteEncodingTexto("Estamos à disposição para o que você precisar.") . "</span><br/>" .
					 "<br/>" .
					 "									<span>" . converteEncodingTexto("Atenciosamente,") . "</span><br/>" .
					 "									<span>" . converteEncodingTexto("Equipe - " . SISTEMA) . "</span>" .
					 "								</div>" .
					 "							</td>" .
					 "						</tr>" .
					 "					</table>" .
					 "				</td>" .
					 "			</tr>" .
					 "		</table>" .
					 "	</center>" .
					 "</div>";
	$dadosMensagem["mensagem"] = $html_mensagem;
	return $dadosMensagem;
}

function getMensagemAddon($nomeEmpresa, $nomeAddon, $mensagem) {
	/*
	===================================================================================================
	ADD ON
	===================================================================================================
	Tiny - Contratação de Add-on
	
	Flex Sianlização Modular Ltda,
	
	Você realizou a contratação do Add-on 'Espaço Adicional de 50 MB'.
	O boleto no valor de R$ 20,83, referente ao período 07/03/2013 a 31/03/2013, com vencimento em 12/03/2013, foi gerado e enviado para o seu e-mail.
	
	Para o esclarecimento de dúvidas, solicitação de auxílio ou envio de sugestões, sinta-se à vontade para utilizar qualquer um dos nossos contatos:
	- Ferramenta de suporte disponibilizada no próprio sistema, acessível através da opção "Suporte", localizada no canto superior direito da tela
	- E-mail: suporte@tiny.com.br
	- Telefone: (54) 3055-8200
	
	Estamos à disposição para o que você precisar.
	
	Atenciosamente,
	Equipe - Tiny
	*/
	$dadosMensagem = array();
	
	$dadosMensagem["logotipo"] = criarLogoDaEmpresaAdminEmAnexo();
	
	$estiloFontePadrao = "font-family: Lato,Helvetica,Arial; font-size: 11pt; text-align: left; font-weight: normal;";
	
	$html_mensagem = "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>" .
					 "<div>" .
		   			 "	<center>" .
		   			 "		<table>" .
		   			 "			<tr>" .
		   			 "				<td valign='top' style='border-collapse: collapse;' align='center'>" .
		   			 "					<table width='600px' cellspacing='0' cellpadding='0' border='0' style='border:1px solid #dddddd; padding: 20px;' align='center'>" .
		   			 "						<tr>" .
		   			 "							<td valign='top' style='border-collapse:collapse'>" .
		   			 "								<div><img src='cid:logotipo'></div>" .
		   			 "								<div style='color: #444; " . $estiloFontePadrao . "'><br/>" .
		   			 "									<h1 style='font-weight: bold; font-size: 12pt;'>" . converteEncodingTexto($nomeEmpresa) . "<span style='" . $estiloFontePadrao . "'>,</span></h1>" .
		   			 "									<span>" . converteEncodingTexto("Você realizou a contratação do Add-on ") . "&nbsp;'<span style='font-weight: bold; font-size: 12pt;'>" . converteEncodingTexto($nomeAddon) . "</span>'&nbsp;para o plano.</span><br/><br/>";
		   			 
		   			 if ($mensagem!=""){
		   			 	$html_mensagem .= "									<span>" . converteEncodingTexto($mensagem) . "</span><br/><br/>";
		   			 } 
	$html_mensagem .="									<span>" . converteEncodingTexto("Para o esclarecimento de dúvidas, solicitação de auxílio ou envio de sugestões, sinta-se à vontade para utilizar qualquer um dos nossos contatos:") . "</span><br/>" .
					 "									<ul><li style='line-height: 25px;'>" . converteEncodingTexto("Ferramenta de suporte disponibilizada no próprio sistema, acessível através da opção \"Suporte\", localizada no canto superior direito da tela") . "</li>" .
					 "									<li style='line-height: 25px;'>" . converteEncodingTexto("E-mail: suporte@tiny.com.br") . "</li>" .
					 "									<li style='line-height: 25px;'>" . converteEncodingTexto("Telefone: (54) 3055-8200") . "</li></ul>" .
					 "<br/>" .
					 "									<span>" . converteEncodingTexto("Estamos à disposição para o que você precisar.") . "</span><br/>" .
					 "<br/>" .
					 "									<span>" . converteEncodingTexto("Atenciosamente,") . "</span><br/>" .
					 "									<span>" . converteEncodingTexto("Equipe - " . SISTEMA) . "</span>" .
					 "								</div>" .
					 "							</td>" .
					 "						</tr>" .
					 "					</table>" .
					 "				</td>" .
					 "			</tr>" .
					 "		</table>" .
					 "	</center>" .
					 "</div>";
	$dadosMensagem["mensagem"] = $html_mensagem;
	return $dadosMensagem;
}

function getMensagemAtivacao($nomeEmpresa, $nomePlano, $mensagem) {
	/*
	===================================================================================================
	Ativação
	===================================================================================================
	Tiny - Contratação de Add-on
	
	Flex Sianlização Modular Ltda,
	
	Você ativou sua conta no Tiny no Plano Professional.
	
	Para o esclarecimento de dúvidas, solicitação de auxílio ou envio de sugestões, sinta-se à vontade para utilizar qualquer um dos nossos contatos:
	- Ferramenta de suporte disponibilizada no próprio sistema, acessível através da opção "Suporte", localizada no canto superior direito da tela
	- E-mail: suporte@tiny.com.br
	- Telefone: (54) 3055-8200
	
	Estamos à disposição para o que você precisar.
	
	Atenciosamente,
	Equipe - Tiny
	*/
	$dadosMensagem = array();
	
	$dadosMensagem["logotipo"] = criarLogoDaEmpresaAdminEmAnexo();
	
	$nomePlano = str_replace("plano ", "", $nomePlano);
	$nomePlano = str_replace("Plano ", "", $nomePlano);
	$nomePlano = str_replace("plano", "", $nomePlano);
	$nomePlano = str_replace("Plano", "", $nomePlano);
	
	$estiloFontePadrao = "font-family: Lato,Helvetica,Arial; font-size: 11pt; text-align: left; font-weight: normal;";
	
	$html_mensagem = "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>" .
					 "<div>" .
		   			 "	<center>" .
		   			 "		<table>" .
		   			 "			<tr>" .
		   			 "				<td valign='top' style='border-collapse: collapse;' align='center'>" .
		   			 "					<table width='600px' cellspacing='0' cellpadding='0' border='0' style='border:1px solid #dddddd; padding: 20px;' align='center'>" .
		   			 "						<tr>" .
		   			 "							<td valign='top' style='border-collapse:collapse'>" .
		   			 "								<div><img src='cid:logotipo'></div>" .
		   			 "								<div style='color: #444; " . $estiloFontePadrao . "'><br/>" .
		   			 "									<h1 style='font-weight: bold; font-size: 12pt;'>" . converteEncodingTexto($nomeEmpresa) . "<span style='" . $estiloFontePadrao . "'>,</span></h1>" .
		   			 "									<span>" . converteEncodingTexto("Você ativou sua conta no " . SISTEMA) . " no Plano &nbsp;'<span style='font-weight: bold; font-size: 12pt;'>" . converteEncodingTexto($nomePlano) . "</span>'&nbsp;.</span><br/><br/>" .
		   			 "									<span>" . converteEncodingTexto($mensagem) . "</span><br/><br/>" .
		   			 "									<span>" . converteEncodingTexto("Para o esclarecimento de dúvidas, solicitação de auxílio ou envio de sugestões, sinta-se à vontade para utilizar qualquer um dos nossos contatos:") . "</span><br/>" .
					 "									<ul><li style='line-height: 25px;'>" . converteEncodingTexto("Ferramenta de suporte disponibilizada no próprio sistema, acessível através da opção \"Suporte\", localizada no canto superior direito da tela") . "</li>" .
					 "									<li style='line-height: 25px;'>" . converteEncodingTexto("E-mail: suporte@tiny.com.br") . "</li>" .
					 "									<li style='line-height: 25px;'>" . converteEncodingTexto("Telefone: (54) 3055-8200") . "</li></ul>" .
					 "<br/>" .
					 "									<span>" . converteEncodingTexto("Estamos à disposição para o que você precisar.") . "</span><br/>" .
					 "<br/>" .
					 "									<span>" . converteEncodingTexto("Atenciosamente,") . "</span><br/>" .
					 "									<span>" . converteEncodingTexto("Equipe - " . SISTEMA) . "</span>" .
					 "								</div>" .
					 "							</td>" .
					 "						</tr>" .
					 "					</table>" .
					 "				</td>" .
					 "			</tr>" .
					 "		</table>" .
					 "	</center>" .
					 "</div>";
	$dadosMensagem["mensagem"] = $html_mensagem;
	return $dadosMensagem;
}

function getMensagemEnvioDoc($nomeEmpresa, $mensagem) {
	/*
	===================================================================================================
	MENSALIDADE
	===================================================================================================
	Tiny - Mensalidade
	
	Olá Flex Sianlização Modular Ltda,
	
	A mensalidade referente à assinatura de sua conta no Tiny foi gerada e está disponível no link http://www.tiny.com.br/doc.view.php?id=590adc0d871df4c97c53609a1a7c2639
	
	Para o esclarecimento de dúvidas, solicitação de auxílio ou envio de sugestões, sinta-se à vontade para utilizar qualquer um dos nossos contatos:
	- Ferramenta de suporte disponibilizada no próprio sistema, acessível através da opção "Suporte", localizada no canto superior direito da tela
	- E-mail: suporte@tiny.com.br
	- Telefone: (54) 3055-8200
	
	Estamos à disposição para o que você precisar.
	
	Atenciosamente,
	Equipe - Tiny
	*/
	$dadosMensagem = array();
	
	$dadosMensagem["logotipo"] = criarLogoDaEmpresaEmAnexo();
	
	$estiloFontePadrao = "font-family: Lato,Helvetica,Arial; font-size: 11pt; text-align: left; font-weight: normal;";
	
	$html_mensagem = "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>" .
					 "<div>" .
		   			 "	<center>" .
		   			 "		<table>" .
		   			 "			<tr>" .
		   			 "				<td valign='top' style='border-collapse: collapse;' align='center'>" .
		   			 "					<table width='600px' cellspacing='0' cellpadding='0' border='0' style='border:1px solid #dddddd; padding: 20px;' align='center'>" .
		   			 "						<tr>" .
		   			 "							<td valign='top' style='border-collapse:collapse'>" .
		   			 "								<div><img src='cid:logotipo'></div>" .
		   			 "								<div style='color: #444; " . $estiloFontePadrao . "'><br/>";
	if (trim($nomeEmpresa) != "") {
		$html_mensagem .= "<h1 style='font-weight: bold; font-size: 12pt;'>" . converteEncodingTexto($nomeEmpresa) . "<span style='" . $estiloFontePadrao . "'>,</span></h1>";
	}
	$html_mensagem .= "									<span>" . converteEncodingTexto($mensagem) . "</span><br/><br/>" .
					  "								</div>" .
					  "							</td>" .
					  "						</tr>" .
					  "					</table>" .
					  "				</td>" .
					  "			</tr>" .
					  "		</table>" .
					  "	</center>" .
					  "</div>";
	$dadosMensagem["mensagem"] = $html_mensagem;
	return $dadosMensagem;
}

function getMensagemEnvioDocEmpresaAdmin($mensagem) {
	/*
	===================================================================================================
	Envio de Documentos empresa admin
	===================================================================================================
	*/
	$dadosMensagem = array();
	
	$dadosMensagem["logotipo"] = criarLogoDaEmpresaAdminEmAnexo();
	
	$estiloFontePadrao = "font-family: Lato,Helvetica,Arial; font-size: 11pt; text-align: left; font-weight: normal;";
	
	$html_mensagem = "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>" .
					 "<div>" .
		   			 "	<center>" .
		   			 "		<table>" .
		   			 "			<tr>" .
		   			 "				<td valign='top' style='border-collapse: collapse;' align='center'>" .
		   			 "					<table width='600px' cellspacing='0' cellpadding='0' border='0' style='border:1px solid #dddddd; padding: 20px;' align='center'>" .
		   			 "						<tr>" .
		   			 "							<td valign='top' style='border-collapse:collapse'>" .
		   			 "								<div><img src='cid:logotipo'></div>" .
		   			 "								<div style='color: #444; " . $estiloFontePadrao . "'><br/>" .
		   			 "									<span>" . converteEncodingTexto($mensagem) . "</span><br/><br/>" .
					 "								</div>" .
					 "							</td>" .
					 "						</tr>" .
					 "					</table>" .
					 "				</td>" .
					 "			</tr>" .
					 "		</table>" .
					 "	</center>" .
					 "</div>";
	$dadosMensagem["mensagem"] = $html_mensagem;
	return $dadosMensagem;
}

function getMensagemMensalidade($nomeEmpresa, $link) {
	/*
	===================================================================================================
	MENSALIDADE
	===================================================================================================
	Tiny - Mensalidade
	
	Olá Flex Sianlização Modular Ltda,
	
	A mensalidade referente à assinatura de sua conta no Tiny foi gerada e está disponível no link http://www.tiny.com.br/doc.view.php?id=590adc0d871df4c97c53609a1a7c2639
	
	Para o esclarecimento de dúvidas, solicitação de auxílio ou envio de sugestões, sinta-se à vontade para utilizar qualquer um dos nossos contatos:
	- Ferramenta de suporte disponibilizada no próprio sistema, acessível através da opção "Suporte", localizada no canto superior direito da tela
	- E-mail: suporte@tiny.com.br
	- Telefone: (54) 3055-8200
	
	Estamos à disposição para o que você precisar.
	
	Atenciosamente,
	Equipe - Tiny
	*/
	$dadosMensagem = array();
	
	$dadosMensagem["logotipo"] = criarLogoDaEmpresaEmAnexo();
	
	$estiloFontePadrao = "font-family: Lato,Helvetica,Arial; font-size: 11pt; text-align: left; font-weight: normal;";
	
	$html_mensagem = "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>" .
					 "<div>" .
		   			 "	<center>" .
		   			 "		<table>" .
		   			 "			<tr>" .
		   			 "				<td valign='top' style='border-collapse: collapse;' align='center'>" .
		   			 "					<table width='600px' cellspacing='0' cellpadding='0' border='0' style='border:1px solid #dddddd; padding: 20px;' align='center'>" .
		   			 "						<tr>" .
		   			 "							<td valign='top' style='border-collapse:collapse'>" .
		   			 "								<div><img src='cid:logotipo'></div>" .
		   			 "								<div style='color: #444; " . $estiloFontePadrao . "'><br/>" .
		   			 "									<h1 style='font-weight: bold; font-size: 12pt;'>" . converteEncodingTexto($nomeEmpresa) . "<span style='" . $estiloFontePadrao . "'>,</span></h1>" .
		   			 "									<span>" . converteEncodingTexto("A mensalidade referente à assinatura de sua conta no " . SISTEMA . " foi gerada e está disponível no link ") . "&nbsp;<a href='" . $link . "'>" . $link . "</a></span><br/><br/>" .
		   			 "									<span>" . converteEncodingTexto("Para o esclarecimento de dúvidas, solicitação de auxílio ou envio de sugestões, sinta-se à vontade para utilizar qualquer um dos nossos contatos:") . "</span><br/>" .
					 "									<ul><li style='line-height: 25px;'>" . converteEncodingTexto("Ferramenta de suporte disponibilizada no próprio sistema, acessível através da opção \"Suporte\", localizada no canto superior direito da tela") . "</li>" .
					 "									<li style='line-height: 25px;'>" . converteEncodingTexto("E-mail: suporte@tiny.com.br") . "</li>" .
					 "									<li style='line-height: 25px;'>" . converteEncodingTexto("Telefone: (54) 3055-8200") . "</li></ul>" .
					 "<br/>" .
					 "									<span>" . converteEncodingTexto("Estamos à disposição para o que você precisar.") . "</span><br/>" .
					 "<br/>" .
					 "									<span>" . converteEncodingTexto("Atenciosamente,") . "</span><br/>" .
					 "									<span>" . converteEncodingTexto("Equipe - " . SISTEMA) . "</span>" .
					 "								</div>" .
					 "							</td>" .
					 "						</tr>" .
					 "					</table>" .
					 "				</td>" .
					 "			</tr>" .
					 "		</table>" .
					 "	</center>" .
					 "</div>";
	$dadosMensagem["mensagem"] = $html_mensagem;
	return $dadosMensagem;
}


function getMensagemBackup($nomeEmpresa, $data, $link) {
	/*
	===================================================================================================
	BACKUP
	===================================================================================================
	Tiny - Backup Agendado
	
	Flex Sianlização Modular Ltda,
	
	O backup que você agendou para a dia 07/03/2013 foi realizado com sucesso, a seguir você encontrará o link para efetuar o download. 
	
	<a href=''>Clique aqui para efetuar o download</a>
	
	Informamos que este arquivo de backup ficará armazenado em nossos servidor pelo período de 24 horas.
	
	Para o esclarecimento de dúvidas, solicitação de auxílio ou envio de sugestões, sinta-se à vontade para utilizar qualquer um dos nossos contatos:
	- Ferramenta de suporte disponibilizada no próprio sistema, acessível através da opção "Suporte", localizada no canto superior direito da tela
	- E-mail: suporte@tiny.com.br
	- Telefone: (54) 3055-8200
	
	Estamos à disposição para o que você precisar.
	
	Atenciosamente,
	Equipe - Tiny
	*/
	$dadosMensagem = array();
	
	$dadosMensagem["logotipo"] = criarLogoDaEmpresaAdminEmAnexo();
	
	$estiloFontePadrao = "font-family: Lato,Helvetica,Arial; font-size: 11pt; text-align: left; font-weight: normal;";
	
	$html_mensagem = "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>" .
					 "<div>" .
		   			 "	<center>" .
		   			 "		<table>" .
		   			 "			<tr>" .
		   			 "				<td valign='top' style='border-collapse: collapse;' align='center'>" .
		   			 "					<table width='600px' cellspacing='0' cellpadding='0' border='0' style='border:1px solid #dddddd; padding: 20px;' align='center'>" .
		   			 "						<tr>" .
		   			 "							<td valign='top' style='border-collapse:collapse'>" .
		   			 "								<div><img src='cid:logotipo'></div>" .
		   			 "								<div style='color: #444; " . $estiloFontePadrao . "'><br/>" .
		   			 "									<h1 style='font-weight: bold; font-size: 12pt;'>" . converteEncodingTexto($nomeEmpresa) . "<span style='" . $estiloFontePadrao . "'>,</span></h1>" .
		   			 "									<span>" . converteEncodingTexto("O backup que você agendou para a dia") . "&nbsp;" . converteEncodingTexto($data) . "&nbsp;foi realizado com sucesso, a seguir você encontrará o link para efetuar o download.</span><br/><br/>" .
		   			 "									<span>" . converteEncodingTexto("<a href='".$link."'>Clique aqui para efetuar o download</a>") . "</span><br/><br/>" .
		   			 "									<span>" . converteEncodingTexto("Informamos que este arquivo de backup ficará armazenado em nossos servidor pelo período de 24 horas.") . "</span><br/><br/>" .
		   			 "									<span>" . converteEncodingTexto("Para o esclarecimento de dúvidas, solicitação de auxílio ou envio de sugestões, sinta-se à vontade para utilizar qualquer um dos nossos contatos:") . "</span><br/>" .
					 "									<ul><li style='line-height: 25px;'>" . converteEncodingTexto("Ferramenta de suporte disponibilizada no próprio sistema, acessível através da opção \"Suporte\", localizada no canto superior direito da tela") . "</li>" .
					 "									<li style='line-height: 25px;'>" . converteEncodingTexto("E-mail: suporte@tiny.com.br") . "</li>" .
					 "									<li style='line-height: 25px;'>" . converteEncodingTexto("Telefone: (54) 3055-8200") . "</li></ul>" .
					 "<br/>" .
					 "									<span>" . converteEncodingTexto("Estamos à disposição para o que você precisar.") . "</span><br/>" .
					 "<br/>" .
					 "									<span>" . converteEncodingTexto("Atenciosamente,") . "</span><br/>" .
					 "									<span>" . converteEncodingTexto("Equipe - " . SISTEMA) . "</span>" .
					 "								</div>" .
					 "							</td>" .
					 "						</tr>" .
					 "					</table>" .
					 "				</td>" .
					 "			</tr>" .
					 "		</table>" .
					 "	</center>" .
					 "</div>";
	$dadosMensagem["mensagem"] = $html_mensagem;
	return $dadosMensagem;
}

function getMensagemExclusao($nomeEmpresa, $data, $textoExclusao) {
	/*
	===================================================================================================
	BACKUP
	===================================================================================================
	Tiny - Tiny - Exclusão de registros agendada
	
	Cliente X,
	
	A exclusão de registros que você agendou para a dia 07/03/2013 foi realizado com sucesso, a seguir você encontrará os detalhes sobre os registros excluídos. 
	
	- Exclusão de x notas fiscais anteriores a dd/mm/yyyy;		
	- Exclusão de x contas a receber e y registros de recebimentos anteriores a dd/mm/yyyy;
	- Exclusão de x contas a pagar e y registros de pagamentos anteriores a dd/mm/yyyy;
	
	
	Para o esclarecimento de dúvidas, solicitação de auxílio ou envio de sugestões, sinta-se à vontade para utilizar qualquer um dos nossos contatos:
	- Ferramenta de suporte disponibilizada no próprio sistema, acessível através da opção "Suporte", localizada no canto superior direito da tela
	- E-mail: suporte@tiny.com.br
	- Telefone: (54) 3055-8200
	
	Estamos à disposição para o que você precisar.
	
	Atenciosamente,
	Equipe - Tiny
	*/
	$dadosMensagem = array();
	
	$dadosMensagem["logotipo"] = criarLogoDaEmpresaAdminEmAnexo();
	
	$estiloFontePadrao = "font-family: Lato,Helvetica,Arial; font-size: 11pt; text-align: left; font-weight: normal;";
	
	$html_mensagem = "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>" .
					 "<div>" .
		   			 "	<center>" .
		   			 "		<table>" .
		   			 "			<tr>" .
		   			 "				<td valign='top' style='border-collapse: collapse;' align='center'>" .
		   			 "					<table width='600px' cellspacing='0' cellpadding='0' border='0' style='border:1px solid #dddddd; padding: 20px;' align='center'>" .
		   			 "						<tr>" .
		   			 "							<td valign='top' style='border-collapse:collapse'>" .
		   			 "								<div><img src='cid:logotipo'></div>" .
		   			 "								<div style='color: #444; " . $estiloFontePadrao . "'><br/>" .
		   			 "									<h1 style='font-weight: bold; font-size: 12pt;'>" . converteEncodingTexto($nomeEmpresa) . "<span style='" . $estiloFontePadrao . "'>,</span></h1>" .
		   			 "									<span>" . converteEncodingTexto("A exclusão de registros que você agendou para a dia") . "&nbsp;" . converteEncodingTexto($data) . "&nbsp;foi realizada com sucesso. A seguir você encontrará os detalhes sobre os registros excluídos.</span><br/><br/>" .
		   			 "									<span>" . converteEncodingTexto($textoExclusao) . "</span><br/><br/>" .
		   			 "									<span>" . converteEncodingTexto("Para o esclarecimento de dúvidas, solicitação de auxílio ou envio de sugestões, sinta-se à vontade para utilizar qualquer um dos nossos contatos:") . "</span><br/>" .
					 "									<ul><li style='line-height: 25px;'>" . converteEncodingTexto("Ferramenta de suporte disponibilizada no próprio sistema, acessível através da opção \"Suporte\", localizada no canto superior direito da tela") . "</li>" .
					 "									<li style='line-height: 25px;'>" . converteEncodingTexto("E-mail: suporte@tiny.com.br") . "</li>" .
					 "									<li style='line-height: 25px;'>" . converteEncodingTexto("Telefone: (54) 3055-8200") . "</li></ul>" .
					 "<br/>" .
					 "									<span>" . converteEncodingTexto("Estamos à disposição para o que você precisar.") . "</span><br/>" .
					 "<br/>" .
					 "									<span>" . converteEncodingTexto("Atenciosamente,") . "</span><br/>" .
					 "									<span>" . converteEncodingTexto("Equipe - " . SISTEMA) . "</span>" .
					 "								</div>" .
					 "							</td>" .
					 "						</tr>" .
					 "					</table>" .
					 "				</td>" .
					 "			</tr>" .
					 "		</table>" .
					 "	</center>" .
					 "</div>";
	$dadosMensagem["mensagem"] = $html_mensagem;
	return $dadosMensagem;
}

function getMensagemExclusaoNaoEfetuada($nomeEmpresa, $data) {
	/*
	===================================================================================================
	BACKUP
	===================================================================================================
	Tiny - Tiny - Exclusão de registros agendada
	
	Cliente X,
	
	A exclusão de registros que você agendou para a dia 07/03/2013 não pode ser realizada, pois o seu backup agendado para a mesma data não foi realizado. 
	O sistema reagendou esta tarefa automaticamente para o próximo dia.
	
	Para o esclarecimento de dúvidas, solicitação de auxílio ou envio de sugestões, sinta-se à vontade para utilizar qualquer um dos nossos contatos:
	- Ferramenta de suporte disponibilizada no próprio sistema, acessível através da opção "Suporte", localizada no canto superior direito da tela
	- E-mail: suporte@tiny.com.br
	- Telefone: (54) 3055-8200
	
	Estamos à disposição para o que você precisar.
	
	Atenciosamente,
	Equipe - Tiny
	*/
	$dadosMensagem = array();
	
	$dadosMensagem["logotipo"] = criarLogoDaEmpresaAdminEmAnexo();
	
	$estiloFontePadrao = "font-family: Lato,Helvetica,Arial; font-size: 11pt; text-align: left; font-weight: normal;";
	
	$html_mensagem = "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>" .
					 "<div>" .
		   			 "	<center>" .
		   			 "		<table>" .
		   			 "			<tr>" .
		   			 "				<td valign='top' style='border-collapse: collapse;' align='center'>" .
		   			 "					<table width='600px' cellspacing='0' cellpadding='0' border='0' style='border:1px solid #dddddd; padding: 20px;' align='center'>" .
		   			 "						<tr>" .
		   			 "							<td valign='top' style='border-collapse:collapse'>" .
		   			 "								<div><img src='cid:logotipo'></div>" .
		   			 "								<div style='color: #444; " . $estiloFontePadrao . "'><br/>" .
		   			 "									<h1 style='font-weight: bold; font-size: 12pt;'>" . converteEncodingTexto($nomeEmpresa) . "<span style='" . $estiloFontePadrao . "'>,</span></h1>" .
		   			 "									<span>" . converteEncodingTexto("A exclusão de registros que você agendou para a dia") . "&nbsp;" . converteEncodingTexto($data) . "&nbsp;não pode ser realizada, " .
		   			 		"							pois o seu backup agendado para a mesma data não foi realizado. O sistema reagendou esta tarefa automaticamente para o próximo dia.</span><br/><br/>" .
		   			 "									<span>" . converteEncodingTexto("Para o esclarecimento de dúvidas, solicitação de auxílio ou envio de sugestões, sinta-se à vontade para utilizar qualquer um dos nossos contatos:") . "</span><br/>" .
					 "									<ul><li style='line-height: 25px;'>" . converteEncodingTexto("Ferramenta de suporte disponibilizada no próprio sistema, acessível através da opção \"Suporte\", localizada no canto superior direito da tela") . "</li>" .
					 "									<li style='line-height: 25px;'>" . converteEncodingTexto("E-mail: suporte@tiny.com.br") . "</li>" .
					 "									<li style='line-height: 25px;'>" . converteEncodingTexto("Telefone: (54) 3055-8200") . "</li></ul>" .
					 "<br/>" .
					 "									<span>" . converteEncodingTexto("Estamos à disposição para o que você precisar.") . "</span><br/>" .
					 "<br/>" .
					 "									<span>" . converteEncodingTexto("Atenciosamente,") . "</span><br/>" .
					 "									<span>" . converteEncodingTexto("Equipe - " . SISTEMA) . "</span>" .
					 "								</div>" .
					 "							</td>" .
					 "						</tr>" .
					 "					</table>" .
					 "				</td>" .
					 "			</tr>" .
					 "		</table>" .
					 "	</center>" .
					 "</div>";
	$dadosMensagem["mensagem"] = $html_mensagem;
	return $dadosMensagem;
}


function getMensagemIndicacao($nomeEmpresa, $link, $textoEnviado) {
	/*
	===================================================================================================
	Indicação
	===================================================================================================
	Flex Sianlização Modular Ltda convidou você para testar o Tiny. 
	
	O TinyERP é um sistema de gestão de recursos empresariais que auxilia na solução dos principais problemas de gestão em uma pequena empresa. 
	
	Conheça o Tiny.
	
	Estamos à disposição para o que você precisar.
	
	Atenciosamente,
	Equipe - Tiny
	*/
	$dadosMensagem = array();
	
	$dadosMensagem["logotipo"] = criarLogoDaEmpresaAdminEmAnexo();
	
	$estiloFontePadrao = "font-family: Lato,Helvetica,Arial; font-size: 11pt; text-align: left; font-weight: normal;";
	
	$estilo = "display: inline-block; *display: inline; padding: 4px 12px; margin-bottom: 0; *margin-left: .3em; font-size: 14px; line-height: 20px; color: #333333;  text-align: center; text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75); vertical-align: middle; cursor: pointer;  background-color: #f5f5f5; *background-color: #e6e6e6; background-image: -moz-linear-gradient(top, #ffffff, #e6e6e6); background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), to(#e6e6e6)); background-image: -webkit-linear-gradient(top, #ffffff, #e6e6e6); background-image: -o-linear-gradient(top, #ffffff, #e6e6e6); background-image: linear-gradient(to bottom, #ffffff, #e6e6e6); background-repeat: repeat-x; border: 1px solid #bbbbbb; *border: 0; border-color: #e6e6e6 #e6e6e6 #bfbfbf; border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25); border-bottom-color: #a2a2a2; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffffff', endColorstr='#ffe6e6e6', GradientType=0); filter: progid:DXImageTransform.Microsoft.gradient(enabled=false); *zoom: 1; -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05); -moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05); box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05); color: #ffffff; text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25); background-color: #006dcc; *background-color: #0044cc; background-image: -moz-linear-gradient(top, #0088cc, #0044cc); background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#0088cc), to(#0044cc)); background-image: -webkit-linear-gradient(top, #0088cc, #0044cc); background-image: -o-linear-gradient(top, #0088cc, #0044cc); background-image: linear-gradient(to bottom, #0088cc, #0044cc); background-repeat: repeat-x; border-color: #0044cc #0044cc #002a80; border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25); filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff0088cc', endColorstr='#ff0044cc', GradientType=0); filter: progid:DXImageTransform.Microsoft.gradient(enabled=false); padding: 11px 19px; font-size: 17.5px;  -webkit-border-radius: 6px; -moz-border-radius: 6px; border-radius: 6px;";	
	
	/*
	$html_mensagem = "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>" .
					 "<div>" .
		   			 "	<center>" .
		   			 "		<table>" .
		   			 "			<tr>" .
		   			 "				<td valign='top' style='border-collapse: collapse;' align='center'>" .
		   			 "					<table width='600px' cellspacing='0' cellpadding='0' border='0' style='border:1px solid #dddddd; padding: 20px;' align='center'>" .
		   			 "						<tr>" .
		   			 "							<td valign='top' style='border-collapse:collapse'>" .
		   			 "								<div><img src='cid:logotipo'></div>" .
		   			 "								<div style='color: #444; " . $estiloFontePadrao . "'><br/>" .
		   			 "									<h1 style='font-weight: bold; font-size: 12pt;'>" . $nomeEmpresa . "<span style='" . $estiloFontePadrao . "'> convidou você para experimentar o " . SISTEMA . "&nbsp;</span><span style='" . $estiloFontePadrao . "'>.</span></h1>" .
		   			 "									<span>" . converteEncodingTexto("O TinyERP é um sistema de gestão de recursos empresariais que auxilia na solução dos principais problemas de gestão em uma pequena empresas.") . "</span><br/>" .
					 "									<br/><span>" . converteEncodingTexto("Você tem até 30 dias para utilizar o sistema em qualquer um dos nossos planos pagos, sem nenhum custo. Este é o período de demonstração, durante o qual você pode experimentar todos os recursos do plano escolhido. Ao término desse período, você pode optar pela contratação ou voltar ao plano grátis.") . "</span><br/>" .
					 "									<br/><a href='" . $link . "' style='display: inline-block; *display: inline; padding: 4px 12px; margin-bottom: 0; *margin-left: .3em; font-size: 14px; line-height: 20px; color: #333333;  text-align: center; text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75); vertical-align: middle; cursor: pointer;  background-color: #f5f5f5; *background-color: #e6e6e6; background-image: -moz-linear-gradient(top, #ffffff, #e6e6e6); background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), to(#e6e6e6)); background-image: -webkit-linear-gradient(top, #ffffff, #e6e6e6); background-image: -o-linear-gradient(top, #ffffff, #e6e6e6); background-image: linear-gradient(to bottom, #ffffff, #e6e6e6); background-repeat: repeat-x; border: 1px solid #bbbbbb; *border: 0; border-color: #e6e6e6 #e6e6e6 #bfbfbf; border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25); border-bottom-color: #a2a2a2; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\"#ffffffff\", endColorstr=\"#ffe6e6e6\", GradientType=0); filter: progid:DXImageTransform.Microsoft.gradient(enabled=false); *zoom: 1; -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05); -moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05); box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05); color: #ffffff; text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25); background-color: #006dcc; *background-color: #0044cc; background-image: -moz-linear-gradient(top, #0088cc, #0044cc); background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#0088cc), to(#0044cc)); background-image: -webkit-linear-gradient(top, #0088cc, #0044cc); background-image: -o-linear-gradient(top, #0088cc, #0044cc); background-image: linear-gradient(to bottom, #0088cc, #0044cc); background-repeat: repeat-x; border-color: #0044cc #0044cc #002a80; border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25); filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\"#ff0088cc\", endColorstr=\"#ff0044cc\", GradientType=0); filter: progid:DXImageTransform.Microsoft.gradient(enabled=false); text-decoration:none'>Conheça o Tiny</a><br/>" .
					  "									<span>" . converteEncodingTexto("Atenciosamente,") . "</span><br/>" .
					  "									<span>" . converteEncodingTexto("Equipe - " . SISTEMA) . "</span>" .
					  "								</div>" .
					  "							</td>" .
					  "						</tr>" .
					  "					</table>" .
					  "				</td>" .
					  "			</tr>" .
					  "		</table>" .
					  "	</center>" .
					  "</div>";*/
					  
	$html_mensagem = "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>" .
					 "<div>" .
		   			 	"<center>" .
		   			 		"<table>" .
		   			 			"<tr>" .
		   			 				"<td valign='top' style='border-collapse: collapse;' align='center'>" .
		   			 					"<table width='600px' cellspacing='0' cellpadding='0' border='0' style='border:1px solid #dddddd; padding: 20px;' align='center'>" .
		   			 						"<tr>" .
		   			 							"<td valign='top' style='border-collapse:collapse'>" .
		   			 								"<div><img src='cid:logotipo'></div>" .
		   			 								"<div style='color: #444; " . $estiloFontePadrao . "'><br/>" .
		   			 									"<span>" . converteEncodingTexto(nl2br($textoEnviado)) . "</span><br/>" .
					 									"<br/>" .
					 									//"<a href='" . $link . "' style='display: inline-block; *display: inline; padding: 4px 12px; margin-bottom: 0; *margin-left: .3em; font-size: 14px; line-height: 20px; color: #333333;  text-align: center; text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75); vertical-align: middle; cursor: pointer;  background-color: #f5f5f5; *background-color: #e6e6e6; background-image: -moz-linear-gradient(top, #ffffff, #e6e6e6); background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), to(#e6e6e6)); background-image: -webkit-linear-gradient(top, #ffffff, #e6e6e6); background-image: -o-linear-gradient(top, #ffffff, #e6e6e6); background-image: linear-gradient(to bottom, #ffffff, #e6e6e6); background-repeat: repeat-x; border: 1px solid #bbbbbb; *border: 0; border-color: #e6e6e6 #e6e6e6 #bfbfbf; border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25); border-bottom-color: #a2a2a2; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\"#ffffffff\", endColorstr=\"#ffe6e6e6\", GradientType=0); filter: progid:DXImageTransform.Microsoft.gradient(enabled=false); *zoom: 1; -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05); -moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05); box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05); color: #ffffff; text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25); background-color: #006dcc; *background-color: #0044cc; background-image: -moz-linear-gradient(top, #0088cc, #0044cc); background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#0088cc), to(#0044cc)); background-image: -webkit-linear-gradient(top, #0088cc, #0044cc); background-image: -o-linear-gradient(top, #0088cc, #0044cc); background-image: linear-gradient(to bottom, #0088cc, #0044cc); background-repeat: repeat-x; border-color: #0044cc #0044cc #002a80; border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25); filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\"#ff0088cc\", endColorstr=\"#ff0044cc\", GradientType=0); filter: progid:DXImageTransform.Microsoft.gradient(enabled=false); text-decoration:none'>Conheça o Tiny</a><br/>" .
					 									"<a href='" . $link . "'><img src='cid:inscrever'/></a><br/>" .
					  								"</div>" .
					  							"</td>" .
					  						"</tr>" .
					  					"</table>" .
					  				"</td>" .
					  			"</tr>" .
					  		"</table>" .
					  "</center>" .
					  "</div>";
					  
	debug($textoEnviado);
	$dadosMensagem["mensagem"] = $html_mensagem;
	return $dadosMensagem;
}

function getMensagemAtivouIndicacao($nomeEmpresa) {
	/*
	===================================================================================================
	Indicação
	===================================================================================================
	Flex Sianlização Modular Ltda convidou você para testar o Tiny. 
	
	O TinyERP é um sistema de gestão de recursos empresariais que auxilia na solução dos principais problemas de gestão em uma pequena empresa. 
	
	Conheça o Tiny.
	
	Estamos à disposição para o que você precisar.
	
	Atenciosamente,
	Equipe - Tiny
	*/
	$dadosMensagem = array();
	
	$dadosMensagem["logotipo"] = criarLogoDaEmpresaAdminEmAnexo();
	
	$estiloFontePadrao = "font-family: Lato,Helvetica,Arial; font-size: 11pt; text-align: left; font-weight: normal;";
	
	$estilo = "display: inline-block; *display: inline; padding: 4px 12px; margin-bottom: 0; *margin-left: .3em; font-size: 14px; line-height: 20px; color: #333333;  text-align: center; text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75); vertical-align: middle; cursor: pointer;  background-color: #f5f5f5; *background-color: #e6e6e6; background-image: -moz-linear-gradient(top, #ffffff, #e6e6e6); background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), to(#e6e6e6)); background-image: -webkit-linear-gradient(top, #ffffff, #e6e6e6); background-image: -o-linear-gradient(top, #ffffff, #e6e6e6); background-image: linear-gradient(to bottom, #ffffff, #e6e6e6); background-repeat: repeat-x; border: 1px solid #bbbbbb; *border: 0; border-color: #e6e6e6 #e6e6e6 #bfbfbf; border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25); border-bottom-color: #a2a2a2; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffffff', endColorstr='#ffe6e6e6', GradientType=0); filter: progid:DXImageTransform.Microsoft.gradient(enabled=false); *zoom: 1; -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05); -moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05); box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05); color: #ffffff; text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25); background-color: #006dcc; *background-color: #0044cc; background-image: -moz-linear-gradient(top, #0088cc, #0044cc); background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#0088cc), to(#0044cc)); background-image: -webkit-linear-gradient(top, #0088cc, #0044cc); background-image: -o-linear-gradient(top, #0088cc, #0044cc); background-image: linear-gradient(to bottom, #0088cc, #0044cc); background-repeat: repeat-x; border-color: #0044cc #0044cc #002a80; border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25); filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff0088cc', endColorstr='#ff0044cc', GradientType=0); filter: progid:DXImageTransform.Microsoft.gradient(enabled=false); padding: 11px 19px; font-size: 17.5px;  -webkit-border-radius: 6px; -moz-border-radius: 6px; border-radius: 6px;";	
	
	
	$html_mensagem = "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>" .
					 "<div>" .
		   			 "	<center>" .
		   			 "		<table>" .
		   			 "			<tr>" .
		   			 "				<td valign='top' style='border-collapse: collapse;' align='center'>" .
		   			 "					<table width='600px' cellspacing='0' cellpadding='0' border='0' style='border:1px solid #dddddd; padding: 20px;' align='center'>" .
		   			 "						<tr>" .
		   			 "							<td valign='top' style='border-collapse:collapse'>" .
		   			 "								<div><img src='cid:logotipo'></div>" .
		   			 "								<div style='color: #444; " . $estiloFontePadrao . "'><br/>" .
		   			 "									<h1 style='font-weight: bold; font-size: 12pt;'>A empresa " . $nomeEmpresa . "<span style='" . $estiloFontePadrao . "'>, que foi indicada por você, ativou sua conta no " . SISTEMA . "&nbsp;</span><span style='" . $estiloFontePadrao . "'>.</span></h1>" .
		   			 "									<span>" . converteEncodingTexto("Sua conta no Tiny <strong>ganhou um espaço adicional de 5MB</strong> por essa indicação.") . "</span><br/>" .
					  "									<span>" . converteEncodingTexto("Atenciosamente,") . "</span><br/>" .
					  "									<span>" . converteEncodingTexto("Equipe - " . SISTEMA) . "</span>" .
					  "								</div>" .
					  "							</td>" .
					  "						</tr>" .
					  "					</table>" .
					  "				</td>" .
					  "			</tr>" .
					  "		</table>" .
					  "	</center>" .
					  "</div>";
	debug($html_mensagem);
	$dadosMensagem["mensagem"] = $html_mensagem;
	return $dadosMensagem;
}

function arrayToXml($array, $header = true, $domElement = null, $DOMDocument = null){
	
	if  (is_null($DOMDocument))  { //Cria o objeto
		$DOMDocument = new DOMDocument("1.0", "UTF-8");
		$DOMDocument->formatOutput = false;
		$xml = arrayToXml($array,$header,$DOMDocument,$DOMDocument);
		//Retira a declaração do header do XML $header = 'false'
		return ($header)? $DOMDocument->saveXML() : $DOMDocument->saveXML($DOMDocument->documentElement);
		 
	}  else  { // Popula
		foreach ($array as $idx => $valor){
			if (is_array($valor)){
				if (count($valor)==0){
					return;
				}
				$isNode = false;
				if (is_int($idx))  {
					$node = $domElement;
				}else{
					$node = $DOMDocument->createElement($idx);
					$domElement->appendChild($node);					
					$isNode = true;
				}					
				arrayToXml($valor,$header,$node,$DOMDocument);
			}else{
				$node = $DOMDocument->createElement($idx);
  				$node->appendChild($DOMDocument->createTextNode($valor));  	
  				$domElement->appendChild($node);			
			}			
		}
	}
}

function removerTagsHtmlTexto($texto){
	$texto = str_replace("<b>","",$texto);
	$texto = str_replace("</b>","",$texto);
	$texto = str_replace("<br>","",$texto);
	$texto = str_replace("<br/>","",$texto);
	$texto = str_replace("</br>","",$texto);
	
	return $texto; 
}

function converterOFXParaXML($conteudo) {
    if (! $comecaOfx = strpos ($conteudo, "<OFX>")) throw new exception ("Tag OFX não encontrada.");
    $cabecalho = substr ($conteudo, 0, $comecaOfx); // Não precisa de excessão já que $comecaOfx passou na função strpos    
    $cabecalhoAnt = $cabecalho;
    
   	$linhas = explode ("\n", $cabecalho); // Não precisa de excessão, sempre vai retornar um array, vazio ou não
   	
    $cabecalho = ""; $i = 0;

    foreach ($linhas as $linha) {
            $separados = explode (":", $linha);
    
            if (count ($separados) === 2) {
                    if ($separados[1] !== "") {
                            $separados[0] = trim ($separados[0]);
                            
                            if ($separados[0] === "OFXHEADER" || $separados[0] === "VERSION" || $separados[0] === "SECURITY" || $separados[0] === "OLDFILEUID" || $separados[0] === "NEWFILEUID") {
                                    if ($separados[0] === "OFXHEADER") {
                                            $separados[1] = "200";
                                    } else if ($separados[0] === "VERSION") {
                                            $separados[1] = "211";
                                    }
                            
                                    $cabecalho .= $separados[0] . " = \"" . strtoupper ($separados[1]) . "\" ";
                            
                                    $i ++;
                            }
                    }
            }
    }

    if ($i !== 5) {
	   	$linhas = explode ("\r", $cabecalhoAnt); // Não precisa de excessão, sempre vai retornar um array, vazio ou não
	    
	    $cabecalho = ""; 
	    $i = 0;
	
	    foreach ($linhas as $linha) {
	            $separados = explode (":", $linha);
	    
	            if (count ($separados) === 2) {
	                    if ($separados[1] !== "") {
	                            $separados[0] = trim ($separados[0]);
	                            
	                            if ($separados[0] === "OFXHEADER" || $separados[0] === "VERSION" || $separados[0] === "SECURITY" || $separados[0] === "OLDFILEUID" || $separados[0] === "NEWFILEUID") {
	                                    if ($separados[0] === "OFXHEADER") {
	                                            $separados[1] = "200";
	                                    } else if ($separados[0] === "VERSION") {
	                                            $separados[1] = "211";
	                                    }
	                            
	                                    $cabecalho .= $separados[0] . " = \"" . strtoupper ($separados[1]) . "\" ";
	                            
	                                    $i ++;
	                            }
	                    }
	            }
	    }	
    }
    
    if ($i !== 5) throw new exception ("Sgml possui cabeçalho inválido.");
    
    $cabecalho = "<?OFX " . $cabecalho . "?>";
    
    // Corpo
    $corpo = substr ($conteudo, $comecaOfx); // Não precisa de excessão já que $comecaOfx passou na função strpos
    
    $corpo = str_replace("</CODE>", "", $corpo);
    $corpo = str_replace("</SEVERITY>", "", $corpo);
    
    if (! preg_match_all ("/\<\/[^\>]+\>/", $corpo, $fechaTags)) throw new exception;
    
    $fechaTags = array_fill_keys ($fechaTags[0], null);

    $linhas = explode ("\n", $corpo);

    $corpo = "";
    
    foreach ($linhas as $linha) {
            $corpo .= trim ($linha);
            
            if (preg_match_all ("/\<[^\/\>]+\>/", $linha, $comecaTags)) {
                    $comecaTags[0] = array_reverse ($comecaTags[0]);
                    
                    foreach ($comecaTags[0] as $comecaTag) {
                            $fechaTag = "</" . substr ($comecaTag, 1);
                            
                            if (! array_key_exists ($fechaTag, $fechaTags)) {
                                    $corpo .= $fechaTag;
                            }
                    }
            }
    }
    // É impossível pelos diversos tratamentos de erros acima que a variável $corpo seja vazia
    $corpo = str_replace("&", "&amp;", $corpo);
    return $cabecalho . $corpo;
}

function formatarNumeroPreVenda($numero, $decimais) {
	$numero = numeroBrz($numero, $decimais);
	$numero = str_replace(".", "", $numero);
	$numero = str_replace(",", ".", $numero);
	return $numero;
}

function acessoRestritoVerificaPermicaoDiaHora() {
	return verificaPermissaoUsuarioDiaHora($_SESSION["user"]);
}


function verificaPermissaoUsuarioDiaHora($usuario) {
	/*
	 * Dias da semana (0-Domingo, ..., 6-Sábado)
	 * Formatos de hora (00:00)
	 */

	if ($usuario["restricao_horario"] == "S") {
		$diaDaSemana = date("w");
		$pos = strpos($usuario["dias"], $diaDaSemana);
		if ($pos === false) {
			return 2;
		} else {
			return acessoRestritoVerificaPermicaoHora($diaDaSemana, $usuario);
		}
	} else {
		return 0;
	}

	/*
	 * 0 - Acesso liberado
	 * 1 - Acesso liberado (igual ou menor a 10 minutos)
	 * 2 - Acesso não liberado
	 */
}

function acessoRestritoVerificaPermicaoHora($diaDaSemana, $usuario) {
	$arrayHorasMinutos = array();
	
	switch ($diaDaSemana) {
		case "1":
		case "2":
		case "3":
		case "4":
		case "5":
			//De segunda a sexta
			$arrayHorasMinutos[] = array("hIni" => $usuario["horarioM_ini"], "hFim" => $usuario["horarioM_fim"]);
			$arrayHorasMinutos[] = array("hIni" => $usuario["horarioT_ini"], "hFim" => $usuario["horarioT_fim"]);
			$arrayHorasMinutos[] = array("hIni" => $usuario["horarioN_ini"], "hFim" => $usuario["horarioN_fim"]);
			break;
		case "6":
			//Sábado
			$arrayHorasMinutos[] = array("hIni" => $usuario["horarioS_ini"], "hFim" => $usuario["horarioS_fim"]);
			break;
		case "0":
			//Domingo
			$arrayHorasMinutos[] = array("hIni" => $usuario["horarioD_ini"], "hFim" => $usuario["horarioD_fim"]);
			break;
	}
	
	$hAtual = (date("H") * 60) + date("i");
	foreach ($arrayHorasMinutos as $item) {
		$aux = explode(":", $item["hIni"]);
		$hIni = ($aux[0] * 60) + $aux[1];
		
		$aux = explode(":", $item["hFim"]);
		$hFim = ($aux[0] * 60) + $aux[1];
		
		if (($hAtual >= $hIni) && ($hAtual <= $hFim)) {
			
			$msgSistemaAvisoLimiteHorario = "<strong>Aviso!</strong> O seu acesso está liberado somente até às " . $item["hFim"] . ". Recomendamos que você finalize as operações que estão em andamento.";
			$_SESSION["mensagemSistemaLimiteAcesso"] = $msgSistemaAvisoLimiteHorario;
			
			if (($hAtual + 10) >= $hFim) {
				return 1;
			} else {
				return 0;
			}
		}
	}
	
	return 2;
}

function removerEspacosEPontosDoNomeDoArquivo($nome) {
	// remove espaços
	$nome = str_replace(" ", "_", $nome);

	// remove pontos, mantendo o último ponto (da extenção)
	$tmp = explode(".", $nome);
	$nome = "." . array_pop($tmp);
	$nome = implode("_", $tmp) . $nome;

	return $nome;
}

function setAttrEventosItem(&$objResponse, $attrs) {
	$objResponse->script("removerAtributos($('#attrEventosItem > div'));");

	$script = "$('#attrEventosItem > div')";
	foreach ($attrs as $name => $value) {
		$script .= ".attr('" . $name . "', '" . addslashes($value) . "')";
	}
	$objResponse->script($script . ";");

	$objResponse->script("$(document).trigger('refreshAcoes');");
}

function runCallback(&$objResponse, $callback, $args = null) {
	$parameters = getStringParameters($args);
	$objResponse->script("if ($.isFunction(" . $callback . ")) { " . $callback . "(" . $parameters . "); }");
}

function getStringParameters($args) {
	$parameters = "";
	if (is_array($args) && count($args) > 0) {
		foreach ($args as $arg) {
			if (is_array($arg) || is_object($arg)) {
				// formata para um array ou objeto json
				if (count($arg) > 0) {
					$parameters .= json_encode($arg);
				} else {
					$parameters .= (is_array($arg)) ? "[]" : "{}" ;
				}
			} else if (is_string($arg)) {
				// escapa a string
				$parameters .= "'" . str_replace(PHP_EOL, "\\n", addslashes($arg)) . "'";
			} else if (is_bool($arg)) {
				$parameters .= $arg ? "true" : "false";
			} else if (is_null($arg)) {
				$parameters .= "null";
			} else {
				$parameters .= $arg;
			}
			$parameters .= ", ";
		}
		$parameters = substr($parameters, 0, -2);
	}
	return $parameters;
}

function setarParametroGeral($parametro, $valor, $idEmpresa = 0) {
	$dados = ParameterEngine :: getDadosParameter($parametro, $idEmpresa);
	$aParametro = array (
			"idFranquia" => 1,
			"idEmpresa" => $idEmpresa,
			"idUsuario" => 0,
			"parametro" => $parametro,
			"descricao" => $dados["descricao"],
			"visibilidade" => 0,
			"valor" => $valor
	);
	if (trim($dados["descricao"]) != "") {
		ParameterEngine :: setParameter($aParametro);
	}
}

function getPageName($removeExtension = false) {
	$name = basename($_SERVER['PHP_SELF']);
	$name = (empty($name)) ? "index.php" : $name ;
	if ($removeExtension) {
		$name = substr($name, 0, strrpos($name, "."));
	}
	return $name;
}

function removeParametersFromString($string) {
	$pos = strpos($string, "?");
	$pos = ($pos === false) ? strpos($string, "#") : $pos ;
	$pos = ($pos === false) ? strlen($string) : $pos ;
	return substr($string, 0, $pos);
}

function calcularDigitoVerificador($numeroEtiqueta) {
	$prefixo = substr($numeroEtiqueta, 0, 2);
	$numero = substr($numeroEtiqueta, 2, 8);
	$sufixo = trim(substr($numeroEtiqueta, 10));

	$retorno = $numero;
	$dv = "";
	$multiplicadores = array(8, 6, 4, 2, 3, 5, 9, 7);
	$soma = 0;

	// Preenche número com 0 à
	if(strlen($numeroEtiqueta) < 12){
		$retorno = "Error..";
	} else if(strlen($numero) < 8 && strlen($numeroEtiqueta) == 12){
		$zeros = "";
		$diferenca = 8 - strlen($numero);
		for($i = 0; i < $diferenca; $i++){
			$zeros .= "0";
		}
		$retorno = $zeros + $numero;
	} else{
		$retorno = substr($numero, 0, 8);
	}
	for($i = 0; $i < 8; $i++){
		$soma += (int)(substr($retorno, $i, ($i+1) - $i)) * $multiplicadores[$i];
	}
	$resto = $soma % 11;
	if($resto == 0){
		$dv = "5";
	} else if($resto == 1){
		$dv = "0";
	} else{
		$dv = 11 - $resto;
	}
	$retorno .= $dv;
	$retorno = $retorno;
	return $retorno;
}

function criptografarId($id, $chave = "") {
	if ($chave != "") {
		$key = $chave;
	} else {
		$key = "tinyIndicacao_grzeca";
	}
	$crypttext = base64_encode(mcrypt_encrypt(MCRYPT_CAST_256, md5($key), $id, MCRYPT_MODE_ECB));
	return $crypttext;
}

function descriptografarId($id) {
	$key = "tinyIndicacao_grzeca";
	$decrypted = rtrim(mcrypt_decrypt(MCRYPT_CAST_256, md5($key), base64_decode($id), MCRYPT_MODE_ECB), "\0");
	return $decrypted;
}

function precisaSalvarCaixaOculto() {
	if (!defined('CAIXA_OCULTO_SALVAR') || (CAIXA_OCULTO_SALVAR)) {
		return true;
	}
	return false;
}

function converterCodificacao($str){
       $encoding[0] = "UTF-8";
       $encoding[1] = "Windows-1252";
       $encoding[2] = "ISO-8859-1";
       $encoding[3] = "ISO-8859-15";
       $encoding[4] = "Windows-1251";
       
       if (mb_detect_encoding($str, $encoding) != "UTF-8"){
        	$str = utf8_encode($str);
       }
       return $str;
}


function chamarApi($url, $parametros, $metodo) {
	$url = URL_API_ADMIN . $url;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
	
// 	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
// 			'X-ID-EMPRESA: ' . A,
// 		));
	
	if ($metodo == "POST") {
		curl_setopt($ch, CURLOPT_POST, TRUE);
	} else if ($metodo == "PUT") {
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	} else if ($metodo == "DELETE") {
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	} else {
		curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
	}
	
	try {
		$data = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($httpcode != 200 && $httpcode != 201 && $httpcode != 204) {
			debug($metodo . "    " . $url);
			if (!is_null($parametros)) {
  				debug($parametros);
			}
  			debug($data);
  			debug("STATUS " . $httpcode);
		}
		if ($httpcode == 204) {
			$ret = array();
			$ret["status"] = 204;
			$data = json_encode($ret);
		}
		curl_close($ch);
		return json_decode($data);
	} catch (Exception $e) {
		$erro = array();
		$erro["status"] = 500;
		$erro["errors"] = array();
		$erro["errors"][]["title"] = "Erro na chamada da API " . $e->getMessage();
		return json_encode($erro);
	}
}

function obterACLDoUsuario() {
	$_SESSION["permissoes"] = array();
	$_SESSION["permissoes"]["usuario"] = array();
	$_SESSION["permissoes"]["empresa"] = array();
	$_SESSION["permissoes"]["restricoes"] = array();
	
	$url = "api/v1/admin/empresas/" . Auth::$idEmpresa . "/permissoes";
	$permissoesEmpresa = chamarApi($url, null, "GET");
	if ($permissoesEmpresa->status == 200) {
		foreach ($permissoesEmpresa->data as $key => $permissao) {
			$_SESSION["permissoes"]["empresa"][$permissao->nomeModulo] = $permissao->idModulo;
		}
	} else {
		\UtilsApi\errorApi("Erro ao obter as permissoes da Empresa", $url . "\n" . json_encode($permissoesEmpresa) . "\n" . print_r($_SESSION, true));
	}
	
	if (isset($_SESSION["user"]["id"])) {
		$idUsuario = $_SESSION["user"]["id"];
		if($idUsuario != "") {
			$url = "api/v1/admin/empresas/" . Auth::$idEmpresa . "/usuarios/" . $idUsuario . "/permissoes";
			$permissoesUsuario = chamarApi($url, null, "GET");
			if ($permissoesUsuario->status == 200) {
				foreach ($permissoesUsuario->data as $key => $permissao) {
					$_SESSION["permissoes"]["usuario"][$permissao->nomeModulo] = $permissao->idModulo;
				}
			} else {
				\UtilsApi\errorApi("Erro ao obter as permissoes do usuario", $url . "<br/>" . $permissoesUsuario->status);
			}
			
			$url = "api/v1/admin/empresas/" . Auth::$idEmpresa . "/usuarios/" . $idUsuario . "/restricoes";
			$restricoesAcoes = chamarApi($url, null, "GET");
			if ($restricoesAcoes->status == 200) {
				foreach ($restricoesAcoes->data as $key => $permissao) {
					$_SESSION["permissoes"]["restricoes"][$permissao->nomeModulo] = array();
					$_SESSION["permissoes"]["restricoes"][$permissao->nomeModulo]["inclusao"] = $permissao->inclusao;
					$_SESSION["permissoes"]["restricoes"][$permissao->nomeModulo]["edicao"] = $permissao->edicao;
					$_SESSION["permissoes"]["restricoes"][$permissao->nomeModulo]["exclusao"] = $permissao->exclusao;
				}
			} else {
				\UtilsApi\errorApi("Erro ao obter as restrições do usuario", $url . "<br/>" . $restricoesAcoes->status);
			}
		} else {
			$_SESSION["permissoes"]["usuario"] = $_SESSION["permissoes"]["empresa"];
		}
	} else {
		$_SESSION["permissoes"]["usuario"] = $_SESSION["permissoes"]["empresa"];
	}
}

function iniciarSessionPermissoes() {
	if (!isset($_SESSION["permissoes"]) || (!isset($_SESSION["recarreguei_permissoes"]))) {
		obterACLDoUsuario();
		$_SESSION["recarreguei_permissoes"] = "S";
	}
}

function encode_parameter($parameter, $replaceSpecialChars = true) {
	$parameter = urlencode($parameter);
	return ($replaceSpecialChars) ? str_replace(array("%2F","%5C"), array("%252F","%255C"), $parameter) : $parameter ;
}

function gerarApiKeyEmpresa($idEmpresa) {
	$apiKey = montarApiKeyEmpresa($idEmpresa);
	
	$aCadastro["id"] = $idEmpresa;
	$aCadastro["apikey"] = $apiKey;
	$url = "api/v1/admin/empresas/" . $idEmpresa;
	$retornoApiAtualizarEmpresa = chamarApi($url, json_encode($aCadastro), "PUT");
	if ($retornoApiAtualizarEmpresa->status != 204) {
		\UtilsApi\errorApi("ApiKey não atualizada", $retornoApiAtualizarEmpresa->status . "<br/>" . json_encode($aCadastro));
	}
	return $apiKey;
}

function montarApiKeyEmpresa($idEmpresa) {
	$apiKey = sha1($idEmpresa . time());
	return $apiKey;
}

function carregarBaseDeDadosCliente($database_name) {
	//Eu tenho vergonha (e muita) desta função, mas é necessário. Ass.:Marcos
	$retorno = array("sucesso" => false);
	if (trim($database_name) != "") {
		$bd = new BancoDados();
		$dadosBanco = $bd->getBancoDados($database_name);
		if (is_null($database_name)) {
			$retorno["mensagem"] = "DataBase inválida";
			mail("error@tiny.com.br", "Banco nao obtido", Auth::$idEmpresa . $database_name . json_encode($_SERVER) . " <br/><br/><br/>" . json_encode(debug_backtrace()));
			die("Banco de dados inexistente");
		}
		$emManutencao = $bd->estaEmManutencao($database_name);
		if ($emManutencao) {
			$retorno["mensagem"] = "Em Manutenção";
		} else {
			$_SESSION["conexao"] = $dadosBanco;
			$_SESSION["database_name"] = $database_name;
			require_once(SYSTEM_DIR . "utils/configs/banco.cliente.php");
			require_once(SYSTEM_DIR . "utils/configs/log.cliente.php");
			$retorno["sucesso"] = true;
		}
	} else {
		$retorno["mensagem"] = "DataBase não informada";
		die("Banco de dados inválido");
	}
	return $retorno;
}

function escapeRequestParametersPHP() {
	$retorno = array();
	foreach ($_REQUEST as $key => $value) {
		$retorno[$key] = escapeRequestParameterPHP($value);
	}
	return $retorno;
}

function escapeRequestParameterPHP($valor) {
	return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

function escapeRequestParametersJS() {
	$retorno = array();
	foreach ($_REQUEST as $key => $value) {
		if (is_array($value)) {
			foreach ($value as $v) {
				$retorno[$key][] = escapeRequestParameterJS($v);
			}
		} else {
			$retorno[$key] = escapeRequestParameterJS($value);
		}
	}
	return $retorno;
}

function escapeRequestParameterJS($valor) {
	return str_ireplace("</script>", "<\/script>", addslashes($valor));
}

function logarMensagemImportacaoEcommerce($msg, $callback, $tipo) {
	$objResponse = new xajaxResponse();
	
	if (Auth::$idEmpresa == 238552417) { // BELEZADEMULHER
		incluirTextoLog($msg);
	}

	if ($callback != "" && $tipo == 'redirect') {
		$objResponse->redirect(HTTP_DIR . $callback);
	}

	return $objResponse;
}

function nonEmptyConcat($str1, $divider, $str2) {
	$str1 = trim($str1);
	$str2 = trim($str2);

	$empty1 = empty($str1);
	$empty2 = empty($str2);

	if (!$empty1 && !$empty2) {
		return $str1 . $divider . $str2;
	} else if (!$empty1) {
		return $str1;
	} else if (!$empty2) {
		return $str2;
	}
}

?>