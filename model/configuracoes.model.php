<?php
require_once(SYSTEM_DIR . "model/common/list.model.class.php");

class Configuracoes extends ListModel{
	
		
   function obterConfiguracoes(){
		$query = " select * from configuracoes where id = 1";
		$this->result = query_mysqli($query);
	}
	
	function alterarConfiguracoes($aDados){		
		$query = " REPLACE INTO `configuracoes` VALUES (1, ".
				 " '".$aDados['titulo']."',".
				 " '".$aDados['valorMaximoX']."',".
				 " '".$aDados['valorMaximoY']."',".
				 " '".$aDados['intervaloX']."',".
				 " '".$aDados['intervaloY']."',".
				 " '".$aDados['animacao']."');";
		query_mysqli($query);
	}	
	
	
}