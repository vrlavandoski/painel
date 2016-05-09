<?php
require_once(SYSTEM_DIR . "model/common/list.model.class.php");

class Clientes extends ListModel{
	
		
    function obterClientesPorNome($nome){
		$query = " select * from clientes " .
				 " where nome like '%$nome%' " .
				 " order by nome";
		$this->result = query_mysqli($query);
	}	
	
	function obterCliente($id){
		$query = " select * from clientes " .
				" where id = $id ";
		$this->result = query_mysqli($query);
	}
	
	function alterarCliente($id, $aDados){		
		$query = " UPDATE `clientes` SET ".
				 " `nome` = '".$aDados['nome']."',".
				 " `siglaGrafico` = '".$aDados['siglaGrafico']."',".
				 " `corGrafico` = '".$aDados['corGrafico']."',".
				 " `ip` = '".$aDados['ip']."',".
				 " `porta` = '".$aDados['porta']."',".
				 " `ativo` = '".$aDados['ativo']."'".
				 " WHERE `clientes`.`id` = ".$id.";";
		query_mysqli($query);
	}
	
	function adicionarCliente($aDados){
		
		$query = "INSERT INTO `clientes` (`id`, `nome`, `siglaGrafico`, `corGrafico`, `ip`, `porta`, `ativo`)".
			 	 "VALUES (NULL, '".$aDados['nome']."', '".$aDados['siglaGrafico']."', '".$aDados['corGrafico']."', '".$aDados['ip']."', '".$aDados['porta']."', '".$aDados['ativo']."');";		
		query_mysqli($query);
	}
	
	function excluir($id){
		$query = "DELETE FROM `clientes` where id = '".$id."'";
		query_mysqli($query);
	}
	
	function obterClientesAtivos(){
		$sql1 = "select * from clientes where ativo = 'S'";
		$this->result = query_mysqli($sql1);		
	}
	
	function obterLeiturasGrafico($limite){
		$sql2 = "select * from (select l.*, c.ativo from leituras l join clientes c on c.id = l.idCliente where ativo = 'S' order by data desc, idCliente limit 0,$limite) as ordenador order by data";
		$this->result = query_mysqli($sql2);
	}
	
}